@extends('adminlte::page')
@section('title','Departamentos')

@section('content_header')
<h1>Departamentos</h1>
@stop

@section('content')
<x-adminlte-button class="mb-3" theme="primary" icon="fas fa-plus" label="Nuevo departamento" onclick="window.location='{{ route('departamentos.create') }}'"/>

@if(session('ok')) <x-adminlte-alert theme="success" title="OK">{{ session('ok') }}</x-adminlte-alert> @endif

<table class="table table-striped table-sm">
    <thead><tr><th>#</th><th>Código</th><th>Piso</th><th>Descripción</th><th>Acciones</th></tr></thead>
    <tbody>
        @forelse($departamentos as $d)
        <tr>
            <td>{{ $d->id }}</td>
            <td>{{ $d->codigo }}</td>
            <td>{{ $d->piso }}</td>
            <td>{{ $d->descripcion }}</td>
            <td>
                <a class="btn btn-xs btn-warning" href="{{ route('departamentos.edit',$d) }}"><i class="fas fa-edit"></i></a>
                <form action="{{ route('departamentos.destroy',$d) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar departamento?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted">Sin registros</td></tr>
        @endforelse
    </tbody>
</table>

@stop
