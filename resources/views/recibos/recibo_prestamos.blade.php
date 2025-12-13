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
        <table class="table" width="100%" cellspacing="0" border="0" cellpadding="0" style="font-size: 1rem;">
            <tbody>
                <tr>
                    <td colspan="3">
                        <img class="masthead-avatar" src="{{ asset('image/caja.png') }}" alt="SSPO_logo"
                            width="auto" height="100px" />
                    </td>
                    <td>
                        <p><strong>FOLIO: _________</strong></p>
                        <p><strong>APROBACIÓN: {{ date('d/m/Y', strtotime($prestamo->fecha_prestamo)) }}</strong>
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
                <tr style="text-align: center;">
                    <td><strong>PRESTAMO: </strong></td>
                    <td><strong>QUINCENAS: </strong></td>
                    <td><strong>PAGO QUINCENAL: </strong></td>
                    <td><strong>PRESTAMO + INTERESES: </strong></td>
                </tr>
                <tr style="text-align: center;">
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
        <h2>AVALES</h2>
        <table id="tbl_aval" class="table" width="100%" cellspacing="0" border="0" cellpadding="0" style="font-size: 1rem; text-align: center;">
            <thead class="table-dark">
                <tr>
                    <th class="b-bottom">N° SOCIO</th>
                    <th class="b-bottom">SOCIO</th>
                    <th class="b-bottom">RFC</th>
                    <th class="b-bottom">SALDO AVALAR</th>
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
                        <td class="b-bottom b-top">{{ $row->nombre_completo }}</td>
                        <td class="b-bottom b-top">{{ $row->rfc }}</td>
                        <td class="b-bottom b-top">${{ number_format($row->monto_aval, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="b-bottom b-top">No hay avales por mostrar</td>
                        <td class="b-bottom b-top"></td>
                        <td class="b-bottom b-top"></td>
                        <td class="b-bottom b-top"></td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h2>TABLA DE INTERESES</h2>
        @php
            function toNumber($value) {
                if ($value === null || $value === '') return 0;

                // Quita $, comas, espacios u otros símbolos
                $clean = str_replace(['$', ',', ' '], '', $value);

                // Convierte a número flotante
                return floatval($clean);
            }
        @endphp
        <table id="tabla" class="table" style="font-size: 1rem; text-align: center;">
            <thead class="table-dark">
                <tr>
                    <th class="b-bottom">Pago</th>
                    <th class="b-bottom">Capital</th>
                    <th class="b-bottom">Interés</th>
                    <th class="b-bottom">Descuento</th>
                    <th class="b-bottom">Saldo Final</th>
                    <th class="b-bottom">Fecha de pago</th>
                </tr>
            </thead>
            @php
                $totalCapital = 0;
                $totalInteres = 0;
                $totalDescuento = 0;
            @endphp
            <tbody>
                @foreach ($tblDos['tabla_interesdos'] as $item)
                    @php
                        $totalCapital   += toNumber($item['Capital']);
                        $totalInteres   += toNumber($item['Interes']);
                        $totalDescuento += toNumber($item['Descuento']);
                    @endphp
                    <tr>
                        <td class="b-bottom b-top">{{ $item['Pago'] }}</td>
                        <td class="b-bottom b-top">${{ $item['Capital'] }}</td>
                        <td class="b-bottom b-top">${{ number_format($item['Interes'],1) }}</td>
                        <td class="b-bottom b-top">${{ $item['Descuento'] }}</td>
                        <td class="b-bottom b-top">${{ $item['Saldo_Final'] }}</td>
                        <td class="b-bottom b-top">{{ $item['Fecha_Pago'] }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-dark">
                <tr>
                    <th>Total</th>
                    <th>${{ number_format($totalCapital, 2) }}</th>
                    <th>${{ number_format($totalInteres, 2) }}</th>
                    <th>${{ number_format($totalDescuento, 2) }}</th>
                    <th>-</th>
                    <th>-</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
