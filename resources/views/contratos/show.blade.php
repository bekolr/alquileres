@extends('adminlte::page')
@section('title','Contrato '.$contrato->id)

@section('content_header')
<h1>Contrato #{{ $contrato->id }}</h1>
@stop

@section('content')
@if(session('ok'))
    <x-adminlte-alert theme="success" title="OK">{{ session('ok') }}</x-adminlte-alert>
@endif

@php
    $money = fn($n) => '$'.number_format((float)$n, 2, ',', '.');
@endphp

<x-adminlte-card title="Resumen" icon="fas fa-file-contract" theme="primary">
    <div class="row">
        <div class="col-md-4"><b>Inquilino:</b> {{ optional($contrato->inquilino)->nombre }}</div>
        <div class="col-md-3"><b>Departamento:</b> {{ optional($contrato->departamento)->codigo }}</div>
        <div class="col-md-5"><b>Período:</b> {{ $contrato->fecha_inicio->format('d/m/Y') }} - {{ $contrato->fecha_fin->format('d/m/Y') }}</div>

        <div class="col-md-3 mt-2"><b>Vencimiento mensual:</b> Día {{ $contrato->dia_vencimiento }}</div>
        <div class="col-md-3 mt-2"><b>Alquiler inicial:</b> {{ $money($contrato->monto_alquiler) }}</div>
        <div class="col-md-3 mt-2"><b>Expensas (vigentes):</b> {{ $money($contrato->departamento->edificio->expensas ?? 0) }}</div>
        <div class="col-md-3 mt-2"><b>Interés diario:</b> {{ $contrato->tasa_interes_diaria }}%</div>
        <div class="col-md-6 mt-2"><b>Tipo ajuste inicial:</b> {{ $contrato->tipo_aumento ?? 'Sin ajuste' }}</div>
        <div class="col-md-6 mt-2"><b>Valor ajuste inicial:</b> {{ $contrato->tipo_aumento === 'PORCENTAJE' ? ($contrato->porcentaje * 100) . '%' : ($contrato->tipo_aumento === 'IPC' ? 'IPC' : 'N/A') }}</div>    
        
    </div>
</x-adminlte-card>

{{-- Totales del contrato --}}
<x-adminlte-card title="Totales del contrato (generado a la fecha)" icon="fas fa-sigma" theme="info">
    <div class="row text-center">
        <div class="col-md-2"><b>Alquiler</b><div>{{ $money($totales['alquiler']) }}</div></div>
        <div class="col-md-2"><b>Expensas</b><div>{{ $money($totales['expensas']) }}</div></div>
        <div class="col-md-2"><b>Comisión</b><div>{{ $money($totales['comision']) }}</div></div>
        <div class="col-md-2"><b>Depósito</b><div>{{ $money($totales['deposito']) }}</div></div>
        <div class="col-md-2"><b>Total Cuotas</b><div>{{ $money($totales['total']) }}</div></div>
        <div class="col-md-2"><b>Pagado</b><div>{{ $money($totales['pagado']) }}</div></div>
    </div>
</x-adminlte-card>

