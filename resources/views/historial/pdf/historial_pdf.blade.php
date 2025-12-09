<h2>Historial:{{ $datos[0]->socio->nombre_completo }} ( {{$fechaInicio->format('d/m/Y')}} -  {{$fechaFin->format('d/m/Y')}})</h2>
<table width="100%" border="1" cellspacing="0" cellpadding="5" style="font-size:11px;">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Folio</th>
            <th>Descripción</th>
            <th>Movimiento</th>
            <th>Monto anterior</th>
            <th>Monto</th>
            <th>Monto actual</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datos as $prestamo)
            <tr>
                <td>{{ $prestamo->fecha }}</td>
                <td>{{ $prestamo->folio }}</td>
                <td>{{ $prestamo->movimiento }}</td>
                <td>{{ $prestamo->tipo_movimiento }}</td>
                <td>${{ number_format($prestamo->saldo_anterior, 2) }}</td>
                <td>${{ number_format($prestamo->monto, 2) }}</td>
                <td>${{ number_format($prestamo->saldo_actual, 2) }}</td>
                <td>{{ $prestamo->estatus }}</td>
            </tr>
        @endforeach
    </tbody>
</table>