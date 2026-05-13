<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\EventoWebController;

// ============================================
// RUTAS PÚBLICAS
// ============================================

// Home
Route::get('/', function () {
    return view('home');
})->name('home');

// Autenticación
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login')->middleware('guest');
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout')->middleware('auth');

// Eventos
Route::get('/eventos', [EventoWebController::class, 'index'])->name('eventos.index');
Route::get('/eventos/{id}', [EventoWebController::class, 'show'])->name('eventos.show');

// ============================================
// RUTAS PROTEGIDAS (requieren autenticación)
// ============================================

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AuthWebController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [AuthWebController::class, 'profile'])->name('profile');
});

// ============================================
// RUTAS DE ADMINISTRADOR
// ============================================

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/eventos/create', [EventoWebController::class, 'create'])->name('eventos.create');
});

