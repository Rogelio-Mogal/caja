@csrf
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="register-box-body">
            <div class="card border border-primary shadow-0 mb-3">
                <div class="card-header">
                    <h4>REPORTE PRÉSTAMOS</h4>
                </div>
                <div class="card-body text-dark">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            @if($datos->isEmpty())
                                <p>No hay resultados para el rango de fechas seleccionado.</p>
                            @else
                                <table class="table table-striped table-hover display responsive nowrap" id="tbl_prestamos">
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
                                        @foreach($datos as $prestamo)
                                            <tr>
                                                <td>{{ $prestamo->fecha_prestamo }}</td>
                                                <td>{{ $prestamo->socio->nombre_completo }}</td>
                                                <td>{{ $prestamo->metodo_pago }}</td>
                                                <td>${{ number_format($prestamo->monto_prestamo, 2) }}</td>
                                                <td>${{ number_format($prestamo->total_intereses, 2) }}</td>
                                                <td>${{ number_format(($prestamo->monto_prestamo + $prestamo->total_intereses), 2) }}</td>
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
                                <hr>
                                <h5 class="mt-4">Totales por Método de Pago</h5>
                                <table class="table table-bordered">
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-12 col-md-12 col-sm-12" align="center">
                    <div class="mb-3 text-end">
                        <a href="{{ route('admin.reportes.exportar.prestamo', ['tipo' => $tipo, 'fecha_inicio' => $fechaInicio->toDateString(), 'fecha_fin' => $fechaFin->toDateString(), 'formato' => 'excel']) }}"
                        class="btn btn-success me-2">Descargar Excel</a>

                        <a href="{{ route('admin.reportes.exportar.prestamo', ['tipo' => $tipo, 'fecha_inicio' => $fechaInicio->toDateString(), 'fecha_fin' => $fechaFin->toDateString(), 'formato' => 'pdf']) }}"
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
            $('#tbl_prestamos').DataTable({
                "language": {
                    "url": "{{ asset('/json/i18n/es_es.json') }}"
                }
            });
        });

    </script>
@stop
