@extends('layouts.app')

@section('content')
@section('title','Efectivo diario')

	<form method="post" action="{{ Route('admin.efectivo.diario.store') }}" id="form_efectivo_diario" autocomple='off'>
		@include('efectivo_diario._form', ['btnEnviar' => 'Guardar'])
	</form>
@stop