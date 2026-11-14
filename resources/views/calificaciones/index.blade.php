@extends('layouts.basedashboard')

@section('titulo', 'Calificaciones de ' . $asignacion->user->username . ' - ' . $asignacion->materia->nombre)

@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-4">
<div>
<h1 class="h3 mb-1">
<i class="bi bi-card-checklist"></i> Calificaciones
</h1>
<p class="text-muted mb-0">
<strong>{{ $asignacion->user->username }}</strong> - {{ $asignacion->materia->nombre }}
</p>
</div>
<a href="{{ route('materiasxusuario.index', $asignacion->users_id) }}" class="btn btn-secondary">
<i class="bi bi-arrow-left"></i> Volver a Materias
</a>
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

<!-- Tarjeta de promedio -->
<div class="card shadow-sm mb-4">
<div class="card-body text-center">
<h5 class="card-title mb-3">Promedio General</h5>
<div class="display-1 fw-bold mb-2">{{ number_format($promedio, 2) }}</div>
<span class="badge text-bg-{{ $badge }} fs-5 px-4 py-2">{{ $nivel }}</span>
</div>
</div>

<!-- Tabla de calificaciones -->
<div class="card shadow-sm">
<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
<h5 class="mb-0"><i class="bi bi-list-ul"></i> Calificaciones Registradas</h5>
@if(auth()->user()->tipo->tipo !== 'estudiante')
<button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregar">
<i class="bi bi-plus-lg"></i> Agregar Calificación
</button>
@endif
</div>
<div class="card-body">
@if($calificaciones->count() === 0)
<div class="text-center text-muted py-5">
<i class="bi bi-inbox" style="font-size: 3rem;"></i>
<p class="mt-3 mb-0">No hay calificaciones registradas</p>
</div>
@else
<div class="table-responsive">
<table class="table table-striped table-hover align-middle">
<thead class="table-dark">
<tr>
<th>#</th>
<th class="text-center">Calificación</th>
<th class="text-center">Nivel</th>
@if(auth()->user()->tipo->tipo !== 'estudiante')
<th class="text-center" width="200">Acciones</th>
@endif
</tr>
</thead>
<tbody>
@foreach($calificaciones as $index => $cal)
<tr>
<td>{{ $index + 1 }}</td>
<td class="text-center">
<strong class="fs-4">{{ number_format($cal->calificacion, 2) }}</strong>
</td>
<td class="text-center">
@php
if ($cal->calificacion >= 9) {
$calBadge = 'success';
$calNivel = 'Excelente';
} elseif ($cal->calificacion >= 7) {
$calBadge = 'primary';
$calNivel = 'Bueno';
} elseif ($cal->calificacion >= 5) {
$calBadge = 'warning';
$calNivel = 'Regular';
} else {
$calBadge = 'danger';
$calNivel = 'Deficiente';
}
@endphp
<span class="badge text-bg-{{ $calBadge }}">{{ $calNivel }}</span>
</td>
@if(auth()->user()->tipo->tipo !== 'estudiante')
<td class="text-center">
<button class="btn btn-sm btn-warning" onclick="editarCalificacion({{ $cal->id }}, {{ $cal->calificacion }})">
<i class="bi bi-pencil-square"></i> Editar
</button>
<button class="btn btn-sm btn-danger" onclick="eliminarCalificacion({{ $cal->id }})">
<i class="bi bi-trash"></i> Eliminar
</button>
</td>
@endif
</tr>
@endforeach
</tbody>
</table>
</div>
@endif
</div>
</div>

<!-- Modal Agregar Calificación -->
@if(auth()->user()->tipo->tipo !== 'estudiante')
<div class="modal fade" id="modalAgregar" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header bg-primary text-white">
<h5 class="modal-title"><i class="bi bi-plus-circle"></i> Agregar Calificación</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<form id="formAgregarCalificacion">
<div class="modal-body">
<div class="mb-3">
<label for="calificacion_agregar" class="form-label">Calificación (0 a 10)</label>
<input type="number" class="form-control form-control-lg" id="calificacion_agregar" name="calificacion" step="0.01" min="0" max="10" required>
<div class="invalid-feedback" id="error-calificacion_agregar"></div>
</div>
<div class="text-center mt-3">
<h6>Vista previa del nivel:</h6>
<span class="badge text-bg-secondary fs-5 px-4 py-2" id="preview-nivel-agregar">-</span>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
<button type="submit" class="btn btn-primary" id="btnAgregar">
<i class="bi bi-check-circle"></i> Agregar
</button>
</div>
</form>
</div>
</div>
</div>

