<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\TagController;


Route::get('/ping', function () {
    return response()->json(['mesaj' => 'Laravel ayakta ve çalışıyor!', 'zaman' => now()]);
});
Route::post('/login', [AuthController::class, 'login']);
 Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->group(function () {
Route::get('/profil',function(Request $request){
    return $request->user();
});
Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);


Route::put('/tasks/{task}', [TaskController::class, 'update']);
Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);

Route::post('/tasks/{task}/files', [\App\Http\Controllers\Api\DocumentController::class, 'store']);
    Route::get('/files/{id}/download', [\App\Http\Controllers\Api\DocumentController::class, 'download']);
    Route::delete('/files/{id}', [\App\Http\Controllers\Api\DocumentController::class, 'destroy']);

    // CommentController Rotaları
    Route::post('/tasks/{task}/comments', [\App\Http\Controllers\Api\CommentController::class, 'store']);
    Route::patch('/comments/{id}/spam', [\App\Http\Controllers\Api\CommentController::class, 'toggleSpam']);
    Route::delete('/comments/{id}', [\App\Http\Controllers\Api\CommentController::class, 'destroy']);
    // TagController Rotaları
    Route::post('/tags', [\App\Http\Controllers\Api\TagController::class, 'store']);
    Route::delete('/tags/{tag}', [\App\Http\Controllers\Api\TagController::class, 'destroy']);
    Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken']);
    // TEST İÇİN GEÇİCİ ROTA (İşimiz bitince silebiliriz)
    Route::get('/test-reminder/{taskId}', function (\Illuminate\Http\Request $request, $taskId) {
        $task = \App\Models\Task::findOrFail($taskId);
        
        // Görev kime atandıysa onu buluyoruz, yoksa oluşturanı alıyoruz
        $userId = $task->assigned_to ?? $task->user_id;
        $user = \App\Models\User::find($userId);

        if($user) {
            // Yazdığımız o Notification sınıfını zorla tetikliyoruz
            $user->notify(new \App\Notifications\TaskReminderNotification($task));
        }

        return response()->json(['message' => 'Test hatırlatıcısı başarıyla fırlatıldı!']);
        
    });
    Route::get('/tags', [\App\Http\Controllers\Api\TagController::class, 'index']);
   Route::post('/logout', [AuthController::class, 'logout']);
   Route::put('/tasks/{id}/details', [\App\Http\Controllers\Api\TaskController::class, 'updateDetails']);
   Route::get('/team-members', [\App\Http\Controllers\Api\TaskController::class, 'teamMembers']);
});