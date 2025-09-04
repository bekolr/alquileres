@extends('adminlte::page')
@section('title','Nuevo Departamento')

@section('content_header')
<h1>Nuevo departamento</h1>
@stop

@section('content')
@if($errors->any()) <x-adminlte-alert theme="danger" title="Errores"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></x-adminlte-alert> @endif
<form method="POST" action="{{ route('departamentos.store') }}">
@csrf
<div class="row">
    <div class="col-md-3"><x-adminlte-input name="codigo" label="Código" value="{{ old('codigo') }}" required/></div>
    <div class="col-md-3"><x-adminlte-input name="piso" label="Piso" value="{{ old('piso') }}"/></div>
    <div class="col-md-6"><x-adminlte-input name="descripcion" label="Descripción" value="{{ old('descripcion') }}"/></div>
    <div class="col-md-4">
        <x-adminlte-select name="edificio_id" label="Edificio" required>
            <option value="">Seleccione...</option>
            @foreach($edificios as $e)
            <option value="{{ $e->id }}" {{ old('edificio_id')==$e->id?'selected':'' }}>{{ $e->nombre }} - {{ $e->direccion }}</option>
            @endforeach
        </x-adminlte-select>
    </div>
</div>
<x-adminlte-button class="mt-2" type="submit" theme="success" icon="fas fa-save" label="Guardar"/>
<a href="{{ route('departamentos.index') }}" class="btn btn-secondary mt-2">Volver</a>
</form>
@stop
