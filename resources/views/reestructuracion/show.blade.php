@extends('layouts.app')

@section('css')
    <style type="text/css">
        @media print {
            .no-imprimir {
                display: none;
            }
        }

        @media screen {
            .solo-impresion {
                display: none;
            }
        }

        .bg-navyblue {
            background-color: #29527e;
        }

        .bg-skyblue {
            background-color: #cce5ff;
        }

        .footer {
            position: relative;
            top: -200px; // this sets the footer -20px from the top of the next
            //header/page ... 20px above the bottom of target page
            //so make sure it is more negative than your footer's height.

            height: 100px; //notice that the top position subtracts
            //more than the assigned height of the footer
        }
    </style>
@stop

@section('content')

    @php
        use Carbon\Carbon;
    @endphp

    <br />

    <div class="card card-outline card-primary no-imprimir">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <h3 class="card-title b-0">REESTRUCTURACIÓN / PRÉSTAMOS ACTIVOS</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="register-box-body">
                <div class="card border border-success shadow-0 mb-3">
                    <div class="card-header">
                        <h4>DETALLES DEL SOCIO</h4>
                    </div>
                    <div class="card-body text-dark">
                        <div class="row mb-3">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="col">
                                    <h5><strong>SOCIO</strong></h5>
                                    <h6>{{ $socio->nombre_completo }}</h6>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="col">
                                    <h5><strong>NÚMERO DE SOCIO</strong></h5>
                                    <h6>{{ $socio->num_socio }}</h6>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="col">
                                    <h5><strong>PRESTAMOS ACTIVOS</strong></h5>
                                    <h6>{{ $socio->numero_prestamos }}</h6>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="col">
                                    <h5><strong>FECHA DE ALTA</strong></h5>
                                    <h6>{{ $socio->fecha_alta }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="col">
                                    <h5><strong>RFC</strong></h5>
                                    <h6>{{ $socio->rfc }}</h6>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="col">
                                    <h5><strong>CURP</strong></h5>
                                    <h6>{{ $socio->curp }}</h6>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="col">
                                    <h5><strong>CUIP</strong></h5>
                                    <h6>{{ $socio->cuip }}</h6>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="col">
                                    <h5><strong>SALDO AHORRADO</strong></h5>
                                    <h6>{{ $socio->saldo }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card border border-primary shadow-0 mb-3">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-lg-6 col-md-12">
                                <h4 class="card-title">HISTORIAL</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-dark">
                        <div class="row mb-3">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card-body table-responsive p-0">
                                    @if (isset($prestamos))

                                        <table id="historial_socios"
                                            class="table table-striped table-hover dataTable dtr-inline display responsive nowrap"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Monto</th>
                                                    <th>Intereses</th>
                                                    <th>Monto+Intereses</th>
                                                    <th>Estatus</th>
                                                    <th>Opc</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($prestamos as $row)
                                                    <tr>
                                                        <td>{{ Carbon::parse($row->fecha_prestamo)->format('d/m/Y H:i:s') }}
                                                        </td>
                                                        <td>$ {{ number_format($row->monto_prestamo, 2) }}
                                                        </td>
                                                        <td>$ {{ number_format($row->total_intereses, 2) }}
                                                        </td>
                                                        <td>$
                                                            {{ number_format($row->monto_prestamo + $row->total_intereses, 2) }}
                                                        </td>
                                                        <td>
                                                            @if ($row->estatus == 'PRE-AUTORIZADO')
                                                                <button type="button" class="btn btn-success btn-sm"
                                                                    style="background-color: rgb(255, 98, 0);">PRE-AUTORIZADO</button>
                                                            @endif
                                                            @if ($row->estatus == 'AUTORIZADO')
                                                                <button type="button"
                                                                    class="btn btn-success btn-sm">AUTORIZADO</button>
                                                            @endif
                                                            @if ($row->estatus == 'CANCELADO')
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm">CANCELADO</button>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group">
                                                                <button type="button"
                                                                    class="btn btn-primary dropdown-toggle"
                                                                    data-mdb-toggle="dropdown" aria-expanded="false">
                                                                    Acciones
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li><a class="dropdown-item"
                                                                            href="{{ Route('admin.prestamos.show', $row->id) }}">Detalles</a>
                                                                    </li>
                                                                    <li>
                                                                        {!! Form::open([
                                                                            'method' => 'POST',
                                                                            'id' => 'form_restructuracion',
                                                                            'route' => ['admin.reestructuracion.store'],
                                                                        ]) !!}
                                                                            <input type="hidden" name="prestamoId[]" value="{{ $row->id }}">
                                                                            {!! Form::button('Pagar péstamos', ['id' => 'btn_saldar', 'class' => 'dropdown-item']) !!}
                                                                        {!! Form::close() !!}
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
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    @else
                                        <h1>No hay información por mostrar.</h1>
                                    @endif
                                </div>
                            </div>

                            @include('reestructuracion._modal_contrasenia')
                        </div>
                    </div>
                </div>

                <br />
                <br />

            </div>
        </div>
    </div>
@stop


@section('js')
    <script>
        $(document).ready(function() {
            $('#historial_socios').DataTable({
                "language": {
                    "url": "{{ asset('/json/i18n/es_es.json') }}"
                }
            });
        });

        // muestra el modal de la contraseña
        $(document).on('click', '#btn_saldar', function() {
            $('.modalContrasenia').modal({
                backdrop: 'static', // Para que el modal no se cierre haciendo clic fuera de él
                keyboard: false // Para que el modal no se cierre con la tecla Esc
            });

            $('.modalContrasenia').modal('show'); // Muestra el modal
        });

        // Escucha el clic en el botón con el atributo data-mdb-dismiss
        $(document).on('click', '[data-mdb-dismiss="modal"]', function() {
            // Cierra el modal
            $('#passwordInput').val('');
            $(this).closest('.modal').modal('hide');
        });

        // Envia la contraseña para validar el AVAL
        $(document).on('click', '.btn-aprobar-aval', function() {
            var aval = $('.modalSocio').val();
            var password = $('.modalPassword').val();
            var avalId = $('.modalIdSocio').val();
            validaAval(aval, password, avalId)
        });

        // ajax para validar la contraseña del aval
        function validaAval(nameAval, pass, avalId) {
            $("#errorPass").hide();
            $.ajax({
                url: "{{ route('valida.aval') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                    aval: nameAval,
                    pass: pass,
                },
                success: function(response) {
                    //console.log('success:', JSON.stringify(response));
                    if (response.estado === 'aprobado') {
                        $('#passwordInput').val('');
                        $(".modalContrasenia").modal('hide');

                        console.log('Aqui se enviaria el formulario');
                        document.getElementById('form_restructuracion').submit();
                    }

                },
                error: function(response) {
                    //console.log('error:', JSON.stringify(response));
                    if (response.responseJSON === 'invalido') {
                        $("#errorPass").show();
                    }
                },
            });
        }
    </script>

    @if (Session::has('correcto'))
        <script type="text/javascript">
            var id = {{ session('id') }};
            setTimeout(function() {
                window.open("{{ url('/prestamos') }}/" + id, '_blank');
            }, 200);
            <?php Session::forget('id'); ?>
        </script>
    @endif

@stop
