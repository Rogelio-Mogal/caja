<!DOCTYPE html>
<html>

<head>
    <title>COMPROBANTE AHORRO VOLUNTARIO</title>

    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">-->

    <style type="text/css">
        @page {
            size: letter;
            margin: 20px 35px 110px 35px !important; //cambiar el valor 100 para ajustar el margen del final de la hoja
            padding: 20px 20px 20px 20px !important;
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
            height: 12cm;
            line-height: 0.5cm;
            border: 1px solid black;
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
    @for ($i = 0; $i < 2; $i++)
        <div class="contenido">
            <table class="table item_table" width="100%" cellspacing="0" border="0" cellpadding="0">
                <tbody>
                    <tr>
                        <td rowspan="2" style="width: 90px;" class="b-bottom"><img class="masthead-avatar mb-5"
                                src="{{ asset('image/caja.png') }}" alt="SSPO_logo" width="auto" height="80px" />
                        </td>
                        <td rowspan="2" style="width: 300;" align="center" class="b-bottom b-right">
                            <h2>CAJA DE AHORRO SSPO</h2>
                        </td>
                        <td colspan="2" class="b-bottom"
                            style="font-size: 1rem; padding-bottom: 0; padding-left: 10px; line-height: 0.3;">FOLIO:
                            ________
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="b-bottom"
                            style="font-size: 1rem; padding-bottom: 0; padding-left: 10px; ">FECHA:
                            {{ date('d-m-Y H:i:s', strtotime($ahorro->fecha_ahorro)) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="b-bottom"
                            style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; ">MOVIMIENTO:
                            DEPOSITO</td>
                        <td colspan="2" class="b-bottom"
                            style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; ">NO. SOCIO:
                            {{ $ahorro->num_socio }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="b-bottom"
                            style="font-size: 1rem; padding-bottom: 3; padding-top: 3; padding-left: 10px; ">NOMBRE:
                            {{ $ahorro->nombre_completo }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p style="margin:0.02% 0;">&nbsp;</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table item_table2" width="100%" cellspacing="0" border="0" cellpadding="0">
                <tbody>
                    <tr align="center" class="encabezados color3">
                        <td style="font-size: 1rem;">DESCRIPCIÓN</td>
                        <td style="font-size: 1rem;">SALDO ANTERIOR</td>
                        <td style="font-size: 1rem;">IMPORTE</td>
                        <td style="font-size: 1rem;">SALDO ACTUAL</td>
                    </tr>
                    <tr>
                        <td style="font-size: 1rem; padding-top: 4; padding-left: 10px">
                            AHORRO VOLUNTARIO
                        </td>
                        <td style="font-size: 1rem; padding-top: 4; text-align: center;">${{ number_format ( ($ahorro->saldo - $ahorro->monto) ,2)}}</td>
                        <td style="font-size: 1rem; padding-top: 4; text-align: center;">${{ number_format ( $ahorro->monto ,2)}}</td>
                        <td style="font-size: 1rem; padding-top: 4; text-align: center;">${{ number_format ( $ahorro->saldo ,2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-size: 1rem; padding-top: 4; padding-left: 10px">
                            <p class="uppercase">FORMA DE PAGO: {{ $ahorro->metodo_pago }}</p>
                        </td>
                    </tr>

                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="height: 10px;"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="font-size: 1rem; padding-top: 4; padding-left: 10px">
                            IMPORTE CON LETRA: <strong> {{ convertir(number_format($ahorro->monto, 2)) }} </strong>
                        </td>
                    </tr>
                </tfoot>

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
