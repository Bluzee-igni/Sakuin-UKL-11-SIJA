<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TabungController;
use App\Http\Controllers\LoginController;

// 1. Ubah ini: Jika belum login, lempar ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Route Login tetap bisa diakses publik
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 3. Bungkus route tabungan dengan Middleware 'auth'
// Ini gunanya biar kalau orang ngetik /tabung tanpa login, bakal ditendang balik ke login
Route::middleware('auth')->group(function () {
    Route::resource('tabung', TabungController::class);
});