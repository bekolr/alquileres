@extends('adminlte::page')
@section('title','Nuevo Contrato')

@section('content_header')
<h1>Nuevo contrato</h1>
@stop

@section('content')
@if($errors->any()) <x-adminlte-alert theme="danger" title="Errores"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></x-adminlte-alert> @endif

<form method="POST" action="{{ route('contratos.store') }}">
@csrf
<x-adminlte-card title="Datos del contrato" icon="fas fa-file-contract" theme="primary">
<div class="row">
    <div class="col-md-6">
        <x-adminlte-select name="inquilino_id" label="Inquilino" required>
            <x-adminlte-options :options="$inquilinos->pluck('nombre','id')->toArray()" :selected="old('inquilino_id')" empty-option="Seleccione..."/>
        </x-adminlte-select>
    </div>
    <div class="col-md-6">
        <x-adminlte-select name="departamento_id" label="Departamento" required>
            <x-adminlte-options :options="$departamentos->pluck('codigo','id')->toArray()" :selected="old('departamento_id')" empty-option="Seleccione..."/>
        </x-adminlte-select>
    </div>
    <div class="col-md-3"><x-adminlte-input name="fecha_inicio" label="Fecha inicio" type="date" value="{{ old('fecha_inicio') }}" required/></div>
    <div class="col-md-3"><x-adminlte-input name="fecha_fin" label="Fecha fin" type="date" value="{{ old('fecha_fin') }}" required/></div>
    <div class="col-md-3"><x-adminlte-input name="dia_vencimiento" label="Día venc." type="number" min="1" max="28" value="{{ old('dia_vencimiento',10) }}" required/></div>
    <div class="col-md-3"><x-adminlte-input name="tasa_interes_diaria" label="Interés diario (ej 0.003)" type="number" step="0.0001" value="{{ old('tasa_interes_diaria',0.003) }}"/></div>

    <div class="col-md-3"><x-adminlte-input name="monto_alquiler" label="Monto alquiler" type="number" step="0.01" value="{{ old('monto_alquiler') }}" required/></div>
    <div class="col-md-3"><x-adminlte-input name="expensas_mensuales" label="Expensas" type="number" step="0.01" value="{{ old('expensas_mensuales',0) }}"/></div>

    <div class="col-md-3"><x-adminlte-input name="incremento_cada_meses" label="Incremento cada (meses)" type="number" min="1" value="{{ old('incremento_cada_meses') }}"/></div>
    <div class="col-md-3"><x-adminlte-input name="porcentaje_incremento" label="% incremento" type="number" step="0.01" value="{{ old('porcentaje_incremento') }}"/></div>
</div>
</x-adminlte-card>

<x-adminlte-button type="submit" theme="success" icon="fas fa-save" label="Guardar y generar cuotas"/>
<a href="{{ route('contratos.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@stop
