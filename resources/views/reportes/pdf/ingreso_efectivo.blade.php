<div style="text-align: center; line-height: 0;">
    <h4>CAJA DE AHORRO DE LA POLICÍA PREVENTIVA DEL ESTADO DE OAXACA</h4>
    <h4>DETERMINACIÓN DE INGRESOS EN EFECTIVO</h4>
    <h4>PARA DEPÓSITO DE LA CAJA DE TESORERÍA</h4>
    <h4>
        FECHA {{$fechaInicio->format('d/m/Y')}} -  {{$fechaFin->format('d/m/Y')}}
    </h4>
</div>

<table width="100%" border="0" cellspacing="0" cellpadding="5" style="border-collapse: separate; border-spacing: 0 15px;">
    <tbody>
        @foreach($datos as $item)
            <tr>
                <td>TOTAL DE AHORROS EN EFECTIVO</td>
                <td>MONTO</td>
                <td>$ {{ number_format($item['ahorros_efectivo'], 2) }}</td>
            </tr>
            <tr>
                <td>TOTAL PAGO DE PRÉSTAMOS EN EFECTIVO</td>
                <td>MONTO</td>
                <td>$ {{ number_format($item['prestamos_efectivo'], 2) }}</td>
            </tr>
            <tr>
                <td>TOTAL DE APORTACIÓN SOCIAL EN EFECTIVO</td>
                <td>MONTO</td>
                <td>$ {{ number_format($item['aportaciones_efectivo'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th class="text-end">SALDO ENTRADAS DE EFECTIVO</th>
            <th></th>
            <th>$ {{ number_format($item['total_aportacion_efectivo'], 2) }} </th>
        </tr>
    </tfoot>
</table>