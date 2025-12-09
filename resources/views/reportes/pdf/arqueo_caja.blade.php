<h2>Reporte arqueo de caja ( {{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }})</h2>
<div class="row">
    @php
        $hasPrestamos = isset($datos[0]['prestamos']) && $datos[0]['prestamos']->isNotEmpty();
        $hasRetiros = isset($datos[0]['retiros']) && $datos[0]['retiros']->isNotEmpty();
        $hasEfectivo = isset($datos[0]['efectivo_diario']) && !is_null($datos[0]['efectivo_diario']);
    @endphp

    @if (!$hasPrestamos && !$hasRetiros && !$hasEfectivo)
        <div class="col-12">
            <p>No hay resultados para el rango de fechas seleccionado.</p>
        </div>
    @else

        <h3 class="text-xl font-bold mb-3 dark:text-white">TOTAL DE PRÉSTAMOS EN EFECTIVO</h3>
        <table class="table" style="width: 100%">
            <tbody>
                @php
                    $totalPrestamos = 0;
                    $totalRetiros = 0;
                    $inicial = $datos[0]['saldo_inicial'] ?? null;
                    $saldoTotal = $inicial?->total ?? 0;
                @endphp

                <tr>
                    <td>SALDO INICIAL</td>
                    <td>${{ number_format($saldoTotal, 2) }}</td>
                </tr>

                @foreach ($datos[0]['prestamos'] as $prestamo)
                    @php
                        $totalPrestamos += $prestamo->diferencia;
                    @endphp
                    <tr>
                        <td>PRÉSTAMO {{ $loop->iteration }}</td>
                        <td>${{ number_format($prestamo->diferencia, 2) }}</td>
                    </tr>
                @endforeach

                @foreach ($datos[0]['retiros'] as $retiro)
                    @php
                        $totalRetiros += $retiro->saldo_aprobado;
                    @endphp
                    <tr>
                        <td>RET DE AHORRO {{ $loop->iteration }}</td>
                        <td>${{ number_format($retiro->saldo_aprobado, 2) }}</td>
                    </tr>
                @endforeach

                <!-- Total final -->
                <tr class="fw-bold bg-light">
                    <td>TOTAL PRÉSTAMOS</td>
                    <td>${{ number_format($totalPrestamos, 2) }}</td>
                </tr>
                <tr class="fw-bold bg-light">
                    <td>TOTAL RETIROS</td>
                    <td>${{ number_format($totalRetiros, 2) }}</td>
                </tr>
                <tr class="fw-bold bg-secondary text-black">
                    <td>SALDO FINAL</td>
                    <td>${{ number_format($saldoTotal - ($totalPrestamos + $totalRetiros), 2) }}</td>
                </tr>
            </tbody>
        </table>


        {{-- Columna derecha: Efectivo Diario --}}
        <h3 class="text-xl font-bold mb-3 dark:text-white">DENOMINACIÓN TOTAL</h3>
        <table class="table" style="width: 100%">
            <tbody>
                @php $efectivo = $datos[0]['efectivo_diario']; @endphp
                <tr>
                    <td>{{ $efectivo->b_mil }} DE $1,000</td>
                    <td>${{ number_format($efectivo->b_mil * 1000, 2) }} </td>
                </tr>
                <tr>
                    <td>{{ $efectivo->b_quinientos }} DE $500</td>
                    <td>${{ number_format($efectivo->b_quinientos * 500, 2) }} </td>
                </tr>
                <tr>
                    <td>{{ $efectivo->b_doscientos }} DE $200</td>
                    <td>${{ number_format($efectivo->b_doscientos * 200, 2) }} </td>
                </tr>
                <tr>
                    <td>{{ $efectivo->b_cien }} DE $100</td>
                    <td>${{ number_format($efectivo->b_cien * 100, 2) }} </td>
                </tr>
                <tr>
                    <td>{{ $efectivo->b_cincuenta }} DE $50</td>
                    <td>${{ number_format($efectivo->b_cincuenta * 50, 2) }} </td>
                </tr>
                <tr>
                    <td>{{ $efectivo->b_veinte }} DE $20</td>
                    <td>${{ number_format($efectivo->b_veinte * 20, 2) }} </td>
                </tr>
                <tr>
                    <td>MONEDAS</td>
                    <td>${{ number_format($efectivo->monedas, 2) }} </td>
                </tr>
                <tr class="fw-bold bg-warning text-black">
                    <td>TOTAL</td>
                    <td>${{ number_format($efectivo->total, 2) }} </td>
                </tr>
                <tr class="fw-bold bg-secondary text-black">
                    <td>DIREFENCIA</td>
                    <td>${{ number_format($efectivo->total - ($saldoTotal - ($totalPrestamos + $totalRetiros)), 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    @endif

</div>
{{--
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Socio</th>
            <th>Método de pago</th>
            <th>Monto</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datos as $p)
            <tr>
                <td>{{ $p->fecha }}</td>
                <td>{{ $p->socio->nombre_completo }}</td>
                <td>{{ $p->metodo_pago }}</td>
                <td>${{ number_format($p->monto, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" class="text-end">Total:</th>
            <th>${{ number_format($totalMonto, 2) }}</th> <!-- Total monto -->
        </tr>
    </tfoot>
</table>
--}}