<!-- Modal Editar Calificación -->
<div class="modal fade" id="modalEditar" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header bg-warning">
<h5 class="modal-title"><i class="bi bi-pencil-square"></i> Editar Calificación</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form id="formEditarCalificacion">
<input type="hidden" id="calificacion_id_editar">
<div class="modal-body">
<div class="mb-3">
<label for="calificacion_editar" class="form-label">Calificación (0 a 10)</label>
<input type="number" class="form-control form-control-lg" id="calificacion_editar" name="calificacion" step="0.01" min="0" max="10" required>
<div class="invalid-feedback" id="error-calificacion_editar"></div>
</div>
<div class="text-center mt-3">
<h6>Vista previa del nivel:</h6>
<span class="badge text-bg-secondary fs-5 px-4 py-2" id="preview-nivel-editar">-</span>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
<button type="submit" class="btn btn-warning" id="btnEditar">
<i class="bi bi-check-circle"></i> Actualizar
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
function editarCalificacion(id, valorActual) {
$('#calificacion_id_editar').val(id);
$('#calificacion_editar').val(valorActual);
actualizarPreviewNivel('calificacion_editar', 'preview-nivel-editar');
$('#modalEditar').modal('show');
}

function eliminarCalificacion(id) {
Swal.fire({
title: '¿Eliminar calificación?',
text: 'Esta acción no se puede deshacer.',
icon: 'warning',
showCancelButton: true,
confirmButtonColor: '#d33',
cancelButtonColor: '#6c757d',
confirmButtonText: 'Sí, eliminar',
cancelButtonText: 'Cancelar'
}).then(function(result) {
if (result.isConfirmed) {
const form = document.createElement('form');
form.method = 'POST';
form.action = '{{ url("calificaciones") }}/' + id;

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

function actualizarPreviewNivel(inputId, previewId) {
const valor = parseFloat($('#' + inputId).val());
const preview = $('#' + previewId);

if (isNaN(valor) || valor < 0 || valor > 10) {
preview.text('-').removeClass('text-bg-success text-bg-primary text-bg-warning text-bg-danger').addClass('text-bg-secondary');
return;
}

let nivel, badge;
if (valor >= 9) {
nivel = 'Excelente';
badge = 'success';
} else if (valor >= 7) {
nivel = 'Bueno';
badge = 'primary';
} else if (valor >= 5) {
nivel = 'Regular';
badge = 'warning';
} else {
nivel = 'Deficiente';
badge = 'danger';
}

preview.text(nivel)
.removeClass('text-bg-success text-bg-primary text-bg-warning text-bg-danger text-bg-secondary')
.addClass('text-bg-' + badge);
}
</script>
@endpush

@push('JSOR')
@if(auth()->user()->tipo->tipo !== 'estudiante')
// Actualizar preview en tiempo real
$('#calificacion_agregar').on('input', function() {
actualizarPreviewNivel('calificacion_agregar', 'preview-nivel-agregar');
});

$('#calificacion_editar').on('input', function() {
actualizarPreviewNivel('calificacion_editar', 'preview-nivel-editar');
});

// Enviar formulario de agregar
$('#formAgregarCalificacion').on('submit', function(e) {
e.preventDefault();

$('#calificacion_agregar').removeClass('is-invalid');
$('#error-calificacion_agregar').text('');

const btnAgregar = $('#btnAgregar');
const originalText = btnAgregar.html();
btnAgregar.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Agregando...');

$.ajax({
url: '{{ url("materiasxusuario") }}/{{ $asignacion->users_id }}/materia/{{ $asignacion->materias_id }}/calificaciones',
method: 'POST',
data: $(this).serialize(),
success: function(response) {
Swal.fire({
icon: 'success',
title: '¡Calificación agregada!',
text: response.message,
timer: 1500,
showConfirmButton: false
}).then(function() {
location.reload();
});
},
error: function(xhr) {
btnAgregar.prop('disabled', false).html(originalText);

if (xhr.status === 422) {
const errors = xhr.responseJSON.errors;
$('#calificacion_agregar').addClass('is-invalid');
$('#error-calificacion_agregar').text(errors.calificacion[0]);
} else {
Swal.fire({
icon: 'error',
title: 'Error',
text: xhr.responseJSON.message || 'Ocurrió un error al agregar la calificación'
});
}
}
});
});

// Enviar formulario de editar
$('#formEditarCalificacion').on('submit', function(e) {
e.preventDefault();

$('#calificacion_editar').removeClass('is-invalid');
$('#error-calificacion_editar').text('');

const btnEditar = $('#btnEditar');
const originalText = btnEditar.html();
const calificacionId = $('#calificacion_id_editar').val();
btnEditar.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Actualizando...');

$.ajax({
url: '{{ url("calificaciones") }}/' + calificacionId,
method: 'POST',
data: $(this).serialize() + '&_method=PUT',
success: function(response) {
Swal.fire({
icon: 'success',
title: '¡Calificación actualizada!',
text: response.message,
timer: 1500,
showConfirmButton: false
}).then(function() {
location.reload();
});
},
error: function(xhr) {
btnEditar.prop('disabled', false).html(originalText);

if (xhr.status === 422) {
const errors = xhr.responseJSON.errors;
$('#calificacion_editar').addClass('is-invalid');
$('#error-calificacion_editar').text(errors.calificacion[0]);
} else {
Swal.fire({
icon: 'error',
title: 'Error',
text: xhr.responseJSON.message || 'Ocurrió un error al actualizar la calificación'
});
}
}
});
});
@endif
@endpush