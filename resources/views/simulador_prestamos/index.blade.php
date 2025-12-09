@extends('layouts.app')

@section('css')
    <style type="text/css">
        @media print {
            .no-imprimir {
                display: none;
            }
        }

        @media screen {
            .solo-impresion {
                display: none;
            }
        }

        .bg-navyblue {
            background-color: #29527e;
        }

        .bg-skyblue {
            background-color: #cce5ff;
        }

        .footer {
            position: relative;
            top: -200px; // this sets the footer -20px from the top of the next
            //header/page ... 20px above the bottom of target page
            //so make sure it is more negative than your footer's height.

            height: 100px; //notice that the top position subtracts
            //more than the assigned height of the footer
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

@section('content')

    @php
        use Carbon\Carbon;
    @endphp

    <br />

    {{-- MEDIA --}}
    <div class="card card-outline card-primary solo-impresion">
        <table class="table" width="100%" cellspacing="0" border="0" cellpadding="0">
            <tbody>
                <tr>
                    <td class="p-0">
                        <img class="masthead-avatar" src="{{ asset('image/sspo.png') }}" alt="SSPO_logo" width="auto"
                            height="100px" />
                    </td>
                    <td colspan="2" class="p-0 text-center align-middle">
                        <h4>CAJA DE AHORRO SSPO</h4>
                        <h4>SIMULADOR DE PRESTAMOS</h4>
                    </td>
                    <td class="p-0">
                        <p class="m-1"><strong>FOLIO: ----</strong></p>
                        <p class="m-1"><strong>FECHA DE CAPTURA:{{ \Carbon\Carbon::now()->format('d/m/Y') }}
                            </strong>
                        </p>
                        <strong>
                            <p class="m-1">No.SOCIO: <span id="numSocio"></span> </p>
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="p-1">
                        <strong>
                            <p class="m-1">SOCIO: <span id="socio"></span> </p>
                        </strong>
                    </td>
                    <td class="p-1">
                        <strong>
                            <p class="m-1">RFC: <span id="rfc_s"></span> </p>
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td class="p-1">
                        <strong>
                            <p class="m-1">PRESTAMO:</p>
                        </strong>
                    </td>
                    <td class="p-1">
                        <strong>
                            <p class="m-1">QUINCENAS:</p>
                        </strong>
                    </td>
                    <td class="p-1">
                        <strong>
                            <p class="m-1">PAGO QUINCENAL:</p>
                        </strong>
                    </td>
                    <td class="p-1">
                        <strong>
                            <p class="m-1">PRESTAMO + INTERESES:</p>
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td class="p-1">
                        <strong>
                            <p class="m-1"> <span id="prestamo"></span> </p>
                        </strong>
                    </td>
                    <td class="p-1">
                        <p class="m-1"> <span id="quincenas"></span> </p>
                    </td>
                    <td class="p-1">
                        <strong>
                            <p class="m-1"> <span id="pagoQuincenal"></span></p>
                        </strong>
                    </td>
                    <td class="p-1">
                        <strong>
                            <p class="m-1">
                                <span id="pres_interes"></span>
                            </p>
                        </strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                <h5 class="card-title"><strong> TABLA DE INTERESES </strong></h5>
            </div>
        </div>

        <div class="card shadow-0 mb-1">
            <div class="card-body text-dark p-1">
                <div class="row mb-1">
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3" style="display: none;">
                        <div class="card-body table-responsive p-0">
                            <table id="tabla-interesuno2" class="table responsive nowrap">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Pago</th>
                                        <th>Capital</th>
                                        <th>Interés</th>
                                        <th>Cap + Int</th>
                                        <th>Saldo Final</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                        <div class="card-body table-responsive p-0">
                            <table id="tabla-interesdos2" class="table table-sm"
                                style="font-size: 0.8rem; text-align: center;">
                                <thead>
                                    <tr>
                                        <th><strong>Pago</strong></th>
                                        <th><strong>Capital</strong></th>
                                        <th><strong>Interés</strong></th>
                                        <th><strong>Descuento</strong></th>
                                        <th><strong>Saldo Final</strong></th>
                                        <th><strong>Fecha de pago</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- END MEDIA --}}


    <div class="card card-outline card-primary no-imprimir">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-8 mb-3">
                    <h3 class="card-title b-0">SALDO Y SIMULADOR</h3>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 mb-3">
                    <button id="imprimir" type="button" class="btn btn-success">IMPRIMIR</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="register-box-body">
                <div class="card border border-success shadow-0 mb-3">
                    <div class="card-header">
                        <h4>DETALLE DE SOCIO PARA PRÉSTAMO</h4>
                    </div>
                    <div class="card-body text-dark">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                                <div class="col">
                                    <div class="form-outline">
                                        <span tabindex="1" data-mdb-toggle="tooltip" title="SOCIO">
                                            {{ Form::hidden('hidde', null) }}
                                            {{--<select class="select mb-2" name="socios_id" id="socios_id"
                                                data-mdb-filter="true" data-mdb-option-height="50" required="true">
                                                <option value="-1" hidden selected>-- Socios --</option>
                                            </select>--}}
                                            <select id="socios_id" name="socios_id" class="form-control select2" style="width: 100%;" required>
                                            </select>
                                            <label class="form-label select-label form-control-lg"
                                                for="socios_id">&nbsp;</label>
                                        </span>
                                        <div class="form-helper" id="socio_feedback" style="color: red; display: none;">
                                            Este
                                            campo es requerido.</div>
                                    </div>
                                    @error('socios_id')
                                        <p class="error-message text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="col mb-3">
                                    <div class="form-outline datepicker-translated" data-mdb-toggle-button="false">
                                        {{ Form::text('fecha_primer_pago', null, ['id' => 'fecha_primer_pago', 'data-mdb-toggle' => 'datepicker', 'class' => 'form-control', 'placeholder' => 'FECHA DEL PRIMER PAGO']) }}
                                        <label for="fecha_primer_pago" class="form-label">FECHA DEL PRIMER PAGO</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-12 mb-3">
                                <div class="col">
                                    <div class="form-outline">
                                        {{ Form::hidden('hidde', null) }}
                                        <span tabindex="2" data-mdb-toggle="tooltip" title="MONTO PRESTAMO">
                                            <div class="form-outline">
                                                {{ Form::text('pp_display', old('monto_prestamo',null), ['id' => 'pp_display', 'oninput' => 'formatNumber(this)', 'onblur' => 'fixDecimals(this)', 'class' => 'form-control uppercase', 'placeholder' => 'MONTO PRESTAMO', 'required']) }}
                                                {{ Form::hidden('monto_prestamo', null, ['id' => 'monto_prestamo', 'class' => 'form-control uppercase generaIntereses generaTotalAvalar']) }}
                                                <label class="form-label" for="monto_prestamo">MONTO
                                                    PRESTAMO</label>
                                                <div class="invalid-feedback">
                                                    Please provide a valid input.
                                                </div>
                                                <input id="monto_prestamos" name="monto_prestamos" type="hidden"
                                                    value="0" step="any" />

                                            </div>
                                        </span>
                                    </div>
                                    @error('monto_prestamo')
                                        <p class="error-message text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 mb-3">
                                <div class="col">
                                    <div class="form-outline">
                                        {{ Form::hidden('hidde', null) }}
                                        <span tabindex="2" data-mdb-toggle="tooltip" title="TOTAL QUINCENAS">
                                            <div class="form-outline">
                                                {{ Form::number('total_quincenas', null, ['id' => 'total_quincenas', 'class' => 'form-control uppercase generaIntereses', 'placeholder' => 'TOTAL QUINCENAS', 'required']) }}
                                                <label class="form-label" for="total_quincenas">TOTAL QUINCENAS</label>
                                                <div class="invalid-feedback">
                                                    Please provide a valid input.
                                                </div>
                                                <input id="total_intereses" name="total_intereses" type="hidden"
                                                    value="0" />
                                                <input id="total_cap_interes" name="total_cap_interes" type="hidden"
                                                    value="0" />
                                                {{ Form::hidden('saldo_socio', null, ['id' => 'saldo_socio', 'class' => 'generaIntereses generaTotalAvalar', 'step' => 'any']) }}
                                            </div>
                                        </span>
                                    </div>
                                    @error('total_quincenas')
                                        <p class="error-message text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-12 mb-3">
                                <div class="col">
                                    <div class="form-outline">
                                        {{ Form::hidden('hidde', null) }}
                                        <span tabindex="2" data-mdb-toggle="tooltip" title="PAGOS QUINCENALES">
                                            <div class="form-outline">
                                                {{ Form::number('pago_quincenal', null, ['id' => 'pago_quincenal', 'class' => 'form-control uppercase', 'placeholder' => 'PAGOS QUINCENALES', 'step' => 'any', 'required']) }}
                                                <label class="form-label" for="pago_quincenal">PAGOS QUINCENALES</label>
                                                <div class="invalid-feedback">
                                                    Please provide a valid input.
                                                </div>
                                            </div>
                                        </span>
                                    </div>
                                    @error('pago_quincenal')
                                        <p class="error-message text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-12 mb-3">
                                <div class="col">
                                    <div class="form-outline">
                                        {{ Form::hidden('hidde', null) }}
                                        <span tabindex="1" data-mdb-toggle="tooltip" title="PRESTAMO + INTERESES">
                                            <div class="form-outline">
                                                {{ Form::number('prestamo_intereses', null, ['id' => 'prestamo_intereses', 'name' => 'prestamo_intereses', 'class' => 'form-control uppercase', 'placeholder' => 'PRESTAMO + INTERESES', 'step' => 'any', 'required']) }}
                                                <label class="form-label" for="prestamo_intereses">PRESTAMO +
                                                    INTERESES</label>
                                                <div class="invalid-feedback">
                                                    Please provide a valid input.
                                                </div>
                                            </div>
                                        </span>
                                    </div>
                                    @error('prestamo_intereses')
                                        <p class="error-message text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-12 mb-3">
                                <div class="col">
                                    <div class="form-outline">
                                        <div class="form-outline">
                                            <span tabindex="3" data-mdb-toggle="tooltip" title="NÚMERO DE SOCIO">
                                                <input class="form-control" id="num_socio" type="text"
                                                    value="NÚMERO DE SOCIO" aria-label="readonly input example"
                                                    readonly />
                                                <label class="form-label form-control-lg" for="num_socio">NÚMERO DE
                                                    SOCIO</label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 mb-3">
                                <div class="col">
                                    <div class="form-outline">
                                        <span tabindex="4" data-mdb-toggle="tooltip" title="PRESTAMOS ACTIVOS">
                                            <input class="form-control" id="numero_prestamos" type="text"
                                                value="PRESTAMOS ACTIVOS" aria-label="readonly input example" readonly />
                                            <label class="form-label form-control-lg" for="numero_prestamos">PRESTAMOS
                                                ACTIVOS</label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 mb-3">
                                <div class="col">
                                    <div class="form-outline">
                                        <span tabindex="5" data-mdb-toggle="tooltip" title="FECHA DE ALTA">
                                            <input class="form-control" id="fecha_alta" type="text"
                                                value="FECHA DE ALTA" aria-label="readonly input example" readonly />
                                            <label class="form-label form-control-lg" for="fecha_alta">FECHA DE
                                                ALTA</label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-12 mb-3">
                                <div class="col">
                                    <div class="form-outline">
                                        <span tabindex="6" data-mdb-toggle="tooltip" title="RFC">
                                            <input class="form-control" id="rfc" type="text" value="RFC"
                                                aria-label="readonly input form-control-lg" readonly />
                                            <label class="form-label form-control-lg" for="rfc">RFC</label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 mb-3">
                                <div class="col">
                                    <div class="form-outline">
                                        <span tabindex="7" data-mdb-toggle="tooltip" title="SALDO AHORRADO">
                                            <input class="form-control" id="saldo" name="saldo" type="text"
                                                value="SALDO AHORRADO" aria-label="readonly input example" readonly />
                                            <label class="form-label form-control-lg" for="saldo">SALDO
                                                AHORRADO</label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 mb-3">
                                <div class="col">
                                    <div class="form-outline">
                                        <span tabindex="8" data-mdb-toggle="tooltip" title="SALDO DISPONIBLE">
                                            <input class="form-control" id="disponible_socio" name="disponible_socio"
                                                type="text" value="SALDO DISPONIBLE"
                                                aria-label="readonly input example" readonly />
                                            <input id="monto_socio" name="monto_socio" type="hidden" value="0" />
                                            <label class="form-label form-control-lg" for="disponible_socio">SALDO
                                                DISPONIBLE</label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 mb-3">
                                <div class="col">
                                    <div class="form-outline">
                                        <span tabindex="9" data-mdb-toggle="tooltip" title="SALDO DISPONIBLE CON AVAL">
                                            <input class="form-control" id="disponible_aval" type="text"
                                                value="SALDO DISPONIBLE CON AVAL" aria-label="readonly input example"
                                                readonly />
                                            <label class="form-label form-control-lg" for="disponible_aval">SALDO
                                                DISPONIBLE CON AVAL</label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 d-md-flex justify-content-center">
                                <button id="simular" type="button" class="btn btn-info"
                                    style="background-color: #ff4500;">SIMULAR</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border border-info shadow-0 mb-3">
                    <div class="card-header">
                        <h4>TABLA DE INTERESES</h4>
                    </div>
                    <div class="card-body text-dark">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 mb-3" style="display: none">
                                <div class="card-body table-responsive p-0">
                                    <table id="tabla-interesuno" class="table responsive nowrap">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Pago</th>
                                                <th>Capital</th>
                                                <th>Interés</th>
                                                <th>Cap + Int</th>
                                                <th>Saldo Final</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                <div class="card-body table-responsive p-0">
                                    <table id="tabla-interesdos" class="table responsive nowrap">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Pago</th>
                                                <th>Capital</th>
                                                <th>Interés</th>
                                                <th>Descuento</th>
                                                <th>Saldo Final</th>
                                                <th>Fecha de pago</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <br />
                <br />

            </div>
        </div>

    </div>
