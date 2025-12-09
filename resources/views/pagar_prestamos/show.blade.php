@extends('layouts.app')

@section('css')
    <style type="text/css">
        @media screen {
            .solo-impresion {
                display: none;
            }
        }

        @media print {
            .no-imprimir {
                display: none;
            }

            .solo-impresion {
                font-family: Arial, sans-serif !important;
                /* Estilo para impresión */
                font-size: 11.5pt !important;
                /* Tamaño de fuente para impresión */
            }

            .font11 {
                font-family: Arial, sans-serif !important;
                /* Estilo para impresión */
                font-size: 11pt !important;
                /* Tamaño de fuente para impresión */
            }

            .nuevaHoja {
                page-break-before: always;
            }
        }

        .border-b {
            border-bottom: 1px solid #000;
            /* Aquí puedes ajustar el grosor y el color de la línea */
        }

        .border-t {
            border-top: 1px solid #000;
            /* Aquí puedes ajustar el grosor y el color de la línea */
        }

        .border-r {
            border-right: 1px solid #000;
            /* Aquí puedes ajustar el grosor y el color de la línea */
        }

        .border-l {
            border-left: 1px solid #000;
            /* Aquí puedes ajustar el grosor y el color de la línea */
        }

        .alto {
            height: 10px;
            /* Define la altura deseada en píxeles u otra unidad de medida */
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
    </style>
@stop

@section('content')

    @php
        use Carbon\Carbon;

        $fechaPrestamo = Carbon::parse($prestamo[0]->fecha_captura);

        // Obtén el año
        $year = $fechaPrestamo->year;

        // Obtén el nombre completo del mes
        $mesEnEspanol = $fechaPrestamo->format('F');

        if ($mesEnEspanol == 'January') {
            $mesEnEspanol = 'Enero';
        }
        if ($mesEnEspanol == 'February') {
            $mesEnEspanol = 'Febrero';
        }
        if ($mesEnEspanol == 'March') {
            $mesEnEspanol = 'Marzo';
        }
        if ($mesEnEspanol == 'April') {
            $mesEnEspanol = 'Abril';
        }
        if ($mesEnEspanol == 'May') {
            $mesEnEspanol = 'Mayo';
        }
        if ($mesEnEspanol == 'June') {
            $mesEnEspanol = 'Junio';
        }
        if ($mesEnEspanol == 'July') {
            $mesEnEspanol = 'Julio';
        }
        if ($mesEnEspanol == 'August') {
            $mesEnEspanol = 'Agosto';
        }
        if ($mesEnEspanol == 'September') {
            $mesEnEspanol = 'Septiembre';
        }
        if ($mesEnEspanol == 'October') {
            $mesEnEspanol = 'Octubre';
        }
        if ($mesEnEspanol == 'November') {
            $mesEnEspanol = 'Noviembre';
        }
        if ($mesEnEspanol == 'December') {
            $mesEnEspanol = 'Diciembre';
        }

        // Obtén el día
        $day = $fechaPrestamo->day;
    @endphp

    <br />
    <form method="post" action="{{ Route('admin.pagar.prestamo.store') }}" id="form_pagar_prestamos" autocomple='off'
        class="needs-validation" novalidate>
        <div class="card card-outline card-primary no-imprimir">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <h4 class="card-title b-0">PAGAR PRÉSTAMO: {{ $socio->nombre_completo }}</h4>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <h4 class="card-title b-0">SALDO SOCIO: ${{ number_format($socio->saldo, 2) }}</h4>
                        <input id="saldo" name="saldo" type="hidden" value="{{ $socio->saldo }}" />
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <h4 class="card-title b-0">SALDO PRÉSTAMO: <samp id="monto_a_saldar">$0</samp></h4>
                        <input id="monto_saldar" name="monto_saldar" type="hidden" value="0" />
                    </div>
                </div>
                <br/>
                <div class="row">
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
                                    old('metodo_pago', null),
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

                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="col mb-3">
                            <div class="form-outline">
                                
                                {{ Form::hidden('h_forma_pago') }}

                                {!! Form::select(
                                    'forma_pago',
                                    ['-1' => '- SELECCIONE -'] + array_combine($tipoValues, $tipoValues), // Combina el array para que sea usable en el select
                                    old('forma_pago', $socio->tipo),
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
                                <input type="date" name="fecha_ultimo_descuento" id="fecha_ultimo_descuento" class="form-control"
                                    value="{{ request('fecha_ultimo_descuento', \Carbon\Carbon::now('America/Mexico_City')->toDateString()) }}">
                                <label class="form-label select-label" for="fecha_ultimo_descuento">ÚLTIMO DESCUENTO</label>
                                <div class="form-helper" id="sangre_feedback" style="color: red; display: none;">Este
                                    campo es requerido.</div>
                            </div>
                            @error('fecha_ultimo_descuento')
                                <p class="error-message text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="col mb-3">
                            <div class="form-outline">
                                {{ Form::text('referencia', old('referencia', $socio->referencia), ['id' => 'referencia', 'class' => 'form-control uppercase', 'placeholder' => 'Referencia', 'tabindex' => '2']) }}
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

                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        Selección
                                         <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select-all">
                                        </div>
                                    </th>
                                    <th scope="col">Préstamo</th>
                                    <th scope="col">Serie</th>
                                    <th scope="col">Capital</th>
                                    <th scope="col">Descuento</th>
                                    <th scope="col">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider table-divider-color">
                                @forelse($prestamo as $row)
                                    <tr>
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input prestamo-check" type="checkbox"
                                                    name="prestamos_id[]" value="{{ $row->pago_id }}"
                                                    data-debe="{{ $row->capital }}">
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
                                {!! Form::button('PAGAR', ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'submitBtn']) !!}
                            </div>
                        </div>
                        </br>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop


@section('js')
    <script>
        /*
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.prestamo-check');
            const totalDisplay = document.getElementById('monto_a_saldar');
            const inputMontoSaldar = document.getElementById('monto_saldar');

            function actualizarTotal() {
                let total = 0;
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        total += parseFloat(checkbox.dataset.debe);
                    }
                });
                totalDisplay.textContent = new Intl.NumberFormat('es-MX', {
                    style: 'currency',
                    currency: 'MXN'
                }).format(total);

                // Guardar el valor sin formato en el input oculto
                inputMontoSaldar.value = total.toFixed(2);
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', actualizarTotal);
            });

            // Llamamos a la función una vez por si hay alguno marcado desde el inicio
            actualizarTotal();
        });
        */

        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.prestamo-check');
            const selectAll = document.getElementById('select-all');
            const totalDisplay = document.getElementById('monto_a_saldar');
            const inputMontoSaldar = document.getElementById('monto_saldar');

            function actualizarTotal() {
                let total = 0;
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        total += parseFloat(checkbox.dataset.debe);
                    }
                });

                totalDisplay.textContent = new Intl.NumberFormat('es-MX', {
                    style: 'currency',
                    currency: 'MXN'
                }).format(total);

                inputMontoSaldar.value = total.toFixed(2);
            }

            // Evento para seleccionar todos
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });
                actualizarTotal();
            });

            // Eventos individuales
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    actualizarTotal();
                    // Si se desmarca uno, desmarcar el select-all
                    if (!this.checked) {
                        selectAll.checked = false;
                    } else if (Array.from(checkboxes).every(cb => cb.checked)) {
                        selectAll.checked = true;
                    }
                });
            });

            // Calcular total inicial
            actualizarTotal();
        });



        $(document).ready(function() {
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
                let saldo = parseFloat($('#saldo').val());
                let debe = parseFloat($('#monto_saldar').val());
                let fecha = $('#fecha_ultimo_descuento').val();
                let formapago = $('#forma_pago').val();
                let metodopago = $('#metodo_pago').val();

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

                if (saldo < debe) {
                    console.log('b');
                    //alert("El saldo no puede ser menor al monto adeudado.");
                    Swal.fire({
                        icon: 'info',
                        title: 'El saldo no puede ser menor al monto adeudado.',
                        text: 'Por favor verifique la información.',
                    });
                    e.preventDefault(); // Evita el envío del formulario
                }

                if(fecha == ''){
                    console.log('c');
                    Swal.fire({
                        icon: 'info',
                        title: 'Mensaje de error.',
                        text: 'Por favor ingrese la fecha del último descuento.',
                    });
                    e.preventDefault(); // Evita el envío del formulario
                }

                if(formapago == -1){
                    console.log('d');
                    Swal.fire({
                        icon: 'info',
                        title: 'Mensaje de error.',
                        text: 'Por favor seleccione una forma de pago.',
                    });
                    e.preventDefault(); // Evita el envío del formulario
                }

                if(metodopago == -1){
                    console.log('d');
                    Swal.fire({
                        icon: 'info',
                        title: 'Mensaje de error.',
                        text: 'Por favor seleccione un método de pago.',
                    });
                    e.preventDefault(); // Evita el envío del formulario
                }
            });
        });
    </script>
@stop
