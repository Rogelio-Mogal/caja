<h2>Reporte de Ahorros ( {{$fechaInicio->format('d/m/Y')}} -  {{$fechaFin->format('d/m/Y')}})</h2>
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
        @foreach($datos as $p)
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

<h3>Totales por Método de Pago</h3>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Método de pago</th>
            <th>Total monto</th>
        </tr>
    </thead>
    <tbody>
        @foreach($totalesPorMetodo as $metodo => $totales)
            <tr>
                <td>{{ $metodo }}</td>
                <td>${{ number_format($totales['total_monto'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th class="text-end">Total:</th>
            <th>${{ number_format($totalMonto, 2) }}</th>
        </tr>
    </tfoot>
</table>