@extends('layouts.app')

@section('content')
@section('title','Cargar ahorro quincenal desde archivo Excel')
	<form method="post" action="{{ Route('admin.excel.ahorros.store') }}" id="form_excel_ahorro" > 
		@include('excel_ahorro._form', ['btnEnviar' => 'Guardar']) 
	</form> 
@stop