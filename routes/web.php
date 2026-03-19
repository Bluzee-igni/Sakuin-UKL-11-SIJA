<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TabungController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\IncomeController;



// root
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('tabung.index')
        : redirect()->route('login');
});

// auth public (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);

    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// protected (wajib login)
Route::middleware('auth')->group(function () {
    Route::resource('tabung', TabungController::class);

    Route::post('/targets', [TabungController::class, 'storeTarget'])->name('targets.store');
    Route::post('/targets/{target}/active', [TabungController::class, 'setActive'])->name('targets.active');

    Route::post('/checkins', [TabungController::class, 'storeCheckin'])->name('checkins.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/incomes/create', [IncomeController::class, 'create'])->name('incomes.create');
    Route::post('/incomes', [IncomeController::class, 'store'])->name('incomes.store');
});
