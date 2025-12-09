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
    <br />
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Usuarios</h3>
        </div>

        <div class="card-body">
            @if (isset($users))
                <div class="register-box-body">
                    <div class="card border border-primary shadow-0 mb-3" style="max-width: 100%;">
                        <div class="card-body text-dark">
                            <table id="tbl-usuarios" class="table table-striped table-hover display responsive nowrap"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Correo</th>
                                        <th>Rol</th>
                                        <th>Estatus</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <?php $roles = $user->roles; ?>
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if (!empty($user->getRoleNames()))
                                                    @foreach ($user->getRoleNames() as $rolName)
                                                        <h5><span>{{ $rolName }}</span></h5>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>@if($user->activo == 1)
                                                    <span class="badge rounded-pill badge-success fs-6">Activo</span>
                                                @endif
                                                @if($user->activo == 0)
                                                    <span class="badge rounded-pill badge-danger fs-6">Sin acceso</span>
                                                @endif
                                            </td>
                                            <td class="text-center">

                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                                        Acciones
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="{{ Route('admin.usuarios.edit', $user) }}"
                                                                class="dropdown-item" href="#">
                                                                Editar
                                                            </a>
                                                        </li>
                                                        <li>
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['admin.usuarios.destroy', $user->id]]) !!}
                                                                    {!! Form::submit('Eliminar acceso', ['class' => 'dropdown-item']) !!}
                                                            {!! Form::close() !!}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td>No hay usuarios registrados</td>
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
