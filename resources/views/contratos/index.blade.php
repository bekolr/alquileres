@extends('adminlte::page')
@section('title','Contratos')

@section('content_header')
<h1>Contratos</h1>
@stop

@section('content')
<x-adminlte-button class="mb-3" theme="primary" icon="fas fa-plus" label="Nuevo contrato" onclick="window.location='{{ route('contratos.create') }}'"/>

@if(session('ok')) <x-adminlte-alert theme="success" title="OK">{{ session('ok') }}</x-adminlte-alert> @endif

<table class="table table-striped table-sm">
    <thead>
        <tr>
            <th>#</th><th>Inquilino</th><th>Depto</th><th>Inicio</th><th>Fin</th>
            <th>Alquiler</th><th>Expensas</th><th>Estado</th><th></th>
        </tr>
    </thead>
    <tbody>
        @forelse($contratos as $c)
        <tr>
            <td>{{ $c->id }}</td>
            <td>{{ $c->inquilino->nombre }}</td>
            <td>{{ $c->departamento->codigo }}</td>
            <td>{{ $c->fecha_inicio->format('d/m/Y') }}</td>
            <td>{{ $c->fecha_fin->format('d/m/Y') }}</td>
            <td>${{ number_format($c->monto_alquiler,2,',','.') }}</td>
            <td>${{ number_format($c->expensas_mensuales,2,',','.') }}</td>
            <td><span class="badge badge-{{ $c->estado==='activo'?'success':($c->estado==='finalizado'?'secondary':'warning') }}">{{ $c->estado }}</span></td>
            <td>
                <a class="btn btn-xs btn-primary" href="{{ route('contratos.show',$c) }}"><i class="fas fa-eye"></i></a>
                <form action="{{ route('contratos.destroy',$c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar contrato? Se eliminarán sus cuotas y pagos.')">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center text-muted">Sin registros</td></tr>
        @endforelse
    </tbody>
</table>
{{ $contratos->links() }}
@stop
