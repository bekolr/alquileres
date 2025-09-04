@extends('adminlte::page')
@section('title','Edificios')

@section('content_header')
<h1>Edificios</h1>
@stop

@section('content')
<x-adminlte-button class="mb-3" theme="primary" icon="fas fa-plus" label="Nuevo edificio"
    onclick="window.location='{{ route('edificios.create') }}'"/>

@if(session('ok')) <x-adminlte-alert theme="success" title="OK">{{ session('ok') }}</x-adminlte-alert> @endif

<table class="table table-striped table-sm">
    <thead><tr><th>#</th><th>Nombre</th><th>Dirección</th><th></th></tr></thead>
    <tbody>
        @foreach($edificios as $e)
        <tr>
            <td>{{ $e->id }}</td>
            <td>{{ $e->nombre }}</td>
            <td>{{ $e->direccion }}</td>
            <td><a class="btn btn-xs btn-primary" href="{{ route('edificios.show',$e) }}"><i class="fas fa-eye"></i></a></td>
        </tr>
        @endforeach
    </tbody>
</table>

@stop
