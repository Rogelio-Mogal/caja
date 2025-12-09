@extends('layouts.app')

@section('content')
@section('title','Editar sector / categoría')
    <form method="post" action="{{ Route('admin.sector.categoria.update', $sectorCategoria) }}" autocomple='off'>
        @method('PATCH')
        @include('sector_categoria._form', ['btnEnviar' => 'Actualizar'])
    </form>
@stop
