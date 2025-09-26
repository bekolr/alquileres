@extends('adminlte::page')

@section('title', 'Filtro de cuotas')

@section('content_header')
    <h1>Filtro de cuotas</h1>
@stop

@section('content')
    <form method="GET" action="{{ route('cuotas.vencimiento') }}" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <label for="estado">Estado</label>
                <select name="estado" id="estado" class="form-control">
                    <option value="todas" {{ $estado==='todas'?'selected':'' }}>Todas</option>
                    <option value="pendiente" {{ $estado==='pendiente'?'selected':'' }}>Pendientes</option>
                    <option value="pagada" {{ $estado==='pagada'?'selected':'' }}>Pagadas</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="desde">Desde</label>
                <input type="date" name="desde" id="desde" class="form-control" value="{{ $desde }}">
            </div>
            <div class="col-md-3">
                <label for="hasta">Hasta</label>
                <input type="date" name="hasta" id="hasta" class="form-control" value="{{ $hasta }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary mr-2">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <a href="{{ route('cuotas.vencimiento') }}" class="btn btn-default">Limpiar</a>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Contrato</th>
                    <th>Periodo</th>
                    <th>Vencimiento</th>
                    <th class="text-right">Monto base</th>
                    <th class="text-right">Expensas</th>
                    <th class="text-center">Estado</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cuotas as $c)
                    <tr>
                        <td>{{ $c->id }}</td>
                        <td>{{ $c->contrato_id }}</td>
                        <td>{{ \Carbon\Carbon::parse($c->periodo)->format('m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($c->vencimiento)->format('d/m/Y') }}</td>
                        <td class="text-right">${{ number_format($c->monto_base,2,',','.') }}</td>
                        <td class="text-right">${{ number_format($c->expensas,2,',','.') }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $c->estado=='pendiente'?'warning':'success' }}">
                                {{ strtoupper($c->estado) }}
                            </span>
                        </td>
                         <td>
                        <a class="btn btn-xs btn-primary" href="{{ route('cuotas.show',$c) }}">
                            <i class="fas fa-dolar"></i> Registrar Pago
                        </a>
                    </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No hay cuotas para este filtro</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@stop
