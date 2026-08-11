<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class AuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);
        if(!Auth::attempt($request->only('email','password'))){
            return response()->json([
                'message'=>'Giriş bilgileri hatalı veya böyle bir kullanıcı yok'

            ], 401);
        }
        $user=User::where('email',$request->email)->firstOrFail();
        $token=$user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message'=>'Giriş başarılı',
            'acces_token'=>$token,
            'token_type'=>'Bearer',
            'user'=>$user
        ]);

    }
    public function updateFcmToken(Request $request){
        $request->validate([
            'fcm_token'=>'required|string'
        ]);
        $user=$request->user();
        $user->update([
            'fcm_token'=>$request->fcm_token
        ]);
        return response()->json([
            'success'=>true,
            'message'=>'FCM Token başarıyla kaydedildi'
        ]);
    }
    
}
