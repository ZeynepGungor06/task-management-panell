<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Notifications\TaskReminderNotification;
use App\Http\Controllers\TagController;

// Giriş ve Kayıt Sayfaları Rotaları
Route::get('/', function () {
    return redirect()->route('login'); // Eğer giriş yapılmışsa Laravel zaten otomatik dashboard'a atar
});
Route::get('/login', [AuthController::class, 'showlogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/statistics',[TaskController::class,'statistics'])->name('statistics');
Route::get('/profile', [App\Http\Controllers\AuthController::class, 'editProfile'])->name('profile.edit');
   Route::put('/profile', [App\Http\Controllers\AuthController::class, 'updateProfileWeb'])->name('profile.update');

// Kullanıcı ve Admin Panelleri (İleride yetkiye göre burayı korumaya alacağız)
Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');
// Görev (Task) İşlemleri
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
// Görev Dosyaları Rotaları
Route::post('/tasks/{task}/files', [DocumentController::class, 'store'])->name('files.store');
Route::get('/files/{id}/download', [DocumentController::class, 'download'])->name('files.download');
Route::delete('/files/{id}', [DocumentController::class, 'destroy'])->name('files.destroy');

// Görev Yorumları Rotaları
Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');

Route::post('/notifications/{id}/read', function (Request $request, $id) {
    $notification = $request->user()->notifications()->findOrFail($id);
    $notification->markAsRead(); 
    return back();
})->name('notifications.read');
Route::put('/comments/{id}/spam', [App\Http\Controllers\CommentController::class, 'toggleSpam'])->name('comments.spam');
Route::patch('/tasks/{id}/details', [TaskController::class, 'updateDetails'])->name('tasks.update_details');
Route::get('/ping', function () {
    return response()->json(['mesaj' => 'Laravel ayakta ve çalışıyor!', 'zaman' => now()]);
});
Route::get('/send-reminders',function(){
    $tasks=Task::with('user')
    ->whereDate('due_date',Carbon::Tomorrow())
    ->where('is_completed',false)
    ->where('is_reminder_sent', false)
    ->get();
    $sentCount=0;

    foreach($tasks as $task){
        if($task->user){
            $mesaj="Merhaba" . $task->user->name . ", '" . $task->title . "' adli görevinizin teslim tarihi yarın doluyor.";

            Mail::raw($mesaj,function($mail) use ($task){
                $mail->to($task->user->email)
                ->subject('Görev Hatırlatması: Yaklaşan teslim Tarihi');
            });
            $task->user->notify(new TaskReminderNotification($task));
            $task->is_reminder_sent=true;
            $task->save();
            $sentCount++;
        }
    }
    return response()->json(['mesaj'=>"İşlem tamam! $sentCount kişiye hatırlatma e-postası gönderildi."]);
});
Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])->group(function () {
Route::post('/tags',[TagController::class, 'store'])->name('tags.store');
Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

});

Route::get('/dashboard', [TaskController::class, 'index'])->middleware('auth')->name('dashboard');