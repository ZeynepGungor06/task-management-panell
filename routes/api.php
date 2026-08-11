<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\TaskController;

Route::get('/ping', function () {
    return response()->json(['mesaj' => 'Laravel ayakta ve çalışıyor!', 'zaman' => now()]);
});
Route::post('/login', [AuthController::class, 'login']);
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
});