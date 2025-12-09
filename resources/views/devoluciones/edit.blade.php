@extends('adminlte::page')
@section('title','Editar devolución')

@section('content')
    <form method="post" action="{{ Route('admin.devoluciones.update', $servicios) }}" autocomple='off'>
        @method('PATCH')
        @include('devoluciones._form', ['btnEnviar' => 'Actualizar'])
    </form>
@stop
