@extends('layouts.basedashboard')

@section('titulo', 'Editar Usuario')

@section('contenido')
<div class="row justify-content-center">
<div class="col-md-8">
<div class="card shadow-sm">
<div class="card-header bg-warning">
<i class="bi bi-pencil-square"></i> Editar Usuario
</div>
<div class="card-body">
<form id="formEditarUsuario" method="POST">
@csrf
@method('PUT')

<div class="mb-3">
<label for="username" class="form-label">Nombre de usuario*</label>
<input type="text" class="form-control" id="username" name="username" value="{{ $usuario->username }}" required>
<div class="invalid-feedback" id="error-username"></div>
</div>

<div class="mb-3">
<label for="email" class="form-label">Correo electrónico*</label>
<input type="email" class="form-control" id="email" name="email" value="{{ $usuario->email }}" required>
<div class="invalid-feedback" id="error-email"></div>
</div>

<div class="mb-3">
<label for="tipos_id" class="form-label">Tipo de usuario*</label>
<select class="form-select" id="tipos_id" name="tipos_id" required>
<option value="">Seleccione un tipo...</option>
@foreach($tipos as $tipo)
<option value="{{ $tipo->id }}" {{ $usuario->tipos_id == $tipo->id ? 'selected' : '' }}>
{{ $tipo->tipo }}
</option>
@endforeach
</select>
<div class="invalid-feedback" id="error-tipos_id"></div>
</div>

<div class="row">
<div class="col-md-6 mb-3">
<label for="password" class="form-label">
Nueva contraseña <small class="text-muted">(déjela vacía para no cambiar)</small>
</label>
<div class="input-group">
<input type="password" class="form-control" id="password" name="password">
<button type="button" class="btn btn-outline-secondary btn-eye" data-target="password">
<i class="bi bi-eye"></i>
</button>
</div>
<div class="invalid-feedback" id="error-password"></div>
</div>

<div class="col-md-6 mb-3">
<label for="password_confirmation" class="form-label">Confirmar contraseña</label>
<div class="input-group">
<input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
<button type="button" class="btn btn-outline-secondary btn-eye" data-target="password_confirmation">
<i class="bi bi-eye"></i>
</button>
</div>
<div class="invalid-feedback" id="error-password_confirmation"></div>
</div>
</div>

<div class="d-flex justify-content-between">
<a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
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

@push('JS')
<script>
// Toggle para mostrar/ocultar contraseñas
document.querySelectorAll('.btn-eye').forEach(function(btn) {
btn.addEventListener('click', function() {
const targetId = this.getAttribute('data-target');
const input = document.getElementById(targetId);
const icon = this.querySelector('i');

if (input.type === 'password') {
input.type = 'text';
icon.classList.remove('bi-eye');
icon.classList.add('bi-eye-slash');
} else {
input.type = 'password';
icon.classList.remove('bi-eye-slash');
icon.classList.add('bi-eye');
}
});
});
</script>
@endpush

@push('JSOR')
$('#formEditarUsuario').on('submit', function(e) {
e.preventDefault();

// Limpiar errores previos
$('.form-control, .form-select').removeClass('is-invalid');
$('.invalid-feedback').text('');

const btnActualizar = $('#btnActualizar');
const originalText = btnActualizar.html();
btnActualizar.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Actualizando...');

$.ajax({
url: '{{ route("usuarios.update", $usuario->id) }}',
method: 'POST',
data: $(this).serialize(),
success: function(response) {
Swal.fire({
icon: 'success',
title: '¡Usuario actualizado!',
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
// Errores de validación: marcar cada campo
const errors = xhr.responseJSON.errors;
$.each(errors, function(field, messages) {
$('#' + field).addClass('is-invalid');
$('#error-' + field).text(messages[0]);
});
} else {
Swal.fire({
icon: 'error',
title: 'Error',
text: xhr.responseJSON.message || 'Ocurrió un error al actualizar el usuario'
});
}
}
});
});
@endpush