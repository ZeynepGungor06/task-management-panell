use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json(['mesaj' => 'Laravel ayakta ve çalışıyor!', 'zaman' => now()]);
});