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
    
    // Manajemen Keuangan (Full Backend)
    Route::get('/management', [\App\Http\Controllers\ManagementController::class, 'index'])->name('management.index');
    Route::post('/management/income', [\App\Http\Controllers\ManagementController::class, 'storeIncome'])->name('management.income.store');
    Route::post('/management/expense', [\App\Http\Controllers\ManagementController::class, 'storeExpense'])->name('management.expense.store');
    Route::post('/management/budget', [\App\Http\Controllers\ManagementController::class, 'setBudget'])->name('management.budget.set');
    Route::post('/management/automation', [\App\Http\Controllers\ManagementController::class, 'storeAutomation'])->name('management.automation.store');
    Route::post('/management/automation/{id}/delete', [\App\Http\Controllers\ManagementController::class, 'destroyAutomation'])->name('management.automation.destroy');
});
