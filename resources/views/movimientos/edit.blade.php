{{-- resources/views/movimientos/edit.blade.php --}}
@extends('adminlte::page')

@section('title', 'Editar Movimiento')

@section('content_header')
    <h1>Editar Movimiento</h1>
@stop

@section('content')
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('movimientos.update', $movimiento) }}" method="POST">
                @csrf @method('PUT')

                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Fecha</label>
                        <input type="date" name="fecha" value="{{ old('fecha', $movimiento->fecha) }}" class="form-control" required>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Tipo</label>
                        <select name="tipo" class="form-control" required>
                            <option value="ingreso" {{ old('tipo',$movimiento->tipo)=='ingreso'?'selected':'' }}>Ingreso</option>
                            <option value="egreso"  {{ old('tipo',$movimiento->tipo)=='egreso'?'selected':'' }}>Egreso</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Concepto</label>
                        <select name="concepto_id" class="form-control" required>
                            @foreach($conceptos as $grupo => $items)
                                <optgroup label="{{ strtoupper($grupo) }}">
                                    @foreach($items as $c)
                                        <option value="{{ $c->id }}" {{ (int)old('concepto_id',$movimiento->concepto_id)===(int)$c->id?'selected':'' }}>
                                            {{ $c->nombre }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Monto</label>
                        <input type="number" step="0.01" min="0.01" name="monto" value="{{ old('monto', $movimiento->monto) }}" class="form-control" required>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Método de pago</label>
                        <select name="metodo_pago" class="form-control">
                            <option value="">-- Seleccione --</option>
                            @foreach($metodos as $m)
                                <option value="{{ $m }}" {{ old('metodo_pago',$movimiento->metodo_pago)==$m?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$m)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Referencia (opcional)</label>
                        <div class="input-group">
                            <select name="referencia_type" class="form-control">
                                <option value="">Sin referencia</option>
                                <option value="App\Models\Cuota"    {{ old('referencia_type',$movimiento->referencia_type)=='App\Models\Cuota'?'selected':'' }}>Cuota</option>
                                <option value="App\Models\Contrato" {{ old('referencia_type',$movimiento->referencia_type)=='App\Models\Contrato'?'selected':'' }}>Contrato</option>
                            </select>
                            <input type="number" name="referencia_id" value="{{ old('referencia_id',$movimiento->referencia_id) }}" class="form-control" placeholder="ID">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion" rows="3" class="form-control">{{ old('descripcion', $movimiento->descripcion) }}</textarea>
                </div>

                <button class="btn btn-primary">Actualizar</button>
                <a href="{{ route('movimientos.index') }}" class="btn btn-default">Cancelar</a>
            </form>
        </div>
    </div>
@stop
