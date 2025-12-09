@csrf
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="register-box-body">
            <div class="card border border-primary shadow-0 mb-3">
                <div class="card-header">
                    <h4>REPORTE INGRESOS EN EFECTIVO</h4>
                </div>
                <div class="card-body text-dark">
                    <div class="row">
                        
                            @if($datos->isEmpty())
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <p>No hay resultados para el rango de fechas seleccionado.</p>
                                </div>
                            @else
                                @foreach($datos as $item)
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <h3 class="text-3xl font-bold dark:text-white">
                                            TOTAL DE AHORROS EN EFECTIVO
                                        </h3>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-12">
                                        <h3 class="text-3xl font-bold dark:text-white">
                                           MONTO
                                        </h3>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-12">
                                        <h3 class="text-center text-3xl font-bold dark:text-white">
                                            $ {{ number_format($item['ahorros_efectivo'], 2) }}
                                        </h3>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <h3 class="text-3xl font-bold dark:text-white">
                                            TOTAL PAGO DE PRÉSTAMOS EN EFECTIVO
                                        </h3>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-12">
                                        <h3 class="text-3xl font-bold dark:text-white">
                                           MONTO
                                        </h3>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-12">
                                        <h3 class="text-center text-3xl font-bold dark:text-white">
                                            $ {{ number_format($item['prestamos_efectivo'], 2) }}
                                        </h3>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <h3 class="text-3xl font-bold dark:text-white">
                                            TOTAL DE APORTACIÓN SOCIAL EN EFECTIVO
                                        </h3>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-12">
                                        <h3 class="text-3xl font-bold dark:text-white">
                                           MONTO
                                        </h3>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-12">
                                        <h3 class="text-center text-3xl font-bold dark:text-white">
                                            $ {{ number_format($item['aportaciones_efectivo'], 2) }} 
                                        </h3>
                                    </div>

                                    <div class="row d-flex align-items-center">
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <h3 class="text-3xl font-bold dark:text-white mb-0">
                                                SALDO ENTRADAS DE EFECTIVO
                                            </h3>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-12">
                                            <h3 class="text-3xl font-bold dark:text-white mb-0">
                                                &nbsp;
                                            </h3>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <hr class="w-full mb-1">
                                                <h3 class="text-3xl font-bold dark:text-white mb-1">
                                                    $ {{ number_format($item['total_aportacion_efectivo'], 2) }} 
                                                </h3>
                                                <hr class="w-full mt-1">
                                                <hr class="w-full mt-1">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-12 col-md-12 col-sm-12" align="center">
                    <div class="mb-3 text-end">
                        <a href="{{ route('admin.reportes.ingresos.efectivo', ['tipo' => $tipo, 'fecha_inicio' => $fechaInicio->toDateString(), 'fecha_fin' => $fechaFin->toDateString(), 'formato' => 'excel']) }}"
                        class="btn btn-success me-2">Descargar Excel</a>

                        <a href="{{ route('admin.reportes.ingresos.efectivo', ['tipo' => $tipo, 'fecha_inicio' => $fechaInicio->toDateString(), 'fecha_fin' => $fechaFin->toDateString(), 'formato' => 'pdf']) }}"
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
