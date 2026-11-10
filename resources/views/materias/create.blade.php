@extends('layouts.basedashboard')

@section('titulo', 'Crear Materia')

@section('contenido')
<div class="row justify-content-center">
<div class="col-md-6">
<div class="card shadow-sm">
<div class="card-header bg-primary text-white">
<i class="bi bi-journal-plus"></i> Nueva Materia
</div>
<div class="card-body">
<form id="formCrearMateria" method="POST">
@csrf

<div class="mb-3">
<label for="nombre" class="form-label">Nombre de la materia*</label>
<input type="text" class="form-control" id="nombre" name="nombre" maxlength="50" required>
<div class="invalid-feedback" id="error-nombre"></div>
</div>

<div class="d-flex justify-content-between">
<a href="{{ route('materias.index') }}" class="btn btn-secondary">
<i class="bi bi-arrow-left"></i> Cancelar
</a>
<button type="submit" class="btn btn-primary" id="btnGuardar">
<i class="bi bi-save"></i> Guardar
</button>
</div>
</form>
</div>
</div>
</div>
</div>
@endsection

@push('JSOR')
$('#formCrearMateria').on('submit', function(e) {
e.preventDefault();

$('.form-control').removeClass('is-invalid');
$('.invalid-feedback').text('');

const btnGuardar = $('#btnGuardar');
const originalText = btnGuardar.html();
btnGuardar.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Guardando...');

$.ajax({
url: '{{ route("materias.store") }}',
method: 'POST',
data: $(this).serialize(),
success: function(response) {
Swal.fire({
icon: 'success',
title: '¡Materia creada!',
text: response.message,
timer: 2000,
showConfirmButton: false
}).then(function() {
window.location.href = response.redirect;
});
},
error: function(xhr) {
btnGuardar.prop('disabled', false).html(originalText);

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
text: xhr.responseJSON.message || 'Ocurrió un error al crear la materia'
});
}
}
});
});
@endpush