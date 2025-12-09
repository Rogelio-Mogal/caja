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
<meta name="csrf-token" content="{{ csrf_token() }}">
@csrf
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">NUEVO PRESTAMO POR SALUD/ENFERMEDAD</h3>
    </div>
    <div class="card-body">
        <div class="register-box-body">
            <div class="card border border-success shadow-0 mb-3">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <h4>DETALLE DE SOCIO PARA PRÉSTAMO</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body text-dark">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    <span tabindex="1" data-mdb-toggle="tooltip" title="SOCIO">
                                        {{ Form::hidden('hidde', null) }}
                                        <select id="socios_id" name="socios_id" class="form-control select2" style="width: 100%;" required>
                                        </select>
                                        <label class="form-label select-label form-control-lg"
                                            for="socios_id">&nbsp;</label>
                                    </span>
                                    <div class="form-helper" id="socio_feedback" style="color: red; display: none;">Este
                                        campo es requerido.</div>
                                </div>
                                @error('socios_id')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::hidden('hidde', null) }}
                                    <span tabindex="2" data-mdb-toggle="tooltip" title="MONTO PRESTAMO">
                                        <div class="form-outline">
                                            {{ Form::text('pp_display', old('monto_prestamo',null), ['id' => 'pp_display', 'oninput' => 'formatNumber(this)', 'onblur' => 'fixDecimals(this)', 'class' => 'form-control uppercase generaIntereses generaTotalAvalar', 'placeholder' => 'MONTO PRESTAMO', 'required']) }}
                                            {{ Form::hidden('monto_prestamo', null, ['id' => 'monto_prestamo', 'class' => 'form-control uppercase generaIntereses generaTotalAvalar', 'placeholder' => 'MONTO PRESTAMO']) }}
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
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="col mb-3">
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

                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::hidden('hidde', null) }}
                                    <span tabindex="2" data-mdb-toggle="tooltip" title="PAGOS QUINCENALES">
                                        <div class="form-outline">
                                            {{ Form::text('pago_quincenal', null, ['id' => 'pago_quincenal', 'class' => 'form-control uppercase', 'placeholder' => 'PAGOS QUINCENALES', 'step' => 'any', 'required']) }}
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
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::hidden('hidde', null) }}
                                    <span tabindex="1" data-mdb-toggle="tooltip" title="PRESTAMO + INTERESES">
                                        <div class="form-outline">
                                            {{ Form::text('prestamo_intereses', null, ['id' => 'prestamo_intereses', 'name' => 'prestamo_intereses', 'class' => 'form-control uppercase', 'placeholder' => 'PRESTAMO + INTERESES', 'step' => 'any', 'required']) }}
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
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-outline datepicker-translated" data-mdb-toggle-button="false">
                                    {{ Form::text('fecha_primer_pago', null, ['id' => 'fecha_primer_pago', 'name' => 'fecha_primer_pago', 'data-mdb-toggle' => 'datepicker', 'class' => 'form-control', 'placeholder' => 'FECHA DEL PRIMER PAGO']) }}
                                    <label for="fecha_primer_pago" class="form-label">FECHA DEL PRIMER PAGO</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-12">
                            <div class="col mb-3">
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
                        <div class="col-lg-3 col-md-3 col-sm-12">
                            <div class="col mb-3">
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
                        <div class="col-lg-3 col-md-3 col-sm-12">
                            <div class="col mb-3">
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
                        <div class="col-lg-3 col-md-3 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    <span tabindex="6" data-mdb-toggle="tooltip" title="RFC">
                                        <input class="form-control" id="rfc" type="text" value="RFC"
                                            aria-label="readonly input form-control-lg" readonly />
                                        <label class="form-label form-control-lg" for="rfc">RFC</label>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-12">
                            <div class="col mb-3">
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
                        <div class="col-lg-3 col-md-3 col-sm-12">
                            <div class="col mb-3">
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
                        <div class="col-lg-3 col-md-3 col-sm-12">
                            <div class="col mb-3">
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
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="copia_talon" id="copia_talon"
                                        {{ old('copia_talon', optional($prestamo)->documentacion['copia_talon'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="copia_talon">Copia talón</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="copia_ine" id="copia_ine"
                                        {{ old('copia_ine', optional($prestamo)->documentacion['copia_ine'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="copia_ine">Copia INE</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="credencial_socio" id="credencial_socio"
                                        {{ old('credencial_socio', optional($prestamo)->documentacion['credencial_socio'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="credencial_socio">Copia credencial de socio</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="pagare" id="pagare"
                                        {{ old('pagare', optional($prestamo)->documentacion['pagare'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pagare">Pagaré</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="solicitud" id="solicitud"
                                        {{ old('solicitud', optional($prestamo)->documentacion['solicitud'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="solicitud">Solicitud</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border border-info shadow-0 mb-3">
                <div class="card-header">
                    <h4>GENERAR TABLA DE INTERESES</h4>
                </div>
                <div class="card-body text-dark">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12" style="display: none">
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
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card-body table-responsive p-0">
                                <table id="tabla-interesdos" class="table responsive nowrap">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Pago</th>
                                            <th>Capital</th>
                                            <th>Interés</th>
                                            <th>Descuento</th>
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
                    </div>
                </div>
            </div>

            <div class="card border border-primary shadow-0 mb-3">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-lg-4 col-md-12">
                            <h4 class="card-title">BUSCAR AVAL</h4>
                        </div>
                        <div class="col-lg-4 col-md-2">
                            <h4 class="card-title">TOTAL PARA AVALAR:</h4>
                            <input type="hidden" id="total_avalar_input" name="total_avalar_input" value="0">
                            <div id="total_avalar_text"></div>
                        </div>
                        <div class="col-lg-4 col-md-2">
                            <h4 class="card-title">TOTAL FALTANTE POR AVALAR:</h4>
                            <input type="hidden" id="total_faltante_avalar_input" name="total_faltante_avalar_input"
                                value="0">
                            <input type="hidden" id="suma_faltante_avalar" name="suma_faltante_avalar"
                                value="0">
                            <div id="total_faltane_avalar_text"></div>
                        </div>
                    </div>
                </div>
                <div class="card-body text-dark">
                    <div
                        class="d-flex justify-content-center row row-cols-lg-auto mb-2 align-items-center  p-2 mx-4 my-1">
                        <div class="col-lg-5 col-md-5 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::hidden('hidde', null) }}
                                    <span tabindex="10" data-mdb-toggle="tooltip" title="AVALES">
                                        <select class="select mb-2" name="aval_id" id="aval_id"
                                            data-mdb-filter="true" data-mdb-option-height="50">
                                            <option value="" hidden selected>-- Avales --</option>
                                        </select>
                                        <label class="form-label select-label form-control-lg"
                                            for="aval_id">AVALES</label>
                                    </span>
                                </div>
                                @error('aval_id')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    <input class="form-control" id="saldo_disponible_aval" type="text"
                                        value="0" aria-label="readonly input example" readonly />
                                    <label class="form-label" for="saldo_disponible_aval">SALDO DISPONIBLE</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    <input class="form-control" id="is_aval" type="text" value="0"
                                        aria-label="readonly input example" readonly />
                                    <label class="form-label" for="is_aval">HA SIDO AVAL</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <div class="col mb-3">
                                <label for="add_aval">&nbsp;</label>
                                {!! Form::button('AGREGAR', ['type' => 'button', 'class' => 'btn btn-primary', 'id' => 'add_aval', 'disabled']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card-body table-responsive p-0">
                                <table id="tbl_aval" class="table responsive nowrap">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Num Socio</th>
                                            <th>Nombre</th>
                                            <th>Apellido Paterno</th>
                                            <th>Apellido Materno</th>
                                            <th>Rfc</th>
                                            <th>Saldo Disponiblo</th>
                                            <th>Saldo avalar</th>
                                            <th>Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Aquí se agregarán las filas de los registros -->
                                    </tbody>
                                </table>
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
            $(document).ready(function() {
                let totalcapint = 0;
                let totalinteres = 0;
                let interesid = 0;
                let saldoAvalar = false;

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

                (function() {
                    'use strict';
                    // Fetch all the forms we want to apply custom Bootstrap validation styles to
                    const forms = document.querySelectorAll('.needs-validation');

                    // Loop over them and prevent submission
                    Array.prototype.slice.call(forms).forEach((form) => {
                        form.addEventListener('submit', (event) => {
                            if (!form.checkValidity()) {
                                event.preventDefault();
                                event.stopPropagation();

                                // Mostrar los mensajes de validación nativos en los campos de entrada
                                const inputs = form.querySelectorAll(
                                    'input, select, textarea');
                                Array.prototype.slice.call(inputs).forEach((input) => {
                                    const feedback = input.parentNode
                                        .querySelector(
                                            '.invalid-feedback');
                                    if (input.validity.valueMissing) {
                                        if (input.nodeName.toLowerCase() ===
                                            'select') {
                                            const select = input;
                                            if (select.value === '-1') {
                                                console.log(
                                                    'puedo hacer esto 2');
                                                feedback.textContent =
                                                    'Por favor, selecciona una opción.';
                                            } else {
                                                feedback.textContent =
                                                    'Please provide a valid input.';
                                            }
                                        } else {
                                            feedback.textContent =
                                                'Este campo es requerido.';
                                        }
                                    }
                                    if (input.validity.valueMissing) {
                                        feedback.textContent =
                                            'Este campo es requerido.';
                                    } else if (input.validity.typeMismatch) {
                                        feedback.textContent =
                                            'Por favor, introduce un valor válido.';
                                    } else if (input.validity.tooShort) {
                                        feedback.textContent =
                                            'El valor es demasiado corto.';
                                    } else if (input.validity.rangeUnderflow) {
                                        feedback.textContent =
                                            'El valor es demasiado pequeño.';
                                    } else if (input.validity.rangeOverflow) {
                                        feedback.textContent =
                                            'El valor es demasiado grande.';
                                    } else if (input.validity.stepMismatch) {
                                        feedback.textContent =
                                            'Por favor, ajusta el valor según el paso.';
                                    }
                                    else if (input.nodeName.toLowerCase() ===
                                        'select' &&
                                        input.validity.valueMissing) {
                                        //console.log('entro');
                                        feedback.textContent =
                                            'Por favor, selecciona una opción.';
                                    }
                                });
                            }
                            form.classList.add('was-validated');
                            if ($('#socios_id').val() === '-1') {
                                $('#socio_feedback').show();
                            } else {
                                $('#socio_feedback').hide();
                            }
                        }, false);
                    });
                })();


                // SWEET ALERT
                var submitBtn = document.getElementById('submitBtn');
                var form = submitBtn.form;

                // Agrega un evento click al botón de envío
                submitBtn.addEventListener('click', function(event) {
                    // Prevenir el envío del formulario por defecto
                    if (form.checkValidity()) {
                        event.preventDefault();
                        //var disponibleSocio = $('#disponible_socio').val();
                        var montoSocio = $('#monto_socio').val();
                        var saldoSocio = $('#saldo_socio').val();
                        var prestamoIntereses = $('#monto_prestamo')
                    .val(); //$('#prestamo_intereses').val();

                        //var montoSocio = parseFloat(disponibleSocio.replace(/[^0-9.-]+/g,""));
                        var montoPrestamo = $('#monto_prestamo').val();
                        //const tablaAval = document.querySelector('#tbl_aval');
                        var faltanteAval = $('#total_faltane_avalar_text').val();
                        //var totalFaltanteAval = parseFloat(faltanteAval.replace(/[^0-9.-]+/g,""));
                        var totalFaltanteAval = $('#total_faltante_avalar_input').val();
                        var sumaAvales = $('#suma_faltante_avalar').val();
                        var totalFaltanteAvalar = $('#total_faltante_avalar_input').val();
                        var socio_num_prestamo = $('#numero_prestamos').val();

                        var apoyoAdicional = 0;

                        //console.log('faltanteAval :' + faltanteAval);
                        //console.log('montoSocio :' + montoSocio);
                        //console.log('saldoSocio :' + saldoSocio);
                        //console.log('tabla: ' + tablaAval.rows.length);
                        $('#socio_feedback').hide();
                        switch (true) {
                            case $('#socios_id').val() === '-1':
                                console.log('case 00');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'No ha seleccionado un socio',
                                    text: 'Por favor elija una opción.',
                                });
                                break;
                            case $('#fecha_primer_pago').val() === '':
                                console.log('case 0');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'La fecha del primer pago es requerido',
                                    text: 'Por favor elija una fecha.',
                                });
                                break;
                            case $('#socios_id').val() === '-1' && apoyoAdicional == 0:
                                console.log('case 1');
                                $('#socio_feedback').show();
                                break;
                            case totalFaltanteAval > 0 && apoyoAdicional == 0:
                                console.log('case 2');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'El prestamo excede su saldo disponible',
                                    text: 'Por favor elija un aval para obtener su prestamo.',
                                });
                                break;
                            case parseFloat(saldoSocio) > parseFloat(prestamoIntereses) &&
                            apoyoAdicional == 0:
                                console.log('case 3');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'El SALDO SOCIO debe ser igual a la cantidad de PRESTAMO + INTERESES',
                                    text: 'Por favor verifique la información.',
                                });
                                break;
                                /*case parseFloat(saldoSocio) == parseFloat(prestamoIntereses) && apoyoAdicional == 0:
                                    console.log('case 4');
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'No es necesario incluir un aval',
                                        text: 'Por favor quite a los avales.',
                                    });
                                    break;*/
                            case totalFaltanteAvalar < 0 && apoyoAdicional == 0:
                                console.log('case 5');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'El monto del aval excede el faltante por avalar',
                                    text: 'Por favor verifique la información.',
                                });
                                break;
                            case totalFaltanteAvalar > 0 && apoyoAdicional == 0:
                                console.log('case 6');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'El monto del aval no cubre el faltante por avalar',
                                    text: 'Por favor verifique la información.',
                                });
                                break;
                                /*case socio_num_prestamo == 3 && apoyoAdicional == 0:
                                    console.log('case 7');
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'El socio cuenta con 3 prestamos.',
                                        text: 'Por favor verifique la información.',
                                    });
                                    break;*/



                            default:
                                $("#submitBtn").attr("disabled", true);
                                document.getElementById('form_prestamo_enfermedad').submit();
                                $("#submitBtn").attr("disabled", true);
                                console.log('enviaria el formulario');
                                break;
                        }
                    } else {
                        // Si el formulario no es válido, no prevengas el envío y deja que la validación nativa lo maneje
                    }

                });

                // CALENDARIO
                const datepickerTranslated = document.querySelector('.datepicker-translated');
                const filterFunction = (date) => {
                    const dayOfMonth = date.getDate();
                    const lastDayOfMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0)
                    .getDate();

                    // Permite la selección solo si es el día 15 o el último día del mes
                    return dayOfMonth === 15 || dayOfMonth === lastDayOfMonth;
                }
                new mdb.Datepicker(datepickerTranslated, {
                    confirmDateOnSelect: true,
                    disablePast: true,
                    title: 'Seleccione la fecha del primer pago',
                    monthsFull: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio',
                        'Agosto',
                        'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                    ],
                    monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep',
                        'Oct', 'Nov',
                        'Dic'
                    ],
                    weekdaysFull: ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag',
                        'Freitag',
                        'Samstag'
                    ],
                    weekdaysShort: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                    weekdaysNarrow: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
                    okBtnText: 'Ok',
                    clearBtnText: 'Limpiar',
                    cancelBtnText: 'Cancelar',
                    filter: filterFunction
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
                                $('#numero_prestamos').val(v.numero_prestamos);
                                $('#rfc').val(v.rfc);
                                $('#saldo').val(formatToCurrency(saldo));
                                $('#disponible_socio').val(formatToCurrency(v
                                    .saldo_disponible));
                                //$('#saldo_socio').attr('max', parseFloat(v
                                //    .saldo_disponible));
                                $('#monto_socio').val(v.saldo_disponible);
                                $('#disponible_aval').val(formatToCurrency(v
                                    .saldo_disponible *
                                    2));
                                //$('#monto_prestamo').attr('max', v.saldo_disponible * 2);
                            });
                        },
                        error: function(response) {
                            console.log('error:', JSON.stringify(response));
                        },
                    });

                    // obtengo el monto de SALDO SOCIO
                    var prestamo_intereses = $('#monto_prestamo')
                .val(); // lo cambiamos por monto prestamo $('#prestamo_intereses').val();
                    var disponible_socio = $('#disponible_socio').val();
                    var saldo_socio = $('#saldo_socio').val();

                    if (parseFloat(prestamo_intereses) <= parseFloat(disponible_socio)) {
                        //console.log('1');
                        saldo_socio = parseFloat(prestamo_intereses);
                        $('#saldo_socio').val(saldo_socio);
                    } else if (parseFloat(prestamo_intereses) > parseFloat(disponible_socio)) {
                        //console.log('2');
                        saldo_socio = parseFloat(disponible_socio);
                        $('#saldo_socio').val(saldo_socio);
                    }
                });

                // OBTENGO LOS VALORES DEL CONCEPTO (prestamo_especial)
                $(document).on('change', '#prestamo_especial', function() {
                    var selectedOption = $(this).find("option:selected");
                    var plazos = selectedOption.attr("data-plazos");
                    var precio = selectedOption.val();
                    $('#monto_prestamo').val(precio).change();
                    $('#total_quincenas').val(plazos).change();
                    $('#monto_prestamo').focus();
                    $('#total_quincenas').focus();
                    $('#concepto').val(selectedOption.attr("data-concepto"));
                    $('#conceptoId').val(selectedOption.attr("data-id"));

                    if (precio < 0) {
                        $('#monto_prestamo').val(0).change();
                        $('#total_quincenas').val(0).change();
                        $('#monto_prestamo').focus();
                        $('#total_quincenas').focus();
                        $('#concepto').val('');
                        $('#conceptoId').val(0);
                    }

                    totalcapint = 0;
                    totalinteres = 0;
                    interesid = 0;
                    tablainteresuno();
                    tablainteresdos();
                    pagoQuincenal();
                });

                // SI HAY VALORES EN total prestamo Y plazos, se genera la tabla intereses
                $(document).on('change', '.generaIntereses', function() {
                    if (($('#monto_prestamo').val() != '' && $('#monto_prestamo').val() > 0) && ($(
                            '#total_quincenas').val() != '' && $('#total_quincenas').val() > 0)) {
                        totalcapint = 0;
                        totalinteres = 0;
                        interesid = 0;
                        tablainteresuno();
                        tablainteresdos();
                        pagoQuincenal();
                    }

                    // obtengo el monto de SALDO SOCIO
                    var prestamo_intereses = $('#monto_prestamo')
                .val(); // lo cambiamos por monto prestamo $('#prestamo_intereses').val();$('#prestamo_intereses').val();
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

                // FUNCION PARA OBTENER LOS SOCIOS
                /*function socios(id, tipo, accion) {
                    //console.log('aval id: ' + id);
                    $.ajax({
                        url: "{{ route('all.socios.prestamo.especial') }}",
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
                                    'data-mdb-secondary-text': 'RFC: ' + socio
                                        .rfc +
                                        '. CUIP: ' + socio.cuip,
                                });
                                select.append(option);
                            });
                        },
                        error: function(response) {
                            console.log('error:', JSON.stringify(response));
                        },
                    });
                }*/

                function socios(id) {
                    $('#socios_id').select2({
                        placeholder: '-- Socios--',
                        allowClear: true,
                        minimumInputLength: 3,
                        width: '100%',
                        ajax: {
                            url: "{{ route('all.socios.prestamo.especial') }}", // Ruta de tu controlador
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

                        interes = (montoprest / 100) * 1.5; // TASA DE INTERES CAMBIAR A 0 PARA QUITAR LOS INTEWRESES
                        interesred = round1(interes);
                        capinter = pagomontored + interesred;
                        totalinteres = totalinteres + interesred;
                        totalcapint = totalcapint + capinter;
                        saldofinal = parseInt(montoprest) - pagomontored;
                        montoprest = saldofinal;

                        var rowData = "<tr><td>" + i + "</td><td>" + pagomontored + "</td><td>" +
                            interesred +
                            "</td><td>" + (pagomontored + interesred) + "</td><td>" + saldofinal +
                            "</td></tr>";
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
                            capital = (totalcapint / mesesprest) - parseFloat(primerElemento.find("td").eq(
                                    2)
                                .text());
                            saldofinald = montoprest - capital;
                            // creamos la tabla
                            var newRow = "<tr><td>" + i + "</td><td>" + formatToCurrency(capital.toFixed(
                                1)) +
                                "</td><td>" +
                                formatToCurrency(interesado) +
                                "</td><td>" + formatToCurrency(pagouno.toFixed(1)) + "</td><td>" +
                                formatToCurrency(
                                    saldofinald.toFixed(1)) +
                                "</td></tr>";
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
                                capital = (totalcapint / mesesprest) - $(this).find("td").eq(2)
                                    .text();
                                if (contador == 2) {
                                    saldofinald = valorColumna4 - capital;
                                    valorAnteriorSaldofinald = saldofinald;
                                } else {
                                    saldofinald = valorAnteriorSaldofinald.toFixed(1) - capital
                                        .toFixed(
                                            1);
                                    if (contador < mesesprest) {
                                        capital = (totalcapint / mesesprest) - $(this).find("td")
                                            .eq(2)
                                            .text();
                                    } else if (contador == mesesprest) {
                                        capital = valorAnteriorSaldofinald;
                                        pagouno = parseFloat(capital) + parseFloat(interesado);
                                        saldofinald = 0;
                                    }
                                }
                                // creamos la tabla
                                var newRow = "<tr><td>" + contador + "</td><td>" + formatToCurrency(
                                        capital
                                        .toFixed(1)) +
                                    "</td><td>" + formatToCurrency(interesado) +
                                    "</td><td>" + formatToCurrency(pagouno.toFixed(1)) +
                                    "</td><td>" +
                                    formatToCurrency(saldofinald
                                        .toFixed(1)) +
                                    "</td></tr>";
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
                    // muestra el monto total del prestamo + intereses
                    var prestamoIntereses = montoprest + totalinteres;
                    var disponibleAval = $('#disponible_aval').val();
                    var maxPrestamo = parseFloat(disponibleAval.replace(/[^0-9.-]+/g, ""));
                    $('#monto_prestamos').val(prestamoIntereses.toFixed(1));
                    $('#prestamo_intereses').val(prestamoIntereses.toFixed(1)).focus();
                    $('#total_quincenas').focus();
                }

                // recorro la tabla para obtener el pago quincenal, es el de mayor importe
                function pagoQuincenal() {
                    // OBTENGO EL DESCUENTO MÁS REPETIDO
                    var ocurrencias = {}; // Objeto para almacenar las ocurrencias

                    // Recorre las filas de la tabla
                    $("#tabla-interesdos tbody tr").each(function() {
                        var valor = $(this).find("td:eq(3)")
                    .text(); // Obtén el valor de la columna 3 (índice 2)

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
        });
    </script>


@stop
