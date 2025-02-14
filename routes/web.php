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

    Route::get('/boletos', [BoletoController::class, 'index'])->name('boletos.index');
    Route::get('/boletos/create', [BoletoController::class, 'create'])->name('boletos.create');
    Route::post('/boletos', [BoletoController::class, 'store'])->name('boletos.store');
    Route::get('/boletos/{boleto}', [BoletoController::class, 'show'])->name('boletos.show');
    Route::post('/boletos/{boleto}/pagar', [BoletoController::class, 'pagar'])->name('boletos.pagar');
    Route::post('/boletos/{boleto}/cancelar', [BoletoController::class, 'cancelar'])->name('boletos.cancelar');
    Route::get('/boletos/{boleto}/pdf', [BoletoController::class, 'pdf'])->name('boletos.pdf');

    Route::resource('clientes', ClienteController::class);
    Route::resource('boletos', BoletoController::class);
});

require __DIR__.'/auth.php';
