@extends('layouts.app')

@section('content')

@section('css')
	<style type="text/css">
		.gradient-custom {
			/* fallback for old browsers */
			background: #f6d465bb;

			/* Chrome 10-25, Safari 5.1-6 */
			background: -webkit-linear-gradient(to right bottom, rgba(246, 212, 101, 0.588), rgba(253, 159, 133, 0.822));

			/* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
			background: linear-gradient(to right bottom, rgba(246, 211, 101, 1), rgba(253, 159, 133, 0.809))
		}
		.negritas-negro {
		font-weight: bold; /* Aplica negrita */
		color: black; /* Aplica color negro */
		}
		.uppercase {
            text-transform: uppercase;
        }
	</style>
@stop

  <section class="vh-500" style="background-color: #fdfdff;">
	<form method="post" action="{{ Route('admin.user.update', $user) }}">
		@method('PATCH')
		@include('users._perfil', ['btnEnviar' => 'Actualizar'])
	</form>
  </section>

  <section class="vh-500" style="background-color: #fdfdff;">
	<form method="post" action="{{ Route('admin.user.update', $user) }}">
		@method('PATCH')
		@include('users._contrasenia', ['btnEnviar' => 'Actualizar'])
	</form>
  </section>

@stop

@section('js')

    @if (Session::has('success'))
        <script type="text/javascript">
            var mensaje = "{{ session('mensaje') }}";
            Swal.fire({
                icon: 'success',
                title: ''+mensaje+'',
                text: 'Operación exitosa.',
            });
            <?php Session::forget('success'); ?>
        </script>
    @endif
    @if (Session::has('error'))
        <script type="text/javascript">
            Swal.fire({
                icon: 'error',
                title: 'Hubo un error durante el proceso. ',
                text: 'Por favor intente de nuevo más tarde.',
            });
            <?php Session::forget('error'); ?>
        </script>
    @endif
@stop
