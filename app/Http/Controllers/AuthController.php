<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showlogin(){
        return view('auth.login');
    }
    public function login(Request $request){
        $credentials=$request->validate([
            "email"=> "required|email",
            "password"=> "required"
        ]);

        if(Auth::attempt($credentials)){
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }
        return back()->withErrors(['email'=> 'Girilen bilgiler eşleşmiyor']);

    }
    public function showRegister(){
        return view('auth.register');
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user',
            'admin_username' => 'nullable|required_if:role,user' // Admin ise boş bırakılabilir
        ]);

        $adminId = null;

        // Sadece normal kullanıcıysa admin kontrolü yap
        if ($request->role === 'user') {
            $admin = User::where('name', $request->admin_username)
                         ->where('role', 'admin')
                         ->first();

            if (!$admin) {
                return back()->withErrors(['admin_username' => 'Belirttiğiniz isimde sistemde geçerli bir Admin bulunamadı!'])->withInput();
            }
            
            $adminId = $admin->id;
        }

        // Kullanıcıyı oluştur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'admin_id' => $adminId
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
public function logout(Request $request){
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
}
}
