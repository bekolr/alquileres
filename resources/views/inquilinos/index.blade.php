@extends('adminlte::page')
@section('title','Inquilinos')

@section('content_header')
    <h1>Inquilinos</h1>
@stop

@section('content')
<x-adminlte-button class="mb-3" theme="primary" icon="fas fa-plus" label="Nuevo inquilino" title="Crear" onclick="window.location='{{ route('inquilinos.create') }}'"/>

@if(session('ok')) <x-adminlte-alert theme="success" title="OK">{{ session('ok') }}</x-adminlte-alert> @endif

<table class="table table-striped table-sm">
    <thead>
        <tr>
            <th>#</th><th>DNI</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($inquilinos as $i)
        <tr>
            <td>{{ $i->id }}</td>
            <td>{{ $i->dni }}</td>
            <td>{{ $i->nombre }}</td>
            <td>{{ $i->email }}</td>
            <td>{{ $i->telefono }}</td>
            <td>
                <a class="btn btn-xs btn-info" href="{{ route('inquilinos.show',$i) }}"><i class="fas fa-eye"></i></a>
                <a class="btn btn-xs btn-warning" href="{{ route('inquilinos.edit',$i) }}"><i class="fas fa-edit"></i></a>
                <form action="{{ route('inquilinos.destroy',$i) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar inquilino?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted">Sin registros</td></tr>
        @endforelse
    </tbody>
</table>
{{ $inquilinos->links() ?? '' }}
@stop
