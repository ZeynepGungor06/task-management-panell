<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;

Route::get('/ping', function () {
    return response()->json(['mesaj' => 'Laravel ayakta ve çalışıyor!', 'zaman' => now()]);
});
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
Route::get('/profil',function(Request $request){
    return $request->user();
});});