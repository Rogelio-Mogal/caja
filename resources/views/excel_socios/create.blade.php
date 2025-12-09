@extends('layouts.app')

@section('content')
@section('title','Cargar socios desde archivo Excel')
	<form method="post" action="{{ Route('admin.excel.socios.store') }}" id="form_excel_socios" > 
		@include('excel_socios._form', ['btnEnviar' => 'Guardar']) 
	</form> 
@stop