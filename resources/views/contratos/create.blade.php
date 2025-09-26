@extends('adminlte::page')
@section('title','Nuevo Contrato')

@section('content_header')
<h1>Nuevo contrato</h1>
@stop

@section('content')
@if($errors->any())
    <x-adminlte-alert theme="danger" title="Errores">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </x-adminlte-alert>
@endif

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
                <x-adminlte-options
                    :options="$departamentos->mapWithKeys(fn($d) => [
                        $d->id => ($d->edificio->nombre ?? 'Edificio') . ' - ' . ($d->codigo ?? $d->nombre)
                    ])->toArray()"
                    :selected="old('departamento_id')"
                    empty-option="Seleccione..."
                />
            </x-adminlte-select>
            
            <small class="text-muted">Las expensas se toman del edificio del departamento.</small>
        </div>

        <div class="col-md-3">
            <x-adminlte-input name="fecha_inicio" label="Fecha inicio" type="date" value="{{ old('fecha_inicio') }}" required/>
        </div>
        <div class="col-md-3">
            <x-adminlte-input name="fecha_fin" label="Fecha fin" type="date" value="{{ old('fecha_fin') }}" required/>
        </div>
        
        <div class="col-md-3">
            <x-adminlte-input name="dia_vencimiento" label="Día venc." type="number" min="1" max="28" value="{{ old('dia_vencimiento',10) }}" required/>
            <small class="text-muted">Se sugiere ≤ 28 para evitar meses cortos.</small>
        </div>
        <div class="col-md-3">
            <x-adminlte-input name="tasa_interes_diaria" label="Interés diario (ej 0.003)" type="number" step="0.0001" value="{{ old('tasa_interes_diaria',0.003) }}"/>
        </div>

        <div class="col-md-4">
            <x-adminlte-input name="monto_alquiler" label="Monto alquiler inicial" type="number" step="0.01" value="{{ old('monto_alquiler') }}" required/>
        </div>

        {{-- Bloque de ajuste inicial (opcional) --}}
        <div class="col-md-4">
            <x-adminlte-select name="tipo_aumento" label="Tipo de ajuste (bloque inicial)">
                <x-adminlte-options :options="$tiposAumento->pluck('nombre','nombre')->toArray()" :selected="old('tipo_aumento')" empty-option="Sin ajuste"/>   
            </x-adminlte-select>
            <small class="text-muted">Definí cómo se ajustan las cuotas del primer bloque.</small>
        </div>
        <div class="col-md-4">
            <x-adminlte-input name="incremento_cada_meses" label="Duración del bloque (meses)" type="number" min="1" value="{{ old('incremento_cada_meses') }}"/>
            <small class="text-muted">Cuántos meses abarca el primer bloque (ej: 3).</small>
        </div>

        <div class="col-md-4" id="wrap-porcentaje" style="display:none;">
            <x-adminlte-input name="porcentaje_incremento" id="porcentaje_incremento" label="% incremento (0.12 = 12%)" type="number" step="0.0001" value="{{ old('porcentaje_incremento') }}"/>
        </div>
    </div>
</x-adminlte-card>
{{-- ====== EXTRAS: Comisión y Depósito ====== --}}
<div class="col-12">
    <x-adminlte-card title="Extras: Comisión y Depósito" icon="fas fa-plus-circle" theme="lightblue" body-class="pt-2">
        <div class="row">

            {{-- Comisión --}}
            <div class="col-md-3">
                <x-adminlte-select name="tiene_comision" label="¿Cobrar comisión?" id="tiene_comision">
                    <option value="0" {{ old('tiene_comision',0)==0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('tiene_comision',0)==1 ? 'selected' : '' }}>Sí</option>
                </x-adminlte-select>
            </div>
            <div class="col-md-3 comision-fields" style="display:none;">
                <x-adminlte-input name="comision" label="Monto comisión" type="number" step="0.01" value="{{ old('comision') }}"/>
            </div>
            <div class="col-md-3 comision-fields" style="display:none;">
                <x-adminlte-input name="comision_cuotas" label="Cuotas comisión" type="number" min="1" value="{{ old('comision_cuotas',1) }}"/>
                <small class="text-muted d-block">Prorratea la comisión en N meses desde el inicio.</small>
            </div>

            {{-- Depósito --}}
            <div class="col-md-3">
                <x-adminlte-select name="tiene_deposito" label="¿Cobrar depósito?" id="tiene_deposito">
                    <option value="0" {{ old('tiene_deposito',0)==0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('tiene_deposito',0)==1 ? 'selected' : '' }}>Sí</option>
                </x-adminlte-select>
            </div>
            <div class="col-md-3 deposito-fields" style="display:none;">
                <x-adminlte-input name="deposito" label="Monto depósito" type="number" step="0.01" value="{{ old('deposito') }}"/>
            </div>
            <div class="col-md-3 deposito-fields" style="display:none;">
                <x-adminlte-input name="deposito_cuotas" label="Cuotas depósito" type="number" min="1" value="{{ old('deposito_cuotas',1) }}"/>
                <small class="text-muted d-block">Prorratea el depósito en N meses desde el inicio.</small>
            </div>

        </div>
    </x-adminlte-card>
</div>

<x-adminlte-button type="submit" theme="success" icon="fas fa-save" label="Guardar (y generar cuotas del bloque si aplica)"/>
<a href="{{ route('contratos.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selTipo = document.querySelector('select[name="tipo_ajuste"]');
    const wrapPct = document.getElementById('wrap-porcentaje');
    const inputPct = document.getElementById('porcentaje_incremento');

    function togglePct() {
        const isPct = selTipo && selTipo.value === 'PORCENTAJE';
        wrapPct.style.display = isPct ? '' : 'none';
        if (inputPct) {
            inputPct.disabled = !isPct;
            if (!isPct) inputPct.value = '';
        }
    }

    if (selTipo) {
        selTipo.addEventListener('change', togglePct);
        togglePct(); // estado inicial según old()
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // PCT existente
    const selTipo = document.querySelector('select[name="tipo_aumento"]');
    const wrapPct = document.getElementById('wrap-porcentaje');
    const inputPct = document.getElementById('porcentaje_incremento');

    function togglePct() {
        const isPct = selTipo && selTipo.value === 'PORCENTAJE';
        wrapPct.style.display = isPct ? '' : 'none';
        if (inputPct) {
            inputPct.disabled = !isPct;
            if (!isPct) inputPct.value = '';
        }
    }
    if (selTipo) {
        selTipo.addEventListener('change', togglePct);
        togglePct();
    }

    // Comisión / Depósito
    const selCom = document.getElementById('tiene_comision');
    const selDep = document.getElementById('tiene_deposito');
    const comFields = document.querySelectorAll('.comision-fields');
    const depFields = document.querySelectorAll('.deposito-fields');

    function showElems(elems, show) {
        elems.forEach(el => {
            el.style.display = show ? '' : 'none';
            el.querySelectorAll('input').forEach(inp => {
                inp.disabled = !show;
                if (!show) inp.value = '';
            });
        });
    }

    function toggleComision() {
        const on = selCom && selCom.value === '1';
        showElems(comFields, on);
    }
    function toggleDeposito() {
        const on = selDep && selDep.value === '1';
        showElems(depFields, on);
    }

    if (selCom) { selCom.addEventListener('change', toggleComision); }
    if (selDep) { selDep.addEventListener('change', toggleDeposito); }

    // Estado inicial según old()
    toggleComision();
    toggleDeposito();
});
</script>
@stop
