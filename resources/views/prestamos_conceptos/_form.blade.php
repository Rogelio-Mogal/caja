@section('css')
    <style type="text/css">
        .btn-uno {
            background-color: #ecbe14 !important;
            color: #000;
        }

        .btn-dos {
            background-color: #6fa84b !important;
            color: #000;
        }

        .btn-tres {
            background-color: #34b5aa !important;
            color: #000;
        }

        .uppercase {
            text-transform: uppercase;
        }
    </style>
@stop

<br />
<meta name="csrf-token" content="{{ csrf_token() }}">
@csrf
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">NUEVO CONCEPTO PRÉSTAMO ESPECIAL</h3>
    </div>
    <div class="card-body">
        <div class="register-box-body">
            <div class="card border border-success shadow-0 mb-3">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <h4>DATOS PRÉSTAMO ESPECIAL</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body text-dark">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('concepto', null, ['id' => 'concepto', 'class' => 'form-control uppercase generaIntereses', 'placeholder' => 'CONCEPTO', 'required']) }}
                                    <label class="form-label" for="concepto">CONCEPTO</label>
                                </div>
                                @error('concepto')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('pp_display', null, ['id' => 'pp_display', 'oninput' => 'formatNumber(this)', 'onblur' => 'fixDecimals(this)', 'class' => 'form-control uppercase', 'placeholder' => 'PRECIO', 'required']) }}
                                    {{ Form::hidden('precio', null, ['id' => 'precio', 'class' => 'form-control uppercase', 'placeholder' => 'PRECIO', 'step' => 'any']) }}
                                    <label class="form-label" for="precio">PRECIO</label>
                                </div>
                                @error('precio')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::number('num_plazos', null, ['id' => 'num_plazos', 'class' => 'form-control uppercase', 'placeholder' => 'NUM PLAZOS', 'required']) }}
                                    <label class="form-label" for="num_plazos">NUM PLAZOS</label>
                                </div>
                                @error('num_plazos')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::number('num_piezas', null, ['id' => 'num_piezas', 'class' => 'form-control uppercase', 'placeholder' => 'NUM PIEZAS', 'required']) }}
                                    <label class="form-label" for="num_piezas">NUM PIEZAS</label>
                                </div>
                                @error('num_piezas')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::textarea('comentarios', null, ['id' => 'comentarios', 'name' => 'comentarios', 'class' => 'form-control uppercase', 'rows' => 2]) }}
                                    <label class="form-label" for="comentarios">NOTA ADICIONAL</label>
                                </div>
                                @error('comentarios')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="row ">
                <div class="col-lg-12 col-md-12 col-sm-12" align="center">
                    <div class="col-2">
                        <br />
                        {!! Form::button('Guardar', ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'submitBtn']) !!}
                    </div>
                </div>
            </div>
            <br />
            <br />

        </div>
    </div>
</div>


@section('js')
    <script>
        function formatNumber(input) {
            let value = input.value.replace(/[^0-9.]/g, '');

            // Si hay más de un punto, eliminar los extras
            const firstDotIndex = value.indexOf('.');
            if (firstDotIndex !== -1) {
                // Mantener el primer punto, eliminar cualquier otro
                value = value.substring(0, firstDotIndex + 1) + value.substring(firstDotIndex + 1).replace(/\./g, '');
            }

            let parts = value.split('.');
            let integerPart = parts[0];
            let decimalPart = parts[1] ?? '';

            // Eliminar ceros iniciales del entero, pero permitiendo que si todo es 0 no borre
            integerPart = integerPart.replace(/^0+(?=\d)/, '');

            // Formatear la parte entera con comas
            let formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

            // Mostrar correctamente si apenas están escribiendo el punto
            input.value = decimalPart !== '' || value.endsWith('.') 
                ? `${formattedInteger}.` + decimalPart 
                : formattedInteger;

            // Actualizar el hidden limpio (sin comas)
            const cleanValue = input.value.replace(/,/g, '');
            document.getElementById('precio').value = cleanValue;
        }

        function fixDecimals(input) {
            let value = input.value.replace(/,/g, '').trim();

            if (value === '' || value === '.') {
                value = '0.00';
            }

            if (value.startsWith('.')) {
                value = '0' + value;
            }

            let parts = value.split('.');
            let integerPart = parts[0];
            let decimalPart = parts[1] || '';

            // Eliminar ceros iniciales
            integerPart = integerPart.replace(/^0+(?=\d)/, '');

            if (decimalPart.length === 0) {
                decimalPart = '00';
            } else if (decimalPart.length === 1) {
                decimalPart = decimalPart + '0';
            } else if (decimalPart.length > 2) {
                decimalPart = decimalPart.slice(0, 2);
            }

            // Formatear parte entera con comas
            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

            input.value = `${integerPart}.${decimalPart}`;

            // Actualizar el hidden limpio
            document.getElementById('precio').value = input.value.replace(/,/g, '');
        }

        function prepareSubmit() {
            const display = document.getElementById('pp_display').value;
            const cleanValue = display.replace(/,/g, '');
            document.getElementById('precio').value = cleanValue;

            return true;
        }

        $(document).ready(function() {
            // Evita enviar el formulario al dar enter
            $(document).on('keypress', 'input,select', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                }
            });
        });
    </script>

    @if (Session::has('error'))
        <script type="text/javascript">
            Swal.fire({
                icon: 'error',
                title: 'Hubo un error durante el proceso.',
                text: 'Por favor intente de nuevo.',
            });
        </script>
    @endif


@stop
