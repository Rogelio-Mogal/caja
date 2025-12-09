@extends('layouts.app')

@section('content')
@section('title','Nuevo sector / categoría')
	<form method="post" action="{{ Route('admin.sector.categoria.store') }}" id="form_socios" autocomple='off'>
		@include('sector_categoria._form', ['btnEnviar' => 'Guardar'])
	</form>
@stop