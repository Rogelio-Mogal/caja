<?php

if ($userPrinterSize == '58'){
    $medidaTicket = 170;//180;685 para el de 58mm, 945 para el de 80mm
}else if ($userPrinterSize == '80'){
    $medidaTicket = 270;//180;685 para el de 58mm, 945 para el de 80mm
}
?>
<!DOCTYPE html>
<html>

<head>

    <style>
        * {
            font-size: 9px;
            font-family: 'DejaVu Sans', serif;
        }

        h1 {
            font-size: 9px;
        }
        h2 {
            font-size: 9px;
        }

        .ticket {
            margin: 2px;
        }

        td,
        th,
        tr,
        table {
            border-top: 0px solid black;
            border-collapse: collapse;
            margin: 0 auto;
        }

        td.precio {
            text-align: right;
            font-size: 9px;
        }

        td.cantidad {
            font-size: 9px;
        }

        td.producto {
            text-align: left;
        }

        th {
            text-align: center;
        }


        .centrado {
            text-align: center;
            align-content: center;
        }

        .ticket {
            width: <?php echo $medidaTicket ?>px;
            max-width: <?php echo $medidaTicket ?>px;
        }

        img {
            /*max-width: inherit;
            width: inherit;*/
        }

        * {
            margin: 0;
            padding: 0;
        }

        .ticket {
            margin: 0;
            padding: 0;
        }

        body {
            text-align: center;
        }

        .letra-grande{
            font-size: 13px;
            font-weight: bold;
        }
        .letra-normal{
            font-size: 9px;
            text-align: left;
            margin-left: 10px;
        }
        .uppercase {
            text-transform: uppercase;
        }
        .blanco {
            color:white;
            font-size: 2px;
        }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
    @php
    $logo=''; $icono=''; $emppresa=''; $rfc='';
    $dom=''; $ciudad=''; $estado=''; $cp='';
    $mail=''; $tel1=''; $tel2=''; $text1=''; $text2='';


    @endphp

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

    <div class="ticket centrado">
        <p class="letra-grande">--- SALDOS ---</p>
    <p>-----------------------------------------------------------------------</p>
        <table border="0" style="line-height: 1.6; width: 100%;" class="letra-normal uppercase">
            <tbody>
                <tr>
                    <td colspan="3">Nombre: {{$socio->nombre_completo}}</td>
                    <td rowspan="2">
                        <img class="masthead-avatar mb-5"
                                src="{{ asset('image/caja.png') }}" alt="caja_logo" width="auto" height="55px" />
                    </td>
                </tr>
                <tr>
                    <td colspan="3">SALDO AL {{$day}} DE {{$mesEnEspanol}} DE {{$year}}</td>
                </tr>
                <tr>
                    <td colspan="4"><strong>AHORROS: ${{ number_format($socio->saldo, 2) }}</strong></td>
                </tr>
                {{ dd($prestamos) }}
                @foreach ($prestamos as $index => $prestamo)
                    <tr>
                        <td colspan="4" style="padding-left: 10px;">
                            Préstamo {{ $index + 1 }}:
                            ${{ number_format($prestamo->capital_pendiente, 2) }} 
                            ({{ $prestamo->ultimaSeriePagada->serie_pago ?? 0 }}/{{ $prestamo->ultimoPagoPendiente->serie_final ?? $prestamo->total_quincenas }}. {{ \Carbon\Carbon::parse($prestamo->ultimoPagoPendiente->fecha_tabla)->format('d/m/Y') }} )
                        </td>
                        <td colspan="4"></td>
                    </tr>
                @endforeach

                <tr>
                    <td colspan="4"><strong>Deuda total: ${{ number_format($totalCapitalPendiente, 2) }}</strong></td>
                   
                </tr>
                <tr>
                    <td colspan="4"><strong>Disponible: ${{ number_format(max(0, $socio->saldo - ($totalCapitalPendiente + $prestamosDetalles)), 2) }}</strong></td>
                    
                </tr>
                <tr align="right">
                    @if($socio->is_aval > 0)
                        <td colspan="4">ES AVAL: SI (${{ number_format($prestamosDetalles, 2) }})</td>
                        @else
                        <td colspan="4">ES AVAL: NO $--</td>
                    @endif
                </tr>
            </tbody>
        </table>

        {{--
        @switch(true)
            @case($anticipo->efectivo > 0)
                <p class="letra-normal"> <strong>FORMA DE PAGO: </strong> EFECTIVO </p>
                @break

            @case($anticipo->tdd > 0)
                <p class="letra-normal"> <strong>FORMA DE PAGO: </strong> TDD </p>
                @break

            @case($anticipo->tdc > 0)
                <p class="letra-normal"> <strong>FORMA DE PAGO: </strong> TDC </p>
                @break
            @case($anticipo->transferencia > 0)
                <p class="letra-normal"> <strong>FORMA DE PAGO: </strong> TRANSFERENCIA </p>
                @break
        @endswitch   
        --}}

    </div>
</body>

</html>