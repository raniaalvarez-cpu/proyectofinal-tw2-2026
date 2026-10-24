@extends('layouts.baseacceso')

@section('titulo', 'Iniciar Sesión')

@push('CSS')
<style>
body {
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
min-height: 100vh;
display: flex;
align-items: center;
justify-content: center;
}
.login-card {
background: white;
border-radius: 15px;
box-shadow: 0 10px 40px rgba(0,0,0,0.2);
overflow: hidden;
max-width: 900px;
width: 100%;
}
.brand-section {
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
color: white;
padding: 3rem 2rem;
display: flex;
flex-direction: column;
justify-content: center;
align-items: center;
text-align: center;
}
.brand-section i {
font-size: 4rem;
margin-bottom: 1rem;
}
.form-section {
padding: 3rem 2rem;
}
.btn-eye {
border: none;
background: transparent;
color: #6c757d;
}
.btn-eye:hover {
color: #667eea;
}
</style>
@endpush

@section('contenido')
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-10">
<div class="login-card">
<div class="row g-0">
<!-- Sección de marca -->
<div class="col-md-5 brand-section">
<i class="bi bi-mortarboard-fill"></i>
<h2 class="fw-bold mb-3">Sistema de Calificaciones</h2>
<p class="mb-0">Gestiona usuarios, materias y calificaciones de forma sencilla</p>
</div>

<!-- Sección de formulario -->
<div class="col-md-7 form-section">
<h3 class="fw-bold mb-4">Iniciar Sesión</h3>

<form id="frmAcceso" method="POST">
@csrf

<div class="mb-3">
<label for="email" class="form-label">
<i class="bi bi-envelope"></i> Correo Electrónico
</label>
<input type="email" class="form-control form-control-lg" id="email" name="email" required autofocus>
</div>

<div class="mb-4">
<label for="password" class="form-label">
<i class="bi bi-lock"></i> Contraseña
</label>
<div class="input-group input-group-lg">
<input type="password" class="form-control" id="password" name="password" required>
<button type="button" class="btn btn-eye" id="togglePassword">
<i class="bi bi-eye" id="eyeIcon"></i>
</button>
</div>
</div>

<button type="submit" class="btn btn-primary btn-lg w-100" id="btnSubmit">
<i class="bi bi-box-arrow-in-right"></i> Ingresar
</button>
</form>
</div>
</div>
</div>
</div>
</div>
</div>
@endsection

@push('JS')
<script>
// Toggle para mostrar/ocultar contraseña
document.getElementById('togglePassword').addEventListener('click', function() {
const passwordInput = document.getElementById('password');
const eyeIcon = document.getElementById('eyeIcon');

if (passwordInput.type === 'password') {
passwordInput.type = 'text';
eyeIcon.classList.remove('bi-eye');
eyeIcon.classList.add('bi-eye-slash');
} else {
passwordInput.type = 'password';
eyeIcon.classList.remove('bi-eye-slash');
eyeIcon.classList.add('bi-eye');
}
});
</script>
@endpush

@push('JSOR')
// Envío del formulario por AJAX
$('#frmAcceso').on('submit', function(e) {
e.preventDefault();

const btnSubmit = $('#btnSubmit');
const originalText = btnSubmit.html();

// Cambiar botón a estado "Verificando..."
btnSubmit.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Verificando...');

$.ajax({
url: '{{ route("login") }}',
method: 'POST',
data: $(this).serialize(),
success: function(response) {
Swal.fire({
icon: 'success',
title: '¡Bienvenido!',
text: response.message,
timer: 2000,
showConfirmButton: false
}).then(() => {
if (response.redirect) {
window.location.href = response.redirect;
}
});
},
error: function(xhr) {
btnSubmit.prop('disabled', false).html(originalText);

if (xhr.status === 422) {
// Errores de validación: recargar para mostrarlos
location.reload();
} else if (xhr.status === 401) {
// Credenciales incorrectas
Swal.fire({
icon: 'error',
title: 'Acceso denegado',
text: 'Las credenciales son incorrectas'
});
} else {
// Error del sistema
Swal.fire({
icon: 'error',
title: 'Error del sistema',
text: 'Ocurrió un error inesperado'
});
}
}
});
});
@endpush