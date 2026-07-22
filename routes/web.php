<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\CommentController;

// Giriş ve Kayıt Sayfaları Rotaları
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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