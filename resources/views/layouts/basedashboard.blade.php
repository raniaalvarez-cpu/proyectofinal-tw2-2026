<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('titulo', 'Sistema de Calificaciones')</title>
<!-- Bootstrap 5.3 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- DataTables Bootstrap 5 CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@stack('CSS')
</head>
<body class="bg-light">
<!-- Navbar azul del dashboard -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
<div class="container-fluid">
<a class="navbar-brand" href="{{ route('usuarios.index') }}">
<i class="bi bi-mortarboard-fill"></i> Sistema de Calificaciones
</a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="menuPrincipal">
<ul class="navbar-nav me-auto mb-2 mb-lg-0">
<li class="nav-item">
<a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active fw-bold' : '' }}" href="{{ route('usuarios.index') }}">
<i class="bi bi-people"></i> Usuarios
</a>
</li>
@if(Auth::user()->tipo->tipo === 'admin')
<li class="nav-item">
<a class="nav-link {{ request()->routeIs('tipos.*') ? 'active fw-bold' : '' }}" href="{{ route('tipos.index') }}"><i class="bi bi-tags"></i> Tipos de Usuario
</a>
</li>
@endif
<li class="nav-item">
<a class="nav-link" href="#">
<i class="bi bi-journal-bookmark"></i> Materias
</a>
</li>
</ul>
<ul class="navbar-nav">
<li class="nav-item dropdown">
<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
<i class="bi bi-person-circle"></i> {{ Auth::user()->username }}
<span class="badge text-bg-light">{{ Auth::user()->tipo->tipo }}</span>
</a>
<ul class="dropdown-menu dropdown-menu-end">
<li>
<form action="{{ route('logout') }}" method="POST" id="frmLogout">
@csrf
<button type="submit" class="dropdown-item text-danger">
<i class="bi bi-box-arrow-right"></i> Cerrar Sesión
</button>
</form>
</li>
</ul>
</li>
</ul>
</div>
</div>
</nav>

<!-- Contenido principal -->
<main class="container-fluid py-4">
@yield('contenido')
</main>

<!-- jQuery 3.7 -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5.3 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
// Token CSRF para todas las peticiones AJAX
$.ajaxSetup({
headers: {
'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});

// Confirmación de cierre de sesión con SweetAlert
$('#frmLogout').on('submit', function(e) {
e.preventDefault();
const form = this;
Swal.fire({
title: '¿Cerrar sesión?',
text: 'Deberás iniciar sesión nuevamente para entrar.',
icon: 'warning',
showCancelButton: true,
confirmButtonColor: '#d33',
cancelButtonColor: '#6c757d',
confirmButtonText: 'Sí, salir',
cancelButtonText: 'Cancelar'
}).then(function(result) {
if (result.isConfirmed) {
form.submit();
}
});
});

@stack('JSOR')
});
</script>
@stack('JS')
</body>
</html>