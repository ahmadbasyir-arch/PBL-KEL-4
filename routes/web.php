<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\AdminPeminjamanController;
use App\Http\Controllers\GoogleController;

/*
|--------------------------------------------------------------------------
| Rute untuk pengguna yang belum login
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // ✅ Tambahkan tombol dan proses Login via Google
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
});

/*
|--------------------------------------------------------------------------
| Rute untuk pengguna yang sudah login
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard umum
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Role: Mahasiswa
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('/mahasiswa/dashboard', [DashboardController::class, 'mahasiswa'])
            ->name('mahasiswa.dashboard');

        Route::get('/peminjaman', [PeminjamanController::class, 'create'])
            ->name('peminjaman.create');

        Route::post('/peminjaman', [PeminjamanController::class, 'store'])
            ->name('peminjaman.store');

        // ✅ Mahasiswa menandai “selesai”, tapi status menunggu validasi admin
        Route::post('/mahasiswa/peminjaman/{id}/selesai', [DashboardController::class, 'selesaikanPeminjaman'])
            ->name('mahasiswa.selesai');
    });

    /*
    |--------------------------------------------------------------------------
    | Role: Admin
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
            ->name('admin.dashboard');

        // ✅ Admin bisa menyetujui, menolak, atau menandai selesai
        Route::post('/admin/peminjaman/{id}/approve', [AdminPeminjamanController::class, 'updateStatus'])
            ->name('admin.peminjaman.approve');

        Route::post('/admin/peminjaman/{id}/reject', [AdminPeminjamanController::class, 'updateStatus'])
            ->name('admin.peminjaman.reject');

        Route::post('/admin/peminjaman/{id}/complete', [AdminPeminjamanController::class, 'updateStatus'])
            ->name('admin.peminjaman.complete');

        // ✅ Admin bisa melihat semua daftar peminjaman
        Route::get('/admin/peminjaman', [AdminPeminjamanController::class, 'index'])
            ->name('admin.peminjaman.index');
    });

    /*
    |--------------------------------------------------------------------------
    | Role: Dosen
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:dosen')->group(function () {
        Route::get('/dosen/dashboard', [DashboardController::class, 'dosen'])
            ->name('dosen.dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | Role: Staff
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/dashboard', [DashboardController::class, 'staff'])
            ->name('staff.dashboard');
    });

    // === VALIDASI SELESAI (Admin memvalidasi ajukan selesai) ===
    Route::post('/admin/peminjaman/{id}/validate', [App\Http\Controllers\AdminPeminjamanController::class, 'validateSelesai'])
        ->name('admin.peminjaman.validate');

    // === LOGIN DENGAN GOOGLE ===
    Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

});