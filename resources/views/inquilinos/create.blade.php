@extends('adminlte::page')
@section('title','Nuevo Inquilino')

@section('content_header')
<h1>Nuevo inquilino</h1>
@stop

@section('content')
@if($errors->any()) <x-adminlte-alert theme="danger" title="Errores"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></x-adminlte-alert> @endif
<form method="POST" action="{{ route('inquilinos.store') }}">
@csrf
<div class="row">
    <div class="col-md-3"><x-adminlte-input name="dni" label="DNI" value="{{ old('dni') }}" required/></div>
    <div class="col-md-5"><x-adminlte-input name="nombre" label="Nombre" value="{{ old('nombre') }}" required/></div>
    <div class="col-md-4"><x-adminlte-input name="email" label="Email" type="email" value="{{ old('email') }}"/></div>
    <div class="col-md-4"><x-adminlte-input name="telefono" label="Teléfono" value="{{ old('telefono') }}"/></div>
    <!-- añadir user_id si es necesario -->
    <div class="col-md-4">
        <x-adminlte-select name="user_id" label="Usuario (opcional)">
            <option value="">-- Ninguno --</option>
            @foreach(\App\Models\User::orderBy('name')->get() as $u)
                <option value="{{ $u->id }}" @if(old('user_id')==$u->id) selected @endif>{{ $u->name }} ({{ $u->email }})</option>
            @endforeach
        </x-adminlte-select>
  </div>
</div>
<x-adminlte-button class="mt-2" type="submit" theme="success" icon="fas fa-save" label="Guardar"/>
<a href="{{ route('inquilinos.index') }}" class="btn btn-secondary mt-2">Volver</a>
</form>
@stop
