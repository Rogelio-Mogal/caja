@extends('layouts.app')

@section('content')
@section('title','Nuevo socio')
	<form method="post" action="{{ Route('admin.socios.store') }}" id="form_socios" autocomple='off'>
		@include('socios._form', ['btnEnviar' => 'Guardar'])
	</form>
@stop