<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifreyi Yenile - Task Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #eef5fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-header { background-color: #5b8fb9; border-top-left-radius: 12px; border-top-right-radius: 12px; }
        .btn-custom { background-color: #72aee6; color: white; font-weight: bold; border-radius: 8px; padding: 10px; }
        .btn-custom:hover { background-color: #5b8fb9; color: white; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="container" style="max-width: 450px;">
        <div class="card shadow-sm" style="border-radius: 12px; border: none;">
            <div class="card-header text-white text-center py-3">
                <h5 class="mb-0"><i class="bi bi-shield-lock"></i> Yeni Şifre Belirle</h5>
            </div>
            <div class="card-body p-4" style="background-color: #ffffff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                
                @if (session('success'))
                    <div class="alert alert-success text-center" style="font-size: 14px;">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" style="font-size: 14px;">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.reset.submit') }}" method="POST">
                    @csrf
                    
                    <!-- E-postayı kullanıcıya göstermeden arka planda gönderiyoruz -->
                    <input type="hidden" name="email" value="{{ session('reset_email') }}">

                    <div class="mb-3">
                        <label for="otp_code" class="form-label" style="color: #6b8299; font-weight: bold;">6 Haneli Kod</label>
                        <input type="text" class="form-control text-center" name="otp_code" id="otp_code" required style="border-color: #d1e3f2; background-color: #f7fbff; font-size: 20px; letter-spacing: 5px;" maxlength="6" placeholder="------">
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label" style="color: #6b8299; font-weight: bold;">Yeni Şifre</label>
                        <input type="password" class="form-control" name="new_password" id="new_password" required style="border-color: #d1e3f2; background-color: #f7fbff;">
                    </div>

                    <div class="mb-4">
                        <label for="new_password_confirmation" class="form-label" style="color: #6b8299; font-weight: bold;">Yeni Şifre (Tekrar)</label>
                        <input type="password" class="form-control" name="new_password_confirmation" id="new_password_confirmation" required style="border-color: #d1e3f2; background-color: #f7fbff;">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-custom">
                            <i class="bi bi-check-circle"></i> Şifreyi Sıfırla
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>