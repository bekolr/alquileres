@extends('adminlte::page')
@section('title','Nuevo Edificio')

@section('content_header')
<h1>Nuevo edificio</h1>
@stop

@section('content')
<form method="POST" action="{{ route('edificios.store') }}">
@csrf
<div class="row">
    <div class="col-md-6"><x-adminlte-input name="nombre" label="Nombre" required/></div>
    <div class="col-md-6"><x-adminlte-input name="direccion" label="Dirección"/></div>
    <div class="col-md-6"><x-adminlte-input name="expensas" label="Expensas mensuales" type="number" step="0.01" value="0.00" required/></div>
</div>
<x-adminlte-button class="mt-2" type="submit" theme="success" icon="fas fa-save" label="Guardar"/>
<a href="{{ route('edificios.index') }}" class="btn btn-secondary mt-2">Volver</a>
</form>
@stop
