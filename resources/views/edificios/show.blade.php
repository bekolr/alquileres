<x-adminlte-card title="Ajustar expensas (solo cuotas no cobradas)" icon="fas fa-balance-scale" theme="warning">
    <form method="POST" action="{{ route('edificios.expensas.pendientes', $edificio) }}">
        @csrf
        <div class="row">
            <div class="col-md-3">
                <x-adminlte-select name="tipo" label="Tipo de cambio" id="tipoCambioSoloPend">
                    <option value="fijo">Fijo ($)</option>
                    <option value="porcentaje">Porcentaje (%)</option>
                </x-adminlte-select>
            </div>

            <div class="col-md-3" id="campoFijoSoloPend">
                <x-adminlte-input name="nuevo_valor" label="Nuevo valor ($)" type="number" step="0.01"/>
            </div>

            <div class="col-md-3 d-none" id="campoPorcentajeSoloPend">
                <x-adminlte-input name="porcentaje" label="Porcentaje (%)" type="number" step="0.01" placeholder="Ej: 15"/>
            </div>

            <div class="col-md-3">
                <x-adminlte-input name="aplicar_desde" label="Aplicar desde (YYYY-MM)" placeholder="2025-10" required/>
            </div>

            <div class="col-md-4">
                <x-adminlte-input-switch name="actualizar_contratos" label="Actualizar contratos activos (futuro)" data-on-text="Sí" data-off-text="No"/>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <x-adminlte-button type="submit" theme="warning" icon="fas fa-save" label="Aplicar ajustes"/>
            </div>
        </div>
    </form>

    <x-slot name="footerSlot">
        <small class="text-muted">
            • Solo afecta cuotas con estado <b>pendiente</b> desde el mes indicado.<br>
            • No modifica cuotas <b>parciales</b> ni <b>pagadas</b>.<br>
            • El interés diario se recalcula automáticamente sobre el nuevo saldo (base+expensas+ajustes − pagos).
        </small>
    </x-slot>
</x-adminlte-card>

@push('js')
<script>
document.getElementById('tipoCambioSoloPend').addEventListener('change', function(){
    const fijo = document.getElementById('campoFijoSoloPend');
    const porc = document.getElementById('campoPorcentajeSoloPend');
    if(this.value === 'porcentaje'){ fijo.classList.add('d-none'); porc.classList.remove('d-none'); }
    else{ porc.classList.add('d-none'); fijo.classList.remove('d-none'); }
});
</script>
@endpush
