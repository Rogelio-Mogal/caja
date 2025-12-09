@extends('layouts.app')

@section('content')
@section('title','Cargar pagos de préstamos desde archivo Excel')
	<form method="post" action="{{ Route('admin.pago.prestamos.store') }}" id="form_excel_pagos" > 
		@include('pagos_prestamos_excel._form', ['btnEnviar' => 'Guardar']) 
	</form> 
@stop