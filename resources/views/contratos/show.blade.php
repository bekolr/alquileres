@extends('adminlte::page')
@section('title','Contrato '.$contrato->id)

@section('content_header')
<h1>Contrato #{{ $contrato->id }}</h1>
@stop

@section('content')
@if(session('ok')) <x-adminlte-alert theme="success" title="OK">{{ session('ok') }}</x-adminlte-alert> @endif

<x-adminlte-card title="Resumen" icon="fas fa-file-contract" theme="primary">
    <div class="row">
        <div class="col-md-4"><b>Inquilino:</b> {{ $contrato->inquilino->nombre }}</div>
        <div class="col-md-3"><b>Departamento:</b> {{ $contrato->departamento->codigo }}</div>
        <div class="col-md-5"><b>Período:</b> {{ $contrato->fecha_inicio->format('d/m/Y') }} - {{ $contrato->fecha_fin->format('d/m/Y') }}</div>

        <div class="col-md-3 mt-2"><b>Vencimiento mensual:</b> Día {{ $contrato->dia_vencimiento }}</div>
        <div class="col-md-3 mt-2"><b>Alquiler:</b> ${{ number_format($contrato->monto_alquiler,2,',','.') }}</div>
        <div class="col-md-3 mt-2"><b>Expensas:</b> ${{ number_format($contrato->expensas_mensuales,2,',','.') }}</div>
        <div class="col-md-3 mt-2"><b>Interés diario:</b> {{ $contrato->tasa_interes_diaria }}</div>
    </div>
</x-adminlte-card>

<x-adminlte-card title="Cuotas" icon="fas fa-file-invoice-dollar" theme="light">
    <table class="table table-sm">
        <thead><tr>
            <th>Periodo</th><th>Vencimiento</th><th>Base+Exp</th><th>Interés a hoy</th><th>Pagado</th><th>Saldo</th><th>Estado</th><th></th>
        </tr></thead>
        <tbody>
        @foreach($contrato->cuotas as $c)
            @php
                $base = $c->importe_base_total;
                $interes = $c->calcularInteresHasta();
            @endphp
            <tr>
                <td>{{ $c->periodo->format('m/Y') }}</td>
                <td>{{ $c->vencimiento->format('d/m/Y') }}</td>
                <td>${{ number_format($base,2,',','.') }}</td>
                <td>${{ number_format($interes,2,',','.') }}</td>
                <td>${{ number_format($c->total_pagado,2,',','.') }}</td>
                <td><b>${{ number_format($c->saldo_con_interes,2,',','.') }}</b></td>
                <td>
                    <span class="badge badge-{{ $c->estado==='pagada'?'success':($c->estado==='parcial'?'warning':'danger') }}">{{ strtoupper($c->estado) }}</span>
                </td>
                <td>
                    <a class="btn btn-xs btn-primary" href="{{ route('cuotas.show',$c) }}"><i class="fas fa-eye"></i> Detalle</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</x-adminlte-card>

<x-adminlte-card title="Actualizar expensas" icon="fas fa-tools" theme="warning">
    <form method="POST" action="{{ route('contratos.expensas', $contrato) }}">
        @csrf
        <div class="row">
            <div class="col-md-3">
                <x-adminlte-input name="nuevas_expensas" label="Nuevo valor ($)" type="number" step="0.01" required/>
            </div>
            <div class="col-md-3">
                <x-adminlte-input name="aplicar_desde" label="Aplicar desde (YYYY-MM)" placeholder="2025-10" required/>
            </div>
            <div class="col-md-4">
                <x-adminlte-select name="modo" label="Afectar cuotas">
                    <option value="solo_pendientes">Solo pendientes</option>
                    <option value="pendientes_y_parciales">Pendientes y parciales</option>
                </x-adminlte-select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <x-adminlte-button type="submit" theme="warning" icon="fas fa-save" label="Aplicar"/>
            </div>
        </div>
    </form>
    <small class="text-muted">
        Nota: las cuotas <b>pagadas</b> no se modifican. Las cuotas parciales pueden cambiar su saldo e interés.
    </small>
</x-adminlte-card>

<a href="{{ route('contratos.index') }}" class="btn btn-secondary">Volver</a>
@stop
