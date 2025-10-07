<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\AdminPeminjamanController;

// --- Rute untuk pengguna yang belum login ---
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// --- Rute untuk pengguna yang sudah login ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard umum
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Role: Mahasiswa ---
    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('/mahasiswa/dashboard', [DashboardController::class, 'mahasiswa'])->name('mahasiswa.dashboard');
        Route::get('/peminjaman', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    });

    // --- Role: Admin ---
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');

        // ✅ Admin bisa menyetujui dan menolak peminjaman
        Route::post('/admin/peminjaman/{id}/approve', [AdminPeminjamanController::class, 'updateStatus'])
            ->name('admin.peminjaman.approve');

        Route::post('/admin/peminjaman/{id}/reject', [AdminPeminjamanController::class, 'updateStatus'])
            ->name('admin.peminjaman.reject');

        // ✅ Admin bisa melihat daftar semua peminjaman
        Route::get('/admin/peminjaman', [AdminPeminjamanController::class, 'index'])
            ->name('admin.peminjaman.index');
    });

    // --- Role: Dosen ---
    Route::middleware('role:dosen')->group(function () {
        Route::get('/dosen/dashboard', [DashboardController::class, 'dosen'])->name('dosen.dashboard');
    });

    // --- Role: Staff ---
    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/dashboard', [DashboardController::class, 'staff'])->name('staff.dashboard');
    });
});