@stop


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
            document.getElementById('monto_prestamo').value = cleanValue;
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
            document.getElementById('monto_prestamo').value = input.value.replace(/,/g, '');
        }

        function prepareSubmit() {
            const display = document.getElementById('pp_display').value;
            const cleanValue = display.replace(/,/g, '');
            document.getElementById('monto_prestamo').value = cleanValue;

            return true;
        }

        $(document).ready(function() {
            $('#retiros').DataTable({
                "language": {
                    "url": "{{ asset('/json/i18n/es_es.json') }}"
                }
            });

            let totalcapint = 0;
            let totalinteres = 0;
            let interesid = 0;
            let saldoAvalar = false;

            let totalcapint2 = 0;
            let totalinteres2 = 0;
            let interesid2 = 0;

            // ACTIVA EL CTRL + P
            $("#imprimir").click(function() {
                window.print();
            });

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

            // GENERA LAS TABLAS DEL SIMILADOR
            $(document).on('click', '#simular', function() {
                if (($('#monto_prestamo').val() != '' && $('#monto_prestamo').val() > 0) && ($(
                        '#total_quincenas').val() != '' && $('#total_quincenas').val() > 0) && ($(
                        '#fecha_primer_pago').val() != '')) {
                    totalcapint = 0;
                    totalinteres = 0;
                    interesid = 0;

                    totalcapint2 = 0;
                    totalinteres2 = 0;
                    interesid2 = 0;

                    $('#prestamo').text(formatToCurrency($('#monto_prestamo').val()));
                    $('#quincenas').text($('#total_quincenas').val());

                    tablainteresuno();
                    tablainteresdos();

                    tablainteresuno2();
                    tablainteresdos2();
                    generaFecha($('#fecha_primer_pago').val(), $('#total_quincenas').val());
                    pagoQuincenal();
                }

                // obtengo el monto de SALDO SOCIO
                var prestamo_intereses = $('#prestamo_intereses').val();
                var monto_socio = $('#monto_socio').val();
                var saldo_socio = $('#saldo_socio').val();

                if (parseFloat(prestamo_intereses) <= parseFloat(monto_socio)) {
                    saldo_socio = parseFloat(prestamo_intereses);
                    $('#saldo_socio').val(saldo_socio);
                } else if (parseFloat(prestamo_intereses) > parseFloat(monto_socio)) {
                    saldo_socio = parseFloat(monto_socio);
                    $('#saldo_socio').val(saldo_socio);
                }
            });

            // AJAX PARA OBTENER LOS DATOS DE LOS SOCIOS
            $(document).on('change', '#socios_id', function() {
                // OBTENGO LOS DATOS DEL SOCIO
                if ($('#socios_id').val() === '-1') {
                    $('#socio_feedback').show();
                } else {
                    $('#socio_feedback').hide();
                }
                $.ajax({
                    url: "{{ route('detalle.socio.prestamo') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        socios_id: $('#socios_id').val(),
                    },
                    success: function(response) {
                        //console.log('success:', JSON.stringify(response));
                        $(response).each(function(i, v) {
                            var saldo = parseFloat(v.saldo);
                            $('#fecha_alta').val(v.fecha_alta);
                            $('#num_socio').val(v.num_socio);
                            $('#numSocio').text(v.num_socio);
                            $('#numero_prestamos').val(v.numero_prestamos);
                            $('#rfc').val(v.rfc);
                            $('#rfc_s').text(v.rfc);
                            $('#socio').text(v.nombre_completo);
                            $('#saldo').val(formatToCurrency(saldo));
                            $('#disponible_socio').val(formatToCurrency(v
                                .saldo_disponible));
                            //$('#saldo_socio').attr('max', parseFloat(v
                            //    .saldo_disponible));
                            $('#monto_socio').val(v.saldo_disponible);
                            $('#disponible_aval').val(formatToCurrency(v
                                .saldo_disponible *
                                2));
                            $('#monto_prestamo').attr('max', v.saldo_disponible * 2);
                        });
                    },
                    error: function(response) {
                        console.log('error:', JSON.stringify(response));
                    },
                });


                // obtengo el monto de SALDO SOCIO
                var prestamo_intereses = $('#prestamo_intereses').val();
                var disponible_socio = $('#disponible_socio').val();
                var saldo_socio = $('#saldo_socio').val();

                if (parseFloat(prestamo_intereses) <= parseFloat(disponible_socio)) {
                    console.log('1');
                    saldo_socio = parseFloat(prestamo_intereses);
                    $('#saldo_socio').val(saldo_socio);
                } else if (parseFloat(prestamo_intereses) > parseFloat(disponible_socio)) {
                    console.log('2');
                    saldo_socio = parseFloat(disponible_socio);
                    $('#saldo_socio').val(saldo_socio);
                }



            });

            // OBTENGO EL VALOR DE SALDO DISPONIBLE DEL AVAL
            $(document).on('change', '#aval_id', function() {
                var selectedOption = $(this).find("option:selected");
                var saldo = selectedOption.attr("saldo");
                var isAval = selectedOption.attr("isAval");
                $("#saldo_disponible_aval").val(saldo);
                $("#is_aval").val(isAval);
            });

            // SI HAY VALORES EN total prestamo Y plazos, se genera la tabla intereses
            $(document).on('change', '.generaIntereses', function() {
                if (($('#monto_prestamo').val() != '' && $('#monto_prestamo').val() > 0) && ($(
                        '#total_quincenas').val() != '' && $('#total_quincenas').val() > 0)) {
                    totalcapint = 0;
                    totalinteres = 0;
                    interesid = 0;

                    totalcapint2 = 0;
                    totalinteres2 = 0;
                    interesid2 = 0;

                    $('#prestamo').text(formatToCurrency($('#monto_prestamo').val()));
                    $('#quincenas').text($('#total_quincenas').val());

                    tablainteresuno();
                    tablainteresdos();

                    tablainteresuno2();
                    tablainteresdos2();
                    generaFecha($('#fecha_primer_pago').val(), $('#total_quincenas').val());
                    pagoQuincenal();
                }

                // obtengo el monto de SALDO SOCIO
                var prestamo_intereses = $('#prestamo_intereses').val();
                var monto_socio = $('#monto_socio').val();
                var saldo_socio = $('#saldo_socio').val();

                if (parseFloat(prestamo_intereses) <= parseFloat(monto_socio)) {
                    saldo_socio = parseFloat(prestamo_intereses);
                    $('#saldo_socio').val(saldo_socio);
                } else if (parseFloat(prestamo_intereses) > parseFloat(monto_socio)) {
                    saldo_socio = parseFloat(monto_socio);
                    $('#saldo_socio').val(saldo_socio);
                }
            });

            // Evitar entrada de datos en el campo al escribir
            var inputPagoQuincenal = $('#pago_quincenal');
            inputPagoQuincenal.on('keydown', function(e) {
                e.preventDefault();
            });
            // Evitar entrada de datos en el campo al escribir
            var inputPrestamosIntereses = $('#prestamo_intereses');
            inputPrestamosIntereses.on('keydown',
                function(e) {
                    e.preventDefault();
                });

            // CALENDARIO
            const datepickerTranslated = document.querySelector('.datepicker-translated');
            const filterFunction = (date) => {
                const dayOfMonth = date.getDate();
                const lastDayOfMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();

                // Permite la selección solo si es el día 15 o el último día del mes
                return dayOfMonth === 15 || dayOfMonth === lastDayOfMonth;
            }

            const myDatepicker = new mdb.Datepicker(datepickerTranslated, {
                confirmDateOnSelect: true,
                disablePast: true,
                title: 'Seleccione la fecha del primer pago',
                monthsFull: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto',
                    'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ],
                monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov',
                    'Dic'
                ],
                weekdaysFull: ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag',
                    'Samstag'
                ],
                weekdaysShort: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                weekdaysNarrow: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
                okBtnText: 'Ok',
                clearBtnText: 'Limpiar',
                cancelBtnText: 'Cancelar',
                filter: filterFunction
            });

            function generaFecha(fecha, plazo) {
                if (!fecha) {
                    $('#tabla-interesdos tbody tr').each(function(index) {
                        $(this).find('td:nth-child(6)').text('-');
                    });
                    $('#tabla-interesdos2 tbody tr').each(function(index) {
                        $(this).find('td:nth-child(6)').text('-');
                    });
                    return;
                }

                var fechaInicialStr = fecha; //$("#fechaInput").val();
                var plazos = parseInt(plazo);

                // Parsear la fecha inicial en formato dd/mm/yyyy
                var fechaInicialArray = fechaInicialStr.split('/');
                var dia = parseInt(fechaInicialArray[0]);
                var mes = parseInt(fechaInicialArray[1]) -
                    1; // Restar 1 al mes porque en JavaScript los meses son 0-indexados
                var anio = parseInt(fechaInicialArray[2]);

                var fecha = new Date(anio, mes, dia);

                // Agregar la fecha de entrada como el primer elemento
                var diaEntrada = fecha.getDate();
                var mesEntrada = fecha.getMonth() + 1; // Sumar 1 al mes para mostrarlo correctamente
                var anioEntrada = fecha.getFullYear();
                var fechaFormateadaEntrada = diaEntrada.toString().padStart(2, '0') + '/' + mesEntrada.toString()
                    .padStart(2, '0') + '/' + anioEntrada;

                var fechaActual = new Date(fecha);
                var fechasGeneradas = [fechaFormateadaEntrada];
                for (var i = 1; i < plazos; i++) {
                    var siguienteFecha = new Date(fechaActual);

                    if (siguienteFecha.getDate() === 15) {
                        // Si la fecha actual es el día 15, avanzamos a fin de mes
                        siguienteFecha.setMonth(siguienteFecha.getMonth() + 1, 0); // Ir al último día del mes
                    } else {
                        // Si no, avanzamos 15 días
                        siguienteFecha.setDate(siguienteFecha.getDate() + 15);
                    }

                    // Mostrar la fecha
                    fechaActual = siguienteFecha;
                    var dia = siguienteFecha.getDate();
                    var mes = siguienteFecha.getMonth() + 1; // Sumar 1 al mes para mostrarlo correctamente
                    var anio = siguienteFecha.getFullYear();
                    var fechaFormateada = dia.toString().padStart(2, '0') + '/' + mes.toString().padStart(2, '0') +
                        '/' + anio;
                    fechasGeneradas.push(fechaFormateada);
                }
                // Luego, selecciona las celdas de la columna "Fecha de pago" y asigna las fechas correspondientes
                $('#tabla-interesdos tbody tr').each(function(index) {
                    $(this).find('td:nth-child(6)').text(fechasGeneradas[index]);
                });

                $('#tabla-interesdos2 tbody tr').each(function(index) {
                    $(this).find('td:nth-child(6)').text(fechasGeneradas[index]);
                });
            }

            // FUNCION PARA OBTENER LOS SOCIOS
            /*function socios(id, tipo, accion) {
                //console.log('aval id: ' + id);
                $.ajax({
                    url: "{{ route('all.socios.prestamo') }}",
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
                        url: "{{ route('all.socios.prestamo') }}", // Ruta de tu controlador
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

            // funcion para generar la tabla intereses
            function round(d) {
                var dAbs = Math.abs(d);
                var i = parseInt(dAbs);
                var result = dAbs - i;
                if (result < 0.001) {
                    return d < 0 ? -i : i;
                } else {
                    return d < 0 ? -(i + 1) : i + 1;
                }
            }

            // funcion para generar la tabla intereses
            function round1(d) {
                var dAbs = Math.abs(d);
                var i = parseInt(dAbs);
                var result = dAbs - i;
                if (result < 0.5) {
                    return d < 0 ? -i : i;
                } else {
                    return d < 0 ? -(i + 1) : i + 1;
                }
            }

            // GENRA LA PRIMERA TABLA DE LOS INTERESES
            function tablainteresuno() {
                $('#tabla-interesuno tbody').empty();
                var montoprest = 0,
                    mesesprest = 0,
                    pagomonto = 0,
                    interes = 0;
                var pagomontored = 0,
                    interesred = 0,
                    capinter = 0,
                    saldofinal = 0;
                var a = 0;

                montoprest = parseFloat($('#monto_prestamo').val());
                mesesprest = parseInt($('#total_quincenas').val());
                pagomonto = montoprest / mesesprest;

                for (var i = 1; i <= mesesprest; i++) {
                    if (montoprest >= pagomonto) {
                        pagomontored = round(pagomonto);
                        a = 1;
                    } else {
                        pagomontored = round(montoprest);
                    }

                    interes = (montoprest / 100) * 1.5; // TASA DE INTERES
                    interesred = round1(interes);
                    capinter = pagomontored + interesred;
                    totalinteres = totalinteres + interesred;
                    totalcapint = totalcapint + capinter;
                    saldofinal = parseInt(montoprest) - pagomontored;
                    montoprest = saldofinal;

                    var rowData = "<tr><td>" + i + "</td><td>" + pagomontored + "</td><td>" + interesred +
                        "</td><td>" + (pagomontored + interesred) + "</td><td>" + saldofinal + "</td></tr>";
                    $("#tabla-interesuno tbody").append(rowData);
                }
            }

            function tablainteresdos() {
                $('#tabla-interesdos tbody').empty();
                var montoprest = 0,
                    mesesprest = 0,
                    pagouno = 0,
                    pagouno1 = 0,
                    capital = 0,
                    saldofinald = 0;

                montoprest = parseFloat($('#monto_prestamo').val());
                mesesprest = parseInt($('#total_quincenas').val())
                var primerIteracion = true;
                var valorColumna4 = 0; // Variable para almacenar el valor de la columna 4
                var valorAnteriorSaldofinald = 0;
                var capitalAnterior = 0;

                for (i = 1; i <= mesesprest; i++) {
                    if (i == 1) {
                        pagouno1 = totalcapint / mesesprest;
                        var pagounost = pagouno1.toFixed(1);
                        pagouno = parseFloat(pagounost);
                        var primerElemento = $("#tabla-interesuno tbody tr").first();
                        var interesado = primerElemento.find("td").eq(2).text();
                        var interesValor = parseFloat(interesado);
                        interesid += interesValor;
                        capital = (totalcapint / mesesprest) - parseFloat(primerElemento.find("td").eq(2)
                            .text());
                        saldofinald = montoprest - capital;
                        // creamos la tabla
                        var newRow = "<tr><td>" + i + "</td><td>" + formatToCurrency(capital.toFixed(1)) +
                            "</td><td>" +
                            formatToCurrency(interesado) +
                            "</td><td>" + formatToCurrency(pagouno.toFixed(1)) + "</td><td>" + formatToCurrency(
                                saldofinald.toFixed(1)) +
                            "</td><td></td></tr>";
                        $("#tabla-interesdos tbody").append(newRow);
                        capital = pagouno - interesid;
                        valorColumna4 = saldofinald.toFixed(1);
                    } else if (primerIteracion) {
                        var interesid = 0;
                        var contador = 2; // Empieza desde 2
                        $("#tabla-interesuno tbody tr").slice(1).each(function() {
                            var interesado = $(this).find("td").eq(2).text();
                            var interesValor = parseFloat(interesado);
                            interesid += interesValor;
                            capital = (totalcapint / mesesprest) - $(this).find("td").eq(2).text();
                            if (contador == 2) {
                                saldofinald = valorColumna4 - capital;
                                valorAnteriorSaldofinald = saldofinald;
                            } else {
                                saldofinald = valorAnteriorSaldofinald.toFixed(1) - capital.toFixed(
                                    1);
                                if (contador < mesesprest) {
                                    capital = (totalcapint / mesesprest) - $(this).find("td").eq(2)
                                        .text();
                                } else if (contador == mesesprest) {
                                    capital = valorAnteriorSaldofinald;
                                    pagouno = parseFloat(capital) + parseFloat(interesado);
                                    saldofinald = 0;
                                }
                            }
                            // creamos la tabla
                            var newRow = "<tr><td>" + contador + "</td><td>" + formatToCurrency(capital
                                    .toFixed(1)) +
                                "</td><td>" + formatToCurrency(interesado) +
                                "</td><td>" + formatToCurrency(pagouno.toFixed(1)) + "</td><td>" +
                                formatToCurrency(saldofinald
                                    .toFixed(1)) +
                                "</td><td></td></tr>";
                            $("#tabla-interesdos tbody").append(newRow);
                            valorAnteriorSaldofinald = saldofinald;
                            capitalAnterior = capital.toFixed(1);
                            contador++;
                        });
                        primerIteracion = false;
                    }
                }
                // muestra el total a descontar por quincena
                $('#pago_quincenal').val(pagouno.toFixed(1)).focus();
                $('#pagoQuincenal').text(formatToCurrency(pagouno.toFixed(1)));
                // muestra el monto total del prestamo + intereses
                var prestamoIntereses = montoprest + totalinteres;
                var disponibleAval = $('#disponible_aval').val();
                var maxPrestamo = parseFloat(disponibleAval.replace(/[^0-9.-]+/g, ""));
                $('#monto_prestamos').val(prestamoIntereses.toFixed(1));
                $('#prestamo_intereses').val(prestamoIntereses.toFixed(1)).focus();
                $('#pres_interes').text(formatToCurrency(prestamoIntereses.toFixed(1)));
                $('#prestamo_intereses').attr('max', maxPrestamo.toFixed(1));
                $('#total_quincenas').focus();
            }

            // GENRA TABLA INTERESES MEDIA
            function tablainteresuno2() {
                $('#tabla-interesuno2 tbody').empty();
                var montoprest = 0,
                    mesesprest = 0,
                    pagomonto = 0,
                    interes = 0;
                var pagomontored = 0,
                    interesred = 0,
                    capinter = 0,
                    saldofinal = 0;
                var a = 0;

                montoprest = parseFloat($('#monto_prestamo').val());
                mesesprest = parseInt($('#total_quincenas').val());
                pagomonto = montoprest / mesesprest;

                for (var i = 1; i <= mesesprest; i++) {
                    if (montoprest >= pagomonto) {
                        pagomontored = round(pagomonto);
                        a = 1;
                    } else {
                        pagomontored = round(montoprest);
                    }

                    interes = (montoprest / 100) * 1.5; // TASA DE INTERES
                    interesred = round1(interes);
                    capinter = pagomontored + interesred;
                    totalinteres2 = totalinteres2 + interesred;
                    totalcapint2 = totalcapint2 + capinter;
                    saldofinal = parseInt(montoprest) - pagomontored;
                    montoprest = saldofinal;

                    var rowData = "<tr><td>" + i + "</td><td>" + pagomontored + "</td><td>" + interesred +
                        "</td><td>" + (pagomontored + interesred) + "</td><td>" + saldofinal + "</td></tr>";
                    $("#tabla-interesuno2 tbody").append(rowData);
                }
            }

            function tablainteresdos2() {
                $('#tabla-interesdos2 tbody').empty();
                var montoprest = 0,
                    mesesprest = 0,
                    pagouno = 0,
                    pagouno1 = 0,
                    capital = 0,
                    saldofinald = 0;

                montoprest = parseFloat($('#monto_prestamo').val());
                mesesprest = parseInt($('#total_quincenas').val())
                var primerIteracion = true;
                var valorColumna4 = 0; // Variable para almacenar el valor de la columna 4
                var valorAnteriorSaldofinald = 0;
                var capitalAnterior = 0;

                for (i = 1; i <= mesesprest; i++) {
                    if (i == 1) {
                        pagouno1 = totalcapint2 / mesesprest;
                        var pagounost = pagouno1.toFixed(1);
                        pagouno = parseFloat(pagounost);
                        var primerElemento = $("#tabla-interesuno2 tbody tr").first();
                        var interesado = primerElemento.find("td").eq(2).text();
                        var interesValor = parseFloat(interesado);
                        interesid2 += interesValor;
                        capital = (totalcapint2 / mesesprest) - parseFloat(primerElemento.find("td").eq(2)
                            .text());
                        saldofinald = montoprest - capital;
                        // creamos la tabla
                        var newRow = "<tr><td>" + i + "</td><td>" + formatToCurrency(capital.toFixed(1)) +
                            "</td><td>" +
                            formatToCurrency(interesado) +
                            "</td><td>" + formatToCurrency(pagouno.toFixed(1)) + "</td><td>" + formatToCurrency(
                                saldofinald.toFixed(1)) +
                            "</td><td></td></tr>";
                        $("#tabla-interesdos2 tbody").append(newRow);
                        capital = pagouno - interesid2;
                        valorColumna4 = saldofinald.toFixed(1);
                    } else if (primerIteracion) {
                        var interesid2 = 0;
                        var contador = 2; // Empieza desde 2
                        $("#tabla-interesuno2 tbody tr").slice(1).each(function() {
                            var interesado = $(this).find("td").eq(2).text();
                            var interesValor = parseFloat(interesado);
                            interesid2 += interesValor;
                            capital = (totalcapint2 / mesesprest) - $(this).find("td").eq(2).text();
                            if (contador == 2) {
                                saldofinald = valorColumna4 - capital;
                                valorAnteriorSaldofinald = saldofinald;
                            } else {
                                saldofinald = valorAnteriorSaldofinald.toFixed(1) - capital.toFixed(
                                    1);
                                if (contador < mesesprest) {
                                    capital = (totalcapint2 / mesesprest) - $(this).find("td").eq(2)
                                        .text();
                                } else if (contador == mesesprest) {
                                    capital = valorAnteriorSaldofinald;
                                    pagouno = parseFloat(capital) + parseFloat(interesado);
                                    saldofinald = 0;
                                }
                            }
                            // creamos la tabla
                            var newRow = "<tr><td>" + contador + "</td><td>" + formatToCurrency(capital
                                    .toFixed(1)) +
                                "</td><td>" + formatToCurrency(interesado) +
                                "</td><td>" + formatToCurrency(pagouno.toFixed(1)) + "</td><td>" +
                                formatToCurrency(saldofinald
                                    .toFixed(1)) +
                                "</td><td></td></tr>";
                            $("#tabla-interesdos2 tbody").append(newRow);
                            valorAnteriorSaldofinald = saldofinald;
                            capitalAnterior = capital.toFixed(1);
                            contador++;
                        });
                        primerIteracion = false;
                    }
                }
            }

            // recorro la tabla para obtener el pago quincenal, es el de mayor importe
            function pagoQuincenal() {                
                // OBTENGO EL DESCUENTO MÁS REPETIDO
                var ocurrencias = {}; // Objeto para almacenar las ocurrencias

                // Recorre las filas de la tabla
                $("#tabla-interesdos tbody tr").each(function() {
                    var valor = $(this).find("td:eq(3)").text(); // Obtén el valor de la columna 3 (índice 2)

                    // Actualiza las ocurrencias en el objeto
                    if (ocurrencias[valor]) {
                        ocurrencias[valor]++;
                    } else {
                        ocurrencias[valor] = 1;
                    }
                });

                // Encuentra el valor que se repite más
                var valorMasRepetido = "";
                var maxRepeticiones = 0;

                for (var valor in ocurrencias) {
                    if (ocurrencias[valor] > maxRepeticiones) {
                        valorMasRepetido = valor;
                        maxRepeticiones = ocurrencias[valor];
                    }
                }
                var descuento = parseFloat(valorMasRepetido.replace(/\$/g, "").replace(",", ""));
                $('#pago_quincenal').val(descuento.toFixed(1)).focus();
                $('#pagoQuincenal').text(formatToCurrency(descuento.toFixed(1)));
            }

            // Función para calcular los intereses
            function calcularIntereses(monto) {
                return Math.round(monto * 0.015);
            }

            // FORMATO MONEDA
            function formatToCurrency(data) {
                return '$' + Intl.NumberFormat('es-MX', {
                    minimumFractionDigits: 2,
                }).format(data);
            }

            function formatToCurrency2(data) {
                return '$' + Intl.NumberFormat('es-MX', {
                    minimumFractionDigits: 2,
                    useGrouping: true, // Agrega comas como separadores de miles
                }).format(data);
            }

        });
    </script>
@stop
