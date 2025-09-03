@extends('adminlte::page')
@section('title','Inquilino')

@section('content_header')
<h1>Inquilino #{{ $inquilino->id }}</h1>
@stop

@section('content')
<x-adminlte-card title="Datos" icon="fas fa-user">
    <p><b>DNI:</b> {{ $inquilino->dni }}</p>
    <p><b>Nombre:</b> {{ $inquilino->nombre }}</p>
    <p><b>Email:</b> {{ $inquilino->email }}</p>
    <p><b>Teléfono:</b> {{ $inquilino->telefono }}</p>
</x-adminlte-card>

<x-adminlte-card title="Contratos" icon="fas fa-file-contract" theme="light">
    <table class="table table-sm">
        <thead><tr><th>ID</th><th>Dpto</th><th>Inicio</th><th>Fin</th><th>Estado</th><th></th></tr></thead>
        <tbody>
            @forelse($inquilino->contratos as $c)
            <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $c->departamento->codigo ?? '-' }}</td>
                <td>{{ $c->fecha_inicio->format('d/m/Y') }}</td>
                <td>{{ $c->fecha_fin->format('d/m/Y') }}</td>
                <td>{{ $c->estado }}</td>
                <td><a class="btn btn-xs btn-primary" href="{{ route('contratos.show',$c) }}"><i class="fas fa-eye"></i></a></td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-muted">Sin contratos</td></tr>
            @endforelse
        </tbody>
    </table>
</x-adminlte-card>
<a href="{{ route('inquilinos.index') }}" class="btn btn-secondary">Volver</a>
@stop
