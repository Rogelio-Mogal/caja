@extends('layouts.app')

@section('content')

    @php
        use Carbon\Carbon;
    @endphp

    <br />
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Ahorro voluntario</h3>
            <div class="card-tools">
                <!-- Buttons, labels, and many other things can be placed here! -->
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            @if (isset($ahorros))
                <div class="register-box-body">
                    <div class="card border border-primary shadow-0 mb-3" style="max-width: 100%;">
                        <div class="card-body text-dark">
                            <table id="socios"
                                class="table table-striped table-hover dataTable dtr-inline display responsive nowrap"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Socio</th>
                                        <th>Ahorro</th>
                                        <th>Fecha</th>
                                        <th>Opc</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ahorros as $row)
                                        <tr>
                                            <td>{{ $row->id }}</td>
                                            <td>{{ $row->nombre }}</td>
                                            <td>$ {{ number_format($row->monto  ,2)}} </td>
                                            <td>{{ $row->fecha_ahorro }}</td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                                        Acciones
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <button type="button" class="dropdown-item show_modal_cancelar"
                                                                data-id="{{ $row->id }}">
                                                                Cancelar
                                                            </button>
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
                            @include('ahorro._modal_cancelar_ahorro')

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
            // Verifica si hay un mensaje de éxito en la URL y muestra el mensaje CANCELADO
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('message') && urlParams.get('message') === 'cancelado') {
                //alert('La operación se realizó con éxito.');
                Swal.fire({
                    icon: 'success',
                    title: 'Ahorro cancelado',
                    text: 'El ahorro se cancelo exitosamente.',
                });
            }

            $('#socios').DataTable({
                "language": {
                    "url": "{{ asset('/json/i18n/es_es.json') }}"
                }
            });

            // MUESTRA EL MODAL PARA CANCELAR EL AHORRO
            $(document).on('click', '.show_modal_cancelar', function() {
                var dataId = $(this).data('id');
                var ruta = '{{ route('admin.ahorros.show', ':id') }}';
                ruta = ruta.replace(':id', dataId);

                $.ajax({
                    url: ruta,
                    type: "GET",
                    dataType: 'json',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        socios_id: dataId,
                    },
                    success: function(response) {
                        //console.log('success:', JSON.stringify(response));
                        $(response).each(function(i, v) {
                            var total = parseFloat(v.monto);

                            const montoFormateado2 = new Intl.NumberFormat('es-MX', {
                                style: 'currency',
                                currency: 'MXN'
                            }).format(v.monto);

                            $('#prestamo_id2').val(v.id);
                            $('#socio2').text(v.nombre_completo);
                            $('#fecha2').text(v.fecha_ahorro);
                            $('#monto2').text(montoFormateado2);
                        });
                    },
                    error: function(response) {
                        console.log('error:', JSON.stringify(response));
                        $(".modalCancelar").modal('hide');
                    },
                });

                $(".modalCancelar").modal('show');
            });

            // Escucha el clic en el botón con el atributo data-mdb-dismiss
            $(document).on('click', '[data-mdb-dismiss="modal"]', function() {
                // Cierra el modal
                $(this).closest('.modal').modal('hide');
            });

            // VALIDAMOS Y ENVIAMOS EL MONTO AHORRO, PARA CANCELAR EL AHORRO
            $(document).on('click', '.btn-cancelar-prestamo', function() {
                var prestamoId = $('#prestamo_id2').val();
                var motivo = $('#motivo_cancelacion').val();
                switch (true) {
                    case motivo === '':
                        console.log('case 1');
                        Swal.fire({
                            icon: 'error',
                            title: 'No ha descrito el motivo de la cancelación',
                            text: 'Por favor verifique la información.',
                        });
                        break;
                    default:
                        //$("#submitBtn").attr("disabled", true);
                        //document.getElementById('form_prestamos').submit();
                        $("#btn-cancelar-prestamo").attr("disabled", true);
                        console.log('enviaria el formulario');
                        cancelaAhorro(prestamoId, motivo);
                        break;
                }
            });

            function cancelaAhorro(prestamoId, motivo) {
                const ajaxData = {
                    "_token": "{{ csrf_token() }}",
                    id: prestamoId,
                    comentarios: motivo,
                };

                var elimina = '{{ route('admin.ahorros.destroy', ':quit') }}';
                var ruta = elimina.replace(':quit', prestamoId)

                $.ajax({
                    url: ruta,
                    type: "DELETE",
                    dataType: 'json',
                    data: ajaxData,
                    success: function(response) {
                        //console.log('success:', JSON.stringify(response));
                        window.location.href = window.location.href + "?message=cancelado";
                    },
                    error: function(response) {
                        console.log('error:', JSON.stringify(response));
                        Swal.fire({
                            type: 'error',
                            title: 'Advertencia.',
                            text: 'Hubo un error durante el proceso, por favor intente de nuevo.',
                        });
                    },
                });
            }

        });
    </script>
@stop
