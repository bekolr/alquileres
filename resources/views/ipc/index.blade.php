@extends('adminlte::page')
@section('title', 'IPC - Listado')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Índice IPC</h1>
        <a href="{{ route('ipc.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo
        </a>
    </div>
@stop

@section('content')
    @if(session('ok'))
        <x-adminlte-alert theme="success" title="OK">{{ session('ok') }}</x-adminlte-alert>
    @endif

    <x-adminlte-card title="Registros IPC" icon="fas fa-chart-line" theme="info">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Año</th>
                        <th>Mes</th>
                        <th>Factor (ej 1.025)</th>
                        <th>% Mensual aprox.</th>
                        <th style="width:160px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($indices as $row)
                    @php
                        $pct = ($row->valor - 1) * 100;
                    @endphp
                    <tr>
                        <td>{{ $row->anio }}</td>
                        <td>{{ $row->mes }}</td>
                        <td>{{ number_format($row->valor, 6) }}</td>
                        <td>{{ number_format($pct, 2) }} %</td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('ipc.edit', $row) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('ipc.destroy', $row) }}" method="POST" onsubmit="return confirm('¿Eliminar registro?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">Sin datos. Cargá el primer índice.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>


    </x-adminlte-card>
@stop
