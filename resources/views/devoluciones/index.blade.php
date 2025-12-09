@extends('layouts.app')

@section('content')

    @php
        use Carbon\Carbon;
    @endphp

    <br />
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Devoluciones</h3>
            <div class="card-tools">
                <!-- Buttons, labels, and many other things can be placed here! -->
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            @if (isset($devolucion))
                <div class="register-box-body">
                    <div class="card border border-primary shadow-0 mb-3" style="max-width: 100%;">
                        <div class="card-body text-dark">
                            <table id="devolucion"
                                class="table table-striped table-hover dataTable dtr-inline display responsive nowrap"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Solicitud</th>
                                        <th>Saldo</th>
                                        <th>Estatus</th>
                                        <th>Opc</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($devolucion as $row)
                                        <tr>
                                            <td>{{ $row->id }}</td>
                                            <td>{{ $row->socio->nombre_completo ?? 'SIN SOCIO' }}</td>
                                            <td>{{ date('d-m-Y H:i:s', strtotime($row->fecha_captura)) }}</td>
                                            <td>$ {{ number_format($row->importe, 2) }} </td>
                                            <td>{{ $row->estatus }} </td>
                                            @if($row->activo == 1 && $row->estatus != 'AUTORIZADO')
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-primary dropdown-toggle"
                                                            data-mdb-toggle="dropdown" aria-expanded="false">
                                                            Acciones
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <button type="button" class="dropdown-item show_modal"
                                                                    data-id="{{ $row->id }}">
                                                                    Finalizar
                                                                </button>
                                                                <button type="button" class="dropdown-item show_modal_cancelar"
                                                                    data-id="{{ $row->id }}">
                                                                    Cancelar
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            @else
                                                <td></td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td>No se encontró ningún registro</td>
                                            <td style="display:none;"></td>
                                            <td style="display:none;"></td>
                                            <td style="display:none;"></td>
                                            <td style="display:none;"></td>
                                            <td style="display:none;"></td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @include('devoluciones._modal_aprobar_devolucion')
                            @include('devoluciones._modal_cancelar_devolucion')
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
            $('#devolucion').DataTable({
                "language": {
                    "url": "{{ asset('/json/i18n/es_es.json') }}"
                }
            });

            // Verifica si hay un mensaje de éxito en la URL y muestra el mensaje CANCELADO
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('message') && urlParams.get('message') === 'cancelado') {
                //alert('La operación se realizó con éxito.');
                Swal.fire({
                    icon: 'success',
                    title: 'Devolución cancelado',
                    text: 'La devolución se cancelo exitosamente.',
                });
            }

            // Verifica si hay un mensaje de éxito en la URL y muestra el mensaje APROBADO
            if (urlParams.has('message') && urlParams.get('message') === 'aprobado') {
                //alert('La operación se realizó con éxito.');
                Swal.fire({
                    icon: 'success',
                    title: 'Devolución aprobado',
                    text: 'La devolución fue aprobado exitosamente.',
                });
            }

            // MUESTRA EL MODAL PARA CONFIRMAR EL PRÉSTAMO
            $(document).on('click', '.show_modal', function() {
                console.log('muestra modal?');
                var dataId = $(this).data('id');
                var ruta = '{{ route('admin.devoluciones.show', ':id') }}';
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
                            $('#prestamo_id').val(v.id);
                            $('#socio').text(v.socio.nombre_completo);
                            $('#fecha').text(v.fecha_captura);
                            $('#monto').text(v.importe);
                        });
                    },
                    error: function(response) {
                        console.log('error:', JSON.stringify(response));
                        $(".modalApruebaPrestamo").modal('hide');
                    },
                });

                $(".modalApruebaPrestamo").modal('show');
            });

            // MUESTRA EL MODAL PARA CANCELAR EL PRÉSTAMO
            $(document).on('click', '.show_modal_cancelar', function() {
                var dataId = $(this).data('id');
                var ruta = '{{ route('admin.devoluciones.show', ':id') }}';
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
                            console.log(v);
                            var total = parseFloat(v.monto_prestamo) + parseFloat(v
                                .total_intereses);
                            $('#prestamo_id2').val(v.id);
                            $('#socio2').text(v.socio.nombre_completo);
                            $('#fecha2').text(v.fecha_captura);
                            $('#monto2').text(v.importe);
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

            // VALIDAMOS Y ENVIAMOS EL MONTO PRÉSTAMO, PARA AFIRMAR EL PRÉSTAMO
            $(document).on('click', '.btn-aprobar-prestamo', function() {
                console.log('valiar los datos y enviar el fomulario');
                var prestamoId = $('#prestamo_id').val();
                var metodoPago = $('#forma_pago').val();
                var primerPago = $('#fecha_primer_pago').val();
                switch (true) {
                    case $('#forma_pago').val() === '-1' || primerPago === '':
                        console.log('case 1');
                        Swal.fire({
                            icon: 'error',
                            title: 'Hay datos pendientes por requisitar',
                            text: 'Por favor verifique la información.',
                        });
                        break;
                    default:
                        $(".btn-aprobar-prestamo").attr("disabled", true);
                        console.log('enviaria el formulario');
                        apruebaPrestamo(prestamoId, metodoPago, primerPago);
                        break;
                }
            });

            // VALIDAMOS Y ENVIAMOS EL MONTO PRÉSTAMO, PARA CANCELAR EL PRÉSTAMO
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
                        cancelaPrestamo(prestamoId, motivo);
                        break;
                }
            });

            function apruebaPrestamo(prestamoId, metodoPago, primerPago) {
                const ajaxData = {
                    "_token": "{{ csrf_token() }}",
                    _method: 'PUT', // Simula método PUT
                    id: prestamoId,
                    forma_pago: metodoPago,
                    fecha_primer_pago: primerPago,
                    nota: $('#nota').val(),
                };

                var actualiza = '{{ route('admin.devoluciones.update', ':quit') }}';
                var ruta = actualiza.replace(':quit', prestamoId)

                $.ajax({
                    //url: "{{ route('admin.devoluciones.store') }}",
                    //url: "{{ url('devoluciones') }}/" + prestamoId, // Ruta tipo resource para update
                    url: ruta,
                    type: "POST",
                    dataType: 'json',
                    data: ajaxData,
                    success: function(response) {
                        //console.log('success:', JSON.stringify(response));
                        setTimeout(function() {
                            // Abre el enlace externo en una nueva ventana o pestaña
                            window.open("{{ url('/recibo-devolucion') }}/" + response,
                                '_blank');

                            // Redirige a la página actual con el mensaje "aprobado" después de 200 milisegundos
                            setTimeout(function() {
                                window.location.href = window.location.href +
                                    "?message=aprobado";
                            }, 200);
                        }, 200);
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

            function cancelaPrestamo(prestamoId, motivo) {
                const ajaxData = {
                    "_token": "{{ csrf_token() }}",
                    id: prestamoId,
                    comentarios: motivo,
                };

                var elimina = '{{ route('admin.devoluciones.destroy', ':quit') }}';
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

            // CALENDARIO
            const datepickerTranslated = document.querySelector('.datepicker-translated');
            const filterFunction = (date) => {
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                const selected = new Date(date);
                selected.setHours(0, 0, 0, 0);

                // Permite seleccionar solo hoy o fechas futuras
                return selected >= today;
            }

            new mdb.Datepicker(datepickerTranslated, {
                confirmDateOnSelect: true,
                disablePast: true,
                title: 'Seleccione una fecha',
                monthsFull: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto',
                    'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                weekdaysFull: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                weekdaysShort: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                weekdaysNarrow: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
                okBtnText: 'Ok',
                clearBtnText: 'Limpiar',
                cancelBtnText: 'Cancelar',
                filter: filterFunction
            });

            // Bloquea entrada manual en el campo de fecha
            $('#fecha_primer_pago').on('keydown', function(e) {
                e.preventDefault();
            });


            /*
            const datepickerTranslated = document.querySelector('.datepicker-translated');
            const filterFunction = (date) => {
                const dayOfMonth = date.getDate();
                const lastDayOfMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();

                // Permite la selección solo si es el día 15 o el último día del mes
                return dayOfMonth === 15 || dayOfMonth === lastDayOfMonth;
            }
            new mdb.Datepicker(datepickerTranslated, {
                confirmDateOnSelect: true,
                disablePast: true,
                title: 'Seleccione una fecha',
                monthsFull: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto',
                    'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ],
                monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov',
                    'Dic'
                ],
                weekdaysFull: ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag',
                    'Samstag'
                ],
                weekdaysShort: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                weekdaysNarrow: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
                okBtnText: 'Ok',
                clearBtnText: 'Limpiar',
                cancelBtnText: 'Cancelar',
                filter: filterFunction
            });

            // Evitar entrada de datos en el campo fecha_primer_pago
            var inputPagoQuincenal = $('#fecha_primer_pago');
            inputPagoQuincenal.on('keydown', function(e) {
                e.preventDefault();
            });
            */

        });
    </script>
@stop
