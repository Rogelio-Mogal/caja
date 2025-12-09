@extends('layouts.app')

@section('content')
@section('title','Nuevo prestamo por salud/enfermedad')

	<form method="post" action="{{ Route('admin.prestamos.enfermedad.store') }}" id="form_prestamo_enfermedad" autocomple='off' class="needs-validation" novalidate>
		@include('prestamos_enfermedad._form', ['btnEnviar' => 'Guardar'])
	</form>
@stop