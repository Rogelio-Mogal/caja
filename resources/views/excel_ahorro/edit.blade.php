@extends('layouts.app')

@section('content')
@section('title','Editar socio')
    <form method="post" action="{{ Route('admin.socios.update', $socios) }}" autocomple='off'>
        @method('PATCH')
        @include('socios._form', ['btnEnviar' => 'Actualizar'])
    </form>
@stop
