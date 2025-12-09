<h2>Reporte liquidación de préstamos</h2>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Nómina</th>
            <th>Empleado</th>
            <th>Socio</th>
            <th>Método de pago</th>
            <th>Último descuento</th>
            <th>Descuento</th>
            <th>Monto préstamo</th>
            <th>Monto pagado</th>
            <th>Monto liquidado</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datos as $p)
            <tr>
                <td>{{ $p->fecha_pago_reestructuracion }}</td>
                <td>{{ $p->num_nomina }}</td>
                <td>{{ $p->num_empleado }}</td>
                <td>{{ $p->socio->nombre_completo }}</td>
                <td>{{ $p->tipo_forma_pago }}</td>
                <td>{{ $p->fecha_ultimo_descuento }}</td>
                <td>${{ number_format( $p->pago_quincenal, 2) }}</td>
                <td>${{ number_format( $p->monto_prestamo, 2) }}</td>
                <td>${{ number_format($p->capital_sin_forma_pago, 2) }}</td>
                <td>${{ number_format($p->capital_con_forma_pago, 2) }}</td>
                <td>${{ number_format(( $p->capital_sin_forma_pago + $p->capital_con_forma_pago), 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="7" class="text-end">Totales:</th>
            <th>${{ number_format($totalMonto, 2) }}</th>
            <th>${{ number_format($totalIntereses, 2) }}</th>
            <th>${{ number_format($totalTres, 2) }}</th>
            <th>${{ number_format($totalCuatro, 2) }}</th>
        </tr>
    </tfoot>
</table>