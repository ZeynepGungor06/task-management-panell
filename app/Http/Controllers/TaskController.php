<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\UnauthorizedTaskAccessException;
use App\Exceptions\DuplicateTaskException;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskCompletedNotification;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user(); 
        $users = [];
        $selectedUserId = $user->id;

        // KİM KİMİ GÖREBİLİR MANTIĞI:
        if ($user->role === 'admin') {
            // Admin sistemdeki HERKESİ (tüm user ve manager'ları) görebilir
            $users = User::all(); 
        } elseif ($user->role === 'manager') {
            // Müdür SADECE kendine bağlı (manager_id'si kendi ID'si olan) kişileri görebilir
            $users = User::where('manager_id', $user->id)->get();
        }

        // Eğer açılır listeden bir kullanıcı seçildiyse:
        if (($user->role === 'admin' || $user->role === 'manager') && $request->has('user_id')) {
            $selectedUserId = $request->user_id;
        }

        $tasks = Task::with(['files', 'comments', 'comments.user'])
             ->where('user_id', $selectedUserId)
             ->when($request->filled('search'), function($query) use ($request){
                return $query->where('title','LIKE','%' . $request->search . '%');
             })
             ->when($request->filled('priority'),function ($query) use ($request){
                return $query->where('priority', $request->priority);
             })
             ->when($request->filled('status'), function ($query) use ($request){
                if($request->status==='completed'){
                    return $query->where('is_completed',true);

                }
                elseif($request->status==='pending'){
                    return $query->where('is_completed', false);
                }
             })
             ->when($request->filled('data_filter'), function ($query) use ($request){
                if($request->date_filter==='overdue'){
                    return $query->whereNotNull('due_date')->where('due_date', '<', now());
                }
                elseif($request->date_filter=== 'today'){
                    return $query->whereNotNull('due_date')->whereDate('due_date', now()->toDateString());
                }
                    
             })
             ->orderBy('created_at', 'desc')
             ->paginate(10)
             ->appends($request->all());

        $selectedUser = User::find($selectedUserId);

        if($request->ajax()){
            $view=view('partials.task_list', compact('tasks','user','selectedUser'))->render();
            return response()->json(['html'=>$view]);
        }

        return view('dashboard', compact('tasks', 'users', 'selectedUser', 'user'));
    }

    public function store(Request $request)
    {
        
        $request->validate(['title' => 'required|string|max:255']);
        
        $task = new Task();
        $task->title = $request->title;
        $task->is_completed = false;
        $task->priority=$request->priority;
        $task->due_date=$request->due_date;
        
        // Admin veya Müdür birini seçtiyse ona ata, yoksa kendine ata
        if ((Auth::user()->role === 'admin' || Auth::user()->role === 'manager') && $request->has('user_id')) {
            $task->user_id = $request->user_id;
        } else {
            $task->user_id = Auth::id(); 
        }
        $duplicateTask = Task::where('user_id', $task->user_id)
            ->where('title', $request->title)
            ->exists();
            
        if($duplicateTask) {
            throw new DuplicateTaskException();
        }

        $task->save();
        if($task->user_id !== Auth::id()){
            $assignedUser=User::find($task->user_id);
            if($assignedUser) {
                $assignedUser->notify(new TaskAssignedNotification($task));
            }
        }
        return back();
    }

    public function destroy(Task $task)
    {
        // GÜVENLİK: Normal kullanıcıların görev silmesini backend'den tamamen engelliyoruz
        if (Auth::user()->role === 'user') {
            throw new UnauthorizedTaskAccessException("Kullanıcılar görev silme yetkisine sahip değildir.");
            
        }
        if(Auth::user()->role === "manager" && $task->user->manager_id!==Auth::id() && $task->user_id !== Auth::id()) {
            throw new UnauthorizedTaskAccessException("Sadece kendi ekibinizdeki kullanıcıların görevlerini silebilirsiniz .");
        }

        $task->delete();
        return back();
    }
    
 public function update(Request $request, Task $task)
    {
        if ($task->due_date && $task->due_date->isPast()) {
        abort(403, 'Bu görevin teslim tarihi geçtiği için işlem yapılamaz.');
    }
        $task->is_completed = !$task->is_completed;
        $task->save();

        
        if ($task->is_completed && Auth::user()->role === 'user' && Auth::user()->manager_id) {
            
            $manager = User::find(Auth::user()->manager_id); 
            
            if ($manager) {
                
                $manager->notify(new \App\Notifications\TaskCompletedNotification($task, Auth::user()));
            }
        }

        return back();
    }
    public function updateDetails(Request $request,$id){
        if ($request->user()->role !== 'admin' && $request->user()->role !== 'manager') {
            abort(403, 'Görev detaylarını sadece yöneticiler değiştirebilir.');
        }
        $request->validate(['priority'=>'required|in:low,medium,high','due_date'=>'nullable|date']);
        $task = \App\Models\Task::findOrFail($id);
        $task->priority = $request->priority;
        $task->due_date = $request->due_date;
        $task->save();
        return back();
    }
    }