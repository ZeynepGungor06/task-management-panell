<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // Dashboard Ekranını Görüntüleme
    public function index(Request $request)
    {
        $user = Auth::user();
        $users = [];
        $selectedUserId = $user->id;

        if ($user->role === 'admin') {
            // Admin, sadece kendine bağlı ('admin_id'si kendi ID'si olan) kullanıcıları çeker
            $users = User::where('admin_id', $user->id)->get();
            
            // Eğer ekrandan bir kullanıcı seçildiyse onun ID'sini al, yoksa kendi ekranında kal
            if ($request->has('user_id')) {
                $selectedUserId = $request->user_id;
            }
        }

        // Seçili kullanıcının görevlerini tarihe göre en yeniden eskiye sıralı getir
        $tasks = Task::where('user_id', $selectedUserId)->orderBy('created_at', 'desc')->get();
        $selectedUser = User::find($selectedUserId);

        return view('dashboard', compact('tasks', 'users', 'selectedUser', 'user'));
    }

    // Yeni Görev Ekleme
    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);
        
        $task = new Task();
        $task->title = $request->title;
        $task->is_completed = false;
        
        // Eğer giriş yapan kişi admin ise ve listeden birini seçip görev atıyorsa:
        if (Auth::user()->role === 'admin' && $request->has('user_id')) {
            $task->user_id = $request->user_id;
        } else {
            // Normal kullanıcıysa görevi kendine atar
            $task->user_id = Auth::id(); 
        }

        $task->save();
        return back(); // İşlem bitince aynı sayfaya geri dön
    }

    // Görevi Tamamlandı / Bekliyor Yapma
    public function update(Task $task)
    {
        $task->is_completed = !$task->is_completed;
        $task->save();
        return back();
    }

    // Görevi Silme
    public function destroy(Task $task)
    {
        $task->delete();
        return back();
    }
}