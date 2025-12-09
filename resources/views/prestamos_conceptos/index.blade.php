@extends('layouts.app')

@section('content')

    @php
        use Carbon\Carbon;
    @endphp

    <br />
    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <h3>Conceptos préstamos especiales</h3>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <a href="{{ Route('admin.prestamos.comceptos.create') }}" class="btn btn-sm btn-success">Nuevo concepto préstamo especial</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if (isset($prestamosConceptos))
                <div class="register-box-body">
                    <div class="card border border-primary shadow-0 mb-3" style="max-width: 100%;">
                        <div class="card-body text-dark">
                            <table id="socios"
                                class="table table-striped table-hover dataTable dtr-inline display responsive nowrap"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Concepto</th>
                                        <th>Precio</th>
                                        <th>Plazos</th>
                                        <th>N° piezas</th>
                                        <th>Comentarios</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($prestamosConceptos as $row)
                                        <tr>
                                            <td> {{ $row->concepto }}</td>
                                            <td>$ {{ number_format($row->precio  ,2)}}</td>
                                            <td> {{ $row->num_plazos }} </td>
                                            <td> {{ $row->num_piezas }}
                                            <td> {{ $row->comentarios }}</td>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td>No se encontró ningún registro</td>
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
            Swal.fire({
                icon: 'success',
                title: 'Préstamo especial creado ',
                text: 'La información se ha gurdado satisfactoriamente.',
            });
        </script>
    @endif



@stop
