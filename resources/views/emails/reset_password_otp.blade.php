<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; color: #333; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .code { font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #5b8fb9; background: #eef5fa; padding: 15px; border-radius: 6px; margin: 20px 0; }
        .footer { font-size: 12px; color: #999; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Şifre Sıfırlama Talebi</h2>
        <p>Merhaba,</p>
        <p>Hesabınızın şifresini sıfırlamak için bir talepte bulundunuz. İşleme devam etmek için aşağıdaki 6 haneli doğrulama kodunu kullanabilirsiniz:</p>
        
        <!-- Controller'dan gelen kod buraya basılacak -->
        <div class="code">{{ $otpCode }}</div>
        
        <p>Bu kodun geçerlilik süresi <strong>3 dakikadır</strong>.</p>
        <p>Eğer bu talebi siz yapmadıysanız, lütfen bu e-postayı dikkate almayın.</p>
        
        <div class="footer">
            &copy; {{ date('Y') }} Task Management System. Tüm hakları saklıdır.
        </div>
    </div>
</body>
</html>