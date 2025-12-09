@extends('layouts.app')

@section('content')
@section('title','Nuevo prestamo especial')

	<form method="post" action="{{ Route('admin.prestamos.especiales.store') }}" id="form_prestamos_especiales" autocomple='off' class="needs-validation" novalidate>
		@include('prestamos_especiales._form', ['btnEnviar' => 'Guardar'])
	</form>
@stop