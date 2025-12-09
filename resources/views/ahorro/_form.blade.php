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

        /* Ajustar la altura de la caja del select2 */
        .select2-container .select2-selection--single {
                height: 35px; /* Ajusta la altura según tus necesidades */
                line-height: 35px; /* Asegura que el texto se alinee verticalmente al centro */
                display: flex; /* Flexbox para alinear el contenido */
                align-items: center; /* Centra el contenido verticalmente */
                position: relative; /* Necesario para posicionar el ícono */
            }

            /* Ajustar la altura del dropdown */
            .select2-container .select2-dropdown {
                max-height: 300px; /* Ajusta el alto máximo del dropdown */
                overflow-y: auto; /* Permite el scroll si el contenido es más alto */
            }

            /* Centrar el texto en el elemento seleccionado */
            .select2-container .select2-selection__rendered {
                line-height: 35px; /* Asegura que el texto seleccionado esté centrado */
                display: flex;
                align-items: center; /* Centra el texto verticalmente */
            }

            /* Posicionar el ícono de "limpiar" a la derecha */
            .select2-container .select2-selection__clear {
                position: absolute; /* Posicionamiento absoluto */
                right: 10px; /* Ajusta según sea necesario para alejarlo del borde derecho */
                top: 40%; /* Posición vertical en el centro */
                transform: translateY(-55%); /* Asegura que esté centrado verticalmente */
                cursor: pointer; /* Establece un puntero de cursor para indicar que es clickeable */
            }

            select:invalid {
                border: 1px solid red;
            }
    </style>
@stop

