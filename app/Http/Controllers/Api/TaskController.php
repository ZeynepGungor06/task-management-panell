<?php

namespace App\Http\Controllers\Api; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\UnauthorizedTaskAccessException;
use App\Exceptions\DuplicateTaskException;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskCompletedNotification;
use App\Models\Tag;

class TaskController extends Controller
{
    public function index(Request $request){
        $user = Auth::user();
        $selectedUserId = Auth::id();

        if($user && $user->role === 'admin'){
            $users = User::all();
        } elseif($user->role === 'manager'){
            $users = User::where('manager_id', $user->id)->get();
        }
        
        // DÜZELTME: Süslü parantez hemen burada kapanmalı! Sadece ID atamasını yapıp çıkmalı.
        if(($user->role === 'admin' || $user->role === 'manager') && $request->has('user_id')){
            $selectedUserId = $request->user_id;
        } 

        // SORGULAMA VE DÖNDÜRME İŞLEMİ IF BLOĞUNUN DIŞINDA VE HERKES İÇİN ÇALIŞMALI
        $tasks = Task::with(['files', 'comments', 'comments.user', 'tags'])
             ->where('user_id', $selectedUserId)
             ->whereNull('parent_id')
             ->when($request->filled('search'), function($query) use ($request){
                $aranan = $request->search;
                if(str_starts_with($aranan,'#')){
                    $etiketAdi = ltrim($aranan,'#');
                    return $query->whereHas('tags',function($q) use ($etiketAdi){
                        $q->where('name','LIKE','%' . $etiketAdi.'%');
                    });
                }
                return $query->where('title','LIKE','%' . $aranan . '%');
             })
             ->when($request->filled('priority'),function ($query) use ($request){
                return $query->where('priority', $request->priority);
             })
             ->when($request->filled('status'), function ($query) use ($request){
                if($request->status === 'completed'){
                    return $query->where('is_completed', true);
                } elseif($request->status === 'pending'){
                    return $query->where('is_completed', false);
                }
             })
             ->when($request->filled('date_filter'), function ($query) use ($request){
                if($request->date_filter === 'overdue'){
                    return $query->whereNotNull('due_date')->where('due_date', '<', now());
                } elseif($request->date_filter === 'today'){
                    return $query->whereNotNull('due_date')->whereDate('due_date', now()->toDateString());
                }    
             })
             ->orderBy('created_at', 'desc')
             ->paginate(10)
             ->appends($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Görevler başarıyla getirildi.',
            'data' => [
                'tasks' => $tasks
            ]
        ], 200);
    }
    public function store(Request $request){
        $request->validate([
            'title' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:tasks,id'
        ]);
        
        $task = new Task();
        $task->title = $request->title;
        $task->is_completed = false;
        $task->priority = $request->priority;
        $task->due_date = $request->due_date;
        $task->parent_id = $request->parent_id;
        
        if ((Auth::user()->role === 'ADMİN' || Auth::user()->role === 'manager') && $request->has('user_id')) {
            $task->user_id = $request->user_id;
        } else {
            $task->user_id = Auth::id(); 
        }

        $duplicateTask = Task::where('user_id', $task->user_id)
            ->where('title', $request->title)
            ->exists();
            
        if($duplicateTask) {
            throw new DuplicateTaskException(); // Exception'lar JSON API'de otomatik olarak anlamlı hata döner.
        }

        $task->save();

        if($request->has('tags')){
            $task->tags()->attach($request->tags);
        }

        if($task->user_id !== Auth::id()){
            $assignedUser = User::find($task->user_id);
            if($assignedUser) {
                $assignedUser->notify(new TaskAssignedNotification($task));
            }
        }

        return response()->json([
            'succes'=>true,
            'message'=>'Görev başarıyla oluşturuldu',
            'task'=>$task
        ],201);
        
    }
    public function destroy(Task $task){
        
        $user = Auth::user();

        // 1. Kural: Normal kullanıcılar görev silemez
        if ($user->role === 'user') {
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcılar görev silme yetkisine sahip değildir.'
            ], 403);
        }

        // 2. Kural: Manager sadece kendi ekibinin veya kendi görevlerini silebilir
        if ($user->role === 'manager' && $task->user->manager_id !== $user->id && $task->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Sadece kendi ekibinizdeki kullanıcıların görevlerini silebilirsiniz.'
            ], 403);
        }

        // 3. Kural: Yetki kontrolünden geçenler görevi silebilir
        $task->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Görev başarıyla silindi.'
        ], 200);
        
    }
    public function update(Request $request, Task $task){
        if($task->due_date && $task->due_date->isPast()){
            return response()->json(['succes'=>false,'message'=>'Bu görevin teslim tarihi geçtiği için işlem yapılamaz'],403);
        }
        if(!$task->is_completed){
            $hasUnfinishedSubtask = $task->children()->where('is_completed', false)->exists();
            if($hasUnfinishedSubtask){
                return response()->json(['success' => false, 'message' => 'Bu görevi tamamlayabilmek için önce tüm alt görevleri bitirmelisiniz.'], 400);
            }
        }

        $task->is_completed = !$task->is_completed;
        $task->save();

        if($request->has('tags')){
            $task->tags()->sync($request->tags);
        } else {
            $task->tags()->detach();
        }
        
        if ($task->is_completed && Auth::user()->role === 'user' && Auth::user()->manager_id) {
            $manager = User::find(Auth::user()->manager_id); 
            if ($manager) {
                $manager->notify(new TaskCompletedNotification($task, Auth::user()));
            }
        }
        return response()->json([
            'succes'=>true,
            'message'=>'Görev durumu başarıyla güncellendi',
            'task'=>$task
        ],200);
    }
}
