<?php

use App\Http\Controllers\AkunKeuanganController;
use App\Http\Controllers\KategoriTransaksiController;
use App\Http\Controllers\TantanganController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Akun keuangan
    Route::get('/akun', [AkunKeuanganController::class, 'index'])->name('akun.index');
    Route::get('/akun/saldo', [AkunKeuanganController::class, 'saldo'])->name('akun.saldo');
    Route::get('/akun/{akun}', [AkunKeuanganController::class, 'show'])->name('akun.show');

    // Kategori transaksi
    Route::apiResource('kategori', KategoriTransaksiController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // Tantangan
    Route::get('/tantangan/riwayat', [TantanganController::class, 'riwayat'])->name('tantangan.riwayat');
    Route::apiResource('tantangan', TantanganController::class);

    // Transaksi
    Route::apiResource('transaksi', TransaksiController::class);
});
