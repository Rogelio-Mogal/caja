@extends('layouts.app')

@section('content')
@section('title','Roles y permisos')

	<form method="post" action="{{ Route('admin.roles.store') }}" id="form_roles" autocomple='off'>
		@include('roles._form', ['btnEnviar' => 'Guardar'])
	</form>
@stop