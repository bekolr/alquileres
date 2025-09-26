{{-- resources/views/movimientos/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Movimientos')

@section('content_header')
    <h1>Movimientos</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filtros --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('movimientos.index') }}" class="form-inline">
                @php
                    $meses = [
                        '01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio',
                        '07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'
                    ];
                @endphp

                <div class="form-group mr-2">
                    <label class="mr-2">Mes</label>
                    <select name="mes" class="form-control">
                        @foreach($meses as $num=>$nom)
                            <option value="{{ $num }}" {{ $mes==$num?'selected':'' }}>{{ $nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mx-2">
                    <label class="mr-2">Año</label>
                    <input type="number" name="anio" class="form-control" value="{{ $anio }}" min="2000" max="{{ now()->year+1 }}">
                </div>

                <div class="form-group mx-2">
                    <label class="mr-2">Tipo</label>
                    <select name="tipo" class="form-control">
                        <option value="">-- Todos --</option>
                        <option value="ingreso" {{ $tipo=='ingreso'?'selected':'' }}>Ingreso</option>
                        <option value="egreso"  {{ $tipo=='egreso'?'selected':'' }}>Egreso</option>
                    </select>
                </div>

                <div class="form-group mx-2">
                    <label class="mr-2">Concepto</label>
                    <select name="concepto_id" class="form-control">
                        <option value="">-- Todos --</option>
                        @foreach($conceptos as $tipoGrupo => $items)
                            <optgroup label="{{ strtoupper($tipoGrupo) }}">
                                @foreach($items as $c)
                                    <option value="{{ $c->id }}" {{ (string)$conceptoId===(string)$c->id?'selected':'' }}>
                                        {{ $c->nombre }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mx-2">
                    <label class="mr-2">Método</label>
                    <input type="text" name="metodo_pago" class="form-control" value="{{ $metodo }}" placeholder="efectivo, transferencia...">
                </div>

                <button class="btn btn-primary mx-2">Filtrar</button>
                <a href="{{ route('movimientos.index') }}" class="btn btn-default">Limpiar</a>

                <a href="{{ route('movimientos.create') }}" class="btn btn-success ml-auto">
                    <i class="fas fa-plus"></i> Nuevo
                </a>
            </form>
        </div>
    </div>

    {{-- Totales --}}
    <div class="row">
        <div class="col-md-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>${{ number_format($totales->total_ingresos ?? 0, 2, ',', '.') }}</h3>
                    <p>Total Ingresos</p>
                </div>
                <div class="icon"><i class="fas fa-arrow-down"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>${{ number_format($totales->total_egresos ?? 0, 2, ',', '.') }}</h3>
                    <p>Total Egresos</p>
                </div>
                <div class="icon"><i class="fas fa-arrow-up"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box {{ $balance >= 0 ? 'bg-primary':'bg-warning' }}">
                <div class="inner">
                    <h3>${{ number_format($balance, 2, ',', '.') }}</h3>
                    <p>Balance</p>
                </div>
                <div class="icon"><i class="fas fa-balance-scale"></i></div>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Concepto</th>
                        <th>Método</th>
                        <th class="text-right">Monto</th>
                        <th>Referencia</th>
                        <th>Descripción</th>
                        <th style="width:120px;"></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($movimientos as $m)
                    <tr>
                        <td>{{ \Illuminate\Support\Carbon::parse($m->fecha)->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $m->tipo_movimiento=='INGRESO'?'success':'danger' }}">
                                {{ strtoupper($m->tipo) }}
                            </span>
                        </td>
                        <td>{{ $m->concepto->nombre ?? '-' }}</td>
                        <td>{{ $m->metodo_pago ?? '-' }}</td>
                        <td class="text-right">${{ number_format($m->monto, 2, ',', '.') }}</td>
                        <td>
                            @if($m->referencia_type && $m->referencia_id)
                                {{ class_basename($m->referencia_type) }} #{{ $m->referencia_id }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="max-width:260px">{{ \Illuminate\Support\Str::limit($m->descripcion, 80) }}</td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="{{ route('movimientos.edit', $m) }}"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('movimientos.destroy', $m) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar movimiento?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">Sin resultados</td></tr>
                @endforelse
                </tbody>
            </table>

            {{ $movimientos->links() }}
        </div>
    </div>
@stop
