<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\AdminPeminjamanController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\AdminRuanganController;
use App\Http\Controllers\AdminUnitController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\RiwayatController;

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

    // Tombol Free User
    Route::get('/login/free', function () {
        return redirect()->route('freeuser.home');
    })->name('free.login');

    // Halaman Free User
    Route::get('/free', function () {
        return view('freeuser.home');
    })->name('freeuser.home');
});

/*
|--------------------------------------------------------------------------
| Rute untuk pengguna yang sudah login
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Lengkapi profil Google
    Route::get('/lengkapi-profil', [GoogleController::class, 'showCompleteProfile'])->name('lengkapi.profil');
    Route::post('/lengkapi-profil', [GoogleController::class, 'storeCompleteProfile'])->name('lengkapi.profil.store');

    // Dashboard default
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Role: ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {

        // Dashboard Admin
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');

        // CRUD Ruangan
        Route::resource('/ruangan', AdminRuanganController::class)->names([
            'index' => 'admin.ruangan.index',
            'create' => 'admin.ruangan.create',
            'store' => 'admin.ruangan.store',
            'edit' => 'admin.ruangan.edit',
            'update' => 'admin.ruangan.update',
            'destroy' => 'admin.ruangan.destroy',
            'show' => 'admin.ruangan.show',
        ]);

        // CRUD Unit
        Route::resource('/unit', AdminUnitController::class)->names([
            'index' => 'admin.unit.index',
            'create' => 'admin.unit.create',
            'store' => 'admin.unit.store',
            'edit' => 'admin.unit.edit',
            'update' => 'admin.unit.update',
            'destroy' => 'admin.unit.destroy',
            'show' => 'admin.unit.show',
        ]);

        // Pengguna
        Route::get('/pengguna', [PenggunaController::class, 'index'])->name('admin.pengguna.index');

        // Peminjaman Admin
        Route::get('/peminjaman', [AdminPeminjamanController::class, 'index'])->name('admin.peminjaman.index');
        Route::post('/peminjaman/{id}/approve', [AdminPeminjamanController::class, 'updateStatus'])->name('admin.peminjaman.approve');
        Route::post('/peminjaman/{id}/reject', [AdminPeminjamanController::class, 'updateStatus'])->name('admin.peminjaman.reject');
        Route::post('/peminjaman/{id}/complete', [AdminPeminjamanController::class, 'updateStatus'])->name('admin.peminjaman.complete');
        Route::post('/peminjaman/{id}/validate', [AdminPeminjamanController::class, 'validateSelesai'])->name('admin.peminjaman.validate');
    });


    /*
    |--------------------------------------------------------------------------
    | Role: MAHASISWA (dashboard)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth','role:mahasiswa'])->group(function () {
        Route::get('/mahasiswa/dashboard', [DashboardController::class, 'mahasiswa'])
            ->name('mahasiswa.dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | Role: DOSEN (dashboard)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth','role:dosen'])->group(function () {
        Route::get('/dosen/dashboard', [DashboardController::class, 'dosen'])
            ->name('dosen.dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | Rute bersama Mahasiswa & Dosen
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth','role:mahasiswa,dosen'])->group(function () {

        // Form peminjaman
        Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])
            ->name('peminjaman.create');

        Route::post('/peminjaman/store', [PeminjamanController::class, 'store'])
            ->name('peminjaman.store');

        // Ajukan selesai
        Route::post('/peminjaman/{id}/ajukan-selesai', [PeminjamanController::class, 'ajukanSelesai'])
            ->name('peminjaman.ajukanSelesai');

        // 🔥 RIWAYAT BARU (pakai RiwayatController)
        Route::get('/riwayat', [RiwayatController::class, 'index'])
            ->name('riwayat');
    });

});

/*
|--------------------------------------------------------------------------
| Halaman publik tanpa login
|--------------------------------------------------------------------------
*/
Route::get('/daftar-peminjaman', [PeminjamanController::class, 'index'])
    ->name('peminjaman.index');
