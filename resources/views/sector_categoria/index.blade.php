@extends('layouts.app')
@section('css')
    <style type="text/css">
        @media only screen and (max-width: 700px) {
            video {
                max-width: 100%;
            }
        }
        .uppercase {
            text-transform: uppercase;
        }
    </style>
@stop
@section('content')
    <br />
    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <h3>Sector / categoría</h3>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    @can('crear-rol')
                        <a href="{{ Route('admin.sector.categoria.create') }}" class="btn btn-sm btn-success">Nuevo sector/categoría</a>
                    @endcan    
                </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            {{-- @if (isset($socios)) --}}
            <div class="register-box-body">
                <div class="card border border-primary shadow-0 mb-3" style="max-width: 100%;">
                    <div class="card-body text-dark">
                        <table id="sector_caregoria" class="table table-striped table-hover display responsive nowrap"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Opc</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


@section('js')
    <script>
        $(document).ready(function() {

            const postData = {
                _token: $('input[name=_token]').val()
            };
            var ruta = "{{ Request::url() }}";
            $('#sector_caregoria').DataTable({
                "language": {
                    "url": "{{ asset('/json/i18n/es_es.json') }}"
                },
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('ajax.sector.categoria') }}", // Debes especificar la ruta correcta de tu controlador
                    type: "GET",
                    data: function(d) {
                        d._token = $('input[name=_token]').val();
                        // Aquí puedes enviar parámetros adicionales si es necesario
                    },
                    "dataSrc": "data",
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                },
                "lazyLoad": true,
                "columns": [{
                        data: 'id'
                    },
                    {
                        data: 'nombre'
                    },
                    {
                        data: 'tipo'
                    },
                    
                    {
                        data: null,
                        render: function(data, type, row) {  
                            return '<div class="btn-group">' +
                                        '<button type="button" class="btn btn-primary dropdown-toggle"' +
                                            'data-mdb-toggle="dropdown" aria-expanded="false">' +
                                            'Acciones' +
                                        '</button>' +
                                        '<ul class="dropdown-menu">' +
                                            '<li>' +
                                                '<a class="dropdown-item"' +
                                                'href="' + ruta + '/' + row.id + '/edit">' +
                                                'Editar' +
                                                '</a>' +
                                            '</li>' +
                                            '<li>' +
                                                '<a class="dropdown-item"' +
                                                'href="' + ruta + '/' + row.id + '">' +
                                                'Detalles/Eliminar' +
                                                '</a>' +
                                            '</li>' +
                                        '</ul>' +
                                    '</div>';
                        }
                    }
                ]
            });
        });
    </script>

    @if (Session::has('correcto'))
        <script type="text/javascript">
            Swal.fire({
                icon: 'success',
                title: 'Pagos de préstamos efectuados. ',
                text: 'Operación exitosa.',
            });
        </script>
    @endif

    @if (Session::has('error'))
        <script type="text/javascript">
            Swal.fire({
                icon: 'error',
                title: 'Hubo un error durante el proceso. ',
                text: 'Por favor intente más tarde.',
            });
        </script>
    @endif

@stop
