@extends('adminlte::page')
@section('title','Editar servicio')

@section('content')
    <form method="post" action="{{ Route('admin.servicios.update', $servicios) }}" files='true' autocomple='off' enctype='multipart/form-data'>
        @method('PATCH')
        @include('servicios._form', ['btnEnviar' => 'Actualizar'])
    </form>
@stop
