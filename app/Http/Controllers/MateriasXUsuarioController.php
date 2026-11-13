<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Materia;
use App\Models\MateriasXUsuario;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MateriasXUsuarioController extends Controller
{
public function index($id)
{
$usuario = User::with('tipo')->findOrFail($id);

// Solo admin y profesor pueden ver esta pantalla
$usuarioConectado = auth()->user();
if (!in_array($usuarioConectado->tipo->tipo, ['admin', 'profesor'])) {
// Si es el propio estudiante viendo sus materias, permitir
if ($usuarioConectado->id !== $usuario->id) {
abort(403, 'No tienes permiso para acceder a esta sección.');
}
}

// Cargar las asignaciones con sus materias y calificaciones
$asignaciones = MateriasXUsuario::with(['materia', 'calificaciones'])
->where('users_id', $id)
->get();

// Calcular promedio de cada asignación
$asignaciones->each(function($asignacion) {
$asignacion->promedio = $asignacion->calificaciones->avg('calificacion') ?? 0;
$asignacion->promedio = round($asignacion->promedio, 2);
});

// Materias disponibles (las que el estudiante aún no tiene asignadas)
$materiasAsignadasIds = $asignaciones->pluck('materias_id');
$materiasDisponibles = Materia::whereNotIn('id', $materiasAsignadasIds)->get();

return view('materiasxusuario.index', compact('usuario', 'asignaciones', 'materiasDisponibles'));
}

public function asignar(Request $request, $id)
{
$validator = Validator::make($request->all(), [
'materias_id' => 'required|exists:materias,id',
], [
'materias_id.required' => 'Debe seleccionar una materia',
'materias_id.exists' => 'La materia seleccionada no es válida',
]);

if ($validator->fails()) {
return response()->json(['errors' => $validator->errors()], 422);
}

try {
// Verificar que no esté ya asignada
$existe = MateriasXUsuario::where('users_id', $id)
->where('materias_id', $request->materias_id)
->exists();

if ($existe) {
return response()->json([
'success' => false,
'message' => 'Esta materia ya está asignada al estudiante',
], 400);
}

MateriasXUsuario::create([
'users_id' => $id,
'materias_id' => $request->materias_id,
]);

return response()->json([
'success' => true,
'message' => 'Materia asignada correctamente',
]);
} catch (\Exception $e) {
Log::error('Error al asignar materia: ' . $e->getMessage());
return response()->json([
'success' => false,
'message' => 'Error al asignar la materia',
], 500);
}
}

public function desasignar($asignacion_id)
{
try {
$asignacion = MateriasXUsuario::findOrFail($asignacion_id);

// Eliminar primero todas las calificaciones de esta asignación
Calificacion::where('materias_x_usuarios_id', $asignacion_id)->delete();

// Luego eliminar la asignación
$asignacion->delete();

return redirect()->back()->with('success', 'Materia desasignada correctamente');
} catch (\Exception $e) {
Log::error('Error al desasignar materia: ' . $e->getMessage());
return redirect()->back()->with('error', 'Error al desasignar la materia');
}
}
}