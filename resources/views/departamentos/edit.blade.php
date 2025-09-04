@extends('adminlte::page')
@section('title','Editar Departamento')

@section('content_header')
<h1>Editar departamento</h1>
@stop

@section('content')
@if($errors->any()) <x-adminlte-alert theme="danger" title="Errores"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></x-adminlte-alert> @endif
<form method="POST" action="{{ route('departamentos.update',$departamento) }}">
@csrf @method('PUT')
<div class="row">
    <div class="col-md-3"><x-adminlte-input name="codigo" label="Código" value="{{ old('codigo',$departamento->codigo) }}" required/></div>
    <div class="col-md-3"><x-adminlte-input name="piso" label="Piso" value="{{ old('piso',$departamento->piso) }}"/></div>
    <div class="col-md-6"><x-adminlte-input name="descripcion" label="Descripción" value="{{ old('descripcion',$departamento->descripcion) }}"/></div>
</div>
<x-adminlte-button class="mt-2" type="submit" theme="success" icon="fas fa-save" label="Actualizar"/>
<a href="{{ route('departamentos.index') }}" class="btn btn-secondary mt-2">Volver</a>
</form>
@stop
