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
                    <h3>Roles y permisos</h3>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    @can('crear-rol')
                        <a href="{{ Route('admin.roles.create') }}" class="btn btn-sm btn-success">Nuevo rol</a>
                    @endcan    
                </div>
            </div>
        </div>

        <div class="card-body">
            @if (isset($roles))
                <div class="register-box-body">
                    <div class="card border border-primary shadow-0 mb-3" style="max-width: 100%;">
                        <div class="card-body text-dark">
                            <table id="roles"
                                class="table table-striped table-hover dataTable dtr-inline display responsive nowrap"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Rol</th>
                                        <th>Opc</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($roles as $row)
                                        <tr>
                                            <td>{{ $row->name }}</td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                                        Acciones
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @can('editar-rol')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                href="{{ Route('admin.roles.edit', $row->id) }}">
                                                                    Editar
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('borrar-rol')
                                                            <li>
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['admin.roles.destroy', $row->id]]) !!}
                                                                    {!! Form::submit('Eliminar', ['class' => 'dropdown-item']) !!}
                                                                {!! Form::close() !!}
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </td> 
                                        </tr>
                                    @empty
                                        <tr>
                                            <td>No se encontró ningún registro</td>
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
            $('#roles').DataTable({
                "language": {
                    "url": "{{ asset('/json/i18n/es_es.json') }}"
                }
            });
        });
    </script>

    @if (Session::has('success'))
        <script type="text/javascript">
            var mensaje = "{{ session('mensaje') }}";
            Swal.fire({
                icon: 'success',
                title: ''+mensaje+'',
                text: 'Operación exitosa.',
            });
            <?php Session::forget('success'); ?>
        </script>
    @endif
    @if (Session::has('error'))
        <script type="text/javascript">
            Swal.fire({
                icon: 'error',
                title: 'Hubo un error durante el proceso. ',
                text: 'Por favor intente de nuevo más tarde.',
            });
            <?php Session::forget('error'); ?>
        </script>
    @endif
@stop
