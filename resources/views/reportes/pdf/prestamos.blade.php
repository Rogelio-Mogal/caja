<h2>Reporte de Préstamos ( {{$fechaInicio->format('d/m/Y')}} -  {{$fechaFin->format('d/m/Y')}})</h2>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Socio</th>
            <th>Método de pago</th>
            <th>Monto</th>
            <th>Intereses</th>
            <th>Monto + Intereses</th>
            
        </tr>
    </thead>
    <tbody>
        @foreach($datos as $p)
            <tr>
                <td>{{ $p->fecha_prestamo }}</td>
                <td>{{ $p->socio->nombre_completo }}</td>
                <td>{{ $p->metodo_pago }}</td>
                <td>${{ number_format($p->monto_prestamo, 2) }}</td>
                <td>${{ number_format($p->total_intereses, 2) }}</td>
                <td>${{ number_format(($p->monto_prestamo + $p->total_intereses), 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" class="text-end">Totales:</th>
            <th>${{ number_format($totalMonto, 2) }}</th> <!-- Total monto -->
            <th>${{ number_format($totalIntereses, 2) }}</th> <!-- Total intereses -->
            <th>${{ number_format(($totalMonto + $totalIntereses), 2) }}</th> <!-- Total intereses -->
        </tr>
    </tfoot>
</table>


<h3>Totales por Método de Pago</h3>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Método de pago</th>
            <th>Total monto</th>
            <th>Total intereses</th>
            <th>Monto + intereses</th>
        </tr>
    </thead>
    <tbody>
        @foreach($totalesPorMetodo as $metodo => $totales)
            <tr>
                <td>{{ $metodo }}</td>
                <td>${{ number_format($totales['total_monto'], 2) }}</td>
                <td>${{ number_format($totales['total_intereses'], 2) }}</td>
                <td>${{ number_format(($totales['total_monto'] + $totales['total_intereses']), 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th class="text-end">Totales:</th>
            <th>${{ number_format($totalMonto, 2) }}</th>
            <th>${{ number_format($totalIntereses, 2) }}</th>
            <th>${{ number_format(($totalMonto + $totalIntereses), 2) }}</th>
        </tr>
    </tfoot>
</table>