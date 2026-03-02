<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TabungController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;

// root
Route::get('/', function () {
    return redirect()->route('login');
});

// auth public
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

// protected (wajib login)
Route::middleware('auth')->group(function () {
    Route::resource('tabung', TabungController::class);

    Route::post('/targets', [TabungController::class, 'storeTarget'])->name('targets.store');
    Route::post('/targets/{target}/active', [TabungController::class, 'setActive'])->name('targets.active');

    Route::post('/checkins', [TabungController::class, 'storeCheckin'])->name('checkins.store');
});