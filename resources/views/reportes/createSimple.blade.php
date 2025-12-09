@extends('adminlte::page')
@section('title','Agregar usuario')

@section('content_header')
   <div class="container mt-1">
		{{-- <h1 class="display-6">Usuarios</h1> --}}
	</div>
@stop

@section('content')
	<div class="container my-1">
		<div class="col-md-12 col-lg-10 col-xl-8">
			<div class="card border-primary shadow">
				<div class="card-header bg-secondary">
					<h4 class="text-white">Registrar un nuevo usuario</h4>
				</div>
					<div class="card-body pt-1">
						<form method="post" action="{{ Route('usuarios.store') }}">
							@csrf
							<small id="alias-error" class="text-danger pl-2"></small>
							<div class="form-row">
								<div class="col-12 mb-3">
									<label class="font-weight-normal" for="validationDefault01"><span class="text-danger">*</span>NOMBRE COMPLETO:</label>
									<input type="text"name="name" value="{{ old('name') }}"  class="form-control" placeholder="" />
								</div>

								<div class="col-12 mb-3">
									<label class="font-weight-normal"><span class="text-danger">*</span>E-MAIL:</label>
									<input type="text" name="email" value="{{ old('email') }}" maxlength="60" class="form-control" placeholder="" />
								</div>

								<div class="col-12 mb-3">
									<label class="font-weight-normal" for="validationDefault02"><span class="text-danger">*</span>CONTRASEÑA:</label>
									<input type="password" name="password" class="form-control" />
								</div>

								<div class="col-12 mb-3">
									<label class="font-weight-normal" class="d-none d-sm-none d-md-block" for="validationDefault03"><span class="text-danger">*</span>CONFIRMAR CONTRASEÑA:</label>
									<input type="password" name="password_confirmation" class="form-control" />
								</div>
							</div> {{-- End div form-row1 --}}

							<div class="form-row">
								<div class="col-md-12">
									<label class="font-weight-normal">
										Todos los campos son obligatorios.<br>
										Por favor revise que los datos ingresados son correctos antes de hacer clic en continuar.
										@if( $errors->any )
											<ul class="text-danger">
												@foreach($errors->all() as $error)
													<li>{{ $error }}</li>
												@endforeach
											</ul>
										@endif
									</label>
								</div>
							</div> {{-- end form-row3 --}}

						</div> {{-- End card-body --}}

						<div class="card-footer text-right">
							<ul class="list-inline" style="padding-right: 20px; margin-bottom: 0px;">
								<li class="list-inline-item"><button type="submit" id="btn-guardar" class="btn btn-sm btn-outline-success">Guardar Registro</button></li>
								<li class="list-inline-item"><a href="{{ route('usuarios.index') }}"><button type="button" class="btn btn-sm btn-outline-secondary">Cancelar</button></a></li>
							</ul>
						</div> {{-- End card-footer --}}
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@stop

@section('js')
    <script>
        console.log('Hi!');
        /*alert('jQuery works!');*/
    </script>
@stop
