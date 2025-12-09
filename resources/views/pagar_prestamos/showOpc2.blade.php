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
                    <td colspan="3" class="p-0">
                        <img class="masthead-avatar" src="{{ asset('image/titulo_1.png') }}" alt="SSPO_logo" width="auto"
                            height="100px" />
                    </td>
                    <td class="p-0">
                        <p class="m-1"><strong>FOLIO: 0000{{ $prestamo->id }}</strong></p>
                        <p class="m-1"><strong>FECHA DE CAPTURA:
                                {{ date('d/m/Y', strtotime($prestamo->fecha_captura)) }}</strong>
                        </p>
                        <p class="m-1"><strong>No.SOCIO: {{ $socio->num_socio }}</strong></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="p-1">
                        <p class="m-1"><strong>SOCIO: {{ $socio->nombre_completo }}</strong></p>
                    </td>
                    <td class="p-1">
                        <p class="m-1"><strong>RFC: {{ $socio->rfc }}</strong></p>
                    </td>
                </tr>
                <tr>
                    <td class="p-1">
                        <p class="m-1"><strong>PRESTAMO: </strong></p>
                    </td>
                    <td class="p-1">
                        <p class="m-1"><strong>QUINCENAS: </strong></p>
                    </td>
                    <td class="p-1">
                        <p class="m-1"><strong>PAGO QUINCENAL: </strong></p>
                    </td>
                    <td class="p-1">
                        <p class="m-1"><strong>PRESTAMO + INTERESES: </strong></p>
                    </td>
                </tr>
                <tr>
                    <td class="p-1">
                        <p class="m-1"><strong>${{ number_format($prestamo->monto_prestamo, 2) }} </strong></p>
                    </td>
                    <td class="p-1">
                        <p class="m-1"><strong>{{ $prestamo->total_quincenas }}</strong></p>
                    </td>
                    <td class="p-1">
                        <p class="m-1"><strong>${{ number_format($prestamo->pago_quincenal, 2) }}</strong></p>
                    </td>
                    <td class="p-1">
                        <p class="m-1">
                            <strong>${{ number_format($prestamo->monto_prestamo + $prestamo->total_intereses, 2) }}</strong>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="row mb-3">
            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                <h4 class="card-title">AVALES</h4>
            </div>
        </div>
        <div class="card border border-primary shadow-0 mb-1">
            <div class="card-body text-dark p-1">
                <div class="row mb-1">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card-body table-responsive p-0">
                            <table id="tbl_aval" class="table table-sm responsive nowrap">
                                <thead>
                                    <tr>
                                        <th scope="col">Num Socio</th>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Apellido Paterno</th>
                                        <th scope="col">Apellido Materno</th>
                                        <th scope="col">RFC</th>
                                        <th scope="col">Saldo avalar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($prestamoDetalle as $row)
                                        <tr>
                                            <td>{{ $row->num_aval }}</td>
                                            <td>{{ $row->nombre }}</td>
                                            <td>{{ $row->apellido_paterno }}</td>
                                            <td>{{ $row->apellido_materno }}</td>
                                            <td>{{ $row->rfc }}</td>
                                            <td>{{ $row->monto_aval }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">No hay avales por mostrar</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3 mb-3">
            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                <h4 class="card-title">TABLA DE INTERESES</h4>
            </div>
        </div>

        <div class="card border border-primary shadow-0 mb-1">
            <div class="card-body text-dark p-1">
                <div class="row mb-1">
                    <div class="col-lg-12 col-md-12 col-sm-12" style="display: none">
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
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card-body table-responsive p-0">
                            <table id="tabla-interesdos2" class="table table-sm responsive nowrap">
                                <thead>
                                    <tr>
                                        <th scope="col">Pago</th>
                                        <th scope="col">Capital</th>
                                        <th scope="col">Interés</th>
                                        <th scope="col">Descuento</th>
                                        <th scope="col">Saldo Final</th>
                                        <th scope="col">Fecha de pago</th>
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
                <div class="col-lg-8 col-md-8 col-sm-8">
                    <h3 class="card-title b-0">DETALLES DEL PRÉSTAMO</h3>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
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
                        <div class="row mb-3">
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="col">
                                    <h5><strong>SOCIO</strong></h5>
                                    <h6>{{ $socio->nombre_completo }}</h6>
                                </div>
                                {{ Form::hidden('monto_prestamo', $prestamo->monto_prestamo, ['id' => 'monto_prestamo']) }}
                                {{ Form::hidden('total_quincenas', $prestamo->total_quincenas, ['id' => 'total_quincenas']) }}
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="col">
                                    <h5><strong>MONTO PRESTAMO</strong></h5>
                                    <h6>{{ $prestamo->monto_prestamo }}</h6>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="col">
                                    <h5><strong>TOTAL QUINCENAS</strong></h5>
                                    <h6>{{ $prestamo->total_quincenas }}</h6>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="col">
                                    <h5><strong>PAGOS QUINCENALES</strong></h5>
                                    <h6>{{ $prestamo->pago_quincenal }}</h6>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="col">
                                    <h5><strong>PRESTAMO + INTERESES</strong></h5>
                                    <h6>{{ $prestamo->monto_prestamo + $prestamo->total_intereses }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="col">
                                    <h5><strong>NÚMERO DE SOCIO</strong></h5>
                                    <h6>{{ $socio->num_socio }}</h6>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="col">
                                    <h5><strong>PRESTAMOS ACTIVOS</strong></h5>
                                    <h6>{{ $socio->numero_prestamos }}</h6>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="col">
                                    <h5><strong>FECHA DE ALTA</strong></h5>
                                    <h6>{{ $socio->fecha_alta }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="col">
                                    <h5><strong>RFC</strong></h5>
                                    <h6>{{ $socio->rfc }}</h6>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="col">
                                    <h5><strong>SALDO AHORRADO</strong></h5>
                                    <h6>{{ $socio->saldo }}</h6>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="col">
                                    <h5><strong>SALDO DISPONIBLE</strong></h5>
                                    <h6>{{ $socio->saldo + 500 - $socio->monto_prestamo }}</h6>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="col">
                                    <h5><strong>SALDO DISPONIBLE</strong></h5>
                                    <h6>{{ ($socio->saldo + 500 - $socio->monto_prestamo) * 2 }}
                                    </h6>
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
                        <div class="row mb-3">
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

                <div class="card border border-primary shadow-0 mb-3">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-lg-6 col-md-12">
                                <h4 class="card-title">AVALES</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-dark">
                        <div class="row mb-3">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card-body table-responsive p-0">
                                    <table id="tbl_aval" class="table responsive nowrap">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Num Socio</th>
                                                <th>Nombre</th>
                                                <th>Apellido Paterno</th>
                                                <th>Apellido Materno</th>
                                                <th>Rfc</th>
                                                <th>Saldo avalar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($prestamoDetalle as $row)
                                                <tr>
                                                    <td>{{ $row->num_aval }}</td>
                                                    <td>{{ $row->nombre }}</td>
                                                    <td>{{ $row->apellido_paterno }}</td>
                                                    <td>{{ $row->apellido_materno }}</td>
                                                    <td>{{ $row->rfc }}</td>
                                                    <td>{{ $row->monto_aval }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td>No hay avales por mostrar</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            @endforelse
                                        </tbody>
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
        $(document).ready(function() {
            let totalcapint = 0;
            let totalinteres = 0;
            let interesid = 0;

            let totalcapint2 = 0;
            let totalinteres2 = 0;
            let interesid2 = 0;

            // ACTIVA EL CTRL + P
            $("#imprimir").click(function() {
                window.print();
            });

            // GENERO LA TABLA DE INTERESES
            tablainteresuno();
            tablainteresdos();

            tablainteresuno2();
            tablainteresdos2();

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
                        var newRow = "<tr><td>" + i + "</td><td>" + capital.toFixed(1) + "</td><td>" +
                            interesado +
                            "</td><td>" + pagouno.toFixed(1) + "</td><td>" + saldofinald.toFixed(1) +
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
                            var newRow = "<tr><td>" + contador + "</td><td>" + capital.toFixed(1) +
                                "</td><td>" + interesado +
                                "</td><td>" + pagouno.toFixed(1) + "</td><td>" + saldofinald
                                .toFixed(1) +
                                "</td></tr>";
                            $("#tabla-interesdos tbody").append(newRow);
                            valorAnteriorSaldofinald = saldofinald;
                            capitalAnterior = capital.toFixed(1);
                            contador++;
                        });
                        primerIteracion = false;
                    }
                }

                // Convertir la variable PHP $tblDos en una variable JavaScript
                var tblDos = @json($tblDos);

                // Obtener la referencia a la tabla
                var tabla = document.getElementById('tabla-interesdos');

                // Obtener la referencia al cuerpo de la tabla (tbody)
                var cuerpoTabla = tabla.querySelector('tbody');

                // Obtener todas las filas existentes en el cuerpo de la tabla
                var filasExist = cuerpoTabla.querySelectorAll('tr');

                // Iterar a través de los datos de tblDos y agregarlos a las filas existentes
                for (var i = 0; i < tblDos.length; i++) {
                    var filaExistente = filasExist[i]; // Obtener la fila existente correspondiente

                    // Agregar la celda de fecha con los datos de tblDos
                    var celdaFecha = document.createElement('td');
                    celdaFecha.textContent = tblDos[i][
                    'Fecha_Pago']; // Cambia 'Fecha_Pago' por la clave correcta de tblDos

                    // Insertar la celda de fecha en la fila existente
                    filaExistente.appendChild(celdaFecha);
                }
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
                        var newRow = "<tr><td>" + i + "</td><td>" + capital.toFixed(1) + "</td><td>" +
                            interesado +
                            "</td><td>" + pagouno.toFixed(1) + "</td><td>" + saldofinald.toFixed(1) +
                            "</td></tr>";
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
                            var newRow = "<tr><td>" + contador + "</td><td>" + capital.toFixed(1) +
                                "</td><td>" + interesado +
                                "</td><td>" + pagouno.toFixed(1) + "</td><td>" + saldofinald
                                .toFixed(1) +
                                "</td></tr>";
                            $("#tabla-interesdos2 tbody").append(newRow);
                            valorAnteriorSaldofinald = saldofinald;
                            capitalAnterior = capital.toFixed(1);
                            contador++;
                        });
                        primerIteracion = false;
                    }
                }
                // Convertir la variable PHP $tblDos en una variable JavaScript
                var tblDos2 = @json($tblDos);

                // Obtener la referencia a la tabla
                var tabla = document.getElementById('tabla-interesdos2');

                // Obtener la referencia al cuerpo de la tabla (tbody)
                var cuerpoTabla = tabla.querySelector('tbody');

                // Obtener todas las filas existentes en el cuerpo de la tabla
                var filasExist = cuerpoTabla.querySelectorAll('tr');

                // Iterar a través de los datos de tblDos y agregarlos a las filas existentes
                for (var i = 0; i < tblDos2.length; i++) {
                    var filaExistente = filasExist[i]; // Obtener la fila existente correspondiente

                    // Agregar la celda de fecha con los datos de tblDos
                    var celdaFecha = document.createElement('td');
                    celdaFecha.textContent = tblDos2[i][
                    'Fecha_Pago']; // Cambia 'Fecha_Pago' por la clave correcta de tblDos

                    // Insertar la celda de fecha en la fila existente
                    filaExistente.appendChild(celdaFecha);
                }
            }
        });
    </script>
@stop
