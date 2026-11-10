<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MateriaController extends Controller
{
public function index()
{
$materias = Materia::withCount('users')->get();

if (request()->ajax()) {
return response()->json(['materias' => $materias]);
}

return view('materias.index', compact('materias'));
}

public function create()
{
return view('materias.create');
}

public function store(Request $request)
{
$validator = Validator::make($request->all(), [
'nombre' => 'required|string|max:50|unique:materias,nombre',
], [
'nombre.required' => 'El nombre de la materia es obligatorio',
'nombre.max' => 'El nombre no puede superar los 50 caracteres',
'nombre.unique' => 'Esta materia ya existe',
]);

if ($validator->fails()) {
if ($request->ajax()) {
return response()->json(['errors' => $validator->errors()], 422);
}
return redirect()->back()->withErrors($validator)->withInput();
}

try {
Materia::create([
'nombre' => $request->nombre,
]);

if ($request->ajax()) {
return response()->json([
'success' => true,
'message' => 'Materia creada correctamente',
'redirect' => route('materias.index'),
]);
}

return redirect()->route('materias.index')->with('success', 'Materia creada correctamente');
} catch (\Exception $e) {
Log::error('Error al crear materia: ' . $e->getMessage());

if ($request->ajax()) {
return response()->json([
'success' => false,
'message' => 'Error al crear la materia',
], 500);
}

return redirect()->back()->with('error', 'Error al crear la materia')->withInput();
}
}

public function edit($id)
{
$materia = Materia::withCount('users')->findOrFail($id);
return view('materias.edit', compact('materia'));
}

public function update(Request $request, $id)
{
$validator = Validator::make($request->all(), [
'nombre' => 'required|string|max:50|unique:materias,nombre,' . $id,
], [
'nombre.required' => 'El nombre de la materia es obligatorio',
'nombre.max' => 'El nombre no puede superar los 50 caracteres',
'nombre.unique' => 'Esta materia ya existe',
]);

if ($validator->fails()) {
if ($request->ajax()) {
return response()->json(['errors' => $validator->errors()], 422);
}
return redirect()->back()->withErrors($validator)->withInput();
}

try {
$materia = Materia::findOrFail($id);
$materia->nombre = $request->nombre;
$materia->save();

if ($request->ajax()) {
return response()->json([
'success' => true,
'message' => 'Materia actualizada correctamente',
'redirect' => route('materias.index'),
]);
}

return redirect()->route('materias.index')->with('success', 'Materia actualizada correctamente');
} catch (\Exception $e) {
Log::error('Error al actualizar materia: ' . $e->getMessage());

if ($request->ajax()) {
return response()->json([
'success' => false,
'message' => 'Error al actualizar la materia',
], 500);
}

return redirect()->back()->with('error', 'Error al actualizar la materia')->withInput();
}
}

public function destroy(Request $request, $id)
{
try {
$materia = Materia::withCount('users')->findOrFail($id);

// Protección de integridad: no permitir borrar si tiene estudiantes
if ($materia->users_count > 0) {
if ($request->ajax()) {
return response()->json([
'success' => false,
'message' => 'No se puede eliminar esta materia porque tiene ' . $materia->users_count . ' estudiante(s) asignado(s).',
], 409);
}

return redirect()->route('materias.index')->with('error', 'No se puede eliminar esta materia porque tiene estudiantes asignados.');
}

$materia->delete();

if ($request->ajax()) {
return response()->json([
'success' => true,
'message' => 'Materia eliminada correctamente',
]);
}

return redirect()->route('materias.index')->with('success', 'Materia eliminada correctamente');
} catch (\Exception $e) {
Log::error('Error al eliminar materia: ' . $e->getMessage());

if ($request->ajax()) {
return response()->json([
'success' => false,
'message' => 'Error al eliminar la materia',
], 500);
}

return redirect()->back()->with('error', 'Error al eliminar la materia');
}
}
}