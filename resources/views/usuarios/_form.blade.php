@csrf
<div class="card card-outline card-primary">
    <div class="card-header">
        @if (\Request::route()->getName() === 'admin.usuarios.create')
            <h3 class="card-title">Nueva cuenta de accesso</h3>
        @elseif (\Request::route()->getName() === 'admin.usuarios.edit')
            <h3 class="card-title">Editar cuenta de acceso</h3>
        @endif
    </div>

    <div class="card-body">
        <div class="register-box-body">
            <div class="card border border-primary shadow-0 mb-3">
                <div class="card-header">
                    <h4>DATOS GENERALES</h4>
                </div>
                <div class="card-body text-dark">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::hidden('socio_id', old('socio_id', request('id')), ['id' => 'socio_id', 'value' => request('id')]) }}
                                    {{ Form::text('name', old('name', $socios->nombre_completo), ['id' => 'name', 'class' => 'form-control uppercase', 'placeholder' => 'NOMBRE', 'autofocus' => 'true', 'tabindex' => '1', 'required']) }}
                                    <label class="form-label" for="name">NOMBRE</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('name')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('email', old('email'), ['id' => 'email', 'class' => 'form-control', 'placeholder' => 'CORREO ELECTRÓNICO', 'tabindex' => '2', 'required']) }}
                                    <label class="form-label" for="email">CORREO ELECTRÓNICO</label>
                                </div>
                                @error('email')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    <input type="password" id="password" name="password" class="form-control" tabindex="3" required />
                                    <label class="form-label" for="password">CONTRASEÑA</label>
                                </div>
                                @error('password')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" tabindex="4" required />
                                    <label class="form-label" for="password_confirmation">CONFIRMAR CONTRASEÑA</label>
                                </div>
                                @error('password_confirmation')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-12 col-md-12 col-sm-12" align="center">
                    <div class="col-2 custom-center-align">
                        <br />
                        {!! Form::button('Guardar', ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'submitBtn']) !!}
                    </div>
                </div>
            </div>
            <br />
        </div>
    </div>
</div>


@section('js')
    <script>
        console.log('Hi!');

        // AL DAR ENTER NO ENVIA EL FORMULARIO PERO AVANZA AL SIGUIENTE INPUT
        $(document).on('keypress', 'input,select', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                var $next = $('[tabIndex=' + (+this.tabIndex + 1) + ']');
                if (!$next.length) {
                    $next = $('[tabIndex=1]');
                }
                $next.focus().click();
            }
        });

        // Evitar entrada de datos en el campo al escribir
        var inputPagoQuincenal = $('#name');
        inputPagoQuincenal.on('keydown', function(e) {
            e.preventDefault();
        });
    </script>

    @if (session('user') == 'fail')
        <script type="text/javascript">
            Swal.fire(
                'Error!',
                'Huho un error durante el proceso y el usuario no se ha registrado.',
                'warning'
            );
        </script>
    @endif
@stop
