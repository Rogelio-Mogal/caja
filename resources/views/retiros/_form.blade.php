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
    </style>
@stop

<br />
@csrf
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Pre-aprobar retiro</h3>
    </div>
    <div class="card-body">
        <div class="register-box-body">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="col mb-3">
                        <select id="socios_id" name="socios_id" class="form-control select2" style="width: 100%;" required>
                        </select>
                        @error('socios_id')
                            <p class="error-message text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="col mb-3">
                        <div class="form-outline">
                            {{ Form::text('saldo', old('saldo', $retiro->saldo), ['id' => 'saldo', 'class' => 'form-control', 'placeholder' => 'SALDO', 'tabindex' => '2','readonly']) }}
                            <label class="form-label" for="saldo">SALDO</label>
                        </div>

                        @error('saldo')
                            <p class="error-message text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="col mb-3">
                        <div class="form-outline">
                            {{ Form::text('pp_display', old('monto_prestamo',$retiro->monto_retiro), ['id' => 'pp_display', 'oninput' => 'formatNumber(this)', 'onblur' => 'fixDecimals(this)', 'tabindex' => '3', 'class' => 'form-control uppercase', 'placeholder' => 'MONTO A RETIRAR', 'required']) }}
                            {{ Form::hidden('monto_retiro', old('monto_retiro', $retiro->monto_retiro), ['id' => 'monto_retiro', 'class' => 'form-control']) }}
                            <label class="form-label" for="monto_retiro">MONTO A RETIRAR</label>
                        </div>
                        @error('monto_retiro')
                            <p class="error-message text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="col mb-3">
                        <div class="form-outline">

                            {{ Form::hidden('tipoSangre') }}

                            {!! Form::select(
                                'forma_pago',
                                [
                                    '-1' => '- FORMA DE PAGO -',
                                    'EFECTIVO' => 'EFECTIVO',
                                    'TRANSFERENCIA ELECTRÓNICA' => 'TRANSFERENCIA ELECTRÓNICA',
                                    'CHEQUE' => 'CHEQUE',
                                ],
                                old('forma_pago', $retiro->forma_pago),
                                ['id' => 'forma_pago', 'class' => 'select', 'required' => 'true', 'tabindex' => '4'],
                            ) !!}
                            <label class="form-label select-label" for="forma_pago">FORMA DE PAGO</label>
                            <div class="form-helper" id="sangre_feedback" style="color: red; display: none;">Este
                                campo es requerido.</div>
                        </div>
                        @error('forma_pago')
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
            document.getElementById('monto_retiro').value = cleanValue;
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
            document.getElementById('monto_retiro').value = input.value.replace(/,/g, '');
        }

        function prepareSubmit() {
            const display = document.getElementById('pp_display').value;
            const cleanValue = display.replace(/,/g, '');
            document.getElementById('monto_retiro').value = cleanValue;

            return true;
        }

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

            socios();

            // OBTENGO EL SALDO DEL ATRUBITO DEL SELECT
            $('#socios_id').change(function() {
                /*var selectedValue = $(this).val();
                var selectedOption = $(this).find('option[value="' + selectedValue + '"]');
                var saldo = selectedOption.data('saldo');
                $('#saldo').val(saldo).focus();
                $('#monto_retiro').attr('max', saldo);
                $('#monto_retiro').focus();*/

                // Recupera los datos almacenados en la selección actual
                var selectedData = $(this).data('selectedData');

                if (selectedData) {
                    var saldo = selectedData.saldo || 0; // Recupera el saldo

                    // Formatea el saldo como moneda mexicana
                    var saldoFormateado = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(saldo);

                    $('#saldo').val(saldoFormateado).focus();
                    $('#monto_retiro').attr('max', saldo).focus();
                }
            });

            // Evitar entrada de datos en el campo saldo
            var inputPagoQuincenal = $('#saldo');
            inputPagoQuincenal.on('keydown', function(e) {
                e.preventDefault();
            });


            // FUNCION PARA OBTENER LOS SOCIOS
            /*
            function socios(id, tipo, accion) {
                //console.log('aval id: ' + id);
                $.ajax({
                    url: "{{ route('all.socios.retiro') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        //console.log('success:', JSON.stringify(response));
                        // Recorrer los datos obtenidos y agregar opciones al select
                        var select = $('#socios_id');
                        $.each(response, function(index, socio) {
                            var option = $('<option>', {
                                value: socio.id,
                                text: socio.nombre_completo,
                                'data-mdb-secondary-text': 'RFC: ' + socio.rfc +
                                    '. CUIP: ' + socio.cuip,
                                'data-saldo': socio.saldo, // Agrega tu atributo personalizado aquí
                            });
                            select.append(option);
                        });
                    },
                    error: function(response) {
                        console.log('error:', JSON.stringify(response));
                    },
                });
            }
            */

            function socios() {
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
                             if (data.error) {
                                    alert(data.message); // Muestra el mensaje del error SQL
                                    return { results: [] };
                                }
                            return {
                                results: $.map(data, function(socio) {
                                    console.log('prestamo: '+ (socio.saldo));
                                    var saldoSocio = parseFloat(socio.saldo.replace(',', '.'));
                                    var monto = parseFloat(socio.monto_prestamos.replace(',', '.'));
                                    var saldoRetiro = saldoSocio - monto;
                                    return {
                                        id: socio.id || '',
                                        text: socio.nombre_completo || 'Sin nombre',
                                        rfc: socio.rfc || 'N/A',
                                        cuip: socio.cuip || 'N/A',
                                        saldo: saldoRetiro || 0 // Incluye saldo aquí
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
                        // Agrega los datos personalizados al seleccionar
                        $('#socios_id').data('selectedData', data); // Almacena los datos seleccionados en el elemento
                        // Mostrar solo el nombre seleccionado
                        return data.text || '-- Socios--';
                    }
                });
            }

        });

        //VALIDA MONTO A RETIRAR
        document.getElementById('form_retiro').addEventListener('submit', function (e) {
            //const saldoStr = document.getElementById('saldo').value.replace(/[^0-9.,]/g, '').replace(/,/g, '');
            let saldoStr = document.getElementById('saldo').value
            .replace(/,/g, '')
            .replace(/[^0-9.-]/g, '');
            const retiroStr = document.getElementById('monto_retiro').value.replace(/,/g, '');

            // Permitir solo un "-" al inicio
            saldoStr = saldoStr.replace(/(?!^)-/g, '');

            //const saldo = parseFloat(saldoStr) || 0;
            const saldo = parseFloat(saldoStr) || 0;
            const retiro = parseFloat(retiroStr) || 0;

            // No permitir valores negativos
            if (saldo < 0 || retiro < 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Mensaje de advertencia',
                    html: 'No se permiten valores negativos.',
                });
                return false;
            }

            if (retiro > saldo) {
                e.preventDefault(); // Detiene el envío
                Swal.fire({
                    icon: 'warning',
                    title: 'Mensaje de advertencia',
                    html: 'El monto a retirar no puede ser mayor al saldo disponible.',
                });
                return false;
            }
        });
    </script>


@stop
