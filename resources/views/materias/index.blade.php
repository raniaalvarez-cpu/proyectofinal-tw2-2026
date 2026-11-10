@extends('layouts.basedashboard')

@section('titulo', 'Gestión de Materias')

@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-4">
<h1 class="h3 mb-0"><i class="bi bi-journal-bookmark-fill"></i> Gestión de Materias</h1>
<a href="{{ route('materias.create') }}" class="btn btn-primary">
<i class="bi bi-plus-lg"></i> Nueva Materia
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
<table id="tablaMaterias" class="table table-striped table-hover align-middle" style="width:100%">
<thead class="table-dark">
<tr>
<th>#</th>
<th>Nombre</th>
<th class="text-center">Estudiantes</th>
<th class="text-center" width="180">Acciones</th>
</tr>
</thead>
<tbody>
@foreach($materias as $materia)
<tr>
<td>{{ $materia->id }}</td>
<td>{{ $materia->nombre }}</td>
<td class="text-center">
<span class="badge text-bg-info">{{ $materia->users_count }}</span>
</td>
<td class="text-center">
<button class="btn btn-sm btn-warning" onclick="editarMateria({{ $materia->id }})">
<i class="bi bi-pencil-square"></i> Editar
</button>
<button class="btn btn-sm btn-danger" onclick="eliminarMateria({{ $materia->id }})" {{ $materia->users_count > 0 ? 'disabled title="No se puede eliminar: tiene estudiantes asignados"' : '' }}>
<i class="bi bi-trash"></i> Eliminar
</button>
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
function editarMateria(id) {
window.location.href = '{{ url("materias") }}/' + id + '/edit';
}

function eliminarMateria(id) {
Swal.fire({
title: '¿Eliminar materia?',
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
form.action = '{{ url("materias") }}/' + id;

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
$('#tablaMaterias').DataTable({
language: {
url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
},
responsive: true,
autoWidth: false,
order: [[0, 'asc']]
});
@endpush