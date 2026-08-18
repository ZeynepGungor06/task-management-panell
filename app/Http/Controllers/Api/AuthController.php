<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\ResetPasswordOtpMail;
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
            'access_token'=>$token,
            'token_type'=>'Bearer',
            'user'=>$user
        ]);

    }
    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,manager,user',
            'manager_email' => 'nullable|required_if:role,user|email'
        ]);
        $managerId=null;
        if ($request->role === 'user' && $request->filled('manager_email')) {
            $manager = User::where('email', $request->manager_email)
                           ->where('role', 'manager')
                           ->first();
                           if(!$manager){
                            return response()->json([
                                'success'=>false,
                                'message'=>"Girdiğiniz '{$request->manager_email}' e-postasıyla kayıtlı bir müdür bulunamadı."
                            ],422);
                           }
                           $managerId=$manager->id;
                           }
                           $user=User::create([
                            'name'=>$request->name,
                            'email'=>$request->email,
                            'password'=>Hash::make($request->password),
                            'role'=>$request->role,
                            'manager_id'=>$managerId,
                           ]);
                           $token=$user->createToken('auth_token')->plainTextToken;
                           return response()->json([
                            'success'=>true,
                            'message'=>'Kayıt başarılı ve giriş yapıldı',
                            'access_token'=>$token,
                            'token_type'=>'Bearer',
                            'user'=>$user


                           ],201);

    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success'=>true,
            'message'=>'Başarıyla çıkış yapıldı ve oturum kapatıldı'
        ],200);
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
    public function updateProfile(Request $request){
        $user=$request->user();
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|string|email|max:255|uniques:users,email,' . $user->id,
        ]);

        $user->name=$request->name;
        $user->email=$request->email;
        $user->save();
        return response()->json([
            'success'=>true,
            'message'=>'Profil bilgileri başarıyla güncellendi',
            'user'=>$user
        ],200);
    }
    public function changePassword(Request $request){
        $request->validate([
            'old_password'=>'required',
            'new_password'=>'required|min:6|confirmed',
        ]);
        $user=$request->user();
        if(!Hash::check($request->old_password,$user->password)){
            return response()->json([

            'success'=>false,
            'message'=>'Şifre Hatalı! Lütfen tekrar deneyin.'
            ],400);
        }
        $user->password=Hash::make($request->new_password);
        $user->save();
        return response()->json([
            'success'=>true,
            'message'=>'Şifreniz başarıyla güncellendi.'
        ],200);
    }
    public function sendResetOtp(Request $request){
        $request->validate([
            'email'=>'required|email|exists:users,email'
        ]);
        $email=$request->email;
        $otpCode=(string) rand(100000, 999999);

        Cache::put('password_reset_otp_' . $email, $otpCode, now()->addMinutes(3));
        Mail::to($email)->send(new ResetPasswordOtpMail($otpCode));

        return response()->json([
            'success'=>true,
            'message'=>'Şifre sıfırlama kodu e-posta adresinize başarıyla gönderildi.'
        ],200);
    }

    
}
