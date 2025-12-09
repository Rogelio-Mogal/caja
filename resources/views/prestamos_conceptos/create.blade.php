@extends('layouts.app')

@section('content')
@section('title','Nuevo concepto préstamo especial')

	<form method="post" action="{{ Route('admin.prestamos.comceptos.store') }}" id="form_prestamos_especiales" autocomple='off' >
		@include('prestamos_conceptos._form', ['btnEnviar' => 'Guardar'])
	</form>
@stop