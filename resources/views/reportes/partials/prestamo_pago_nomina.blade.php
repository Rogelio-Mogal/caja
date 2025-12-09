@csrf
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="register-box-body">
            <div class="card border border-primary shadow-0 mb-3">
                <div class="card-header">
                    <h4>REPORTE PAGOS PRÉSTAMOS VÍA NÓMINA</h4>
                </div>
                <div class="card-body text-dark">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            @if($datos->isEmpty())
                                <p>No hay resultados para el rango de fechas seleccionado.</p>
                            @else
                                <table class="table table-striped table-hover display responsive nowrap" id="tbl_ahorros">
                                    <thead>
                                        <tr>
                                            <th>Año</th> <!-- Oculto -->
                                            <th>Mes (numérico)</th> <!-- Oculto -->
                                            <th>MES</th>
                                            <th>QUINCENA</th>
                                            <th>CAPITAL</th>
                                            <th>INTERESES</th>
                                            <th>SUB/TOTAL</th>
                                            <th>DIFERENCIA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($datos as $nomina)
                                             <tr>
                                                <td>{{ $nomina->anio }}</td> <!-- oculto -->
                                                <td>{{ $nomina->mes }}</td>  <!-- oculto -->
                                                <td>
                                                    {{ \Carbon\Carbon::create($nomina->anio, $nomina->mes)->translatedFormat('F Y') }}
                                                </td>
                                                <td>{{ $nomina->quincena }}</td>
                                                <td>${{ number_format($nomina->total_capital, 2) }}</td>
                                                <td>${{ number_format($nomina->total_interes, 2) }}</td>
                                                <td>${{ number_format($nomina->total_capital_interes, 2) }}</td>
                                                <td>${{ number_format($nomina->diferencia_intereses, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-12 col-md-12 col-sm-12" align="center">
                    <div class="mb-3 text-end">
                        <a href="{{ route('admin.reportes.exportar.cancela.prestamo.nomina', ['tipo' => $tipo, 'fecha_inicio' => $fechaInicio->toDateString(), 'fecha_fin' => $fechaFin->toDateString(), 'formato' => 'excel']) }}"
                        class="btn btn-success me-2">Descargar Excel</a>

                        <a href="{{ route('admin.reportes.exportar.cancela.prestamo.nomina', ['tipo' => $tipo, 'fecha_inicio' => $fechaInicio->toDateString(), 'fecha_fin' => $fechaFin->toDateString(), 'formato' => 'pdf']) }}"
                        class="btn btn-danger">Descargar PDF</a>
                    </div>
                </div>
            </div>
            <br />
        </div>
    </div>
</div>


@section('js')
    <script>

        $(document).ready(function() {
            $('#tbl_ahorros').DataTable({
                "language": {
                    "url": "{{ asset('/json/i18n/es_es.json') }}"
                },
                "columnDefs": [
                    { "targets": [0, 1], "visible": false, "searchable": false }  // oculta columnas año y mes numérico
                ],
                "order": [[0, 'asc'], [1, 'asc'], [3, 'asc']]  // ordena por año, mes numérico, quincena
            });
        });

    </script>
@stop
