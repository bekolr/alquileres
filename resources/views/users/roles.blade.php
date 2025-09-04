@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <h1>Roles</h1>
@stop

@section('content')
  <x-adminlte-button label="Nuevo Rol" data-toggle="modal" data-target="#modalPurple" class="bg-primary"/>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="roles-table">
            <thead>
                <tr>
                    <th>ID</th>
                  
                    <th>role</th>
                   
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                       
                        <td>{{ $role->name }}</td>
                       
                        <td>
                           <button 
                             class="btn btn-sm btn-warning btn-editar"
                                data-id="{{ $role->id }}"
                                data-name="{{ $role->name }}"
                                data-toggle="modal"
                                data-target="#modalEditar">

                               Editar
                            </button>
                            <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta role?')">Eliminar</button>
                            </form>

                            <button class="btn btn-sm btn-info">
                                <a href="{{ route('roles.edit', $role->id) }}" class="text-white">Ver Permisos</a>
                            </button>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Themed --}}
<x-adminlte-modal id="modalPurple" title="Theme Purple" theme="purple"
    icon="fas fa-bolt" size='lg' disable-animations>
   <form   action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="role">Nombre Rol</label>
            <input type="text" class="form-control" id="role" name="role" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>

</x-adminlte-modal>
{{-- Example button to open modal --}}
<x-adminlte-button label="Open Modal" data-toggle="modal" data-target="#modalPurple" class="bg-purple"/>
<x-adminlte-modal id="modalEditar" title="Editar Rol" theme="warning" icon="fas fa-edit" size='md' disable-animations>
    <form id="form-editar" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="edit-name">Nombre del Rol</label>
            <input type="text" class="form-control" id="edit-name" name="name" required>
        </div>
        <button type="submit" class="btn btn-warning">Actualizar</button>
    </form>
</x-adminlte-modal>
@stop



@section('js')
    {{-- Incluye jQuery y DataTables si no lo has hecho aún --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#roles-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                }
            });
        });

   

           $(document).on('click', '.btn-editar', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');

        $('#edit-name').val(name);

        // Cambia la acción del formulario dinámicamente
        $('#form-editar').attr('action', '/roles/' + id);
    });
    </script>

@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@stop



