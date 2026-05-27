<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\TargetTabunganController;
use App\Http\Controllers\Api\V1\TransaksiTabunganController;
use App\Http\Controllers\Api\V1\KategoriTransaksiController;
use App\Http\Controllers\Api\V1\NotifikasiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Rute ini secara default akan memiliki prefix /api.
| Karena Sanctum belum terinstall dan aplikasi menggunakan session, 
| kita tambahkan middleware 'web' dan 'auth' agar endpoint ini 
| mengenali user yang sedang login dari browser (AJAX/Fetch).
|
*/

Route::prefix('v1')->middleware(['web', 'auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'ringkasan']);

    // Target Tabungan
    Route::apiResource('target-tabungan', TargetTabunganController::class);
    Route::patch('/target-tabungan/{target}/status', [TargetTabunganController::class, 'ubahStatus']);
    Route::get('/target-tabungan/{target}/ringkasan', [TargetTabunganController::class, 'ringkasan']);

    // Transaksi Tabungan (nested untuk pembuatan)
    Route::post('/target-tabungan/{target}/transaksi', [TransaksiTabunganController::class, 'store']);
    Route::apiResource('transaksi', TransaksiTabunganController::class)->except(['store']);

    // Kategori Transaksi
    Route::apiResource('kategori', KategoriTransaksiController::class);

    // Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index']);
    Route::patch('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'tandaiBaca']);
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'tandaiSemuaBaca']);
    Route::delete('/notifikasi/{notifikasi}', [NotifikasiController::class, 'destroy']);

    // Legacy saldo (backward compat)
    Route::get('/saldo', [DashboardController::class, 'ringkasan']);
});
