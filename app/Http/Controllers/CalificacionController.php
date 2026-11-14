<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\MateriasXUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CalificacionController extends Controller
{
public function index($usuario_id, $materia_id)
{
// Verificar que la materia esté asignada al usuario
$asignacion = MateriasXUsuario::with(['materia', 'user'])
->where('users_id', $usuario_id)
->where('materias_id', $materia_id)
->first();

if (!$asignacion) {
return redirect()->back()->with('error', 'Esta materia no está asignada al estudiante');
}

// Cargar las calificaciones de esta asignación
$calificaciones = Calificacion::where('materias_x_usuarios_id', $asignacion->id)
->orderBy('id', 'asc')
->get();

// Calcular promedio
$promedio = $calificaciones->avg('calificacion') ?? 0;
$promedio = round($promedio, 2);

// Determinar nivel
if ($promedio >= 9) {
$nivel = 'Excelente';
$badge = 'success';
} elseif ($promedio >= 7) {
$nivel = 'Bueno';
$badge = 'primary';
} elseif ($promedio >= 5) {
$nivel = 'Regular';
$badge = 'warning';
} else {
$nivel = 'Deficiente';
$badge = 'danger';
}

return view('calificaciones.index', compact('asignacion', 'calificaciones', 'promedio', 'nivel', 'badge'));
}

public function store(Request $request, $usuario_id, $materia_id)
{
$validator = Validator::make($request->all(), [
'calificacion' => 'required|numeric|min:0|max:10',
], [
'calificacion.required' => 'La calificación es obligatoria',
'calificacion.numeric' => 'La calificación debe ser un número',
'calificacion.min' => 'La calificación debe ser al menos 0',
'calificacion.max' => 'La calificación no puede ser mayor a 10',
]);

if ($validator->fails()) {
return response()->json(['errors' => $validator->errors()], 422);
}

try {
$asignacion = MateriasXUsuario::where('users_id', $usuario_id)
->where('materias_id', $materia_id)
->first();

if (!$asignacion) {
return response()->json([
'success' => false,
'message' => 'La materia no está asignada al estudiante',
], 400);
}

Calificacion::create([
'calificacion' => $request->calificacion,
'materias_x_usuarios_id' => $asignacion->id,
]);

return response()->json([
'success' => true,
'message' => 'Calificación agregada correctamente',
]);
} catch (\Exception $e) {
Log::error('Error al crear calificación: ' . $e->getMessage());
return response()->json([
'success' => false,
'message' => 'Error al crear la calificación',
], 500);
}
}

public function update(Request $request, $id)
{
$validator = Validator::make($request->all(), [
'calificacion' => 'required|numeric|min:0|max:10',
], [
'calificacion.required' => 'La calificación es obligatoria',
'calificacion.numeric' => 'La calificación debe ser un número',
'calificacion.min' => 'La calificación debe ser al menos 0',
'calificacion.max' => 'La calificación no puede ser mayor a 10',
]);

if ($validator->fails()) {
return response()->json(['errors' => $validator->errors()], 422);
}

try {
$calificacion = Calificacion::findOrFail($id);
$calificacion->calificacion = $request->calificacion;
$calificacion->save();

return response()->json([
'success' => true,
'message' => 'Calificación actualizada correctamente',
]);
} catch (\Exception $e) {
Log::error('Error al actualizar calificación: ' . $e->getMessage());
return response()->json([
'success' => false,
'message' => 'Error al actualizar la calificación',
], 500);
}
}

public function destroy($id)
{
try {
$calificacion = Calificacion::findOrFail($id);
$calificacion->delete();

return redirect()->back()->with('success', 'Calificación eliminada correctamente');
} catch (\Exception $e) {
Log::error('Error al eliminar calificación: ' . $e->getMessage());
return redirect()->back()->with('error', 'Error al eliminar la calificación');
}
}
}