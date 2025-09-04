@extends('adminlte::page')
@section('title', 'IPC - Nuevo')

@section('content_header')
    <h1>Nuevo índice IPC</h1>
@stop

@section('content')
    @if ($errors->any())
        <x-adminlte-alert theme="danger" title="Errores">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </x-adminlte-alert>
    @endif

    <form action="{{ route('ipc.store') }}" method="POST">
        @csrf
        <x-adminlte-card title="Datos" icon="fas fa-plus" theme="primary">
            <div class="row g-3">
                <div class="col-md-4">
                    <x-adminlte-input name="anio" label="Año" type="number" min="2000" max="2100" value="{{ old('anio', now()->year) }}" required/>
                </div>
                <div class="col-md-4">
              <x-adminlte-select name="mes" label="Mes" required>
    <option value="" disabled {{ old('mes') ? '' : 'selected' }}>Seleccione...</option>
    @foreach(range(1,12) as $m)
        @php
            $mesNombre = \Carbon\Carbon::createFromDate(null, $m, 1)
                ->locale('es')                // forzar español
                ->isoFormat('MMMM');          // nombre de mes en texto
            // Opcional: capitalizar primera letra (español suele ir en minúscula)
            $mesNombre = \Illuminate\Support\Str::ucfirst($mesNombre);
        @endphp
        <option value="{{ $m }}" {{ old('mes')==$m?'selected':'' }}>
            {{ $mesNombre }}
        </option>
    @endforeach
</x-adminlte-select>
                </div>
                <div class="col-md-4">
                    <x-adminlte-input name="valor" label="Factor (ej: 1.025)" type="number" step="0.000001" value="{{ old('valor') }}" required/>
                    <small class="text-muted">1.025 equivale a +2.5% mensual.</small>
                </div>
            </div>
        </x-adminlte-card>

        <x-adminlte-button type="submit" theme="success" icon="fas fa-save" label="Guardar"/>
        <a href="{{ route('ipc.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
@stop
