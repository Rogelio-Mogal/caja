@extends('layouts.app')
@section('content')

@section('title','Editar efectivo diario')


    <form method="post" action="{{ Route('admin.efectivo.diario.update', $efectivo) }}">
        @method('PATCH')
        @include('efectivo_diario._form', ['btnEnviar' => 'Actualizar'])
    </form>
@stop
