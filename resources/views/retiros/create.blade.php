@extends('layouts.app')

@section('content')
@section('title','Pre aprobar retiro')

	<form method="post" action="{{ Route('admin.retiros.store') }}" id="form_retiro" autocomple='off'>
		@include('retiros._form', ['btnEnviar' => 'Guardar'])
	</form>
@stop