@extends('layouts.app')

@section('content')
@section('title','Editar liquidar préstamo')
    <form method="post" action="{{ Route('admin.pagar.prestamo.update', $prestamo->id) }}">
        @method('PATCH')
        @include('pagar_prestamos._form', ['btnEnviar' => 'Actualizar'])
    </form>
@stop
