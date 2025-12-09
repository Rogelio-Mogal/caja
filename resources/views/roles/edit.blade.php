@extends('layouts.app')

@section('content')

@section('content')
<form method="post" action="{{ Route('admin.roles.update', $role) }}" autocomple='off'>
    @method('PATCH')
    @include('roles._form', ['btnEnviar' => 'Actualizar'])
</form>
@stop

@stop





