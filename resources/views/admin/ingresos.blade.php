@extends('adminlte::page')
@section('title','Ingresos')

@section('content_header')
<h1>Ingresos por mes</h1>
@stop

@section('content')
<form method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-3"><x-adminlte-input name="desde" label="Desde" type="date" :value="$desde->toDateString()"/></div>
        <div class="col-md-3"><x-adminlte-input name="hasta" label="Hasta" type="date" :value="$hasta->toDateString()"/></div>
        <div class="col-md-2 align-self-end"><x-adminlte-button type="submit" theme="primary" label="Filtrar"/></div>
    </div>
</form>

<table class="table table-striped">
    <thead><tr><th>Mes</th><th>Total</th></tr></thead>
    <tbody>
    @foreach($pagos as $fila)
        <tr>
            <td>{{ $fila->ym }}</td>
            <td>${{ number_format($fila->total,2,',','.') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@stop
