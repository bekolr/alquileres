@extends('adminlte::page')
@section('title','Editar Inquilino')

@section('content_header')
<h1>Editar inquilino</h1>
@stop

@section('content')
@if($errors->any()) <x-adminlte-alert theme="danger" title="Errores"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></x-adminlte-alert> @endif
<form method="POST" action="{{ route('inquilinos.update',$inquilino) }}">
@csrf @method('PUT')
<div class="row">
    <div class="col-md-3"><x-adminlte-input name="dni" label="DNI" value="{{ old('dni',$inquilino->dni) }}" required/></div>
    <div class="col-md-5"><x-adminlte-input name="nombre" label="Nombre" value="{{ old('nombre',$inquilino->nombre) }}" required/></div>
    <div class="col-md-4"><x-adminlte-input name="email" label="Email" type="email" value="{{ old('email',$inquilino->email) }}"/></div>
    <div class="col-md-4"><x-adminlte-input name="telefono" label="Teléfono" value="{{ old('telefono',$inquilino->telefono) }}"/></div>
</div>
<x-adminlte-button class="mt-2" type="submit" theme="success" icon="fas fa-save" label="Actualizar"/>
<a href="{{ route('inquilinos.index') }}" class="btn btn-secondary mt-2">Volver</a>
</form>
@stop
