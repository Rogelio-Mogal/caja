@csrf
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="register-box-body">
            <div class="card border border-primary shadow-0 mb-3">
                <div class="card-header">
                    <h4>REPORTE AHORROS</h4>
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
                                            <th>Fecha</th>
                                            <th>Socio</th>
                                            <th>Método de pago</th>
                                            <th>Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($datos as $ahorro)
                                            <tr>
                                                <td>{{ $ahorro->fecha }}</td>
                                                <td>{{ $ahorro->socio->nombre_completo }}</td>
                                                <td>{{ $ahorro->metodo_pago }}</td>
                                                <td>${{ number_format($ahorro->monto, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Totales:</th>
                                            <th>${{ number_format($totalMonto, 2) }}</th>
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-12 col-md-12 col-sm-12" align="center">
                    <div class="mb-3 text-end">
                        <a href="{{ route('admin.reportes.exportar.ahorros', ['tipo' => $tipo, 'fecha_inicio' => $fechaInicio->toDateString(), 'fecha_fin' => $fechaFin->toDateString(), 'formato' => 'excel']) }}"
                        class="btn btn-success me-2">Descargar Excel</a>

                        <a href="{{ route('admin.reportes.exportar.ahorros', ['tipo' => $tipo, 'fecha_inicio' => $fechaInicio->toDateString(), 'fecha_fin' => $fechaFin->toDateString(), 'formato' => 'pdf']) }}"
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
                }
            });
        });

    </script>
@stop
