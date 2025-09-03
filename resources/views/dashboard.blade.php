@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')
    <h1>Panel de control</h1>
@stop

@section('content')
    <x-adminlte-info-box title="Bienvenido" text="Sesión iniciada correctamente." icon="fas fa-user-check"/>
@stop

@section('right-sidebar') {{-- opcional si activas right_sidebar en config --}}
@endsection