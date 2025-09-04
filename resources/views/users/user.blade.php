@extends('adminlte::page')

@section('title', 'users')

@section('content_header')
    <h1>users</h1>
@stop

@section('content')
  <x-adminlte-button label="Nuevo Rol" data-toggle="modal" data-target="#modalPurple" class="bg-primary"/>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                  
                    <th>user</th>
                   
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                       
                        <td>{{ $user->name }}</td>
                       
                        <td>
                           <button 
                             class="btn btn-sm btn-warning btn-editar"
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-toggle="modal"
                                data-target="#modalEditar">

                               Editar
                            </button>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta user?')">Eliminar</button>
                            </form>

                            <button class="btn btn-sm btn-info">
                                <a href="{{ route('users.edit', $user->id) }}" class="text-white">Asignar roles</a>
                            </button>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    {{-- Themed --}}
<x-adminlte-modal id="modalPurple" title="Theme Purple" theme="purple"
    icon="fas fa-bolt" size='lg' disable-animations>
   <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Correo electrónico</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
            <label>Confirmar Contraseña</label>
            <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Asignar Roles</label>
                @foreach($roles as $role)
                    <div class="form-check">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="form-check-input" id="rol_{{ $role->id }}">
                        <label class="form-check-label" for="rol_{{ $role->id }}">{{ $role->name }}</label>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">Crear Usuario</button>
        </form>
    </div>

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
            $('#users-table').DataTable({
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
        $('#form-editar').attr('action', '/users/' + id);
    });
    </script>

@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@stop



