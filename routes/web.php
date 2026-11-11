<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\AkunController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\KategoriController;
use App\Http\Controllers\Web\TantanganController;
use App\Http\Controllers\Web\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->name('app.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');

    Route::resource('transaksi', TransaksiController::class);

    Route::resource('kategori', KategoriController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('tantangan', TantanganController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});
