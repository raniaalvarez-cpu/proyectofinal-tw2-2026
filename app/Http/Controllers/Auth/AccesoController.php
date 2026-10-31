<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AccesoController extends Controller
{
public function mostrarFormulario()
{
// Si ya hay sesión, redirigir según tipo de usuario
if (Auth::check()) {
return $this->redirigirSegunTipo();
}

return view('auth.acceso');
}

public function iniciarSesion(Request $request)
{
// Validación
$validator = Validator::make($request->all(), [
'email' => 'required|email',
'password' => 'required|min:6',
], [
'email.required' => 'El correo es obligatorio',
'email.email' => 'El correo no es válido',
'password.required' => 'La contraseña es obligatoria',
'password.min' => 'La contraseña debe tener al menos 6 caracteres',
]);

if ($validator->fails()) {
if ($request->ajax()) {
return response()->json(['errors' => $validator->errors()], 422);
}
return redirect()->back()->withErrors($validator)->withInput($request->only('email'));
}

// Intentar autenticación
$credentials = $request->only('email', 'password');

if (Auth::attempt($credentials)) {
$request->session()->regenerate();

$usuario = Auth::user();

// Verificar que el usuario tenga tipo asignado
if (!$usuario->tipo) {
Auth::logout();
$request->session()->invalidate();
$request->session()->regenerateToken();

if ($request->ajax()) {
return response()->json([
'success' => false,
'message' => 'El usuario no tiene un tipo asignado',
], 401);
}

return redirect()->route('acceso')->withErrors([
'email' => 'El usuario no tiene un tipo asignado',
]);
}

// Redirigir según tipo de usuario
if ($request->ajax()) {
return response()->json([
'success' => true,
'message' => '¡Bienvenido!',
'redirect' => $this->obtenerUrlRedireccion(),
]);
}

return $this->redirigirSegunTipo();
}

// Credenciales incorrectas
if ($request->ajax()) {
return response()->json([
'success' => false,
'message' => 'Las credenciales son incorrectas',
], 401);
}

return redirect()->back()->withErrors([
'email' => 'Las credenciales son incorrectas',
])->withInput($request->only('email'));
}

public function cerrarSesion(Request $request)
{
Auth::logout();
$request->session()->invalidate();
$request->session()->regenerateToken();

return redirect()->route('acceso');
}

/**
 * Redirige según el tipo de usuario conectado
 */
private function redirigirSegunTipo()
{
$usuario = Auth::user();
$tipo = $usuario->tipo->tipo;

switch ($tipo) {
case 'admin':
case 'profesor':
return redirect()->route('usuarios.index');

case 'estudiante':
// La ruta materiasxusuario.index se crea en la Clase 5
// Por ahora redirigimos a la raíz
return redirect('/');

default:
// Tipo desconocido: cerrar sesión y mostrar error
Auth::logout();
request()->session()->invalidate();
request()->session()->regenerateToken();

return redirect()->route('acceso')->withErrors([
'email' => 'Tipo de usuario no válido',
]);
}
}

/**
 * Devuelve solo la URL de redirección (para respuestas JSON)
 */
private function obtenerUrlRedireccion()
{
$usuario = Auth::user();
$tipo = $usuario->tipo->tipo;

switch ($tipo) {
case 'admin':
case 'profesor':
return route('usuarios.index');

case 'estudiante':
return url('/');

default:
return route('acceso');
}
}
}