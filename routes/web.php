<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AccesoController;
use App\Http\Controllers\Auth\UsuarioController;

// Rutas públicas de autenticación
Route::get('/', [AccesoController::class, 'mostrarFormulario'])->name('acceso');
Route::post('/login', [AccesoController::class, 'iniciarSesion'])->name('login');
Route::post('/logout', [AccesoController::class, 'cerrarSesion'])->name('logout');

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth'])->group(function () {
    Route::resource('usuarios', UsuarioController::class)->only([
        'index', 'create', 'store', 'edit', 'update', 'destroy'
    ]);
});
