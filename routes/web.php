<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\EventoWebController;
use App\Http\Controllers\Web\EntradaWebController;
use App\Http\Controllers\Web\AdminWebController;

// ============================================
// RUTAS PÚBLICAS
// ============================================

Route::get('/', function () {
    return view('home');
})->name('home');

// Autenticación (formularios)
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthWebController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthWebController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout')->middleware('auth');

// Eventos — create ANTES de {id} para evitar conflicto de rutas
Route::get('/eventos', [EventoWebController::class, 'index'])->name('eventos.index');

// ============================================
// RUTAS DE ADMINISTRADOR
// ============================================

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/eventos/create', [EventoWebController::class, 'create'])->name('eventos.create');

    Route::prefix('admin')->group(function () {
        Route::get('/',                        [AdminWebController::class, 'index'])->name('admin.index');
        Route::get('/eventos/{id}/edit',       [AdminWebController::class, 'editEvento'])->name('admin.eventos.edit');
        Route::get('/sectores/create',         [AdminWebController::class, 'createSector'])->name('admin.sectores.create');
        Route::get('/sectores/{id}/edit',      [AdminWebController::class, 'editSector'])->name('admin.sectores.edit');
    });
});

// Esta ruta va después de /eventos/create para que "create" no sea capturado como {id}
Route::get('/eventos/{id}', [EventoWebController::class, 'show'])->name('eventos.show');

// ============================================
// RUTAS PROTEGIDAS (requieren autenticación)
// ============================================

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthWebController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [AuthWebController::class, 'profile'])->name('profile');
    Route::get('/mis-entradas', [EntradaWebController::class, 'index'])->name('entradas.index');
    Route::get('/entradas/{id}', [EntradaWebController::class, 'show'])->name('entradas.show');
});
