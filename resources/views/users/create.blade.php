@extends('layouts.app')

@section('content')
@section('title','Nuevo usuario')
	<form method="post" action="{{ Route('admin.usuarios.store') }}" id="form_usuario" autocomple='off'>
		@include('users._form', ['btnEnviar' => 'Guardar'])
	</form>
@stop

