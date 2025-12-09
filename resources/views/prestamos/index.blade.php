@extends('layouts.app')

@section('content')

    @php
        use Carbon\Carbon;
    @endphp

    <br />
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Prestamos por día</h3>
            <div class="card-tools">
                <!-- Buttons, labels, and many other things can be placed here! -->
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            @if (isset($prestamos))
                <div class="register-box-body">
                    <div class="card border border-primary shadow-0 mb-3" style="max-width: 100%;">
                        <div class="card-body text-dark">
                            <table id="socios"
                                class="table table-striped table-hover dataTable dtr-inline display responsive nowrap"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Socio</th>
                                        <th>Monto</th>
                                        <th>Intereses</th>
                                        <th>Monto+Intereses</th>
                                        <th>Opc</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($prestamos as $row)
                                        <tr>
                                            <td>{{ Carbon::parse($row->fecha_prestamo)->format('d/m/Y H:i:s') }}</td>
                                            <td>{{ $row->nombre_completo }}</td>
                                            <td>$ {{ number_format($row->monto_prestamo, 2) }} </td>
                                            <td>$ {{ number_format($row->total_intereses, 2) }} </td>
                                            <td>$ {{ number_format($row->monto_prestamo + $row->total_intereses, 2) }}
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                                        Acciones
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item"
                                                                href="{{ Route('admin.prestamos.show', $row->id) }}">Detalles</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td>No se encontró ningún registro</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
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
            $('#socios').DataTable({
                "language": {
                    "url": "{{ asset('/json/i18n/es_es.json') }}"
                }
            });
        });
    </script>

    @if (Session::has('id'))
        <script type="text/javascript">
            var id = {{ session('id') }};
            setTimeout(function() {
                window.open("{{ url('/prestamos') }}/" + id, '_blank');
            }, 200);
            <?php Session::forget('id'); ?>
        </script>
    @endif

    @if (Session::has('id_especial'))
        <script type="text/javascript">
            var id = {{ session('id_especial') }};
            setTimeout(function() {
                window.open("{{ url('/prestamos') }}/" + id, '_blank');
            }, 200);
            <?php Session::forget('id_especial'); ?>
        </script>
    @endif
@stop
