Contoh 1 :
?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\AdminPeminjamanController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\AdminRuanganController;
use App\Http\Controllers\AdminUnitController;
use App\Http\Controllers\PenggunaController; // ✅ Tambahan baru

/*
|--------------------------------------------------------------------------
| Rute untuk pengguna yang belum login
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Login via Google
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');


    
});
/*
|--------------------------------------------------------------------------
| Rute untuk pengguna yang belum login
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Rute untuk pengguna yang belum login
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Halaman utama publik (Free User)
    Route::get('/', function () {
        return view('freeuser.home');
    })->name('freeuser.home');

    // Login dan register
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Login via Google
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
});

/*
|--------------------------------------------------------------------------
| Rute untuk pengguna yang sudah login
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Lengkapi profil setelah login Google
    Route::get('/lengkapi-profil', [GoogleController::class, 'showCompleteProfile'])->name('lengkapi.profil');
    Route::post('/lengkapi-profil', [GoogleController::class, 'storeCompleteProfile'])->name('lengkapi.profil.store');

    // Dashboard umum
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Role: Admin (semua fitur admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
        // Dashboard admin
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');

        // 🔹 Data Ruangan (CRUD)
        Route::get('/ruangan', [AdminRuanganController::class, 'index'])->name('admin.ruangan.index');
        Route::get('/ruangan/create', [AdminRuanganController::class, 'create'])->name('admin.ruangan.create');
        Route::post('/ruangan', [AdminRuanganController::class, 'store'])->name('admin.ruangan.store');
        Route::get('/ruangan/{id}/edit', [AdminRuanganController::class, 'edit'])->name('admin.ruangan.edit');
        Route::put('/ruangan/{id}', [AdminRuanganController::class, 'update'])->name('admin.ruangan.update');
        Route::delete('/ruangan/{id}', [AdminRuanganController::class, 'destroy'])->name('admin.ruangan.destroy');
        Route::get('/ruangan/{id}', [AdminRuanganController::class, 'show'])->name('admin.ruangan.show');

        // 🔹 Data Unit (CRUD)
        Route::get('/unit', [AdminUnitController::class, 'index'])->name('admin.unit.index');
        Route::get('/unit/create', [AdminUnitController::class, 'create'])->name('admin.unit.create');
        Route::post('/unit', [AdminUnitController::class, 'store'])->name('admin.unit.store');
        Route::get('/unit/{id}/edit', [AdminUnitController::class, 'edit'])->name('admin.unit.edit');
        Route::put('/unit/{id}', [AdminUnitController::class, 'update'])->name('admin.unit.update');
        Route::delete('/unit/{id}', [AdminUnitController::class, 'destroy'])->name('admin.unit.destroy');
        Route::get('/unit/{id}', [AdminUnitController::class, 'show'])->name('admin.unit.show');

        // 🔹 Data Pengguna (Mahasiswa & Dosen)
        Route::get('/pengguna', [PenggunaController::class, 'index'])->name('admin.pengguna.index');

        // 🔹 Peminjaman
        Route::get('/peminjaman', [AdminPeminjamanController::class, 'index'])->name('admin.peminjaman.index');
        Route::post('/peminjaman/{id}/approve', [AdminPeminjamanController::class, 'updateStatus'])->name('admin.peminjaman.approve');
        Route::post('/peminjaman/{id}/reject', [AdminPeminjamanController::class, 'updateStatus'])->name('admin.peminjaman.reject');
        Route::post('/peminjaman/{id}/complete', [AdminPeminjamanController::class, 'updateStatus'])->name('admin.peminjaman.complete');
        Route::post('/peminjaman/{id}/validate', [AdminPeminjamanController::class, 'validateSelesai'])->name('admin.peminjaman.validate');
        
    });

    /*
    |--------------------------------------------------------------------------
    | Role: Mahasiswa & Dosen
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:mahasiswa,dosen')->group(function () {
        Route::get('/mahasiswa/dashboard', [DashboardController::class, 'mahasiswa'])
            ->name('mahasiswa.dashboard');

        // Form peminjaman (GET & POST)
        Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::post('/peminjaman/store', [PeminjamanController::class, 'store'])->name('peminjaman.store');

        // Ajukan selesai
        Route::post('/peminjaman/{id}/ajukan-selesai', [PeminjamanController::class, 'ajukanSelesai'])
            ->name('peminjaman.ajukanSelesai');     
            
    });

    /*
|--------------------------------------------------------------------------
| Rute publik (tanpa login)
|--------------------------------------------------------------------------
*/

// Halaman utama Free User
Route::get('/', function () {
    return view('freeuser.home');
})->name('freeuser.home');

// Daftar peminjaman (tanpa login)
Route::get('/daftar-peminjaman', [App\Http\Controllers\PeminjamanController::class, 'index'])
    ->name('peminjaman.index');

});