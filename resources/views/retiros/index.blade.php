@extends('layouts.app')

@section('content')

    @php
        use Carbon\Carbon;
    @endphp

    <br />
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Retiros pre-aprobados</h3>
        </div>

        <div class="card-body">
            @if (isset($retiro))
                <div class="register-box-body">
                    <div class="card border border-primary shadow-0 mb-3" style="max-width: 100%;">
                        <div class="card-body text-dark">
                            <table id="retiros"
                                class="table table-striped table-hover dataTable dtr-inline display responsive nowrap"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Fecha de captura</th>
                                        <th>Socio</th>
                                        <th>Monto</th>
                                        <th>Forma de pago</th>
                                        <th>Opc</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($retiro as $row)
                                        <tr>
                                            <td>{{ $row->fecha_captura }}</td>
                                            <td>{{ $row->nombre_completo }}</td>
                                            <td>$ {{ number_format($row->monto_retiro  ,2)}} </td>
                                            <td>{{ $row->forma_pago }}</td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                                        Acciones
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item"
                                                                href="#">Detalles</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td> 
                                        </tr>
                                    @empty
                                        <tr>
                                            <td>No se encontró ningún registro</td>
                                            <td style="display:none;"></td>
                                            <td style="display:none;"></td>
                                            <td style="display:none;"></td>
                                            <td style="display:none;"></td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>


                        </div>
                    </div>
                </div>
            @else
                <h1>No hay información por mostrar.</h1>
            @endif
        </div>
    </div>
@stop


@section('js')
    <script>
        $(document).ready(function() {
            $('#retiros').DataTable({
                "language": {
                    "url": "{{ asset('/json/i18n/es_es.json') }}"
                }
            });
        });
    </script>
@stop
