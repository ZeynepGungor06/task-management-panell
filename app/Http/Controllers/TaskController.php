<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\UnauthorizedTaskAccessException;

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
             ->orderBy('created_at', 'desc')
             ->get();

        $selectedUser = User::find($selectedUserId);

        return view('dashboard', compact('tasks', 'users', 'selectedUser', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);
        
        $task = new Task();
        $task->title = $request->title;
        $task->is_completed = false;
        
        // Admin veya Müdür birini seçtiyse ona ata, yoksa kendine ata
        if ((Auth::user()->role === 'admin' || Auth::user()->role === 'manager') && $request->has('user_id')) {
            $task->user_id = $request->user_id;
        } else {
            $task->user_id = Auth::id(); 
        }

        $task->save();
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
    
    // update fonksiyonu aynı kalabilir...
}