@csrf
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="register-box-body">
            <div class="card border border-primary shadow-0 mb-3">
                <div class="card-header">
                    <h4>REPORTE LIQUIDACIÓN DE PRÉSTAMOS</h4>
                </div>
                <div class="card-body text-dark">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            @if($datos->isEmpty())
                                <p>No hay resultados para el rango de fechas seleccionado.</p>
                            @else
                                <table class="table table-striped table-hover display responsive nowrap" id="tbl_pagos">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <!--<th>Nómina</th>
                                            <th>Empleado</th>-->
                                            <th>Socio</th>
                                            <th>Método de pago</th>
                                            <!--<th>Último descuento</th>-->
                                            <th>Descuento</th>
                                            <th>Monto préstamo</th>
                                            <th>Monto pagado</th>
                                            <th>Monto liquidado</th>
                                            <!--<th>Total</th>-->
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($datos as $pagos)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse(time: $pagos->fecha_pago_reestructuracion)->format('d-m-Y') }}</td>
                                                <!--<td>{{ $pagos->num_nomina }}</td>
                                                <td>{{ $pagos->num_empleado }}</td>-->
                                                <td>{{ $pagos->socio->nombre_completo }}</td>
                                                <td>{{ $pagos->tipo_forma_pago }}</td>
                                                <!--<td>{{ $pagos->fecha_ultimo_descuento }}</td>-->
                                                <td>${{ number_format( $pagos->pago_quincenal, 2) }}</td>
                                                <td>${{ number_format( $pagos->monto_prestamo, 2) }}</td>
                                                <td>${{ number_format($pagos->capital_sin_forma_pago, 2) }}</td>
                                                <td>${{ number_format($pagos->capital_con_forma_pago, 2) }}</td>
                                                <!--<td>${{ number_format(( $pagos->capital_sin_forma_pago + $pagos->capital_con_forma_pago), 2) }}</td>-->
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-primary dropdown-toggle"
                                                            data-mdb-toggle="dropdown" aria-expanded="false">
                                                            Acciones
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item"
                                                                href="{{ route('admin.pagar.prestamo.edit', $pagos->id) }}">
                                                                    Editar
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <button type="button" class="dropdown-item show_modal_cancelar"
                                                                    data-id="{{ $pagos->id }}">
                                                                    Cancelar
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Totales:</th>
                                            <th>${{ number_format($totalDescuento, 2) }}</th>
                                            <th>${{ number_format($totalMonto, 2) }}</th>
                                            <th>${{ number_format($totalIntereses, 2) }}</th>
                                            <th>${{ number_format($totalTres, 2) }}</th>
                                            <th></th>
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
                        <a href="{{ route('admin.reportes.exportar.prestamo.liquidacion', ['tipo' => $tipo, 'fecha_inicio' => $fechaInicio->toDateString(), 'fecha_fin' => $fechaFin->toDateString(), 'formato' => 'excel']) }}"
                        class="btn btn-success me-2">Descargar Excel</a>

                        <a href="{{ route('admin.reportes.exportar.prestamo.liquidacion', ['tipo' => $tipo, 'fecha_inicio' => $fechaInicio->toDateString(), 'fecha_fin' => $fechaFin->toDateString(), 'formato' => 'pdf']) }}"
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
            $('#tbl_pagos').DataTable({
                "language": {
                    "url": "{{ asset('/json/i18n/es_es.json') }}"
                }
            });
        });

    </script>
@stop
