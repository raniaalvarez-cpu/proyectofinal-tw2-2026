<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AccesoController;

// Ruta de acceso (login)
Route::get('/', [AccesoController::class, 'mostrarFormulario'])->name('acceso');
Route::post('/login', [AccesoController::class, 'iniciarSesion'])->name('login');
Route::post('/logout', [AccesoController::class, 'cerrarSesion'])->name('logout');