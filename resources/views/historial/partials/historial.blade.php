@csrf
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="register-box-body">
            <div class="card border border-primary shadow-0 mb-3">
                <div class="card-header">
                    <h4>HISTORIAL: {{ $datos[0]->socio->nombre_completo }}</h4>
                </div>
                <div class="card-body text-dark">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            @if($datos->isEmpty())
                                <p>No hay resultados para el rango de fechas seleccionado.</p>
                            @else
                                <table class="table table-striped" id="tbl_prestamos">
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-12 col-md-12 col-sm-12" align="center">
                    <div class="mb-3 text-end">
                        {{--<a href="{{ route('admin.reportes.exportar.prestamo', ['tipo' => $tipo, 'fecha_inicio' => $fechaInicio->toDateString(), 'fecha_fin' => $fechaFin->toDateString(), 'formato' => 'excel']) }}"
                        class="btn btn-success me-2">Descargar Excel</a>--}}

                        <a href="{{ route('admin.reportes.exportar.historial', ['tipo' => $tipo, 'fecha_inicio' => $fechaInicio->toDateString(), 'fecha_fin' => $fechaFin->toDateString(), 'socios_id' => $datos[0]->socio->id, 'formato' => 'pdf']) }}"
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
