@extends('adminlte::page')
@section('title','Cuota '.$cuota->periodo->format('m/Y'))

@section('content_header')
<h1>Cuota {{ $cuota->periodo->format('m/Y') }} — Contrato #{{ $cuota->contrato_id }}</h1>
@stop

@section('content')
@php
    $interes = $cuota->calcularInteresHasta();
    $total = $cuota->importe_base_total + $interes;
@endphp

<x-adminlte-card title="Resumen" icon="fas fa-file-invoice">
    <ul>
        <li><b>Vencimiento:</b> {{ $cuota->vencimiento->format('d/m/Y') }}</li>
        <li><b>Base+Expensas:</b> ${{ number_format($cuota->importe_base_total,2,',','.') }}</li>
         <li><b>Alquiler:</b> ${{ number_format($cuota->monto_alquiler,2,',','.') }}</li>
          <li><b>Expensa:</b> ${{ number_format($cuota->monto_expensas,2,',','.') }}</li>
           <li><b>Comision:</b> ${{ number_format($cuota->monto_comision,2,',','.') }}</li>
     <li><b>Comision:</b> ${{ number_format($cuota->monto_deposito,2,',','.') }}</li>
        <li><b>Interés a hoy:</b> ${{ number_format($interes,2,',','.') }}</li>
        <li><b>Total:</b> ${{ number_format($cuota->monto_total,2,',','.') }}</li>
        <li><b>Pagado:</b> ${{ number_format($cuota->total_pagado,2,',','.') }}</li>
        <li><b>Estado:</b> {{ strtoupper($cuota->estado) }}</li>
    </ul>
</x-adminlte-card>

<x-adminlte-card title="Registrar pago" theme="success" icon="fas fa-cash-register">
    <form method="POST" action="{{ route('cuotas.pagar',$cuota) }}">
        @csrf
        <div class="row">
            <div class="col-md-3">
                <x-adminlte-input name="fecha_pago" label="Fecha de pago" type="date" :value="now()->toDateString()" required/>
            </div>
            <div class="col-md-3">
                <x-adminlte-input name="importe" label="Importe" type="number" step="0.01" min="0.01" required/>
            </div>
            <div class="col-md-3">
                <x-adminlte-input name="medio" label="Medio (opcional)"/>
            </div>
            <div class="col-md-12">
                <x-adminlte-textarea name="nota" label="Nota" rows=2/>
            </div>
        </div>
        <x-adminlte-button class="mt-2" type="submit" theme="success" label="Guardar pago" icon="fas fa-check"/>
    </form>
</x-adminlte-card>

@if($cuota->pagos->count())
<x-adminlte-card title="Pagos" icon="fas fa-list" theme="light">
    <table class="table table-sm">
        <thead><tr><th>Fecha</th><th>Importe</th><th>Medio</th><th>Nota</th></tr></thead>
        <tbody>
            @foreach($cuota->pagos as $p)
            <tr>
                <td>{{ $p->fecha_pago->format('d/m/Y') }}</td>
                <td>${{ number_format($p->importe,2,',','.') }}</td>
                <td>{{ $p->medio }}</td>
                <td>{{ $p->nota }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-adminlte-card>
@endif
@stop
