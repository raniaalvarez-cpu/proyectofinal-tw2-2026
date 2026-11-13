@extends('layouts.basedashboard')

@section('titulo', 'Materias de ' . $usuario->username)

@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-4">
<div>
<h1 class="h3 mb-1">
<i class="bi bi-journal-bookmark-fill"></i> Materias de {{ $usuario->username }}
</h1>
<p class="text-muted mb-0">
<span class="badge text-bg-info">{{ $usuario->tipo->tipo }}</span>
{{ $usuario->email }}
</p>
</div>
@if(auth()->user()->tipo->tipo !== 'estudiante')
<a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
<i class="bi bi-arrow-left"></i> Volver a Usuarios
</a>
@endif
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
<i class="bi bi-check-circle"></i> {{ session('success') }}
<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
<i class="bi bi-x-circle"></i> {{ session('error') }}
<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card shadow-sm mb-4">
<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
<h5 class="mb-0"><i class="bi bi-list-check"></i> Materias Asignadas</h5>
@if(auth()->user()->tipo->tipo !== 'estudiante' && $materiasDisponibles->count() > 0)
<button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalAsignar">
<i class="bi bi-plus-lg"></i> Asignar Materia
</button>
@endif
</div>
<div class="card-body">
@if($asignaciones->count() === 0)
<div class="text-center text-muted py-5">
<i class="bi bi-inbox" style="font-size: 3rem;"></i>
<p class="mt-3 mb-0">Este estudiante no tiene materias asignadas</p>
</div>
@else
<div class="table-responsive">
<table class="table table-striped table-hover align-middle">
<thead class="table-dark">
<tr>
<th>Materia</th>
<th class="text-center">Notas</th>
<th class="text-center">Promedio</th>
<th class="text-center">Nivel</th>
<th class="text-center" width="250">Acciones</th>
</tr>
</thead>
<tbody>
@foreach($asignaciones as $asignacion)
<tr>
<td>{{ $asignacion->materia->nombre }}</td>
<td class="text-center">
<span class="badge text-bg-secondary">{{ $asignacion->calificaciones->count() }}</span>
</td>
<td class="text-center">
<strong>{{ number_format($asignacion->promedio, 2) }}</strong>
</td>
<td class="text-center">
@php
$promedio = $asignacion->promedio;
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
@endphp
<span class="badge text-bg-{{ $badge }}">{{ $nivel }}</span>
</td>
<td class="text-center">
<button class="btn btn-sm btn-info" onclick="agregarCalificacion({{ $usuario->id }}, {{ $asignacion->materias_id }})">
<i class="bi bi-card-checklist"></i> Calificaciones
</button>
@if(auth()->user()->tipo->tipo !== 'estudiante')
<button class="btn btn-sm btn-danger" onclick="desasignarMateria({{ $asignacion->id }})">
<i class="bi bi-x-circle"></i> Desasignar
</button>
@endif
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endif
</div>
</div>

<!-- Modal para asignar materia -->
@if(auth()->user()->tipo->tipo !== 'estudiante' && $materiasDisponibles->count() > 0)
<div class="modal fade" id="modalAsignar" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header bg-primary text-white">
<h5 class="modal-title"><i class="bi bi-plus-circle"></i> Asignar Materia</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<form id="formAsignarMateria">
<div class="modal-body">
<div class="mb-3">
<label for="materias_id" class="form-label">Seleccione la materia a asignar</label>
<select class="form-select" id="materias_id" name="materias_id" required>
<option value="">Seleccione una materia...</option>
@foreach($materiasDisponibles as $materia)
<option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
@endforeach
</select>
<div class="invalid-feedback" id="error-materias_id"></div>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
<button type="submit" class="btn btn-primary" id="btnAsignar">
<i class="bi bi-check-circle"></i> Asignar
</button>
</div>
</form>
</div>
</div>
</div>
@endif
@endsection

@push('JS')
<script>
function agregarCalificacion(usuarioId, materiaId) {
window.location.href = '{{ url("materiasxusuario") }}/' + usuarioId + '/materia/' + materiaId + '/calificaciones';
}

function desasignarMateria(asignacionId) {
Swal.fire({
title: '¿Desasignar materia?',
text: 'Se eliminarán todas las calificaciones de esta materia.',
icon: 'warning',
showCancelButton: true,
confirmButtonColor: '#d33',
cancelButtonColor: '#6c757d',
confirmButtonText: 'Sí, desasignar',
cancelButtonText: 'Cancelar'
}).then(function(result) {
if (result.isConfirmed) {
const form = document.createElement('form');
form.method = 'POST';
form.action = '{{ url("materiasxusuario") }}/' + asignacionId + '/desasignar';

const csrf = document.createElement('input');
csrf.type = 'hidden';
csrf.name = '_token';
csrf.value = '{{ csrf_token() }}';

const method = document.createElement('input');
method.type = 'hidden';
method.name = '_method';
method.value = 'DELETE';

form.appendChild(csrf);
form.appendChild(method);
document.body.appendChild(form);
form.submit();
}
});
}
</script>
@endpush

@push('JSOR')
@if(auth()->user()->tipo->tipo !== 'estudiante' && $materiasDisponibles->count() > 0)
$('#formAsignarMateria').on('submit', function(e) {
e.preventDefault();

$('.form-select').removeClass('is-invalid');
$('.invalid-feedback').text('');

const btnAsignar = $('#btnAsignar');
const originalText = btnAsignar.html();
btnAsignar.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Asignando...');

$.ajax({
url: '{{ url("materiasxusuario") }}/{{ $usuario->id }}/asignar',
method: 'POST',
data: $(this).serialize(),
success: function(response) {
Swal.fire({
icon: 'success',
title: '¡Materia asignada!',
text: response.message,
timer: 1500,
showConfirmButton: false
}).then(function() {
location.reload();
});
},
error: function(xhr) {
btnAsignar.prop('disabled', false).html(originalText);

if (xhr.status === 422) {
const errors = xhr.responseJSON.errors;
$.each(errors, function(field, messages) {
$('#' + field).addClass('is-invalid');
$('#error-' + field).text(messages[0]);
});
} else {
Swal.fire({
icon: 'error',
title: 'Error',
text: xhr.responseJSON.message || 'Ocurrió un error al asignar la materia'
});
}
}
});
});
@endif
@endpush