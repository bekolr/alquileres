@extends('adminlte::page')

@section('title', 'Roles Permisos')

@section('content_header')
    <h1>Roles Permisos</h1>
@stop

@section('content')
 <div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $role->name }}</h3>
    </div>
        <div class="card-body">
<form action="{{ route('roles.permisos.update', $role->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label>Permisos disponibles:</label>
        <div class="row">
            @foreach($permissions as $permission)
                <div class="col-md-3">
                    <div class="form-check">
                        <input 
                            type="checkbox" 
                            name="permissions[]" 
                            value="{{ $permission->name }}" 
                            class="form-check-input" 
                            id="permiso_{{ $permission->id }}"
                            {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="permiso_{{ $permission->id }}">
                            {{ $permission->name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <button type="submit" class="btn btn-success mt-3">Guardar cambios</button>
</form>
            
        </div>
            
    </div>
 </div>
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

   

 
    </script>

@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@stop



