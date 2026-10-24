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

if ($request->ajax()) {
return response()->json([
'success' => true,
'message' => '¡Bienvenido!',
'redirect' => route('acceso'), // Por ahora redirige a la misma página
]);
}
return redirect()->intended(route('acceso'));
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
}