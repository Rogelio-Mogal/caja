@extends('layouts.app')

@section('content')
@section('title','Ahorro voluntario')

	<form method="post" action="{{ Route('admin.ahorros.store') }}" id="form_ahorro_voluntario" files='true' autocomple='off' enctype='multipart/form-data'>
		@include('ahorro._form', ['btnEnviar' => 'Guardar'])
	</form>
@stop