@csrf
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="register-box-body">
            <div class="card border border-primary shadow-0 mb-3">
                <div class="card-header">
                    <h4>REPORTE ARQUEO DE CAJA</h4>
                </div>
                <div class="card-body text-dark">
                    <div class="row">
                        @php
                            $hasPrestamos = isset($datos[0]['prestamos']) && $datos[0]['prestamos']->isNotEmpty();
                            $hasRetiros = isset($datos[0]['retiros']) && $datos[0]['retiros']->isNotEmpty();
                            $hasEfectivo = isset($datos[0]['efectivo_diario']) && !is_null($datos[0]['efectivo_diario']);
                        @endphp

                        @if (!$hasPrestamos && !$hasRetiros && !$hasEfectivo)
                            <div class="col-12">
                                <p>No hay resultados para el rango de fechas seleccionado.</p>
                            </div>
                        @else
                            {{-- Columna izquierda: Préstamos --}}
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <h3 class="text-xl font-bold mb-3 dark:text-white">TOTAL DE PRÉSTAMOS EN EFECTIVO</h3>
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        @php
                                            $totalPrestamos = 0;
                                            $totalRetiros = 0;
                                            $inicial = $datos[0]['saldo_inicial'] ?? null;
                                            $saldoTotal = $inicial?->total ?? 0;
                                        @endphp

                                        <tr>
                                            <td>SALDO INICIAL</td>
                                            <td>${{ number_format($saldoTotal, 2) }}</td>
                                        </tr>

                                        @foreach($datos[0]['prestamos'] as $prestamo)
                                            @php
                                                $totalPrestamos += $prestamo->diferencia;
                                            @endphp
                                            <tr>
                                                <td>PRÉSTAMO {{ $loop->iteration }}</td>
                                                <td>${{ number_format($prestamo->diferencia, 2) }}</td>
                                            </tr>
                                        @endforeach

                                        @foreach($datos[0]['retiros'] as $retiro)
                                            @php
                                                $totalRetiros += $retiro->saldo_aprobado;
                                            @endphp
                                            <tr>
                                                <td>RET DE AHORRO {{ $loop->iteration }}</td>
                                                <td>${{ number_format($retiro->saldo_aprobado, 2) }}</td>
                                            </tr>
                                        @endforeach

                                        <!-- Total final -->
                                        <tr class="fw-bold bg-light">
                                            <td>TOTAL PRÉSTAMOS</td>
                                            <td>${{ number_format($totalPrestamos, 2) }}</td>
                                        </tr>
                                        <tr class="fw-bold bg-light">
                                            <td>TOTAL RETIROS</td>
                                            <td>${{ number_format($totalRetiros, 2) }}</td>
                                        </tr>
                                        <tr class="fw-bold bg-secondary text-black">
                                            <td>SALDO FINAL</td>
                                            <td>${{ number_format($saldoTotal - ($totalPrestamos + $totalRetiros), 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- Columna derecha: Efectivo Diario --}}
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <h3 class="text-xl font-bold mb-3 dark:text-white">DENOMINACIÓN TOTAL</h3>
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        @php $efectivo = $datos[0]['efectivo_diario']; @endphp
                                        <tr>
                                            <td>{{ $efectivo->b_mil }} DE $1,000</td>
                                            <td>${{ number_format( ($efectivo->b_mil * 1000), 2) }} </td>
                                        </tr>
                                        <tr>
                                            <td>{{ $efectivo->b_quinientos }} DE $500</td>
                                            <td>${{ number_format( ($efectivo->b_quinientos * 500), 2) }} </td>
                                        </tr>
                                        <tr>
                                            <td>{{ $efectivo->b_doscientos }} DE $200</td>
                                            <td>${{ number_format( ($efectivo->b_doscientos * 200), 2) }} </td>
                                        </tr>
                                        <tr>
                                            <td>{{ $efectivo->b_cien }} DE $100</td>
                                            <td>${{ number_format( ($efectivo->b_cien * 100), 2) }} </td>
                                        </tr>
                                        <tr>
                                            <td>{{ $efectivo->b_cincuenta }} DE $50</td>
                                            <td>${{ number_format( ($efectivo->b_cincuenta * 50), 2) }} </td>
                                        </tr>
                                        <tr>
                                            <td>{{ $efectivo->b_veinte }} DE $20</td>
                                            <td>${{ number_format( ($efectivo->b_veinte * 20), 2) }} </td>
                                        </tr>
                                        <tr>
                                            <td>MONEDAS</td>
                                            <td>${{ number_format( $efectivo->monedas, 2) }} </td>
                                        </tr>
                                        <tr class="fw-bold bg-warning text-black">
                                            <td>TOTAL</td>
                                            <td>${{ number_format( $efectivo->total, 2) }} </td>
                                        </tr>
                                        <tr class="fw-bold bg-secondary text-black">
                                            <td>DIREFENCIA</td>
                                            <td>${{ number_format( ($efectivo->total - ($saldoTotal - ($totalPrestamos + $totalRetiros))), 2) }} </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    </div>
                </div>



            </div>

            <div class="row mb-3">
                <div class="col-lg-12 col-md-12 col-sm-12" align="center">
                    <div class="mb-3 text-end">
                        <a href="{{ route('admin.reportes.arqueo.caja', ['tipo' => $tipo, 'fecha_inicio' => $fechaInicio->toDateString(), 'fecha_fin' => $fechaFin->toDateString(), 'formato' => 'excel']) }}"
                        class="btn btn-success me-2">Descargar Excel</a>

                        <a href="{{ route('admin.reportes.arqueo.caja', ['tipo' => $tipo, 'fecha_inicio' => $fechaInicio->toDateString(), 'fecha_fin' => $fechaFin->toDateString(), 'formato' => 'pdf']) }}"
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
