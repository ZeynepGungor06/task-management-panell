<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifremi Unuttum - Task Management System</title>
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
                <h5 class="mb-0"><i class="bi bi-envelope-at"></i> Şifremi Unuttum</h5>
            </div>
            <div class="card-body p-4" style="background-color: #ffffff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                
                <p class="text-muted text-center mb-4" style="font-size: 14px;">
                    Hesabınıza ait e-posta adresini girin. Size 6 haneli bir sıfırlama kodu göndereceğiz.
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger" style="font-size: 14px;">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.forgot.send') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="form-label" style="color: #6b8299; font-weight: bold;">E-Posta Adresiniz</label>
                        <input type="email" class="form-control" name="email" id="email" required style="border-color: #d1e3f2; background-color: #f7fbff;" placeholder="ornek@email.com">
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-custom">
                            <i class="bi bi-send"></i> Kodu Gönder
                        </button>
                        <a href="{{ route('login') }}" class="btn btn-link text-decoration-none" style="color: #6b8299; font-size: 14px;">
                            <i class="bi bi-arrow-left"></i> Giriş Ekranına Dön
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>