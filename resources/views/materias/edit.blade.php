@extends('layouts.basedashboard')

@section('titulo', 'Editar Materia')

@section('contenido')
<div class="row justify-content-center">
<div class="col-md-6">
<div class="card shadow-sm">
<div class="card-header bg-warning">
<i class="bi bi-pencil-square"></i> Editar Materia
</div>
<div class="card-body">
@if($materia->users_count > 0)
<div class="alert alert-info">
<i class="bi bi-info-circle"></i> Esta materia tiene <strong>{{ $materia->users_count }}</strong> estudiante(s) asignado(s). No podrá eliminarse mientras tenga estudiantes.
</div>
@endif

<form id="formEditarMateria" method="POST">
@csrf
@method('PUT')

<div class="mb-3">
<label for="nombre" class="form-label">Nombre de la materia*</label>
<input type="text" class="form-control" id="nombre" name="nombre" value="{{ $materia->nombre }}" maxlength="50" required>
<div class="invalid-feedback" id="error-nombre"></div>
</div>

<div class="d-flex justify-content-between">
<a href="{{ route('materias.index') }}" class="btn btn-secondary">
<i class="bi bi-arrow-left"></i> Cancelar
</a>
<button type="submit" class="btn btn-warning" id="btnActualizar">
<i class="bi bi-save"></i> Actualizar
</button>
</div>
</form>
</div>
</div>
</div>
</div>
@endsection

@push('JSOR')
$('#formEditarMateria').on('submit', function(e) {
e.preventDefault();

$('.form-control').removeClass('is-invalid');
$('.invalid-feedback').text('');

const btnActualizar = $('#btnActualizar');
const originalText = btnActualizar.html();
btnActualizar.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Actualizando...');

$.ajax({
url: '{{ route("materias.update", $materia->id) }}',
method: 'POST',
data: $(this).serialize(),
success: function(response) {
Swal.fire({
icon: 'success',
title: '¡Materia actualizada!',
text: response.message,
timer: 2000,
showConfirmButton: false
}).then(function() {
window.location.href = response.redirect;
});
},
error: function(xhr) {
btnActualizar.prop('disabled', false).html(originalText);

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
text: xhr.responseJSON.message || 'Ocurrió un error al actualizar la materia'
});
}
}
});
});
@endpush