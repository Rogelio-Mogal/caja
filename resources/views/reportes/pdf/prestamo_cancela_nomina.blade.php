<h2>Reporte cancela préstamos vía nómina</h2>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>N° NOMINA</th>
            <th>N° EMPLEADO</th>
            <th>NOMBRE DEL SOCIO</th>
            <th>DESCUENTO QUINCENAL</th>
            <th>QUINCENA A LA CUAL SE LIQUIDA</th>
        </tr>
    </thead>
    <tbody>
        @php
            $datosOrdenados = $datos->sortBy('nombre_completo');
            $ultimoNombre = null;
        @endphp
        @foreach($datosOrdenados as $nomina)
            @if($ultimoNombre && $ultimoNombre !== $nomina['nombre_completo'])
                {{-- Fila separadora --}}
                <tr>
                    <td colspan="5" style="border-top: 2px solid #000;"></td>
                </tr>
            @endif

            <tr>
                <td>{{ $nomina['num_nomina'] }}</td>
                <td>{{ $nomina['num_empleado'] }}</td>
                <td>{{ $nomina['nombre_completo'] }}</td>
                <td>${{ number_format($nomina['descuento'], 2) }}</td>
                <td>{{ $nomina['fecha_tabla'] }}</td>
            </tr>

            @php
                $ultimoNombre = $nomina['nombre_completo'];
            @endphp
        @endforeach
    </tbody>
</table>