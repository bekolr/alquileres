@extends('adminlte::page')
@section('title','Mis cuotas')

@section('content_header')
<h1>Hola, {{ $inquilino->nombre }}</h1>
@stop

@section('content')
@if(!$contrato)
    <x-adminlte-callout theme="warning" title="Sin contrato activo">
        No encontramos un contrato activo a tu nombre.
    </x-adminlte-callout>
@else
    <x-adminlte-card title="Contrato {{ $contrato->departamento->codigo }}" theme="primary" icon="fas fa-file-contract">
        <p><b>Período:</b> {{ $contrato->fecha_inicio->format('d/m/Y') }} a {{ $contrato->fecha_fin->format('d/m/Y') }}</p>
        <p><b>Alquiler:</b> ${{ number_format($contrato->monto_alquiler,2,',','.') }}
           — <b>Expensas:</b> ${{ number_format($contrato->expensas_mensuales,2,',','.') }}</p>
    </x-adminlte-card>

    <x-adminlte-datatable id="tablaCuotas" :heads="['Periodo','Vencimiento','Base+Exp','Interés a hoy','Pagado','Saldo','Estado','Acciones']" theme="light">
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
                    <span class="badge badge-{{ $c->estado==='pagada'?'success':($c->estado==='parcial'?'warning':'danger') }}">
                        {{ strtoupper($c->estado) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('cuotas.show',$c) }}" class="btn btn-xs btn-primary">
                        <i class="fas fa-eye"></i> Detalle
                    </a>
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>
@endif
@stop
