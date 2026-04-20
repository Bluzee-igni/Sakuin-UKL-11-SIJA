<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// root
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('tabung.index')
        : redirect()->route('login');
});

// auth public (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\LoginController::class, 'index'])->name('login');
    Route::post('/login', [\App\Http\Controllers\LoginController::class, 'authenticate']);

    Route::get('/register', [\App\Http\Controllers\RegisterController::class, 'index'])->name('register');
    Route::post('/register', [\App\Http\Controllers\RegisterController::class, 'store']);
});

Route::post('/logout', [\App\Http\Controllers\LoginController::class, 'logout'])->middleware('auth')->name('logout');

// protected (wajib login)
Route::middleware('auth')->group(function () {
    Route::resource('tabung', \App\Http\Controllers\TabungController::class);

    Route::post('/targets', [\App\Http\Controllers\TabungController::class, 'storeTarget'])->name('targets.store');
    Route::post('/targets/{target}/active', [\App\Http\Controllers\TabungController::class, 'setActive'])->name('targets.active');

    Route::post('/checkins', [\App\Http\Controllers\TabungController::class, 'storeCheckin'])->name('checkins.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/incomes/create', [\App\Http\Controllers\IncomeController::class, 'create'])->name('incomes.create');
    Route::post('/incomes', [\App\Http\Controllers\IncomeController::class, 'store'])->name('incomes.store');
});
