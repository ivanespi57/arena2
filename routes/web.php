<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\EventoWebController;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\EntradaWebController;

// ============================================
// RUTAS PÚBLICAS
// ============================================

// Home - Catálogo de eventos
Route::get('/', [EventoWebController::class, 'index'])->name('home');
Route::get('/eventos/{id}', [EventoWebController::class, 'show'])->name('eventos.show');

// Autenticación
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthWebController::class, 'register'])->name('register.submit');
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->name('login.submit');

// ============================================
// RUTAS PROTEGIDAS
// ============================================

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

    // Entradas
    Route::get('/mis-entradas', [EntradaWebController::class, 'index'])->name('entradas.index');
    Route::get('/entradas/{id}', [EntradaWebController::class, 'show'])->name('entradas.show');
});
