<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BoletoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Rotas para boletos
    Route::resource('boletos', BoletoController::class)->except(['edit', 'update', 'destroy']);
    Route::get('boletos/{boleto}/pdf', [BoletoController::class, 'pdf'])->name('boletos.pdf');
    Route::post('boletos/{boleto}/cancel', [BoletoController::class, 'cancel'])->name('boletos.cancel');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('clientes', ClienteController::class);
    Route::resource('boletos', BoletoController::class);
});

require __DIR__.'/auth.php';
