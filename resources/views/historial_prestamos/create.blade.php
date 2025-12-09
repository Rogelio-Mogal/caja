@extends('layouts.app')

@section('content')
@section('title','Nuevo prestamo')

	<form method="post" action="{{ Route('admin.prestamos.store') }}" id="form_prestamos" autocomple='off' class="needs-validation" novalidate>
		@include('prestamos._form', ['btnEnviar' => 'Guardar'])
	</form>
@stop