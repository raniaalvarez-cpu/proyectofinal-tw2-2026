@extends('layouts.basedashboard')

@section('titulo', 'Gestión de Usuarios')

@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-4">
<h1 class="h3 mb-0"><i class="bi bi-people-fill"></i> Gestión de Usuarios</h1>
<a href="{{ route('usuarios.create') }}" class="btn btn-primary">
<i class="bi bi-plus-lg"></i> Nuevo Usuario
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

<div class="card shadow-sm">
<div class="card-body">
<table id="tablaUsuarios" class="table table-striped table-hover align-middle" style="width:100%">
<thead class="table-dark">
<tr>
<th>#</th>
<th>Usuario</th>
<th>Email</th>
<th>Tipo</th>
<th class="text-center" width="180">Acciones</th>
</tr>
</thead>
<tbody>
@foreach($usuarios as $usuario)
<tr>
<td>{{ $usuario->id }}</td>
<td>{{ $usuario->username }}</td>
<td>{{ $usuario->email }}</td>
<td>
@switch($usuario->tipo->tipo)
@case('admin')
<span class="badge text-bg-danger">{{ $usuario->tipo->tipo }}</span>
@break
@case('profesor')
<span class="badge text-bg-primary">{{ $usuario->tipo->tipo }}</span>
@break
@case('estudiante')
<span class="badge text-bg-success">{{ $usuario->tipo->tipo }}</span>
@break
@default
<span class="badge text-bg-secondary">{{ $usuario->tipo->tipo }}</span>
@endswitch
</td>
<td class="text-center">
<button class="btn btn-sm btn-warning" onclick="editarUsuario({{ $usuario->id }})">
<i class="bi bi-pencil-square"></i> Editar
</button>
<button class="btn btn-sm btn-danger" onclick="eliminarUsuario({{ $usuario->id }})">
<i class="bi bi-trash"></i> Eliminar
</button>
@if($usuario->tipo->tipo === 'estudiante')
<button class="btn btn-sm btn-info" onclick="gestionarMaterias({{ $usuario->id }})">
<i class="bi bi-journal-bookmark"></i> Materias
</button>
@endif
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
@endsection

@push('JS')
<script>
function editarUsuario(id) {
window.location.href = '{{ url("usuarios") }}/' + id + '/edit';
}

function eliminarUsuario(id) {
Swal.fire({
title: '¿Eliminar usuario?',
text: 'Esta acción no se puede deshacer.',
icon: 'warning',
showCancelButton: true,
confirmButtonColor: '#d33',
cancelButtonColor: '#6c757d',
confirmButtonText: 'Sí, eliminar',
cancelButtonText: 'Cancelar'
}).then(function(result) {
if (result.isConfirmed) {
// Crear formulario POST con _method=DELETE
const form = document.createElement('form');
form.method = 'POST';
form.action = '{{ url("usuarios") }}/' + id;

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

function gestionarMaterias(id) {
Swal.fire({
icon: 'info',
title: 'Gestión de Materias',
text: 'Esta funcionalidad se implementará en la Clase 5.',
confirmButtonText: 'Entendido'
});
}
</script>
@endpush

@push('JSOR')
$('#tablaUsuarios').DataTable({
language: {
url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
},
responsive: true,
autoWidth: false,
order: [[0, 'asc']]
});
@endpush