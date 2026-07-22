<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\InvalidManagerException;

class AuthController extends Controller
{
    public function showlogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors(['email' => 'Girilen bilgiler eşleşmiyor']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // 1. Doğrulama kurallarını yeni rollere göre güncelledik
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,manager,user',
            'manager_email' => 'nullable|required_if:role,user|email' // Sadece User ise zorunlu
        ]);

        $managerId = null;

        // 2. Eğer rol "user" ise ve müdür e-postası girildiyse kontrol et
        if ($request->role === 'user' && $request->filled('manager_email')) {
    $manager = User::where('email', $request->manager_email)
                   ->where('role', 'manager')
                   ->first();
    
    if (!$manager) {
        // Eski 'return back()' yerine OOP Hata Nesnemizi fırlatıyoruz!
        throw new InvalidManagerException("Girdiğiniz '{$request->manager_email}' e-postasıyla kayıtlı bir müdür bulunamadı.");
    }
    
    $managerId = $manager->id;
}

        // 3. Kullanıcıyı seçtiği rol ile oluştur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, // Formda ne seçildiyse (admin, manager veya user) o kaydedilir
            'manager_id' => $managerId,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}