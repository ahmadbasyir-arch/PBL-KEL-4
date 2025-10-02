<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PeminjamanController;

// --- Rute untuk Pengguna yang Belum Login (Guest) ---
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});


// --- Rute untuk Pengguna yang Sudah Login (Authenticated) ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // [PENTING] Route untuk dashboard dengan nama 'dashboard'
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute untuk peminjaman
    Route::get('/peminjaman', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');

    // Tambahkan rute lain yang memerlukan login di sini (riwayat, pengembalian, dll.)
});