<br />
@csrf
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Ahorro voluntario</h3>
    </div>
    <div class="card-body">
        <div class="register-box-body">
            <div class="row">
                <div class="col-lg-5 col-md-5 col-sm-5">
                    <div class="col mb-3">
                        {{--<select class="select mb-2" name="socios_id" id="socios_id" data-mdb-filter="true" required>
                            <option value="" hidden selected>-- Socios --</option>--}}
                            {{--@foreach ($socios as $socio)
                                <option value="{{ $socio->id }}">{{ $socio->nombre }}</option>
                            @endforeach--}}
                        {{--</select>--}}
                        <select id="socios_id" name="socios_id" class="form-control select2" style="width: 100%;" required>
                        </select>
                        <label class="form-label select-label form-control-lg" for="socios_id">&nbsp;</label>
                        @error('socios_id')
                            <p class="error-message text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="col mb-3">
                        <div class="form-outline">
                            {{ Form::text('pp_display', old('monto', $ahorro->monto), ['id' => 'pp_display', 'oninput' => 'formatNumber(this)', 'onblur' => 'fixDecimals(this)', 'class' => 'form-control uppercase', 'placeholder' => 'Monto', 'tabindex' => '2', 'required']) }}
                            {{ Form::hidden('monto', old('monto', $ahorro->monto), ['id' => 'monto', 'class' => 'form-control uppercase', 'placeholder' => 'Monto','step'=>'any']) }}
                            <label class="form-label" for="monto">Monto</label>
                        </div>
                        @error('monto')
                            <p class="error-message text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="col mb-3">
                        <div class="form-outline">
                            
                            {{ Form::hidden('tipoSangre') }}

                            {!! Form::select(
                                'metodo_pago',
                                [
                                    '-1' => '- MÉTODO DE PAGO -',
                                    'EFECTIVO' => 'EFECTIVO',
                                    'TRANSFERENCIA ELECTRÓNICA' => 'TRANSFERENCIA ELECTRÓNICA',
                                    'DEPÓSITO' => 'DEPÓSITO',
                                    'CHEQUE' => 'CHEQUE',
                                ],
                                old('metodo_pago', $ahorro->tipo_sangre),
                                ['id' => 'metodo_pago', 'class' => 'select', 'required' => true,'tabindex' => '3'],
                            ) !!}
                            <label class="form-label select-label" for="metodo_pago">MÉTODO DE PAGO</label>
                            <div class="form-helper" id="sangre_feedback" style="color: red; display: none;">Este
                                campo es requerido.</div>
                        </div>
                        @error('metodo_pago')
                            <p class="error-message text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-1 col-md-1 col-sm-2">
                    <div class="col mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_aportacion" id="is_aportacion"
                                {{ old('is_aportacion', $ahorro->is_aportacion ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_aportacion">Aportación</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="col mb-3">
                        <div class="form-outline">
                            {{ Form::text('referencia', old('referencia', $ahorro->referencia), ['id' => 'referencia', 'class' => 'form-control', 'placeholder' => 'Referencia', 'tabindex' => '4']) }}
                            <label class="form-label" for="referencia">Referencia</label>
                        </div>
                        @error('referencia')
                            <p class="error-message text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12" align="center">
                    <div class="col-2">
                        <br />
                        {!! Form::button('Guardar', ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'submitBtn']) !!}
                    </div>
                </div>
            </div>
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
            document.getElementById('monto').value = cleanValue;
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
            document.getElementById('monto').value = input.value.replace(/,/g, '');
        }

        //valida reqiote en metodo_pago
        document.querySelector('form').addEventListener('submit', function(e) {
            const metodo = document.getElementById('metodo_pago').value;
            console.log('metodo: '+metodo);
            e.preventDefault();
            if (metodo === '-1') {
                e.preventDefault();
                alert('Por favor selecciona un método de pago válido.');
            }
        });



        $(document).ready(function() {
            // Estilos para el select2 con BoostrapMD
            // Inicializar Select2
            $('#socios_id').select2({
                placeholder: "-- Socios --",
                allowClear: true,
                minimumInputLength: 3,
                width: '100%',
            });

            // ACTIVA LA BUSQUEDA
            $(document).on('select2:open', () => {
                let allFound = document.querySelectorAll('.select2-container--open .select2-search__field');
                $(this).one('mouseup keyup', () => {
                    setTimeout(() => {
                        allFound[allFound.length - 1].focus();
                    }, 0);
                });
            });

            // VALIDA EL metodo_pago
            var submitBtn = document.getElementById('submitBtn');
            var form = submitBtn.form;

            submitBtn.addEventListener('click', function(event) {
                // Deja que el navegador valide primero
                if (!form.checkValidity()) {
                    // Formulario no válido, el navegador mostrará los errores nativos
                    return; // No haces nada aquí, permites que el navegador actúe
                }

                // Si pasa la validación nativa, validas el valor personalizado
                if (document.getElementById('metodo_pago').value === '-1') {
                    event.preventDefault(); // Detienes el envío porque es un valor inválido a nivel de negocio
                    document.getElementById('sangre_feedback').style.display = 'block';
                } else {
                    // Valor válido, enviar el formulario
                    document.getElementById('sangre_feedback').style.display = 'none';
                    submitBtn.disabled = true;
                    form.submit();
                }
            });

            socios();
            // FUNCION PARA OBTENER LOS SOCIOS
            function socios(id) {
                $('#socios_id').select2({
                    placeholder: '-- Socios--',
                    allowClear: true,
                    minimumInputLength: 3,
                    width: '100%',
                    ajax: {
                        url: "{{ route('get.socios.ajax.by.select') }}", // Ruta de tu controlador
                        type: "POST",
                        dataType: 'json',
                        delay: 250, // Retraso para evitar muchas solicitudes
                        data: function(params) {
                            return {
                                search: params.term, // Término de búsqueda introducido por el usuario
                                _token: "{{ csrf_token() }}" // Token CSRF para seguridad
                            };
                        },
                        processResults: function(data) {
                            console.log('data: '+ data);
                            return {
                                results: $.map(data, function(socio) {
                                    return {
                                        id: socio.id || '',
                                        text: socio.nombre_completo || 'Sin nombre',
                                        rfc: socio.rfc || 'N/A',
                                        cuip: socio.cuip || 'N/A'
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    //minimumInputLength: 3, // Mínimo de caracteres para iniciar la búsqueda
                    language: {
                        inputTooShort: function() {
                            return 'Por favor, introduzca 3 o más caracteres';
                        },
                        noResults: function() {
                            return "No se encontraron resultados";
                        },
                        searching: function() {
                            return "Buscando...";
                        }
                    },
                    escapeMarkup: function(markup) {
                        return markup; // Permitir HTML en los resultados
                    },
                    templateResult: function(data) {
                        // Renderizar cada opción con datos adicionales
                        if (data.loading) {
                            return data.text;
                        }
                        var markup = `
                            <div>
                                <strong>${data.text}</strong>
                                <br>
                                <small>RFC: ${data.rfc || 'N/A'} | CUIP: ${data.cuip || 'N/A'}</small>
                            </div>`;
                        return markup;
                    },
                    templateSelection: function(data) {
                        // Mostrar solo el nombre seleccionado
                        return data.text || '-- Socios--';
                    }
                });
            }

            // Evita enviar el formulario al dar enter
            $(document).on('keypress', 'input,select', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                }
            });
        });
    </script>


@stop
