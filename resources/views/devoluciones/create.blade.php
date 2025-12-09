@extends('layouts.app')

@section('content')
@section('title','Devoluciones')

	<form method="post" action="{{ Route('admin.devoluciones.store') }}" id="form_devolucion" autocomple='off'>
		@include('devoluciones._form', ['btnEnviar' => 'Guardar'])
	</form>
@stop