@extends('layouts.app')

@section('content')

@section('css')
    <style type="text/css">
        .uppercase {
            text-transform: uppercase;
        }
    </style>
@stop

@php
    use Carbon\Carbon;
@endphp

<br />
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Tesorería / Finalizar préstamo</h3>
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
                        <table id="tesoreria_prestamos"
                            class="table table-striped table-hover dataTable dtr-inline display responsive nowrap"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>Fecha de captura</th>
                                    <th>Socio</th>
                                    <th>Monto solicitado</th>
                                    <th>Monto + intereses</th>
                                    <th>Num Plazos</th>
                                    <th>Pago quincenal</th>
                                    <th>Opc</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prestamos as $row)
                                    <tr>
                                        <td>{{ $row->fecha_captura }}</td>
                                        <td>{{ $row->nombre_completo }}</td>
                                        <td>$ {{ number_format($row->monto_prestamo, 2) }} </td>
                                        <td>$ {{ number_format($row->monto_prestamo + $row->total_intereses, 2) }}
                                        </td>
                                        <td>{{ $row->total_quincenas }}</td>
                                        <td>$ {{ number_format($row->pago_quincenal, 2) }}</td>
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
                                    </tr>
                                @empty
                                    <tr>
                                        <td>No se encontró ningún registro</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @include('tesoreria_prestamo._modal_aprobar_prestamo')
                        @include('tesoreria_prestamo._modal_cancelar_prestamo')
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
                title: 'Prestamo cancelado',
                text: 'El prestamo se cancelo exitosamente.',
            });
        }

        // Verifica si hay un mensaje de éxito en la URL y muestra el mensaje APROBADO
        if (urlParams.has('message') && urlParams.get('message') === 'aprobado') {
            //alert('La operación se realizó con éxito.');
            Swal.fire({
                icon: 'success',
                title: 'Prestamo aprobado',
                text: 'El prestamo fue aprobado exitosamente.',
            });
        }

        $('#tesoreria_prestamos').DataTable({
            "language": {
                "url": "{{ asset('/json/i18n/es_es.json') }}"
            }
        });

        // MUESTRA EL MODAL PARA CONFIRMAR EL PRÉSTAMO
        $(document).on('click', '.show_modal', function() {
            console.log('muestra modal?');
            var dataId = $(this).data('id');
            var ruta = '{{ route('admin.tesoreria.prestamos.show', ':id') }}';
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
                        var total = parseFloat(v.monto_prestamo) + parseFloat(v
                            .total_intereses);
                        var fechaOriginal = v.fecha_primer_pago;
                        var partesFecha = fechaOriginal.split('-');
                        var fechaFormateada = partesFecha[2] + '/' + partesFecha[1] + '/' + partesFecha[0];

                        const montoFormateado = new Intl.NumberFormat('es-MX', {
                            style: 'currency',
                            currency: 'MXN'
                        }).format(v.monto_prestamo);

                        const montoInteresesFormateado = new Intl.NumberFormat('es-MX', {
                            style: 'currency',
                            currency: 'MXN'
                        }).format(total);

                        const pagoQuincenaFormateado = new Intl.NumberFormat('es-MX', {
                            style: 'currency',
                            currency: 'MXN'
                        }).format(v.pago_quincenal);

                        $('#prestamo_id').val(v.id);
                        $('#monto_retiro').val(v.monto_retiro);
                        $('#socio').text(v.nombre_completo);
                        $('#fecha').text(v.fecha_captura);
                        $('#monto').text(montoFormateado);
                        $('#monto_interes').text(montoInteresesFormateado);
                        $('#plazo').text(v.total_quincenas);
                        $('#pago_quincenal').text(pagoQuincenaFormateado);
                        $('#fecha_primer_pago').val(fechaFormateada);

                        //DOCUMENTACIÓN
                        var doc = v.documentacion ?? {};

                        // Oculta checkboxes que ya fueron entregados (true)
                        if (doc.copia_talon) {
                            $('#group_doc_copia_talon').hide();
                        } else {
                            $('#group_doc_copia_talon').show();
                            $('#doc_copia_talon').prop('checked', false);
                        }

                        if (doc.copia_ine) {
                            $('#group_doc_copia_ine').hide();
                        } else {
                            $('#group_doc_copia_ine').show();
                            $('#doc_copia_ine').prop('checked', false);
                        }

                        if (doc.credencial_socio) {
                            $('#group_doc_credencial_socio').hide();
                        } else {
                            $('#group_doc_credencial_socio').show();
                            $('#doc_credencial_socio').prop('checked', false);
                        }

                        if (doc.pagare) {
                            $('#group_doc_pagare').hide();
                        } else {
                            $('#group_doc_pagare').show();
                            $('#doc_pagare').prop('checked', false);
                        }

                        if (doc.solicitud) {
                            $('#group_doc_solicitud').hide();
                        } else {
                            $('#group_doc_solicitud').show();
                            $('#doc_solicitud').prop('checked', false);
                        }
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
            var ruta = '{{ route('admin.tesoreria.prestamos.show', ':id') }}';
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
                        var total = parseFloat(v.monto_prestamo) + parseFloat(v
                            .total_intereses);

                        const montoFormateado2 = new Intl.NumberFormat('es-MX', {
                            style: 'currency',
                            currency: 'MXN'
                        }).format(v.monto_prestamo);

                        const montoInteresesFormateado2 = new Intl.NumberFormat('es-MX', {
                            style: 'currency',
                            currency: 'MXN'
                        }).format(total);

                        const pagoQuincenaFormateado2 = new Intl.NumberFormat('es-MX', {
                            style: 'currency',
                            currency: 'MXN'
                        }).format(v.pago_quincenal);

                        $('#prestamo_id2').val(v.id);
                        $('#monto_retiro2').val(v.monto_retiro);
                        $('#socio2').text(v.nombre_completo);
                        $('#fecha2').text(v.fecha_captura);
                        $('#monto2').text(montoFormateado2);
                        $('#monto_interes2').text(montoInteresesFormateado2);
                        $('#plazo2').text(v.total_quincenas);
                        $('#pago_quincenal2').text(pagoQuincenaFormateado2);
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
                id: prestamoId,
                forma_pago: metodoPago,
                fecha_primer_pago: primerPago,
                nota : $('#nota').val(),
                // NUEVO: Documentación marcada como entregada
                copia_talon: $('#doc_copia_talon').is(':checked') ? 1 : 0,
                copia_ine: $('#doc_copia_ine').is(':checked') ? 1 : 0,
                credencial_socio: $('#doc_credencial_socio').is(':checked') ? 1 : 0,
                pagare: $('#doc_pagare').is(':checked') ? 1 : 0,
                solicitud: $('#doc_solicitud').is(':checked') ? 1 : 0,
            };

            $.ajax({
                url: "{{ route('admin.tesoreria.prestamos.store') }}",
                type: "POST",
                dataType: 'json',
                data: ajaxData,
                success: function(response) {
                    //console.log('success:', JSON.stringify(response));
                    setTimeout(function() {
                        // Abre el enlace externo en una nueva ventana o pestaña
                        window.open("{{ url('/recibo-prestamo-aprobado') }}/" + response,
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

            var elimina = '{{ route('admin.tesoreria.prestamos.destroy', ':quit') }}';
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
    });
</script>
@stop
