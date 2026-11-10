@extends('layouts.basedashboard')

@section('titulo', 'Editar Tipo de Usuario')

@section('contenido')
<div class="row justify-content-center">
<div class="col-md-6">
<div class="card shadow-sm">
<div class="card-header bg-warning">
<i class="bi bi-pencil-square"></i> Editar Tipo de Usuario
</div>
<div class="card-body">
@if($tipo->users_count > 0)
<div class="alert alert-info">
<i class="bi bi-info-circle"></i> Este tipo tiene <strong>{{ $tipo->users_count }}</strong> usuario(s) asignado(s). No podrá eliminarse mientras tenga usuarios.
</div>
@endif

<form id="formEditarTipo" method="POST">
@csrf
@method('PUT')

<div class="mb-3">
<label for="tipo" class="form-label">Nombre del tipo*</label>
<input type="text" class="form-control" id="tipo" name="tipo" value="{{ $tipo->tipo }}" maxlength="50" required>
<div class="invalid-feedback" id="error-tipo"></div>
</div>

<div class="d-flex justify-content-between">
<a href="{{ route('tipos.index') }}" class="btn btn-secondary">
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
$('#formEditarTipo').on('submit', function(e) {
e.preventDefault();

$('.form-control').removeClass('is-invalid');
$('.invalid-feedback').text('');

const btnActualizar = $('#btnActualizar');
const originalText = btnActualizar.html();
btnActualizar.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Actualizando...');

$.ajax({
url: '{{ route("tipos.update", $tipo->id) }}',
method: 'POST',
data: $(this).serialize(),
success: function(response) {
Swal.fire({
icon: 'success',
title: '¡Tipo actualizado!',
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
text: xhr.responseJSON.message || 'Ocurrió un error al actualizar el tipo'
});
}
}
});
});
@endpush