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
use Illuminate\Support\Facades\Http;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class TaskController extends Controller
{
    public function index(Request $request){
        $user = Auth::user();
        $selectedUserId = Auth::id();

        
        
        // DÜZELTME: Süslü parantez hemen burada kapanmalı! Sadece ID atamasını yapıp çıkmalı.
        if(($user->role === 'admin' || $user->role === 'manager') && $request->has('user_id')){
            $selectedUserId = $request->user_id;
        } 

        // SORGULAMA VE DÖNDÜRME İŞLEMİ IF BLOĞUNUN DIŞINDA VE HERKES İÇİN ÇALIŞMALI
        $tasks = Task::with(['files', 'comments', 'comments.user', 'tags'])
             ->where('user_id', $selectedUserId)
             ->when($request->filled('parent_id'), function ($query) use ($request) {
        return $query->where('parent_id', $request->parent_id);
    }, function ($query) {
        return $query->whereNull('parent_id');
    })
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
        $userId = Auth::id();
        if ((Auth::user()->role === 'admin' || Auth::user()->role === 'manager') && $request->has('user_id')) {
            $userId = $request->user_id;
        } 

        $duplicateTask = Task::where('user_id', $userId)
            ->where('title', $request->title)
            ->exists();
            
        if($duplicateTask) {
            throw new DuplicateTaskException();
        }
        DB::beginTransaction();
        try {
        $task = new Task();
        $task->title = $request->title;
        $task->is_completed = false;
        $task->priority = $request->priority;
        $task->due_date = $request->due_date;
        $task->parent_id = $request->parent_id;
        $task->assigned_to=$request->assigned_to;
        $task->user_id = $userId;
       

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

        if ($request->filled('assigned_to')) {
        $assignedUser = User::find($request->assigned_to);
        
        if($assignedUser && $assignedUser->fcm_token != null){
            try {
                $messaging = Firebase::messaging();
                
                $message = CloudMessage::withTarget('token', $assignedUser->fcm_token)
                    ->withNotification(Notification::create(
                        'Yeni Görev Atandı! 🚀',
                        'Sana yeni bir görev atandı: ' . $task->title
                    ))
                    ->withData(['task_id' => (string) $task->id]); // Mobilci için görev ID'si
                    
                $messaging->send($message);
                
            } catch (\Exception $e) {
                // Firebase kaynaklı (şifre, ağ vb.) bir hata olursa sistemi çökertme, sadece logla
                Log::error('Firebase Bildirim Hatası: ' . $e->getMessage());
            }
        }
    }
    try{
        $targetId = $assignedUser ? $assignedUser->id : $userId;
        $firestore=$firestore = \Kreait\Laravel\Firebase\Facades\Firebase::firestore()->database();
        $firestore->collection('notifications')->add([
            'user_id' => $targetId,
            'title' => 'Yeni görev atandı',
            'message' => 'Sana yeni bir görev atandı: '. $task->title,
            'task_id' => $task->id,
            'is_read' => false,
            'created_at' => now()->toDateTimeString()
        ]);
    }catch(\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Firestore Yazma Hatası: ' . $e->getMessage());
    }
    DB::commit();

        return response()->json([
            'succes'=>true,
            'message'=>'Görev başarıyla oluşturuldu',
            'task'=>$task
        ],201);}catch(\Exception $e) {
            DB::rollBack();
            Log::error('API Görev ekleme hatası: '. $e->getMessage());
            return response()->json([
                'success'=>false,
                'message'=> 'Görev oluşturulurken sistemsel bir hata meydana geldi'
            ],500);
        }
        
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
        DB::beginTransaction();
        try {

        // 3. Kural: Yetki kontrolünden geçenler görevi silebilir
        $task->delete();
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Görev başarıyla silindi.'
        ], 200);}catch(\Exception $e) {
            DB::rollBack();
            Log::error('API Görev silme hatası: '. $e->getMessage());
            return response()->json([
                'success'=> false,
                'message'=> 'Görev silinirken sistemsel bir hata meydana geldi'
            ],500);
        }
        
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
       DB::beginTransaction();

        try {
            $task->is_completed = !$task->is_completed;
            $task->save();

            if($request->has('tags')){
                $task->tags()->sync($request->tags);
            } else {
                $task->tags()->detach();
            }
            
           // ... (Üst kısımdaki etiket (tags) kodları aynı kalacak) ...

            // Görev tamamlandıysa bildirim işlemlerini başlat
            if ($task->is_completed) {
                
                // Bildirimin gideceği kişiyi belirle (Örn: Görevi oluşturan kişiye veya yöneticiye)
                // Eğer user ise yöneticisine, değilse görevi ilk oluşturan kişiye bildirim gitsin.
                $notifyUserId = (Auth::user()->role === 'user' && Auth::user()->manager_id) 
                                ? Auth::user()->manager_id 
                                : $task->user_id;

                $targetUser = User::find($notifyUserId);

                if ($targetUser) {
                    
                    // 1. Laravel İçi Bildirim (Ve Arka Plan Kuyruğu)
                    $targetUser->notify(new TaskCompletedNotification($task, Auth::user()));

                    // 2. Firebase Push Notification (Mobil Cihaza Bildirim)
                    if ($targetUser->fcm_token != null) {
                        try {
                            $messaging = Firebase::messaging();
                            $message = CloudMessage::withTarget('token', $targetUser->fcm_token)
                                ->withNotification(Notification::create(
                                    'Görev Tamamlandı! ✅',
                                    Auth::user()->name . ' şu görevi tamamladı: ' . $task->title
                                ))
                                ->withData(['task_id' => (string) $task->id]); 
                                
                            $messaging->send($message);
                        } catch (\Exception $e) {
                            Log::error('Firebase Push Bildirim Hatası: ' . $e->getMessage());
                        }
                    }

                    // 3. Firestore Veritabanına Yazma (Mobilin Dinlediği Kısım)
                    try {
                        // Kesin yol garantisi
                         putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('app/firebase-auth.json'));
                        $firestore = \Kreait\Laravel\Firebase\Facades\Firebase::firestore()->database();
                        $firestore->collection('notifications')->add([
                            'user_id' => $targetUser->id, 
                            'title' => 'Görev Tamamlandı! ✅',
                            'message' => Auth::user()->name . ' şu görevi tamamladı: ' . $task->title,
                            'task_id' => $task->id,
                            'is_read' => false,
                            'created_at' => now()->toDateTimeString()
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Firestore Yazma Hatası: ' . $e->getMessage());
                    }
                }
            }

            
            // ... (Alt kısımdaki return kodları aynı kalacak) ...
            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Görev durumu başarıyla güncellendi',
                'task' => $task
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Görev Güncelleme Hatası: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Görev güncellenirken sistemsel bir hata meydana geldi.'
            ], 500);
        }
    }
    public function updateDetails(Request $request,$id){
        if ($request->user()->role !== 'admin' && $request->user()->role !== 'manager') {
            return response()->json([
                'success' => false,
                'message' => 'Görev detaylarını sadece yöneticiler değiştirebilir.'
            ], 403); // 403: Forbidden (Yasak)
        }
        
    
        $request->validate([
            'title' => 'required|string|max:255', 
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date'
        ]);
        
    
        DB::beginTransaction();
        try {
            $task=\App\Models\Task::where('id',$id)->lockForUpdate()->firstOrFail();
            $task->title=$request->title;
            $task->priority=$request->priority;
            $task->due_date=$request->due_date;
            $task->save();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Görev detayları başarıyla güncellendi.',
                'task' => $task 
            ], 200);



        }
        catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            DB::rollBack();
            return response()->json([
                'success'=>false,
                'message'=>'Belirtilen görev bulunamadı'
            ],404);
        }
        catch(\Exception $e){
             DB::rollBack();
            Log::error('API Görev Detay Güncelleme Hatası: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Görev detayları güncellenirken sistemsel bir hata meydana geldi.'
            ], 500);
        }
    }
    public function teamMembers(Request $request)
    {
        $user = $request->user();
        $users = collect(); 

        if($user->role === 'admin'){
        
            $users = User::select('id', 'name', 'email', 'role')->get();
        } elseif($user->role === 'manager'){
            
            $users = User::where('manager_id', $user->id)
                         ->select('id', 'name', 'email', 'role')
                         ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $users
        ], 200);
    }
}