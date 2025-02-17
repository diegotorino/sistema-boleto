<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BoletoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\TaskBoardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('clientes', ClienteController::class);
    Route::resource('boletos', BoletoController::class);

    // Rotas para o sistema de tarefas
    Route::get('/tasks', [TaskBoardController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [TaskBoardController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskBoardController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskBoardController::class, 'show'])->name('tasks.show');
    Route::put('/tasks/{task}', [TaskBoardController::class, 'update'])->name('tasks.update');
    Route::put('/tasks/{task}/status', [TaskBoardController::class, 'updateStatus'])->name('tasks.update.status');
    Route::delete('/tasks/{task}', [TaskBoardController::class, 'destroy'])->name('tasks.destroy');
});

require __DIR__.'/auth.php';
