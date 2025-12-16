@extends('layouts.app')

@section('content')
@section('title','Editar prestamo')
    <form method="post" action="{{ Route('admin.prestamos.update', $prestamo) }}" id="form_prestamos" autocomple='off'>
        @method('PATCH')
        @include('prestamos._form', ['btnEnviar' => 'Actualizar'])
    </form>
@stop
