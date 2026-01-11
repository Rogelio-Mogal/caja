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

    <div class="card-body">
        <div class="register-box-body">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <h5 class="card-title b-0">PAGAR PRÉSTAMO / EDITAR: {{ $socio->nombre_completo }}</h5>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <h5 class="card-title b-0">SALDO SOCIO: ${{ number_format($socio->saldo, 2) }}</h5>
                        <input id="saldo" name="saldo" type="hidden" value="{{ $socio->saldo }}" />
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <h5 class="card-title b-0">SALDO PRÉSTAMO: <samp id="monto_a_saldar">$0</samp></h5>
                        <input id="monto_saldar" name="monto_saldar" type="hidden" value="0" />
                    </div>
                </div>
                <br/>
                <div class="row">

                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="col mb-3">
                            <div class="form-outline">

                                {{ Form::hidden('h_forma_pago') }}

                                {!! Form::select(
                                    'forma_pago',
                                    ['-1' => '- SELECCIONE -'] + array_combine($tipoValues, $tipoValues), // Combina el array para que sea usable en el select
                                    old('forma_pago', optional($pagos->last())->forma_pago ?? '-1'),
                                    [
                                        'class' => 'select mb-2',
                                        'id' => 'forma_pago',
                                        'data-mdb-filter' => 'true',
                                        'tabindex' => '1',
                                    ]
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

                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="col mb-3">
                            <div class="form-outline">

                                {{ Form::hidden('h_metodo_pago') }}

                                {!! Form::select(
                                    'metodo_pago',
                                    [
                                        '-1' => '- MÉTODO DE PAGO -',
                                        'EFECTIVO' => 'EFECTIVO',
                                        'TRANSFERENCIA ELECTRÓNICA' => 'TRANSFERENCIA ELECTRÓNICA',
                                        'DEPÓSITO' => 'DEPÓSITO',
                                        'CHEQUE' => 'CHEQUE',
                                        'TRASLADO DE AHORRO' => 'TRASLADO DE AHORRO',
                                    ],
                                    old('metodo_pago', optional($pagos->last())->metodo_pago ?? '-1'),
                                    ['id' => 'metodo_pago', 'class' => 'select', 'required' => 'true','tabindex' => '3'],
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

                    <div class="col-lg-2 col-md-2 col-sm-6">
                        <div class="col mb-3">
                            <div class="form-outline">
                                {{ Form::date(
                                    'fecha_ultimo_descuento',
                                    optional($prestamo->fecha_pago_reestructuracion)
                                        ? \Carbon\Carbon::parse($prestamo->fecha_pago_reestructuracion)->format('Y-m-d')
                                        : \Carbon\Carbon::now('America/Mexico_City')->format('Y-m-d'),
                                    [
                                        'id' => 'fecha_ultimo_descuento',
                                        'class' => 'form-control',
                                    ]
                                ) }}
                                <label for="fecha_ultimo_descuento" class="form-label">ÚLTIMO DESCUENTO</label>
                            </div>

                            @error('fecha_ultimo_descuento')
                                <p class="error-message text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-6">
                        <div class="col mb-3">
                            <div class="form-outline">
                                {{ Form::text('referencia', old('referencia', $pagos[0]->referencia), ['id' => 'referencia', 'class' => 'form-control uppercase', 'placeholder' => 'Referencia', 'tabindex' => '2']) }}
                                <label class="form-label select-label" for="referencia">REFERENCIA</label>
                                <div class="form-helper" id="sangre_feedback" style="color: red; display: none;">Este
                                    campo es requerido.</div>
                            </div>
                            @error('referencia')
                                <p class="error-message text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <div class="register-box-body">
                    <div class="card border border-success shadow-0 mb-3">
                        <div class="card-header">
                            <h4>PRÉSTAMOS</h4>
                        </div>

                        <meta name="csrf-token" content="{{ csrf_token() }}">
                        @csrf
                        <input id="socios_id" name="socios_id" type="hidden" value="{{ $socio->id }}" />

                        <table id="tabla-prestamos" class="table">
                            <thead>
                                <tr>
                                    <!--
                                    <th scope="col">
                                        Selección
                                         <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select-all">
                                        </div>
                                    </th>-->
                                    <th scope="col">Selección</th>
                                    <th scope="col">Préstamo</th>
                                    <th scope="col">Serie</th>
                                    <th scope="col">Capital</th>
                                    <th scope="col">Descuento</th>
                                    <th scope="col">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider table-divider-color">
                                @forelse($pagos as $row)
                                    <tr>
                                        <th scope="row">
                                            <div class="form-check">
                                                {{-- <input class="form-check-input prestamo-check" type="checkbox"
                                                    name="prestamos_id[]" value="{{ $row->pago_id }}"
                                                    data-debe="{{ $row->capital }}">

                                                <input class="form-check-input prestamo-check"
                                                    type="checkbox"
                                                    name="prestamos_id[]"
                                                    value="{{ $row->pago_id }}"
                                                    data-debe="{{ $row->capital }}"
                                                    data-group="{{ str_replace(' ', '_', $row->numero_prestamo) }}">--}}

                                                <input class="form-check-input prestamo-check"
                                                    type="checkbox"
                                                    name="prestamos_id[]"
                                                    value="{{ $row->pago_id }}"
                                                    data-debe="{{ $row->capital }}"
                                                    data-group="{{ str_replace(' ', '_', $row->numero_prestamo) }}"
                                                    data-serie="{{ $row->serie_pago }}"
                                                    data-fecha="{{ \Carbon\Carbon::parse($row->fecha_tabla)->format('Y-m-d') }}">



                                            </div>
                                        </th>
                                        <td>{{$row->numero_prestamo}}</td>
                                        <td>{{$row->serie_pago}}</td>
                                        {{--<td>$ {{ number_format($row->monto_prestamo, 2) }}</td>--}}
                                        <td>$ {{ number_format($row->capital, 2) }} </td>
                                        <td>$ {{ number_format($row->decuento, 2) }} </td>
                                        <td>{{ \Carbon\Carbon::parse($row->fecha_tabla)->format('d/m/Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">No se encontró ningún registro</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                                {!! Form::button('ACTUALIZAR', ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'submitBtn']) !!}
                            </div>
                        </div>
                        </br>
                    </div>
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

        const inputFecha = document.getElementById('fecha_ultimo_descuento');

        inputFecha.addEventListener('change', function () {
            validarFechaPermitida(this);
            filtrarPorFecha();
        });

        function validarFechaPermitida(input) {
            if (!input.value) return;

            const [y, m, d] = input.value.split('-').map(Number);
            const ultimoDia = new Date(y, m, 0).getDate();

            if (d !== 15 && d !== ultimoDia) {
                alert('Solo se permite el día 15 o el último día del mes');

                // Forzar automáticamente al último día del mes
                input.value = `${y}-${String(m).padStart(2, '0')}-${String(ultimoDia).padStart(2, '0')}`;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            actualizarTotal();
            filtrarPorFecha();
        });

        function actualizarTotal() {
            let total = 0;

            document.querySelectorAll('.prestamo-check').forEach(checkbox => {
                if (checkbox.checked) {
                    total += parseFloat(checkbox.dataset.debe);
                }
            });

            document.getElementById('monto_a_saldar').textContent =
                new Intl.NumberFormat('es-MX', {
                    style: 'currency',
                    currency: 'MXN'
                }).format(total);

            document.getElementById('monto_saldar').value = total.toFixed(2);
        }

        function obtenerSerieMinima(group) {
            let min = null;

            document
                .querySelectorAll(`.prestamo-check[data-group="${group}"]:not(:disabled)`)
                .forEach(cb => {
                    let serie = parseInt(cb.dataset.serie);
                    if (min === null || serie < min) {
                        min = serie;
                    }
            });

            return min;
        }

        function esSeleccionValida(group) {
            let series = [];

            document
                .querySelectorAll(`.prestamo-check[data-group="${group}"]:checked:not(:disabled)`)
                .forEach(cb => {
                    series.push(parseInt(cb.dataset.serie));
                });

            if (series.length === 0) return true;

            series.sort((a, b) => a - b);

            let serieMinima = obtenerSerieMinima(group);

            // ❌ Debe iniciar desde la primera serie pendiente
            if (series[0] !== serieMinima) {
                return false;
            }

            // ❌ Deben ser consecutivas
            for (let i = 1; i < series.length; i++) {
                if (series[i] !== series[i - 1] + 1) {
                    return false;
                }
            }

            return true;
        }

        function haySeleccionInvalida() {
            let invalido = false;

            $('.prestamo-check:checked').each(function () {
                let group = $(this).data('group');

                if (!esSeleccionValida(group)) {
                    invalido = true;
                    return false; // rompe el each
                }
            });

            return invalido;
        }

        function filtrarPorFecha() {
            let fechaSeleccionada = $('#fecha_ultimo_descuento').val();
            if (!fechaSeleccionada) return;

            let fechaBase = new Date(fechaSeleccionada + 'T00:00:00');

            $('.prestamo-check').each(function () {
                let fechaPago = $(this).data('fecha'); // YYYY-MM-DD
                let fechaCheckbox = new Date(fechaPago + 'T00:00:00');

                // ❌ Si el pago es menor o igual a la fecha seleccionada
                if (fechaCheckbox <= fechaBase) {
                    this.checked = false;
                    this.disabled = true;
                } else {
                    this.disabled = false;
                }
            });
            actualizarTotal();
        }

        $(document).ready(function() {

            // INICIOS DE DATATABLES
            let table = $('#tabla-prestamos').DataTable({
                paging: false,
                ordering: false,
                info: false,
                searching: false,

                rowGroup: {
                    dataSrc: 1, // columna "Préstamo"
                    startRender: function (rows, group) {

                        let groupId = group.replace(/\s+/g, '_');

                        return $('<tr/>')
                            .append(`
                                <td colspan="6">
                                    <div class="form-check">
                                        <input type="checkbox"
                                            class="form-check-input select-group"
                                            data-group="${groupId}">
                                        <strong>${group}</strong>
                                    </div>
                                </td>
                            `);
                    }
                },

                columnDefs: [
                    { targets: 1, visible: false }
                ]
            });

            // Seleccionar / deseleccionar por grupo
            $(document).on('change', '.select-group', function () {
                let group = $(this).data('group');
                let checked = this.checked;

                if (!checked) {
                    // Desmarcar todas las válidas
                    $(`.prestamo-check[data-group="${group}"]`).prop('checked', false);
                    actualizarTotal();
                    return;
                }

                let serieMinima = obtenerSerieMinima(group);
                let siguiente = serieMinima;

                $(`.prestamo-check[data-group="${group}"]:not(:disabled)`).each(function () {
                    let serie = parseInt($(this).data('serie'));

                    if (serie === siguiente) {
                        this.checked = true;
                        siguiente++;
                    } else {
                        this.checked = false;
                    }
                });

                actualizarTotal();
            });

            $(document).on('change', '.prestamo-check', function () {
                if (this.disabled) {
                    this.checked = false;
                    return;
                }

                let group = $(this).data('group');

                if (!esSeleccionValida(group)) {
                    this.checked = false;

                    Swal.fire({
                        icon: 'warning',
                        title: 'Selección inválida',
                        text: 'Debe seleccionar los pagos desde la primera serie pendiente y en orden consecutivo.'
                    });

                    return;
                }
                actualizarTotal();
            });

            let totalcapint = 0;
            let totalinteres = 0;
            let interesid = 0;

            let totalcapint2 = 0;
            let totalinteres2 = 0;
            let interesid2 = 0;

            // ACTIVA EL CTRL + P
            //$("#imprimir").click(function() {
            //    window.print();
            //});

            // VALIDAR ANTES DE ENVIAR EL FORMULARIO
            $('#form_pagar_prestamos').on('submit', function(e) {
                e.preventDefault();
                // VALIDACIÓN: series consecutivas
                if (haySeleccionInvalida()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Selección inválida',
                        text: 'Debe seleccionar los pagos desde la primera serie pendiente y en orden consecutivo.'
                    });
                    return;
                }

                let saldo = parseFloat($('#saldo').val());
                let debe = parseFloat($('#monto_saldar').val());
                let fecha = $('#fecha_ultimo_descuento').val();
                let formapago = $('#forma_pago').val();
                let metodopago = $('#metodo_pago').val();
                const FORMA_TRASLADO_AHORRO = 'LIQUIDACIÓN DE PRÉSTAMO - TRASLADO DE AHORRO';
                const formaPagoTexto = $('#forma_pago option:selected').text().trim();

                if (isNaN(saldo) || isNaN(debe) || debe <= 0 ) {
                    //alert("Error: No se pudieron leer los valores de saldo o debe.");
                    console.log('a');
                    Swal.fire({
                        icon: 'error',
                        title: 'No se pudieron leer los valores de saldo o debe.',
                        text: 'Por favor verifique la información.',
                    });
                    e.preventDefault(); // Evita el envío
                    return;
                }

                /*
                if (saldo < debe) {
                    console.log('b');
                    //alert("El saldo no puede ser menor al monto adeudado.");
                    Swal.fire({
                        icon: 'info',
                        title: 'El saldo no puede ser menor al monto adeudado.',
                        text: 'Por favor verifique la información.',
                    });
                    e.preventDefault(); // Evita el envío del formulario
                }*/

                if (formaPagoTexto === 'LIQUIDACIÓN DE PRÉSTAMO - TRASLADO DE AHORRO' && saldo < debe) {
                    console.log('b');
                    Swal.fire({
                        icon: 'info',
                        title: 'Saldo insuficiente',
                        text: 'Para la liquidación por traslado de ahorro, el saldo no puede ser menor al monto adeudado.',
                    });
                    return;
                }

                if(fecha == ''){
                    console.log('c');
                    Swal.fire({
                        icon: 'info',
                        title: 'Mensaje de error.',
                        text: 'Por favor ingrese la fecha del último descuento.',
                    });
                    return;
                }

                if(formapago == -1){
                    console.log('d');
                    Swal.fire({
                        icon: 'info',
                        title: 'Mensaje de error.',
                        text: 'Por favor seleccione una forma de pago.',
                    });
                   return;
                }

                if(metodopago == -1){
                    console.log('d');
                    Swal.fire({
                        icon: 'info',
                        title: 'Mensaje de error.',
                        text: 'Por favor seleccione un método de pago.',
                    });
                    return;
                }

                // ✅ SI TODO ESTÁ BIEN → CONFIRMAR ENVÍO
                const montoFormateado = new Intl.NumberFormat('es-MX', {
                        style: 'currency',
                        currency: 'MXN'
                    }).format(debe);

                const fechaDesk = $('#fecha_ultimo_descuento').val();
                function formatearFecha(fecha) {
                    const partes = fecha.split('-'); // yyyy-mm-dd
                    return `${partes[2]}-${partes[1]}-${partes[0]}`; // dd-mm-yyyy
                }

                const fechaFormateada = formatearFecha(fechaDesk);

                Swal.fire({
                    title: 'Confirmar operación',
                    html: `
                        <p>
                            <b style="font-size: 22px; font-weight: bold;">ÚLTIMO DESCUENTO:</b>
                            <span style="font-size: 22px; font-weight: bold;">
                                ${fechaFormateada}
                            </span>
                        </p>
                        <p><b>Monto a saldar:</b> ${montoFormateado}</p>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, pagar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // 🔥 ENVIAR FORMULARIO
                        this.submit();

                        // Opcional: bloquear botón
                        $('#submitBtn').prop('disabled', true);
                    }
                });
            });

            //SELECT2
            const $formaPago  = $('#forma_pago');
            const $metodoPago = $('#metodo_pago');

            // ⛔ DESHABILITAR select2 desde el inicio
            $metodoPago.prop('disabled', true);
            $metodoPago.val('-1').trigger('change');

            function resetMetodoPago() {
                $metodoPago.val('-1').trigger('change');

                $metodoPago.find('option').prop('disabled', false);
            }

            $formaPago.on('change', function () {

                const valor = $(this).val();

                // ⛔ Si no selecciona nada válido, deshabilitar nuevamente
                if (valor === '-1' || valor === null || valor === '') {
                    $metodoPago.prop('disabled', true);
                    resetMetodoPago();
                    return;
                }

                // ✅ HABILITAR select2
                $metodoPago.prop('disabled', false);
                resetMetodoPago();

                // 1️⃣ REESTRUCTURACIÓN → deshabilita traslado
                if (valor === 'LIQUIDAR PRÉSTAMO - REESTRUCTURACIÓN' || valor === 'LIQUIDAR PRÉSTAMO - PAGO TOTAL') {

                    $metodoPago
                        .find('option[value="TRASLADO DE AHORRO"]')
                        .prop('disabled', true);
                }

                // 2️⃣ TRASLADO DE AHORRO → solo traslado
                if (valor === 'LIQUIDAR PRÉSTAMO - TRASLADO DE AHORRO') {

                    $metodoPago.find('option').each(function () {
                        if (
                            $(this).val() !== 'TRASLADO DE AHORRO' &&
                            $(this).val() !== '-1'
                        ) {
                            $(this).prop('disabled', true);
                        }
                    });
                }

                // 🔄 refrescar Select2
                $metodoPago.trigger('change.select2');
            });
        });
    </script>


@stop
