<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\SampahController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanAdminController;
use App\Http\Controllers\NasabahLoginController;
use App\Http\Controllers\NasabahRegisterController;
use App\Http\Controllers\NasabahGoogleAuthController;
use App\Http\Controllers\NasabahPasswordResetController;
use App\Http\Controllers\NasabahDashboardController;
use App\Http\Controllers\NasabahTransaksiPPOBController;
use App\Http\Controllers\NasabahRiwayatSetorController;
use App\Http\Controllers\NasabahProfilController;
use App\Http\Controllers\NasabahEmoneyController;
use App\Http\Controllers\NasabahTransaksiSetorController;
use App\Http\Controllers\NasabahPulsaController;
use App\Http\Controllers\NasabahPlnController;

Route::get('/', function () {
    return view('welcome');
});

// Additional route to access the landing-style page (maps to current welcome view)
Route::get('/landing', function () {
    return view('landing');
});

// Handle contact form submissions from landing page
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Admin auth routes
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login.form');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
Route::get('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Default login route required by auth middleware
Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');

// Admin dashboard
Route::middleware(['admin.session'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Admin nasabah routes
    Route::get('/admin/nasabah', [NasabahController::class, 'daftar'])->name('admin.nasabah.daftar');
    Route::post('/admin/nasabah', [NasabahController::class, 'daftar'])->name('admin.nasabah.post');

    // Admin transaksi routes
    Route::get('/admin/transaksi', [TransaksiController::class, 'index'])->name('admin.transaksi');
    Route::get('/admin/transaksi/setor/{id}', [TransaksiController::class, 'showSetorDetail'])->name('admin.transaksi.setor.detail');
    Route::post('/admin/transaksi/setor/{id}', [TransaksiController::class, 'updateSetorDetail'])->name('admin.transaksi.setor.update');
    Route::post('/admin/transaksi/penarikan/{id}/action', [TransaksiController::class, 'handlePenarikanAction'])->name('admin.transaksi.penarikan.action');

    // Admin sampah routes
    Route::get('/admin/sampah', [SampahController::class, 'daftar'])->name('admin.sampah.daftar');
    Route::post('/admin/sampah', [SampahController::class, 'daftar'])->name('admin.sampah.post');
    Route::get('/admin/sampah/create', [SampahController::class, 'create'])->name('admin.sampah.create');
    Route::post('/admin/sampah/store', [SampahController::class, 'store'])->name('admin.sampah.store');
    Route::get('/admin/sampah/{id}/edit', [SampahController::class, 'edit'])->name('admin.sampah.edit');
    Route::put('/admin/sampah/{id}', [SampahController::class, 'update'])->name('admin.sampah.update');

    // Admin laporan routes
    Route::get('/admin/laporan', [LaporanController::class, 'index'])->name('admin.laporan');
    Route::get('/admin/laporan/excel/keuangan', [LaporanController::class, 'excelKeuangan'])->name('admin.laporan.excel.keuangan');
    Route::get('/admin/laporan/pdf/keuangan', [LaporanController::class, 'pdfKeuangan'])->name('admin.laporan.pdf.keuangan');
    Route::get('/admin/laporan/excel/sampah', [LaporanController::class, 'excelSampah'])->name('admin.laporan.excel.sampah');
    Route::get('/admin/laporan/pdf/sampah', [LaporanController::class, 'pdfSampah'])->name('admin.laporan.pdf.sampah');
    Route::get('/admin/laporan/excel/nasabah', [LaporanController::class, 'excelNasabah'])->name('admin.laporan.excel.nasabah');
    Route::get('/admin/laporan/pdf/nasabah', [LaporanController::class, 'pdfNasabah'])->name('admin.laporan.pdf.nasabah');

    // Admin pengaturan routes
    Route::get('/admin/pengaturan', [PengaturanAdminController::class, 'index'])->name('admin.pengaturan');
    Route::post('/admin/pengaturan/action', [PengaturanAdminController::class, 'store'])->name('admin.pengaturan.action');
});

// Nasabah login routes
Route::get('/nasabah/login', [NasabahLoginController::class, 'showLogin'])->name('nasabah.login');
Route::post('/nasabah/authenticate', [NasabahLoginController::class, 'authenticate'])->name('nasabah.authenticate');
Route::post('/nasabah/google/authenticate', [NasabahGoogleAuthController::class, 'authenticate'])->name('nasabah.google.authenticate');
Route::get('/nasabah/logout', [NasabahLoginController::class, 'logout'])->name('nasabah.logout');
Route::get('/nasabah/lupa-password', [NasabahPasswordResetController::class, 'showForgotForm'])->name('nasabah.password.request');
Route::post('/nasabah/lupa-password', [NasabahPasswordResetController::class, 'sendResetLink'])->name('nasabah.password.email');
Route::get('/nasabah/reset-password/{token}', [NasabahPasswordResetController::class, 'showResetForm'])->name('nasabah.password.reset.form');
Route::post('/nasabah/reset-password', [NasabahPasswordResetController::class, 'reset'])->name('nasabah.password.update');

// Nasabah dashboard routes
Route::get('/nasabah/dashboard', [NasabahDashboardController::class, 'index'])->name('nasabah.dashboard');
Route::get('/nasabah/transaksi', [NasabahTransaksiPPOBController::class, 'index'])->name('nasabah.transaksi');
Route::get('/nasabah/riwayat-setor', [NasabahRiwayatSetorController::class, 'index'])->name('nasabah.riwayat-setor');
Route::get('/nasabah/profil', [NasabahProfilController::class, 'index'])->name('nasabah.profil');
Route::get('/nasabah/profil/ubah', [NasabahProfilController::class, 'edit'])->name('nasabah.profil.edit');
Route::post('/nasabah/profil/update', [NasabahProfilController::class, 'update'])->name('nasabah.profil.update');
Route::get('/nasabah/setor', [NasabahTransaksiSetorController::class, 'index'])->name('nasabah.setor');
Route::post('/nasabah/setor', [NasabahTransaksiSetorController::class, 'index'])->name('nasabah.setor.post');

// Nasabah PPOB routes
Route::get('/nasabah/emoney', [NasabahEmoneyController::class, 'index'])->name('nasabah.emoney');
Route::post('/nasabah/emoney', [NasabahEmoneyController::class, 'store'])->name('nasabah.emoney.store');
Route::get('/nasabah/pulsa', [NasabahPulsaController::class, 'index'])->name('nasabah.pulsa');
Route::post('/nasabah/pulsa', [NasabahPulsaController::class, 'store'])->name('nasabah.pulsa.store');
Route::get('/nasabah/pln', [NasabahPlnController::class, 'index'])->name('nasabah.pln');
Route::post('/nasabah/pln', [NasabahPlnController::class, 'store'])->name('nasabah.pln.store');

// Nasabah register routes
Route::get('/nasabah/register', [NasabahRegisterController::class, 'showRegister'])->name('nasabah.register');
Route::post('/nasabah/register', [NasabahRegisterController::class, 'store'])->name('nasabah.store');
