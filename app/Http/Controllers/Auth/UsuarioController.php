<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tipo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
public function index()
{
$usuario = Auth::user();
$tipo = $usuario->tipo->tipo;

if ($tipo === 'admin') {
$usuarios = User::with('tipo')->get();
} elseif ($tipo === 'profesor') {
// El profesor solo ve estudiantes
$usuarios = User::with('tipo')
->whereHas('tipo', function($q) {
$q->where('tipo', 'estudiante');
})
->get();
} else {
// Estudiante u otro tipo: no autorizado
abort(403, 'No tienes permiso para acceder a esta sección.');
}

if (request()->ajax()) {
return response()->json(['usuarios' => $usuarios]);
}

return view('usuarios.index', compact('usuarios'));
}

public function create()
{
$tipos = Tipo::all();
return view('usuarios.create', compact('tipos'));
}

public function store(Request $request)
{
$validator = Validator::make($request->all(), [
'username' => 'required|string|max:255',
'email' => 'required|email|unique:users,email',
'password' => 'required|string|min:6|confirmed',
'tipos_id' => 'required|exists:tipos,id',
], [
'username.required' => 'El nombre de usuario es obligatorio',
'email.required' => 'El correo es obligatorio',
'email.email' => 'El correo no es válido',
'email.unique' => 'Este correo ya está registrado',
'password.required' => 'La contraseña es obligatoria',
'password.min' => 'La contraseña debe tener al menos 6 caracteres',
'password.confirmed' => 'Las contraseñas no coinciden',
'tipos_id.required' => 'El tipo de usuario es obligatorio',
'tipos_id.exists' => 'El tipo de usuario seleccionado no es válido',
]);

if ($validator->fails()) {
if ($request->ajax()) {
return response()->json(['errors' => $validator->errors()], 422);
}
return redirect()->back()->withErrors($validator)->withInput();
}

try {
$user = User::create([
'username' => $request->username,
'email' => $request->email,
'password' => Hash::make($request->password),
'tipos_id' => $request->tipos_id,
]);

if ($request->ajax()) {
return response()->json([
'success' => true,
'message' => 'Usuario creado correctamente',
'redirect' => route('usuarios.index'),
]);
}

return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente');
} catch (\Exception $e) {
Log::error('Error al crear usuario: ' . $e->getMessage());

if ($request->ajax()) {
return response()->json([
'success' => false,
'message' => 'Error al crear el usuario',
], 500);
}

return redirect()->back()->with('error', 'Error al crear el usuario')->withInput();
}
}

public function edit($id)
{
$usuario = User::findOrFail($id);
$tipos = Tipo::all();
return view('usuarios.edit', compact('usuario', 'tipos'));
}

public function update(Request $request, $id)
{
$validator = Validator::make($request->all(), [
'username' => 'required|string|max:255',
'email' => 'required|email|unique:users,email,' . $id,
'password' => 'nullable|string|min:6|confirmed',
'tipos_id' => 'required|exists:tipos,id',
], [
'username.required' => 'El nombre de usuario es obligatorio',
'email.required' => 'El correo es obligatorio',
'email.email' => 'El correo no es válido',
'email.unique' => 'Este correo ya está registrado',
'password.min' => 'La contraseña debe tener al menos 6 caracteres',
'password.confirmed' => 'Las contraseñas no coinciden',
'tipos_id.required' => 'El tipo de usuario es obligatorio',
'tipos_id.exists' => 'El tipo de usuario seleccionado no es válido',
]);

if ($validator->fails()) {
if ($request->ajax()) {
return response()->json(['errors' => $validator->errors()], 422);
}
return redirect()->back()->withErrors($validator)->withInput();
}

try {
$user = User::findOrFail($id);
$user->username = $request->username;
$user->email = $request->email;
$user->tipos_id = $request->tipos_id;

// Solo actualizar contraseña si se proporcionó
if ($request->filled('password')) {
$user->password = Hash::make($request->password);
}

$user->save();

if ($request->ajax()) {
return response()->json([
'success' => true,
'message' => 'Usuario actualizado correctamente',
'redirect' => route('usuarios.index'),
]);
}

return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente');
} catch (\Exception $e) {
Log::error('Error al actualizar usuario: ' . $e->getMessage());

if ($request->ajax()) {
return response()->json([
'success' => false,
'message' => 'Error al actualizar el usuario',
], 500);
}

return redirect()->back()->with('error', 'Error al actualizar el usuario')->withInput();
}
}

public function destroy(Request $request, $id)
{
try {
$user = User::findOrFail($id);
$user->delete();

if ($request->ajax()) {
return response()->json([
'success' => true,
'message' => 'Usuario eliminado correctamente',
]);
}

return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente');
} catch (\Exception $e) {
Log::error('Error al eliminar usuario: ' . $e->getMessage());

if ($request->ajax()) {
return response()->json([
'success' => false,
'message' => 'Error al eliminar el usuario',
], 500);
}

return redirect()->back()->with('error', 'Error al eliminar el usuario');
}
}
}