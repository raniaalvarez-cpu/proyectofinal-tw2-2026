<?php

namespace App\Http\Controllers;

use App\Models\Tipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TipoController extends Controller
{
public function index()
{
$tipos = Tipo::withCount('users')->get();

if (request()->ajax()) {
return response()->json(['tipos' => $tipos]);
}

return view('tipos.index', compact('tipos'));
}

public function create()
{
return view('tipos.create');
}

public function store(Request $request)
{
$validator = Validator::make($request->all(), [
'tipo' => 'required|string|max:50|unique:tipos,tipo',
], [
'tipo.required' => 'El tipo de usuario es obligatorio',
'tipo.max' => 'El tipo no puede superar los 50 caracteres',
'tipo.unique' => 'Este tipo de usuario ya existe',
]);

if ($validator->fails()) {
if ($request->ajax()) {
return response()->json(['errors' => $validator->errors()], 422);
}
return redirect()->back()->withErrors($validator)->withInput();
}

try {
Tipo::create([
'tipo' => $request->tipo,
]);

if ($request->ajax()) {
return response()->json([
'success' => true,
'message' => 'Tipo de usuario creado correctamente',
'redirect' => route('tipos.index'),
]);
}

return redirect()->route('tipos.index')->with('success', 'Tipo de usuario creado correctamente');
} catch (\Exception $e) {
Log::error('Error al crear tipo: ' . $e->getMessage());

if ($request->ajax()) {
return response()->json([
'success' => false,
'message' => 'Error al crear el tipo de usuario',
], 500);
}

return redirect()->back()->with('error', 'Error al crear el tipo de usuario')->withInput();
}
}

public function edit($id)
{
$tipo = Tipo::withCount('users')->findOrFail($id);
return view('tipos.edit', compact('tipo'));
}

public function update(Request $request, $id)
{
$validator = Validator::make($request->all(), [
'tipo' => 'required|string|max:50|unique:tipos,tipo,' . $id,
], [
'tipo.required' => 'El tipo de usuario es obligatorio',
'tipo.max' => 'El tipo no puede superar los 50 caracteres',
'tipo.unique' => 'Este tipo de usuario ya existe',
]);

if ($validator->fails()) {
if ($request->ajax()) {
return response()->json(['errors' => $validator->errors()], 422);
}
return redirect()->back()->withErrors($validator)->withInput();
}

try {
$tipo = Tipo::findOrFail($id);
$tipo->tipo = $request->tipo;
$tipo->save();

if ($request->ajax()) {
return response()->json([
'success' => true,
'message' => 'Tipo de usuario actualizado correctamente',
'redirect' => route('tipos.index'),
]);
}

return redirect()->route('tipos.index')->with('success', 'Tipo de usuario actualizado correctamente');
} catch (\Exception $e) {
Log::error('Error al actualizar tipo: ' . $e->getMessage());

if ($request->ajax()) {
return response()->json([
'success' => false,
'message' => 'Error al actualizar el tipo de usuario',
], 500);
}

return redirect()->back()->with('error', 'Error al actualizar el tipo de usuario')->withInput();
}
}

public function destroy(Request $request, $id)
{
try {
$tipo = Tipo::withCount('users')->findOrFail($id);

// Protección de integridad: no permitir borrar si tiene usuarios
if ($tipo->users_count > 0) {
if ($request->ajax()) {
return response()->json([
'success' => false,
'message' => 'No se puede eliminar este tipo porque tiene ' . $tipo->users_count . ' usuario(s) asignado(s).',
], 409);
}

return redirect()->route('tipos.index')->with('error', 'No se puede eliminar este tipo porque tiene usuarios asignados.');
}

$tipo->delete();

if ($request->ajax()) {
return response()->json([
'success' => true,
'message' => 'Tipo de usuario eliminado correctamente',
]);
}

return redirect()->route('tipos.index')->with('success', 'Tipo de usuario eliminado correctamente');
} catch (\Exception $e) {
Log::error('Error al eliminar tipo: ' . $e->getMessage());

if ($request->ajax()) {
return response()->json([
'success' => false,
'message' => 'Error al eliminar el tipo de usuario',
], 500);
}

return redirect()->back()->with('error', 'Error al eliminar el tipo de usuario');
}
}
}