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
use App\Http\Controllers\FonnteController;

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

    Route::get('/lengkapi-profil', [GoogleController::class, 'showCompleteProfile'])->name('lengkapi.profil');
    Route::post('/lengkapi-profil', [GoogleController::class, 'storeCompleteProfile'])->name('lengkapi.profil.store');

    // ROUTE PENGATURAN PROFIL
    Route::get('/settings', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/settings', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // ROUTE NOTIFIKASI
    Route::get('/notifications/mark-read', [\App\Http\Controllers\ProfileController::class, 'markNotificationsRead'])->name('notifications.markRead');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {

        Route::get('/peminjaman/{id}/validasi', [AdminPeminjamanController::class, 'formValidasi'])
            ->name('admin.peminjaman.formValidasi');

        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');

        // API endpoint untuk chart durasi (nama route : admin.chart.durasi)
       Route::get('/charts/durasi', [DashboardController::class, 'apiChartDurasi'])
    ->name('admin.charts.durasi');


        Route::resource('/ruangan', AdminRuanganController::class)->names([
            'index' => 'admin.ruangan.index',
            'create' => 'admin.ruangan.create',
            'store' => 'admin.ruangan.store',
            'edit' => 'admin.ruangan.edit',
            'update' => 'admin.ruangan.update',
            'destroy' => 'admin.ruangan.destroy',
            'show'   => 'admin.ruangan.show',
        ]);

        Route::resource('/unit', AdminUnitController::class)->names([
            'index' => 'admin.unit.index',
            'create' => 'admin.unit.create',
            'store' => 'admin.unit.store',
            'edit' => 'admin.unit.edit',
            'update' => 'admin.unit.update',
            'destroy' => 'admin.unit.destroy',
            'show'   => 'admin.unit.show',
        ]);

        Route::resource('/pengguna', PenggunaController::class)->names([
            'index' => 'admin.pengguna.index',
            'edit' => 'admin.pengguna.edit',
            'update' => 'admin.pengguna.update',
            'destroy' => 'admin.pengguna.destroy',
        ]);

        // ROUTE RANKING
        Route::get('/ranking/export', [\App\Http\Controllers\AdminRankingController::class, 'exportPdf'])->name('admin.ranking.export');
        Route::get('/ranking', [\App\Http\Controllers\AdminRankingController::class, 'index'])->name('admin.ranking.index');

        Route::get('/peminjaman', [AdminPeminjamanController::class, 'index'])->name('admin.peminjaman.index');
        Route::post('/peminjaman/{id}/approve', [AdminPeminjamanController::class, 'updateStatus'])->name('admin.peminjaman.approve');
        Route::post('/peminjaman/{id}/reject', [AdminPeminjamanController::class, 'updateStatus'])->name('admin.peminjaman.reject');
        Route::post('/peminjaman/{id}/complete', [AdminPeminjamanController::class, 'updateStatus'])->name('admin.peminjaman.complete');
        Route::post('/peminjaman/{id}/validate', [AdminPeminjamanController::class, 'validateSelesai'])->name('admin.peminjaman.validate');

        // ROUTE JADWAL
        Route::get('/jadwal', [\App\Http\Controllers\AdminJadwalController::class, 'index'])->name('admin.jadwal.index');
        Route::post('/jadwal/import', [\App\Http\Controllers\AdminJadwalController::class, 'import'])->name('admin.jadwal.import');

        // ROUTE LAPORAN
        Route::get('/laporan', [\App\Http\Controllers\AdminLaporanController::class, 'index'])->name('admin.laporan.index');
        Route::get('/laporan/print', [\App\Http\Controllers\AdminLaporanController::class, 'print'])->name('admin.laporan.print');
        Route::get('/laporan/pdf', [\App\Http\Controllers\AdminLaporanController::class, 'exportPdf'])->name('admin.laporan.pdf');
        // ROUTE ULASAN (Admin)
        Route::get('/ulasan/export', [\App\Http\Controllers\UlasanController::class, 'exportPdf'])->name('admin.ulasan.export');
        Route::get('/ulasan', [\App\Http\Controllers\UlasanController::class, 'index'])->name('admin.ulasan.index');

        // ROUTE MASTER MATA KULIAH
        Route::get('/matkul', [\App\Http\Controllers\AdminMataKuliahController::class, 'index'])->name('admin.matkul.index');
        Route::post('/matkul/import', [\App\Http\Controllers\AdminMataKuliahController::class, 'import'])->name('admin.matkul.import');

    });

    /*
    |--------------------------------------------------------------------------
    | MAHASISWA
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth','role:mahasiswa'])->group(function () {
        Route::get('/mahasiswa/dashboard', [DashboardController::class, 'mahasiswa'])
            ->name('mahasiswa.dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | DOSEN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth','role:dosen'])->group(function () {
        Route::get('/dosen/dashboard', [DashboardController::class, 'dosen'])
            ->name('dosen.dashboard');

        // ROUTE EDIT PEMINJAMAN KHUSUS DOSEN
        Route::get('/dosen/peminjaman/{id}/edit', 
            [PeminjamanController::class, 'edit'])
            ->name('dosen.peminjaman.edit');

        Route::put('/dosen/peminjaman/{id}', 
            [PeminjamanController::class, 'update'])
            ->name('dosen.peminjaman.update');

        // ROUTE MANAJEMEN PENGGUNA (DOSEN)
        Route::resource('/dosen/pengguna', \App\Http\Controllers\DosenPenggunaController::class)
            ->names([
                'index' => 'dosen.pengguna.index',
                'edit' => 'dosen.pengguna.edit',
                'update' => 'dosen.pengguna.update',
                'destroy' => 'dosen.pengguna.destroy',
            ]);
    });

    /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:super_admin'])->prefix('superadmin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'admin'])
            ->name('superadmin.dashboard'); // Reuse admin dashboard for now

        Route::resource('/prodi', \App\Http\Controllers\SuperAdminProdiController::class)
            ->names('superadmin.prodi');
    });

    /*
    |--------------------------------------------------------------------------
    | RUTE BERSAMA MAHASISWA & DOSEN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth','role:mahasiswa,dosen'])->group(function () {

        // EDIT
        Route::get('/peminjaman/{id}/edit', [PeminjamanController::class, 'edit'])
            ->name('peminjaman.edit');

        // UPDATE
        Route::put('/peminjaman/{id}', [PeminjamanController::class, 'update'])
            ->name('peminjaman.update');

        // CREATE
        Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])
            ->name('peminjaman.create');

        Route::post('/peminjaman/store', [PeminjamanController::class, 'store'])
            ->name('peminjaman.store');

        // AJUKAN SELESAI
        Route::post('/peminjaman/{id}/ajukan-selesai', [PeminjamanController::class, 'ajukanSelesai'])
            ->name('peminjaman.ajukanSelesai');

        // KEMBALIKAN
        Route::post('/peminjaman/{id}/kembalikan', [PeminjamanController::class, 'kembalikan'])
            ->name('peminjaman.kembalikan');

        // FEEDBACK
        Route::post('/peminjaman/{id}/feedback', [PeminjamanController::class, 'storeFeedback'])
            ->name('peminjaman.feedback');

        // ULASAN (General)
        Route::get('/ulasan/tulis', [\App\Http\Controllers\UlasanController::class, 'create'])->name('ulasan.create');
        Route::post('/ulasan', [\App\Http\Controllers\UlasanController::class, 'store'])->name('ulasan.store');

        // RIWAYAT
        Route::get('/riwayat', [RiwayatController::class, 'index'])
            ->name('riwayat');

        // SHOW
        Route::get('/peminjaman/{id}', [PeminjamanController::class, 'show'])
            ->name('peminjaman.show');
    });

});

/*
|--------------------------------------------------------------------------
| Halaman publik
|--------------------------------------------------------------------------
*/
Route::get('/daftar-peminjaman', [PeminjamanController::class, 'index'])
    ->name('peminjaman.index');

Route::get('/login/free', function () {
    return redirect()->route('freeuser.home');
})->name('free.login');

Route::get('/free', [\App\Http\Controllers\FreeUserController::class, 'index'])
    ->name('freeuser.home');

Route::get('/free/events', [\App\Http\Controllers\FreeUserController::class, 'getEvents'])
    ->name('freeuser.events');

Route::get('/test-wa', [FonnteController::class, 'test']);
