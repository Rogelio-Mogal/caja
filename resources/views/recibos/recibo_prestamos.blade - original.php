<!DOCTYPE html>
<html>

<head>
    <title>COMPROBANTE DE PRESTAMO</title>
    <link href="{{ asset('mdb/css/mdb.min.css') }}" rel="stylesheet">
    <style type="text/css">
        @page {
            size: letter;
            margin: 20px 35px 30px 35px !important;
            /*cambiar el valor 100 para ajustar el margen del final de la hoja, TERCER NUMERO */
            padding: 20px 20px 20px 20px !important;
        }

        @media print {
            body {
                width: 21cm !important;
                height: 29.7cm !important;
                /* margin: 30mm 45mm 25mm 45mm !important;*/
                /* change the margins as you want them to be. */
            }
        }

        @font-face {
            font-family: 'Arial'; 
            font-weight: normal;
            font-style: normal;
            font-variant: normal;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #0A0A0A;
        }

        table.texto {
            font-family: 'Arial', sans-serif;
            font-size: 5px;
            color: #0A0A0A;
        }

        td {
            margin: 0px;
            padding: 0px;
        }

        .encabezados {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            color: #0A0A0A;
        }

        .page-number:before {
            content: "Page " counter(page);
        }

        /* Create two equal columns that floats next to each other */
        .column {
            float: left;
            width: 48%;
            padding: 4px;
        }

        .column2 {
            float: left;
            width: 97%;
            padding: 4px;
        }

        .footer {
            position: fixed;
            bottom: -2.4cm;
            left: 0cm;
            right: 0cm;
            line-height: 0.5cm;
        }

        .contenido {
            position: relative;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 26.5cm;
            line-height: 0.5cm;
            border: 0.5px solid white;
            vertical-align: middle;
        }

        .b-top {
            border-top: 1px solid black;
        }

        .b-right {
            border-right: 1px solid black;
        }

        .b-bottom {
            border-bottom: 1px solid black;
        }

        .b-left {
            border-left: 1px solid black;
        }

        .color1 {
            background-color: #d9d9d9;
        }

        .color2 {
            background-color: #a6a6a6;
        }

        .color3 {
            background-color: #d9d9d9;
        }

        .color4 {
            background-color: #ddebf7;
        }

        /* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
            margin: -20px;
        }

        div {
            text-align: justify;
            text-justify: inter-word;
        }

        .uppercase {
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="contenido">
        <table class="table" width="100%" cellspacing="0" border="0" cellpadding="0">
            <tbody>
                <tr>
                    <td colspan="3">
                        <img class="masthead-avatar" src="{{ asset('image/caja.png') }}" alt="SSPO_logo"
                            width="auto" height="100px" />
                    </td>
                    <td>
                        <p><strong>FOLIO: 0000{{ $prestamo->id }}</strong></p>
                        <p><strong>FECHA DE CAPTURA: {{ date('d/m/Y', strtotime($prestamo->fecha_captura)) }}</strong>
                        </p>
                        <p><strong>No.SOCIO: {{ $socio->num_socio }}</strong></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <p><strong>SOCIO: {{ $socio->nombre_completo }}</strong></p>
                    </td>
                    <td>
                        <p><strong>RFC: {{ $socio->rfc }}</strong></p>
                    </td>
                </tr>
                <tr>
                    <td><strong>PRESTAMO: </strong></td>
                    <td><strong>QUINCENAS: </strong></td>
                    <td><strong>PAGO QUINCENAL: </strong></td>
                    <td><strong>PRESTAMO + INTERESES: </strong></td>
                </tr>
                <tr>
                    <td><strong>${{ number_format($prestamo->monto_prestamo, 2) }} </strong></td>
                    <td><strong>{{ $prestamo->total_quincenas }}</strong></p>
                    </td>
                    <td><strong>${{ number_format($prestamo->pago_quincenal, 2) }}</strong></td>
                    <td><strong>${{ number_format($prestamo->monto_prestamo + $prestamo->total_intereses, 2) }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- AVALES --}}
        <h3>AVALES</h3>
        <table id="tbl_aval" class="table" width="100%" cellspacing="0" border="0" cellpadding="0">
            <thead class="table-dark">
                <tr>
                    <th class="b-bottom">Num Socio</th>
                    <th class="b-bottom">Nombre</th>
                    <th class="b-bottom">Apellido Paterno</th>
                    <th class="b-bottom">Apellido Materno</th>
                    <th class="b-bottom">RFC</th>
                    <th class="b-bottom">Saldo avalar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prestamoDetalle as $row)
                    <tr>
                        <!--
                                <td>{{-- Carbon::parse($row->fecha_prestamo)->format('d/m/Y H:i:s') --}}
                                </td>
                                <td>$ {{-- number_format($row->monto_prestamo, 2) --}} </td>
                            -->
                        <td class="b-bottom b-top">{{ $row->num_aval }}</td>
                        <td class="b-bottom b-top">{{ $row->nombre }}</td>
                        <td class="b-bottom b-top">{{ $row->apellido_paterno }}</td>
                        <td class="b-bottom b-top">{{ $row->apellido_materno }}</td>
                        <td class="b-bottom b-top">{{ $row->rfc }}</td>
                        <td class="b-bottom b-top">{{ $row->monto_aval }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="b-bottom b-top">No hay avales por mostrar</td>
                        <td class="b-bottom b-top"></td>
                        <td class="b-bottom b-top"></td>
                        <td class="b-bottom b-top"></td>
                        <td class="b-bottom b-top"></td>
                        <td class="b-bottom b-top"></td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h3>TABLA DE INTERESES</h3>
        <?php
            global $pagouno2;
            $pagouno2 = $prestamo->monto_prestamo + $prestamo->total_intereses;

            $montoprest = $prestamo->monto_prestamo;
            $mesesprest = $prestamo->total_quincenas;
            $pagomonto = 0;
            $interes = 0;
            
            $totalcapint = 0;
            $totalinteres = 0;
            $interesid = 0;

            $montoprest2 =  $prestamo->monto_prestamo;
            $mesesprest2 =  $prestamo->total_quincenas;
            
            // Función para redondear
            function round_php($d)
            {
                $dAbs = abs($d);
                $i = intval($dAbs);
                $result = $dAbs - $i;
                if ($result < 0.001) {
                    return $d < 0 ? -$i : $i;
                } else {
                    return $d < 0 ? -($i + 1) : $i + 1;
                }
            }
            
            // Función para redondear (versión alternativa)
            function round1_php($d)
            {
                $dAbs = abs($d);
                $i = intval($dAbs);
                $result = $dAbs - $i;
                if ($result < 0.5) {
                    return $d < 0 ? -$i : $i;
                } else {
                    return $d < 0 ? -($i + 1) : $i + 1;
                }
            }
            
            // Genera la primera tabla de intereses
            function tablainteresuno($montoprest2,$mesesprest2)
            {
                global $totalcapint, $totalinteres;
            
                $montoprest = $montoprest2;
                $mesesprest = $mesesprest2;
                $pagomonto = $montoprest2 / $mesesprest2;
            
                echo '<table id="tbl_uno" class="table responsive nowrap" style="display: none">
                        <thead class="table-dark">
                            <tr>
                                <th class="b-bottom">Pago</th>
                                <th>Capital</th>
                                <th>Interés</th>
                                <th>Descuento</th>
                                <th>Saldo Final</th>
                            </tr>
                        </thead>
                        <tbody>';
                $tablaDatos = []; // Arreglo para almacenar los datos de la tabla
                for ($i = 1; $i <= $mesesprest; $i++) {
                    if ($montoprest >= $pagomonto) {
                        $pagomontored = round_php($pagomonto);
                        $a = 1;
                    } else {
                        $pagomontored = round_php($montoprest);
                    }
            
                    $interes = ($montoprest / 100) * 1.5; // TASA DE INTERES
                    $interesred = round1_php($interes);
                    $capinter = $pagomontored + $interesred;
                    $totalinteres = $totalinteres + $interesred;
                    $totalcapint = $totalcapint + $capinter;
                    $saldofinal = intval($montoprest) - $pagomontored;
                    $montoprest = $saldofinal;
            
                    echo "<tr><td>$i</td><td>$pagomontored</td><td>$interesred</td><td>" . ($pagomontored + $interesred) . "</td><td>$saldofinal</td></tr>";
                    // Almacena los datos en el arreglo
                    $tablaDatos[] = [
                        'Pago' => $i,
                        'Capital' => $pagomontored,
                        'Interés' => $interesred,
                        'Descuento' => $pagomontored + $interesred,
                        'Saldo Final' => $saldofinal
                    ];
                }
            
                echo '</tbody> </table>';
                return $tablaDatos;
            }


            function tablainteresdos($montoprest2,$mesesprest2) {
                global  $totalcapint,$montoprest, $mesesprest;

                $interesid = 0; // Inicializa $interesid
                $tablaDatos = tablainteresuno($montoprest2,$mesesprest2); // Llama a la función tablainteresuno para obtener los datos de la tabla uno

                // Inicializa otras variables
                $pagouno = 0;
                $pagouno1 = 0;
                $capital = 0;
                $saldofinald = 0;
                
                $primerIteracion = true;
                $valorColumna4 = 0;
                $valorAnteriorSaldofinald = 0;
                $valorAnteriorSaldofinald2 = 0;
                $capitalAnterior = 0;
                $nuevoSaldo = 0;

                // Inicializa una variable para almacenar la tabla HTML generada
                $tablaHTML = '<table id="tbl_dos" class="table responsive nowrap" border="0">
                    <thead class="table-dark">
                        <tr>
                            <th class="b-bottom">Pago</th>
                            <th class="b-bottom">Capital</th>
                            <th class="b-bottom">Interés</th>
                            <th class="b-bottom">Descuento</th>
                            <th class="b-bottom">Saldo Final</th>
                        </tr>
                    </thead>
                <tbody>';

                for ($i = 1; $i <= $mesesprest2; $i++) {
                    if ($i == 1) {
                        $pagouno1 = $totalcapint / $mesesprest2;
                        $pagounost = number_format($pagouno1, 1);
                        $pagouno = floatval($pagounost);
                        $primerElemento = $tablaDatos[0]; // Accede al primer elemento de la tabla uno
                        $interesado = floatval($primerElemento['Interés']);
                        $interesValor = floatval($interesado);
                        $interesid += $interesValor;
                        $capital = ($totalcapint / $mesesprest2) - floatval($primerElemento['Interés']);
                        $saldofinald = $montoprest2 - $capital;

                        // Crea la fila de la tabla HTML
                        $tablaHTML .= "<tr>
                            <td>$i</td>
                            <td>" . number_format($capital, 1) ."</td>
                            <td>$interesado</td>
                            <td>" . number_format($pagouno, 1) . "</td>
                            <td>" . number_format($saldofinald, 1) ."</td>
                        </tr>";

                        $capital = $pagouno - $interesid;
                        $valorColumna4 = $saldofinald;
                        $valorAnteriorSaldofinald = $saldofinald;
                    } elseif ($primerIteracion) {
                        $interesid = 0;
                        $contador = 2;
                        $i = 0;
                        
                        foreach ($tablaDatos as $row) {
                            if ($contador == 2) {
                                $i = 0;
                                $interesado = floatval($tablaDatos[1]['Interés']); //floatval($row['Interés']);
                                $interesValor = floatval($interesado);
                                $interesid += $interesValor;
                                // REDONDEAR( ( ( TOTAL PRESTAMO + TOTAL INTERESES ) / PLAZOS ) - INTERES 2 TABLA UNO, 1 )
                                $capital = ($totalcapint / $mesesprest2) - $interesado;

                                // Redondear hacia abajo para mantener un solo decimal
                                $valor_redondeado = round($valorColumna4, 1);

                                $saldofinald = $valor_redondeado - (($totalcapint/$mesesprest2) - $interesado);

                                $tablaHTML .= "<tr>
                                    <td class='b-bottom b-top'>$contador</td>
                                    <td class='b-bottom b-top'>" . number_format(floatval($capital), 1) ."</td>
                                    <td class='b-bottom b-top'>$interesado</td>
                                    <td class='b-bottom b-top'>" . number_format(floatval($pagouno), 1) . "</td>
                                    <td class='b-bottom b-top'>" . round($saldofinald,1) ."</td>
                                </tr>";

                                $valorAnteriorSaldofinald =  round($saldofinald, 1);
                                $valorAnteriorSaldofinald2 =  round($saldofinald, 1);
                                $i = $i+1;

                            } else if ($contador > 2 && $contador <= $mesesprest2) {

                                $interesado = floatval($tablaDatos[$i]['Interés']); //floatval($row['Interés']);
                                $interesValor = floatval($interesado);
                                $interesid += $interesValor;
                                $valAnteriro = floatval($valorAnteriorSaldofinald); //round($valorAnteriorSaldofinald, 1);

                                
                                if ($contador < $mesesprest2) {
                                    $capital = ($totalcapint / $mesesprest2) - floatval($tablaDatos[$i]['Interés']);
                                    //$saldofinald = $valorAnteriorSaldofinald - $capital;
                                   //ok $saldofinald = $valAnteriro -(($totalcapint/$mesesprest2) - floatval($tablaDatos[$i]['Interés']));

                                    $dato = $valAnteriro -(($totalcapint/$mesesprest2) - floatval($tablaDatos[$i]['Interés']));
                                    if ($dato == 0.0) {
                                        // El valor no tiene decimales, dejarlo igual
                                        $saldofinald = $valAnteriro -(($totalcapint/$mesesprest2) - floatval($tablaDatos[$i]['Interés']));
                                    } else {
                                        $dato = $valAnteriro -(($totalcapint/$mesesprest2) - floatval($tablaDatos[$i]['Interés']));
                                        $saldofinald  = round($dato, 1, PHP_ROUND_HALF_DOWN);
                                    }

                                    ////
                                    //$nuevoSaldo = 0;

                                    $decimales = $saldofinald - floor($saldofinald);

                                    if ($decimales == 0.0) {
                                        // El valor no tiene decimales, dejarlo igual
                                        $nuevoSaldo = $saldofinald;
                                        $valorAnteriorSaldofinald2 =$nuevoSaldo;
                                    } else {
                                        $nuevoSaldo = round($saldofinald, 1, PHP_ROUND_HALF_DOWN);
                                        $valorAnteriorSaldofinald2 =$nuevoSaldo;
                                    }

                                   /* if ($decimales >= 0.5) {
                                        // El valor tiene dos o más decimales y termina en .50 o más, subir el decimal
                                        $nuevoSaldo = round($saldofinald, 1);
                                    } elseif ($decimales > 0.01 && $decimales < 0.09) {
                                        // El valor tiene dos o más decimales entre 0.01 y 0.09, dejar solo un decimal
                                        //$nuevoSaldo = number_format($saldofinald, 1);
                                        $nuevoSaldo = floor($saldofinald * 10) / 10;
                                    } elseif ($decimales > 0.05 && $decimales < 0.1 ) {
                                        // Si el valor pasa de .N5, redondearlo al siguiente decimal
                                        $nuevoSaldo = round($saldofinald, 1);
                                    }else {
                                        // El valor tiene dos o más decimales que no cumplen las condiciones anteriores, dejarlo igual
                                        $nuevoSaldo = $saldofinald;
                                    }*/
                                } elseif ($contador == $mesesprest2) {
                                    $capital = floatval($valAnteriro);
                                    $pagouno = $capital + floatval($tablaDatos[$i]['Interés']);
                                    $saldofinald = 0;
                                    $nuevoSaldo = 0;
                                }

                                $tablaHTML .= "<tr>
                                    <td class='b-bottom'>$contador</td>
                                    <td class='b-bottom'>" . number_format(floatval($capital), 1) . "</td>
                                    <td class='b-bottom'>$interesado</td>
                                    <td class='b-bottom'>" . number_format(floatval($pagouno), 1) . "</td>
                                    <td class='b-bottom'>" . number_format(floatval($nuevoSaldo), 1) . "</td>
                                </tr>";

                                //PRUEBAS
                                //$tablaHTML .= "<tr>
                                //   <td class='b-bottom'>$contador</td>
                                //    <td class='b-bottom'>" . number_format(floatval($capital), 1) . "</td>
                                //    <td class='b-bottom'>$interesado</td>
                                //    <td class='b-bottom'>" . number_format(floatval($pagouno), 1) . "</td>
                                //    <td class='b-bottom'>" . number_format(floatval($nuevoSaldo), 1) . 
                                //        "  valorAnteriorSaldofinald: ".$valAnteriro.
                                //       "  valorAnteriorSaldofinald2: ".$valorAnteriorSaldofinald2.
                                //        "  totalcapint: ".$totalcapint.
                                //        "  mesesprest2: ".$mesesprest2.
                                //        "  Interés: ".floatval($tablaDatos[$i]['Interés']).
                                //        
                                //        "</td>
                                //</tr>";
                            }
                            
                            $valorAnteriorSaldofinald = $saldofinald; // este si funciona
                            $valorAnteriorSaldofinald2 = number_format(floatval($nuevoSaldo), 1);
                            $capitalAnterior = number_format($capital, 1);
                            $contador++;
                            $i++;
                        }
                        
                        $primerIteracion = false;
                    }
                }

                $tablaHTML .= '</tbody> </table>';
                
                // Devuelve la tabla HTML generada
                return $tablaHTML;
            }

            // Llama a la función y almacena el resultado en una variable
            $tablaGenerada = tablainteresdos($montoprest2, $mesesprest2);

            // Devuelve la tabla HTML generada a JavaScript
            echo $tablaGenerada;
        ?>
    </div>
</body>
</html>
