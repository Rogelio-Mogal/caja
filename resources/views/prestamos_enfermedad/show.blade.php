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
                font-family: Arial, sans-serif !important; /* Estilo para impresión */
                font-size: 11.5pt !important; /* Tamaño de fuente para impresión */
            }
            .font11{
                font-family: Arial, sans-serif !important; /* Estilo para impresión */
                font-size: 11pt !important; /* Tamaño de fuente para impresión */
            }
            .nuevaHoja{
                page-break-before: always;
            }
        }

        .border-b {
            border-bottom: 1px solid #000; /* Aquí puedes ajustar el grosor y el color de la línea */
        }
        .border-t {
            border-top: 1px solid #000; /* Aquí puedes ajustar el grosor y el color de la línea */
        }

        .border-r {
            border-right: 1px solid #000; /* Aquí puedes ajustar el grosor y el color de la línea */
        }
        .border-l {
            border-left: 1px solid #000; /* Aquí puedes ajustar el grosor y el color de la línea */
        }

        .alto {
            height: 10px; /* Define la altura deseada en píxeles u otra unidad de medida */
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

        $fechaPrestamo = Carbon::parse($prestamo->fecha_captura);

        // Obtén el año
        $year = $fechaPrestamo->year;

        // Obtén el nombre completo del mes
        $mesEnEspanol = $fechaPrestamo->format('F');
        
        if ($mesEnEspanol == 'January' ){
            $mesEnEspanol = 'Enero';
        }
        if ($mesEnEspanol == 'February' ){
            $mesEnEspanol = 'Febrero';
        }
        if ($mesEnEspanol == 'March' ){
            $mesEnEspanol = 'Marzo';
        }
        if ($mesEnEspanol == 'April' ){
            $mesEnEspanol = 'Abril';
        }
        if ($mesEnEspanol == 'May' ){
            $mesEnEspanol = 'Mayo';
        }
        if ($mesEnEspanol == 'June' ){
            $mesEnEspanol = 'Junio';
        }
        if ($mesEnEspanol == 'July' ){
            $mesEnEspanol = 'Julio';
        }
        if ($mesEnEspanol == 'August' ){
            $mesEnEspanol = 'Agosto';
        }
        if ($mesEnEspanol == 'September' ){
            $mesEnEspanol = 'Septiembre';
        }
        if ($mesEnEspanol == 'October' ){
            $mesEnEspanol = 'Octubre';
        }
        if ($mesEnEspanol == 'November' ){
            $mesEnEspanol = 'Noviembre';
        }
        if ($mesEnEspanol == 'December' ){
            $mesEnEspanol = 'Diciembre';
        }

        // Obtén el día
        $day = $fechaPrestamo->day;
    @endphp

    <br />
    {{-- MEDIA --}}
    <div class="card card-outline card-primary solo-impresion">
        <!-- ENCABEZADO -->
        <table width="100%" cellspacing="0" border="0" cellpadding="0">
            <tbody>
                <tr>
                    <td rowspan="4">
                        <p class="text-center"><img class="masthead-avatar mb-5" src="{{ asset('image/sspo.png') }}"
                                alt="SSPO_logo" width="100px" /></p>
                    </td>
                    <td class="d-flex justify-content-end">FOLIO:______</td>
                </tr>
                <tr>
                    <td class="d-flex justify-content-center">
                        <h3>SOLICITUD DE PRÉSTAMO</h3>
                    </td>
                </tr>
                <tr>
                    <td class="d-flex justify-content-center small text-uppercase">CAJA DE AHORRO DE LOS TRABAJADORES DE LA
                        POLICIA PREV. DEL EDO. DE OAXACA A.C.</td>
                </tr>
                <tr>
                    <td class="d-flex justify-content-end">Santa María Coyotepec, Oaxaca a {{ $day }} de
                        {{ $mesEnEspanol }} de {{ $year }}</td>
                </tr>
            </tbody>
        </table>

        <!-- SOLICITADO -->
        <table width="100%" cellspacing="0" border="0" cellpadding="0">
            <tbody>
                <tr>
                    <td colspan="6"><strong>CANTIDAD SOLICITADA</strong></td>
                </tr>
                <tr>
                    <td colspan="2">${{ number_format($prestamo->monto_prestamo, 2) }}</td>
                    <td colspan="4">( {{ convertir(number_format($prestamo->monto_prestamo, 2)) }} )</td>
                </tr>
                <tr>
                    <td colspan="2">FORMA DE PAGO:</td>
                    <td colspan="2">EN ____</td>
                    <td colspan="2">DESCUENTOS FIJOS DE ${{ number_format($prestamo->pago_quincenal, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="6"></td>
                </tr>
            </tbody>
        </table>

        <!-- SOLICITANTE -->
        <table width="100%" cellspacing="0" border="0" cellpadding="0">
            <tbody>
                <tr>
                    <td colspan="6" class="alto"></td>
                </tr>
                <tr>
                    <td colspan="6"><strong>DATOS DEL SOLICITANTE</strong></td>
                </tr>
                <tr>
                    <td colspan="2">AHORROS: ${{ number_format($socio->saldo, 2) }}</td>
                    <td colspan="2">DEUDA: ${{ number_format($prestamos, 2) }}</td>
                    <td colspan="2">QNA. ___/___/___</td>
                </tr>
                <tr>
                    <td colspan="6">
                        @if($socio->is_aval > 0)
                            ES AVAL: SI. ${{ number_format($prestamosDetalles,2) }}
                            @else
                            ES AVAL: NO
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- SOLICITANTE DETALLE-->
        <table width="100%" cellspacing="0" border="0" cellpadding="0">
            <tbody>
                <tr>
                    <td colspan="8" class="alto"></td>
                </tr>
                <tr>
                    <td colspan="2" class="border-t border-l"><strong>NOMBRE: </strong></td>
                    <td colspan="2" class="border-t">{{ $socio->apellido_paterno }}</td>
                    <td colspan="2" class="border-t">{{ $socio->apellido_materno }}</td>
                    <td colspan="2" class="border-t border-r">{{ $socio->nombre }}</td>
                </tr>
                <!--<tr>
                    <td colspan="2"></td>
                    <td colspan="2">APELLIDO PATERNO</td>
                    <td colspan="2">APELLIDO MATERNO</td>
                    <td colspan="2">NOMBRE(S)</td>
                </tr>-->
                <tr>
                    <td colspan="2" class="border-l"><strong>DIRECCIÓN: </strong></td>
                    <td colspan="6" class="border-r">{{ $socio->domicilio }}</td>
                </tr>
                <tr>
                    <td class="border-l"><strong>POBLACIÓN: </strong></td>
                    <td colspan="3">__________</td>
                    <td><strong>TELÉFONO: </strong></td>
                    <td colspan="3" class="border-r">{{ $socio->telefono }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="border-l"><strong>ADSCRIPCIÓN: ____________</strong></td>
                    <td colspan="2"><strong>CIA: ____________</strong></td>
                    <td colspan="2"><strong>BTN: ____________</strong></td>
                    <td colspan="2" class="border-r"><strong>RFC: {{ $socio->rfc }}</strong></td>
                </tr>
                <tr>
                    <td colspan="8" class="border-l border-r">EN MI CARÁCTER DE SOLICITANTE AUTORIZO QUE EL DEPOSITO SEA REALIZADO A LA TERJETA NO.
                        ____ DE BANCO _________ A NOMBRE DE ___________
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="border-b border-l"><strong>No. NÓMINA: ____</strong></td>
                    <td colspan="4" class="border-b border-r"><strong>No. EMPLEADO: ____</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- AVAL-->
        <table width="100%" cellspacing="0" border="0" cellpadding="0">
            <tbody>
                @forelse($prestamoDetalle as $row)
                    <tr>
                        <td colspan="10" class="alto"></td>
                    </tr>
                    <tr>
                        <td colspan="5"><strong> DATOS DEL AVAL {{ $loop->iteration }} </strong> </td>
                        <td colspan="5"><strong>MONTO AVALADO: ${{ number_format($row->monto_aval, 2) }} </strong></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="border-t border-l"><strong>NOMBRE:</strong> </td>
                        <td colspan="2" class="border-t">{{ $row->apellido_paterno }}</td>
                        <td colspan="2" class="border-t">{{ $row->apellido_materno }}</td>
                        <td colspan="2" class="border-t">{{ $row->nombre }}</td>
                        <td colspan="8" class="border-t border-r">&nbsp;</td>
                    </tr>
                   <!-- <tr>
                        <td colspan="8" class="border-l">&nbsp;</td>
                    </tr>-->
                    <tr>
                        <td colspan="2" class="border-l"><strong>DIRECCIÓN:</strong> </td>
                        <td colspan="6">{{ $row->domicilio }}</td>
                        <td colspan="2" class="border-r">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="border-l border-b"><strong>POBLACIÓN:</strong> </td>
                        <td colspan="2" class="border-b">__________</td>
                        <td colspan="2" class="border-b"><strong>TELÉFONO:</strong> </td>
                        <td colspan="2" class="border-b">{{ $row->telefono }}</td>
                        <td colspan="2" class="border-b border-r"><strong>FIRMA</strong> </td>
                    </tr>
                @empty
                    <tr>
                        <tr>
                            <td colspan="10">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="5"><strong>DATOS DEL AVAL 1 </strong></td>
                            <td colspan="5">MONTO AVALADO: $ ### </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border-t border-l">NOMBRE: </td>
                            <td colspan="2" class="border-t">###</td>
                            <td colspan="2" class="border-t">###</td>
                            <td colspan="2" class="border-t">###</td>
                            <td colspan="2" class="border-t border-r">###</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border-l">DIRECCIÓN: </td>
                            <td colspan="8" class="border-r">###</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border-l border-b">POBLACIÓN: </td>
                            <td colspan="3" class="border-b">###</td>
                            <td colspan="2" class="border-b">TELÉFONO: </td>
                            <td colspan="3" class="border-r border-b">###</td>
                        </tr>
                    </tr>
                    <tr>
                        <tr>
                            <td colspan="10">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="5"><strong>DATOS DEL AVAL 2 </strong></td>
                            <td colspan="5">MONTO AVALADO: $ ### </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border-t border-l">NOMBRE: </td>
                            <td colspan="2" class="border-t">###</td>
                            <td colspan="2" class="border-t">###</td>
                            <td colspan="2" class="border-t">###</td>
                            <td colspan="2" class="border-t border-r">###</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border-l">DIRECCIÓN: </td>
                            <td colspan="8" class="border-r">###</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border-l border-b">POBLACIÓN: </td>
                            <td colspan="3" class="border-b">###</td>
                            <td colspan="2" class="border-b">TELÉFONO: </td>
                            <td colspan="3" class="border-r border-b">###</td>
                        </tr>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- FINAL -->
        <table>
            <tbody>
                <tr>
                    <td colspan="3" class="alto"></td>
                </tr>
                <tr>
                    <td colspan="3" class="font11">
                        Declaro que los datos consignados en este formulario son correctos y completos y que no se ha
                        omitido
                        ni falseado información que deba contener esta declaración, siendo fiel expresión de la verdad.
                    </td>
                </tr>
                @if (count($prestamoDetalle) > 0)
                    @else
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td class="d-flex justify-content-center border-t"><strong>FIRMA DEL SOLICITANTE</strong></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td class="d-flex justify-content-center border-t"><strong>FIRMA DE AUTORIZACIÓN</strong></td>
                </tr>
                <td colspan="3">
                    <strong class="font11">Anexar una copia de la credencial de elector, de la credencial de socio y del último talón de pago de
                    nómina.</strong>
                </td>
            </tbody>
        </table>

        <!-- TABLA DE PAGOS -->
        <div class="nuevaHoja no-imprimir">
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

    <?php
        function unidad($numuero)
        {
            switch ($numuero) {
                case 9:
                    $numu = 'NUEVE';
                    break;
                case 8:
                    $numu = 'OCHO';
                    break;
                case 7:
                    $numu = 'SIETE';
                    break;
                case 6:
                    $numu = 'SEIS';
                    break;
                case 5:
                    $numu = 'CINCO';
                    break;
                case 4:
                    $numu = 'CUATRO';
                    break;
                case 3:
                    $numu = 'TRES';
                    break;
                case 2:
                    $numu = 'DOS';
                    break;
                case 1:
                    $numu = 'UNO';
                    break;
                case 0:
                    $numu = '';
                    break;
            }
            return $numu;
        }
        
        function decena($numdero)
        {
            if ($numdero >= 90 && $numdero <= 99) {
                $numd = 'NOVENTA ';
                if ($numdero > 90) {
                    $numd = $numd . 'Y ' . unidad($numdero - 90);
                }
            } elseif ($numdero >= 80 && $numdero <= 89) {
                $numd = 'OCHENTA ';
                if ($numdero > 80) {
                    $numd = $numd . 'Y ' . unidad($numdero - 80);
                }
            } elseif ($numdero >= 70 && $numdero <= 79) {
                $numd = 'SETENTA ';
                if ($numdero > 70) {
                    $numd = $numd . 'Y ' . unidad($numdero - 70);
                }
            } elseif ($numdero >= 60 && $numdero <= 69) {
                $numd = 'SESENTA ';
                if ($numdero > 60) {
                    $numd = $numd . 'Y ' . unidad($numdero - 60);
                }
            } elseif ($numdero >= 50 && $numdero <= 59) {
                $numd = 'CINCUENTA ';
                if ($numdero > 50) {
                    $numd = $numd . 'Y ' . unidad($numdero - 50);
                }
            } elseif ($numdero >= 40 && $numdero <= 49) {
                $numd = 'CUARENTA ';
                if ($numdero > 40) {
                    $numd = $numd . 'Y ' . unidad($numdero - 40);
                }
            } elseif ($numdero >= 30 && $numdero <= 39) {
                $numd = 'TREINTA ';
                if ($numdero > 30) {
                    $numd = $numd . 'Y ' . unidad($numdero - 30);
                }
            } elseif ($numdero >= 20 && $numdero <= 29) {
                if ($numdero == 20) {
                    $numd = 'VEINTE ';
                } else {
                    $numd = 'VEINTI' . unidad($numdero - 20);
                }
            } elseif ($numdero >= 10 && $numdero <= 19) {
                switch ($numdero) {
                    case 10:
                        $numd = 'DIEZ ';
                        break;
                    case 11:
                        $numd = 'ONCE ';
                        break;
                    case 12:
                        $numd = 'DOCE ';
                        break;
                    case 13:
                        $numd = 'TRECE ';
                        break;
                    case 14:
                        $numd = 'CATORCE ';
                        break;
                    case 15:
                        $numd = 'QUINCE ';
                        break;
                    case 16:
                        $numd = 'DIECISEIS ';
                        break;
                    case 17:
                        $numd = 'DIECISIETE ';
                        break;
                    case 18:
                        $numd = 'DIECIOCHO ';
                        break;
                    case 19:
                        $numd = 'DIECINUEVE ';
                        break;
                }
            } else {
                $numd = unidad($numdero);
            }
            return $numd;
        }
        
        function centena($numc)
        {
            if ($numc >= 100) {
                if ($numc >= 900 && $numc <= 999) {
                    $numce = 'NOVECIENTOS ';
                    if ($numc > 900) {
                        $numce = $numce . decena($numc - 900);
                    }
                } elseif ($numc >= 800 && $numc <= 899) {
                    $numce = 'OCHOCIENTOS ';
                    if ($numc > 800) {
                        $numce = $numce . decena($numc - 800);
                    }
                } elseif ($numc >= 700 && $numc <= 799) {
                    $numce = 'SETECIENTOS ';
                    if ($numc > 700) {
                        $numce = $numce . decena($numc - 700);
                    }
                } elseif ($numc >= 600 && $numc <= 699) {
                    $numce = 'SEISCIENTOS ';
                    if ($numc > 600) {
                        $numce = $numce . decena($numc - 600);
                    }
                } elseif ($numc >= 500 && $numc <= 599) {
                    $numce = 'QUINIENTOS ';
                    if ($numc > 500) {
                        $numce = $numce . decena($numc - 500);
                    }
                } elseif ($numc >= 400 && $numc <= 499) {
                    $numce = 'CUATROCIENTOS ';
                    if ($numc > 400) {
                        $numce = $numce . decena($numc - 400);
                    }
                } elseif ($numc >= 300 && $numc <= 399) {
                    $numce = 'TRESCIENTOS ';
                    if ($numc > 300) {
                        $numce = $numce . decena($numc - 300);
                    }
                } elseif ($numc >= 200 && $numc <= 299) {
                    $numce = 'DOSCIENTOS ';
                    if ($numc > 200) {
                        $numce = $numce . decena($numc - 200);
                    }
                } elseif ($numc >= 100 && $numc <= 199) {
                    if ($numc == 100) {
                        $numce = 'CIEN ';
                    } else {
                        $numce = 'CIENTO ' . decena($numc - 100);
                    }
                }
            } else {
                $numce = decena($numc);
            }
        
            return $numce;
        }
        
        function miles($nummero)
        {
            if ($nummero >= 1000 && $nummero < 2000) {
                $numm = 'MIL ' . centena($nummero % 1000);
            }
            if ($nummero >= 2000 && $nummero < 10000) {
                $numm = unidad(Floor($nummero / 1000)) . ' MIL ' . centena($nummero % 1000);
            }
            if ($nummero < 1000) {
                $numm = centena($nummero);
            }
        
            return $numm;
        }
        
        function decmiles($numdmero)
        {
            if ($numdmero == 10000) {
                $numde = 'DIEZ MIL';
            }
            if ($numdmero > 10000 && $numdmero < 20000) {
                $numde = decena(Floor($numdmero / 1000)) . 'MIL ' . centena($numdmero % 1000);
            }
            if ($numdmero >= 20000 && $numdmero < 100000) {
                $numde = decena(Floor($numdmero / 1000)) . ' MIL ' . miles($numdmero % 1000);
            }
            if ($numdmero < 10000) {
                $numde = miles($numdmero);
            }
        
            return $numde;
        }
        
        function cienmiles($numcmero)
        {
            if ($numcmero == 100000) {
                $num_letracm = 'CIEN MIL';
            }
            if ($numcmero >= 100000 && $numcmero < 1000000) {
                $num_letracm = centena(Floor($numcmero / 1000)) . ' MIL ' . centena($numcmero % 1000);
            }
            if ($numcmero < 100000) {
                $num_letracm = decmiles($numcmero);
            }
            return $num_letracm;
        }
        
        function millon($nummiero)
        {
            if ($nummiero >= 1000000 && $nummiero < 2000000) {
                $num_letramm = 'UN MILLON ' . cienmiles($nummiero % 1000000);
            }
            if ($nummiero >= 2000000 && $nummiero < 10000000) {
                $num_letramm = unidad(Floor($nummiero / 1000000)) . ' MILLONES ' . cienmiles($nummiero % 1000000);
            }
            if ($nummiero < 1000000) {
                $num_letramm = cienmiles($nummiero);
            }
        
            return $num_letramm;
        }
        
        function decmillon($numerodm)
        {
            if ($numerodm == 10000000) {
                $num_letradmm = 'DIEZ MILLONES';
            }
            if ($numerodm > 10000000 && $numerodm < 20000000) {
                $num_letradmm = decena(Floor($numerodm / 1000000)) . 'MILLONES ' . cienmiles($numerodm % 1000000);
            }
            if ($numerodm >= 20000000 && $numerodm < 100000000) {
                $num_letradmm = decena(Floor($numerodm / 1000000)) . ' MILLONES ' . millon($numerodm % 1000000);
            }
            if ($numerodm < 10000000) {
                $num_letradmm = millon($numerodm);
            }
        
            return $num_letradmm;
        }
        
        function cienmillon($numcmeros)
        {
            if ($numcmeros == 100000000) {
                $num_letracms = 'CIEN MILLONES';
            }
            if ($numcmeros >= 100000000 && $numcmeros < 1000000000) {
                $num_letracms = centena(Floor($numcmeros / 1000000)) . ' MILLONES ' . millon($numcmeros % 1000000);
            }
            if ($numcmeros < 100000000) {
                $num_letracms = decmillon($numcmeros);
            }
            return $num_letracms;
        }
        
        function milmillon($nummierod)
        {
            if ($nummierod >= 1000000000 && $nummierod < 2000000000) {
                $num_letrammd = 'MIL ' . cienmillon($nummierod % 1000000000);
            }
            if ($nummierod >= 2000000000 && $nummierod < 10000000000) {
                $num_letrammd = unidad(Floor($nummierod / 1000000000)) . ' MIL ' . cienmillon($nummierod % 1000000000);
            }
            if ($nummierod < 1000000000) {
                $num_letrammd = cienmillon($nummierod);
            }
        
            return $num_letrammd;
        }
        
        function convertir($numero)
        {
            $num = str_replace(',', '', $numero);
            $num = number_format($num, 2, '.', '');
            $cents = substr($num, strlen($num) - 2, strlen($num) - 1);
            $num = (int) $num;
        
            $numf = milmillon($num);
        
            return $numf . ' PESOS ' . $cents . '/100 MN';
        }
    ?>
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
                        'Fecha_Pago'
                    ]; // Cambia 'Fecha_Pago' por la clave correcta de tblDos

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
                        'Fecha_Pago'
                    ]; // Cambia 'Fecha_Pago' por la clave correcta de tblDos

                    // Insertar la celda de fecha en la fila existente
                    filaExistente.appendChild(celdaFecha);
                }
            }
        });
    </script>
@stop