<x-adminlte-card title="Cuotas" icon="fas fa-file-invoice-dollar" theme="light">
    <div class="table-responsive">
        <table class="table table-sm table-striped">
            <thead>
                <tr>
                    <th>Periodo</th>
                    <th>Vencim.</th>
                    <th class="text-right">Alquiler</th>
                    <th class="text-right">Expensas</th>
                    <th class="text-right">Comisión</th>
                    <th class="text-right">Depósito</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Interés a hoy</th>
                    <th class="text-right">Pagado</th>
                  
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($contrato->cuotas as $c)
                @php
                    // Compatibilidad: si no hay columnas nuevas, usamos importes "legacy"
                    $alq   = $c->monto_alquiler   ?? (float)($c->monto_base - $c->expensas);
                    $exp   = $c->monto_expensas   ?? (float)($c->expensas);
                    $com   = $c->monto_comision   ?? 0;
                    $dep   = $c->monto_deposito   ?? 0;
                    $tot   = $c->monto_total      ?? (float)($alq + $exp + $com + $dep);

                    $interes = method_exists($c,'calcularInteresHasta') ? (float)$c->calcularInteresHasta() : 0;
                    $pagado  = (float)($c->total_pagado ?? 0);
                    $saldo   = (float)($c->saldo_con_interes ?? max($tot + $interes - $pagado, 0));

                    $estado = $c->estado ?? ($saldo <= 0 ? 'pagada' : ( $pagado > 0 ? 'parcial' : (now()->gt($c->vencimiento) ? 'vencida' : 'pendiente')));
                    $badge  = match($estado){
                        'pagada'   => 'success',
                        'parcial'  => 'warning',
                        'vencida'  => 'danger',
                        default    => 'secondary'
                    };
                @endphp
                <tr>
                    <td>{{ $c->periodo->format('m/Y') }}</td>
                    <td>{{ $c->vencimiento->format('d/m/Y') }}</td>
                    <td class="text-right">{{ $money($alq) }}</td>
                    <td class="text-right">{{ $money($exp) }}</td>
                    <td class="text-right">
                        {{ $money($com) }}
                        @if($com > 0)
                            <span class="badge badge-info">aplica</span>
                        @endif
                    </td>
                    <td class="text-right">
                        {{ $money($dep) }}
                        @if($dep > 0)
                            <span class="badge badge-info">aplica</span>
                        @endif
                    </td>
                    <td class="text-right"><b>{{ $money($tot) }}</b></td>
                    <td class="text-right">{{ $money($interes) }}</td>
                    <td class="text-right">{{ $money($pagado) }}</td>
                    
                    <td><span class="badge badge-{{ $badge }}">{{ strtoupper($estado) }}</span></td>
                    <td>
                        <a class="btn btn-xs btn-primary" href="{{ route('cuotas.show',$c) }}">
                            <i class="fas fa-eye"></i> Detalle
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="12" class="text-center text-muted">Sin cuotas generadas.</td></tr>
            @endforelse
            </tbody>
            @if($contrato->cuotas->count())
            <tfoot>
                <tr class="font-weight-bold">
                    <td colspan="2" class="text-right">Totales:</td>
                    <td class="text-right">{{ $money($totales['alquiler']) }}</td>
                    <td class="text-right">{{ $money($totales['expensas']) }}</td>
                    <td class="text-right">{{ $money($totales['comision']) }}</td>
                    <td class="text-right">{{ $money($totales['deposito']) }}</td>
                    <td class="text-right">{{ $money($totales['total']) }}</td>
                    <td></td>
                    <td class="text-right">{{ $money($totales['pagado']) }}</td>
                    
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</x-adminlte-card>

<x-adminlte-card title="Actualizar cuotas y expensas" icon="fas fa-tools" theme="warning">
    <form method="POST" action="{{ route('contratos.expensas', $contrato) }}">
        @csrf
        <div class="row">
            <div class="col-md-3">
                <x-adminlte-input name="nuevas_expensas" label="Nuevo valor de expensa ($)" type="number" step="0.01" required/>
            </div>
            <div class="col-md-3">
                <x-adminlte-input name="incremento_en_meses" label="Incremento en meses" placeholder="4" required/>
            </div>
            <div class="col-md-3">
                <x-adminlte-select name="modo" label="Tipo de ajuste" required>
                    <option value="">-- Seleccionar --</option>
                    <option value="IPC">IPC</option>
                    <option value="PORCENTAJE">% fijo</option>
                </x-adminlte-select>
            </div>
            <div class="col-md-3">
                <x-adminlte-input name="valor_ajuste" label="Valor ajuste (ej 0.07 para 7%)" type="number" step="0.0001" required/>
            </div>
            <div class="col-md-12 mt-2">
                <small class="text-muted">
                    Las cuotas <b>pagadas</b> no se modifican. Las cuotas <b>parciales</b> pueden cambiar su saldo e interés.
                </small>
            </div>
        </div>
        <div class="mt-3">
            <x-adminlte-button type="submit" theme="warning" icon="fas fa-save" label="Aplicar"/>
            <a href="{{ route('contratos.index') }}" class="btn btn-secondary ml-2">Volver</a>
        </div>
    </form>
</x-adminlte-card>
@stop
