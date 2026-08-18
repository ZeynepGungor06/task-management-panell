<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilim - Task Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- DASHBOARD CSS KODLARININ AYNISI -->
    <style>
        body {
            background-color: #eef5fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            color: #333;
        }
        .navbar {
            background-color: #ffffff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(114, 174, 230, 0.1);
        }
        .navbar h1 {
            margin: 0;
            color: #5b8fb9;
            font-size: 20px;
        }
        .logout-btn {
            background-color: #ff6b6b;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .logout-btn:hover { background-color: #e53935; }
        .container {
            max-width: 800px; /* Profil sayfası için biraz daralttık */
            margin: 40px auto;
            padding: 0 20px;
        }
    </style>
</head>
<body>

    <!-- Üst Menü (DASHBOARD KODUYLA BİREBİR AYNI) -->
    <div class="navbar w-100 mb-4" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
        
        <!-- 1. SOL KUTU: Başlık ve Dashboard Butonu (Profilde olduğumuz için Görevlere Dön butonu koyduk) -->
        <div class="d-flex align-items-center gap-3">
            <h1 class="m-0" style="font-size: 1.5rem; color: #5b8fb9;">Task Management System
                <span style="font-size: 14px; color: #6b8299; margin-left:10px;">
                    ({{ $user->name }} - {{ ucfirst($user->role) }})
                </span>
            </h1>
            
            <!-- Profil sayfasındayken, ana sayfaya dönme butonu -->
            <a href="{{ route('dashboard') }}" class="btn btn-sm" style="border: 1px solid #d1e3f2; color: #5b8fb9; background-color: #fff;">
                <i class="bi bi-arrow-left"></i> Görevlere Dön
            </a>
        </div>

        <!-- 2. SAĞ KUTU: Çıkış -->
        <div class="d-flex align-items-center gap-3">
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn btn btn-sm btn-danger" style="font-weight: bold;">Çıkış Yap</button>
            </form>
        </div>
    </div>

    <!-- PROFIL KUTUSU İÇERİĞİ -->
    <div class="container">
        <div class="card shadow-sm" style="border-radius: 12px; border: none;">
            <div class="card-header text-white" style="background-color: #5b8fb9; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="mb-0"><i class="bi bi-person-circle"></i> Profil Bilgilerim</h5>
            </div>
            <div class="card-body" style="background-color: #ffffff; padding: 24px; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label" style="color: #6b8299; font-weight: bold;">Ad Soyad</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required style="border-color: #d1e3f2; background-color: #f7fbff;">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label" style="color: #6b8299; font-weight: bold;">E-Posta Adresi</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required style="border-color: #d1e3f2; background-color: #f7fbff;">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn" style="background-color: #72aee6; color: white; font-weight: bold; padding: 12px; border-radius: 8px;">
                            <i class="bi bi-save"></i> Bilgilerimi Güncelle
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
        <div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Şifre Güncelle</h5>
    </div>
    <div class="card-body">
        
        <!-- Başarı Mesajını Gösterme Alanı -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Laravel Validasyon Hatalarını Gösterme Alanı -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form doğrudan POST isteği atacak -->
        <form action="{{ route('password.update.web') }}" method="POST">
            @csrf <!-- Bu satır Laravel'de form güvenliği için zorunludur -->

            <div class="mb-3">
                <label for="old_password" class="form-label">Mevcut Şifre</label>
                <input type="password" class="form-control" name="old_password" id="old_password" required>
            </div>
            
            <div class="mb-3">
                <label for="new_password" class="form-label">Yeni Şifre</label>
                <!-- DİKKAT: name="new_password" olmalı -->
                <input type="password" class="form-control" name="new_password" id="new_password" required>
            </div>
            
            <div class="mb-3">
                <label for="new_password_confirmation" class="form-label">Yeni Şifre (Tekrar)</label>
                <!-- DİKKAT: name="new_password_confirmation" olmalı -->
                <input type="password" class="form-control" name="new_password_confirmation" id="new_password_confirmation" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Şifreyi Güncelle</button>
        </form>
    </div>
</div>
    </div>

    <!-- Bootstrap JS (Menüler vb. için) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>