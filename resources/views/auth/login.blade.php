<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <style>
        body {
            background-color: #eef5fa; /* Soft mavi arkaplan */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(114, 174, 230, 0.15);
            width: 100%;
            max-width: 400px;
        }
        .card h2 {
            color: #5b8fb9; /* Koyu soft mavi */
            text-align: center;
            margin-bottom: 24px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #6b8299;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1e3f2;
            border-radius: 8px;
            box-sizing: border-box;
            background-color: #f7fbff;
            outline: none;
            color: #333;
        }
        .form-group input:focus {
            border-color: #72aee6;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background-color: #72aee6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #5a94d0;
        }
        .link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #72aee6;
            text-decoration: none;
            font-size: 14px;
        }
        .link:hover {
            text-decoration: underline;
        }
        .error {
            background-color: #ffeaea;
            color: #d9534f;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 16px;
            text-align: center;
        }
        /* YENİ: Başarı mesajı için stil */
        .success-msg {
            background-color: #eaffea;
            color: #28a745;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 16px;
            text-align: center;
        }
        /* YENİ: Şifremi unuttum linki stili */
        .forgot-password {
            display: block;
            text-align: right;
            margin-top: 8px;
            font-size: 13px;
            color: #72aee6;
            text-decoration: none;
        }
        .forgot-password:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Sisteme Giriş Yap</h2>
        
        <!-- YENİ: Şifre başarıyla sıfırlandığında çıkacak yeşil mesaj kutusu -->
        @if(session('success'))
            <div class="success-msg">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>E-posta Adresi</label>
                <input type="email" name="email" required placeholder="ornek@mail.com">
            </div>
            <div class="form-group">
                <label>Şifre</label>
                <input type="password" name="password" required placeholder="••••••••">
                
                <!-- YENİ: Şifremi Unuttum Linki (Şifre kutusunun hemen altında, sağa dayalı) -->
                <a href="{{ route('password.forgot.form') }}" class="forgot-password">Şifremi unuttum</a>
            </div>
            
            <button type="submit" class="btn">Giriş Yap</button>
        </form>
        <a href="{{ route('register') }}" class="link">Hesabın yok mu? Yeni Kayıt Oluştur</a>
    </div>
</body>
</html>