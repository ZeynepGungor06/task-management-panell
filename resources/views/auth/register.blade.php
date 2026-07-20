<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <style>
        body {
            background-color: #eef5fa;
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
            max-width: 450px;
        }
        .card h2 {
            color: #5b8fb9;
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
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1e3f2;
            border-radius: 8px;
            box-sizing: border-box;
            background-color: #f7fbff;
            outline: none;
            color: #333;
        }
        .form-group input:focus, .form-group select:focus {
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
    </style>
</head>
<body>
    <div class="card">
        <h2>Yeni Kayıt Oluştur</h2>

        @if($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ url('/register') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label>Hesap Türü</label>
                <select name="role" id="roleSelect" required>
                    <option value="user">Normal Kullanıcı</option>
                    <option value="admin">Sistem Yöneticisi (Admin)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Adınız Soyadınız</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ad Soyad">
            </div>
            
            <div class="form-group">
                <label>E-posta Adresi</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="ornek@mail.com">
            </div>
            
            <div class="form-group">
                <label>Şifre</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>
            
            <!-- Bu alan JavaScript ile Admin seçildiğinde gizlenecek -->
            <div class="form-group" id="adminField">
                <label>Bağlı Olduğunuz Adminin Adı</label>
                <input type="text" name="admin_username" id="adminInput" value="{{ old('admin_username') }}" placeholder="Örn: Sistem Yöneticisi">
            </div>
            
            <button type="submit" class="btn">Kayıt Ol</button>
        </form>
        <a href="{{ route('login') }}" class="link">Zaten hesabın var mı? Giriş Yap</a>
    </div>

    <!-- Rol Seçimine Göre Formu Dinamik Yapan JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var roleSelect = document.getElementById('roleSelect');
            var adminField = document.getElementById('adminField');
            var adminInput = document.getElementById('adminInput');
            
            function toggleAdminField() {
                if(roleSelect.value === 'admin') {
                    adminField.style.display = 'none';
                    adminInput.required = false;
                    adminInput.value = ''; // İçini boşaltıyoruz ki arka plana boş gitsin
                } else {
                    adminField.style.display = 'block';
                    adminInput.required = true;
                }
            }

            // Seçim değiştiğinde tetikle
            roleSelect.addEventListener('change', toggleAdminField);
            
            // Sayfa yüklendiğinde mevcut duruma göre çalıştır
            toggleAdminField();
        });
    </script>
</body>
</html>