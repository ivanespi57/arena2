<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\EventoWebController;
use App\Http\Controllers\Web\EntradaWebController;
use App\Http\Controllers\Web\AdminWebController;

Route::get('/', function () {
    return view('home');
})->name('home');

// Autenticación (formularios)
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthWebController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthWebController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout')->middleware('auth');

// /eventos/create debe ir antes de /eventos/{id} para que "create" no se trate como un ID
Route::get('/eventos', [EventoWebController::class, 'index'])->name('eventos.index');

// Rutas de administrador

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/eventos/create', [EventoWebController::class, 'create'])->name('eventos.create');
    Route::post('/eventos',       [EventoWebController::class, 'store'])->name('eventos.store');

    Route::prefix('admin')->group(function () {
        Route::get('/',                        [AdminWebController::class, 'index'])->name('admin.index');
        Route::get('/eventos/{id}/edit',       [AdminWebController::class, 'editEvento'])->name('admin.eventos.edit');
        Route::get('/sectores/create',         [AdminWebController::class, 'createSector'])->name('admin.sectores.create');
        Route::get('/sectores/{id}/edit',      [AdminWebController::class, 'editSector'])->name('admin.sectores.edit');
    });
});

Route::get('/eventos/{id}', [EventoWebController::class, 'show'])->name('eventos.show');

// Rutas protegidas — requieren sesión iniciada

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthWebController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [AuthWebController::class, 'profile'])->name('profile');
    Route::get('/mis-entradas', [EntradaWebController::class, 'index'])->name('entradas.index');
    Route::get('/entradas/{id}', [EntradaWebController::class, 'show'])->name('entradas.show');
});
