@extends('layouts.app')

@section('css')
    <style type="text/css">
        table tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        table tr:nth-child(even) {
            background-color: #fff;
        }

        .dropdown-menu {
            min-width: unset !important;
        }

        .custom-icon-green {
            color: #28a745;
        }

        .custom-icon-red {
            color: #dc3545;
        }

        .custom-icon-blue {
            color: #007bff;
        }
    </style>
@stop


@section('content')
    @php use Carbon\Carbon; @endphp
    <br />
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Reportes</h3>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.reportes.index') }}" class="row g-3 mb-4 align-items-end">
                <div class="row">
                    <input type="hidden" name="tipo" value="{{ $tipo }}">

                    <div class="col-lg-5 col-md-5 col-sm-5">
                        <label for="fecha_inicio" class="form-label">Desde</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control"
                            value="{{ request('fecha_inicio', \Carbon\Carbon::now()->startOfMonth()->toDateString()) }}">
                    </div>

                    <div class="col-lg-5 col-md-5 col-sm-5">
                        <label for="fecha_fin" class="form-label">Hasta</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control"
                            value="{{ request('fecha_fin', \Carbon\Carbon::now('America/Mexico_City')->toDateString()) }}">
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="form-control btn btn-primary">Filtrar</button>
                    </div>
                </div>
            </form>

            @if(!$fechaInicio || !$fechaFin)
                <div class="alert alert-info">
                    Por favor selecciona un rango de fechas para ver el reporte de {{ $tipo }}.
                </div>
            @elseif($datos->isEmpty())
                <div class="alert alert-warning">
                    No se encontraron resultados para el rango de fechas seleccionado.
                </div>
            @else
                @if($tipo == 'prestamos')
                    @include('reportes.partials.prestamos', ['datos' => $datos])
                @elseif($tipo == 'pago-liquidacion')
                    @include('reportes.partials.pagos', ['datos' => $datos])
                @elseif($tipo == 'retiros')
                    @include('reportes.partials.retiros', ['datos' => $datos])
                @elseif($tipo == 'ahorros')
                    @include('reportes.partials.ahorros', ['datos' => $datos])
                @elseif($tipo == 'prestamo-pago-nomina')
                    @include('reportes.partials.prestamo_pago_nomina', ['datos' => $datos])
                @elseif($tipo == 'ingreso-efectivo')
                    @include('reportes.partials.ingreso_efectivo', ['datos' => $datos])
                @elseif($tipo == 'arqueo-caja')
                    @include('reportes.partials.arqueo_caja', ['datos' => $datos])
                @endif
            @endif
        </div>
        <br />
    </div>
@stop

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            let usuarioId = 0;
            console.log('jQuery is working...');
            $('#tbl-usuarios').DataTable({
                "language": {
                    "url": "{{ asset('/json/i18n/es_es.json') }}"
                },
                "order": [
                    [0, "asc"]
                ],
            });

            $('.btn-eliminar').submit(function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Seguro que desea eliminar al usuario seleccionado?',
                    text: "Esta acción no se puede deshacer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar",
                }).then(resultado => {
                    if (resultado.value) {
                        this.submit();
                        // Hicieron click en "Sí"
                        /*console.log("*se elimina*");*/
                    } else {
                        // Dijeron que no
                        /*console.log("*NO se elimina*");*/
                    }
                });
            })

            $(document).on('click', '.btn-user', function() {
                //$( ".btn-user" ).click(function() {
                usuarioId = $(this).attr("usuarioId");
                console.log('val: ' + usuarioId);
            });
        });
    </script>
    @if (session('usuarioEliminado') == 'ok')
        <script type="text/javascript">
            Swal.fire(
                'Eliminado!',
                'El usuario ha sido eliminado.',
                'success'
            );
        </script>
    @endif

    @if (session('user') == 'fail')
        <script type="text/javascript">
            Swal.fire(
                'Error!',
                'Huho un error durante el proceso y el usuario no se ha eliminado.',
                'warning'
            );
        </script>
    @endif
@stop
