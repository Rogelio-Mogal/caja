<!DOCTYPE html>
<html>

<head>
    <title>SOLICITUD DE RETURO DE AHORRO</title>

    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">-->

    <style type="text/css">
        @page {
            size: letter;
            margin: 20px 35px 65px 35px !important; //cambiar el valor 100 para ajustar el margen del final de la hoja
            padding: 20px 20px 20px 20px !important;
            border:1px!important;
        }

        @media print {
            body {
                width: 21cm !important;
                height: 29.7cm !important;
                margin: 30mm 45mm 30mm 45mm !important;
                /* change the margins as you want them to be. */
            }
        }

        @font-face {
            font-family: 'Arial'; //monospace
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
            height: 25.5cm;
            line-height: 0.5cm;
            border: 0;
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

@php
    use Carbon\Carbon;
    $today = Carbon::now();

    if ($today->day > 15) {
        $desiredDate = $today->copy()->endOfMonth(); // Último día del mes actual
    } else {
        $desiredDate = $today->copy()->day(15); // Día 15 del mes actual
    }
    $day = $desiredDate->day;
    $year = $desiredDate->year;

    $mesEnEspanol = $desiredDate->locale('es')->translatedFormat('F');
@endphp

<body>
    @for ($i = 0; $i < 1; $i++)
        <div class="contenido">
            <table class="table item_table" width="100%" cellspacing="0" border="0" cellpadding="0">
                <tbody>
                    <tr>
                        <td rowspan="3" style="width: 90px;">
                            <img class="masthead-avatar mb-5"
                                src="{{ asset('image/caja.png') }}" alt="SSPO_logo" width="auto" height="80px" />
                        </td>
                        <td rowspan="3" style="width: 245;" align="center">
                            &nbsp;
                        </td>
                        <td colspan="3" class="b-bottom"
                            style="font-size: 1rem; padding-bottom: 0; padding-left: 10px; padding-right: 10px; line-height: 0.5; text-align: right;">FOLIO:
                            ______
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="b-left b-right"
                            style="font-size: 1rem; padding-bottom: 0; padding-right: 25px; text-align: right ">
                            <strong>DEPENDENCIA: POLICÍA ESTATAL</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="b-bottom b-left b-right"
                            style="font-size: 1rem; padding-bottom: 0; padding-right: 25px; text-align: right ">
                            <strong>SECCIÓN: CAJA DE AHORRO</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"
                            style="font-size: 1rem; padding-bottom: 0; padding-top: 0; padding-left: 10px; padding-right: 10px;  text-align: right; line-height: 2.5 ">
                            <strong>ASUNTO:</strong> Solicitud de <strong>Retiro de Ahorro.</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"
                            style="font-size: 1rem; padding-bottom: 0; padding-top: 0; padding-left: 10px; padding-right: 10px; text-align: right; line-height: 2.5 ">
                            Santa María Coyotepec, Oax., a {{ \Carbon\Carbon::parse($retiro->fecha_captura)->translatedFormat('d \d\e F \d\e Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"
                            style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; text-align: left; line-height: 1 ">
                            OFICIAL. FEDERICO PEDRO PEREZ REYES
                            <br/>
                            PDTE. DEL CONSEJO DE ADMÓN. DE LA CAJA DE AHORRO
                            <br/>
                            DE LOS TRAB. DE LA POL. PREV. DEL EDO. DE OAXACA A.C.
                            <br/>
                            PRESENTE
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" 
                        style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; text-align: left; line-height: 0.7 ">
                        &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" 
                        style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; text-align: left; line-height: 1.2 ">
                        Por medio de la presente solicito por mi propio derecho, retirar de mi fondo de ahorro que a la fecha asciende a ${{ number_format($retiro->saldo,2) }},
                        la cantidad de $ {{ number_format($retiro->monto_retiro,2) }} por así convenir a mis intereses, reconociendo así mismo que a la quincena {{$day}}/{{$mesEnEspanol}}/{{$year}}, tengo
                        una deuda total de $ {{ number_format($totalCapitalPendiente ?? 0, 2) }}.
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"
                        style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; text-align: left; line-height: 1.5 ">
                        NOMBRE: {{$retiro->nombre_completo}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"
                        style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; text-align: left; line-height: 1.5 ">
                        DIRECCIÓN: {{$retiro->domicilio}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"
                        style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; text-align: left; line-height: 1.5 ">
                        POBLACIÓN: {{$retiro->lugar_origen}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"
                        style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; text-align: left; line-height: 1.5 ">
                        TELÉFONO: 
                        </td>
                        <td colspan="3"
                        style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; text-align: left; line-height: 1.5 ">
                        ADSCRIPCIÓN: 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"
                        style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; text-align: left; line-height: 1.5 ">
                        EN MI CARÁCTER DE SOLICITANTE, AUTORIZO QUE EL DEPÓSITO SEA REALIZADO A LA TARJETA NO._____________________________ 
                        DEL BANCO:______________________________
                        A NOMBRE DE:_____________________________________________________________________
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"
                        style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; text-align: left; line-height: 1.5 ">
                        Declaro que los datos consignados en este formulario son correctos y completos y que no se ha omitido ni falseado información que
                        deba contener esta declaración, siendo fiel expresión de la verdad.
                        </td>
                    </tr>

                    <tr>
                        <td colspan="5"
                        style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 50px; text-align: left; line-height: 1.5;width: 50px; ">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Ahorro:  $ {{ number_format($retiro->saldo ?? 0, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"
                        style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 50px; text-align: left; line-height: 1.5;width: 50px; ">
                            Menos (-) retiro: $ {{ number_format($retiro->monto_retiro,2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"
                        style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 50px; text-align: left; line-height: 1.5;width: 50px; ">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Saldo: $ {{ number_format(($retiro->saldo - $retiro->monto_retiro),2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <p style="margin:3% 0;">&nbsp;</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <p style="text-align: center;">
                                AUTORIZO
                            </p>
                        </td>
                        <td>
                        </td>
                        <td colspan="2">
                            <p style="text-align: left;">
                                {{$retiro->nombre_completo}}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"
                        style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; text-align: left; line-height: .5;width: 50px; ">
                        &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"
                        style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; text-align: left; line-height: 1.5;width: 50px; ">
                        Anexar una copia de la credencial de elector y de la credencial de socio de la Caja de Ahorro.
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"
                        style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; text-align: left; line-height: 1.5;width: 50px; ">
                        Registro _____________
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"
                        style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; text-align: left; line-height: 1.5;width: 50px; ">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_____________
                        </td>
                    </tr>
                </tbody>
            </table>

       

        </div>
        <br />
    @endfor

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

</body>

</html>
