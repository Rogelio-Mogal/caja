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
        <h3 class="card-title">Tesorería / Finalizar retiro</h3>
        <div class="card-tools">
            <!-- Buttons, labels, and many other things can be placed here! -->
        </div>
        <!-- /.card-tools -->
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        @if (isset($retiro))
            <div class="register-box-body">
                <div class="card border border-primary shadow-0 mb-3" style="max-width: 100%;">
                    <div class="card-body text-dark">
                        <table id="tesoreria_retiros"
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
                                        <td>$ {{ number_format($row->monto_retiro, 2) }} </td>
                                        <td>{{ $row->forma_pago }}</td>
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
                                        <td style="display:none;"></td>
                                        <td style="display:none;"></td>
                                        <td style="display:none;"></td>
                                        <td style="display:none;"></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @include('tesoreria_retiro._modal_aprobar_retiro')
                        @include('tesoreria_retiro._modal_cancelar_retiro')
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
    @if (Session::has('id'))
        <script type="text/javascript">
            var id = {{ session('id') }};
            setTimeout(function() {
                window.open("{{ url('/solicitud-retiro') }}/" + id, '_blank');
            }, 200);
        </script>
    @endif
<script>
    function formatNumber(input) {
        let value = input.value.replace(/[^0-9.]/g, '');

        // Si hay más de un punto, eliminar los extras
        const firstDotIndex = value.indexOf('.');
        if (firstDotIndex !== -1) {
            // Mantener el primer punto, eliminar cualquier otro
            value = value.substring(0, firstDotIndex + 1) + value.substring(firstDotIndex + 1).replace(/\./g, '');
        }

        let parts = value.split('.');
        let integerPart = parts[0];
        let decimalPart = parts[1] ?? '';

        // Eliminar ceros iniciales del entero, pero permitiendo que si todo es 0 no borre
        integerPart = integerPart.replace(/^0+(?=\d)/, '');

        // Formatear la parte entera con comas
        let formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        // Mostrar correctamente si apenas están escribiendo el punto
        input.value = decimalPart !== '' || value.endsWith('.') 
            ? `${formattedInteger}.` + decimalPart 
            : formattedInteger;

        // Actualizar el hidden limpio (sin comas)
        const cleanValue = input.value.replace(/,/g, '');
        document.getElementById('saldo_aprobado').value = cleanValue;
    }

    function fixDecimals(input) {
        let value = input.value.replace(/,/g, '').trim();

        if (value === '' || value === '.') {
            value = '0.00';
        }

        if (value.startsWith('.')) {
            value = '0' + value;
        }

        let parts = value.split('.');
        let integerPart = parts[0];
        let decimalPart = parts[1] || '';

        // Eliminar ceros iniciales
        integerPart = integerPart.replace(/^0+(?=\d)/, '');

        if (decimalPart.length === 0) {
            decimalPart = '00';
        } else if (decimalPart.length === 1) {
            decimalPart = decimalPart + '0';
        } else if (decimalPart.length > 2) {
            decimalPart = decimalPart.slice(0, 2);
        }

        // Formatear parte entera con comas
        integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        input.value = `${integerPart}.${decimalPart}`;

        // Actualizar el hidden limpio
        document.getElementById('saldo_aprobado').value = input.value.replace(/,/g, '');
    }

    function prepareSubmit() {
        const display = document.getElementById('pp_display').value;
        const cleanValue = display.replace(/,/g, '');
        document.getElementById('saldo_aprobado').value = cleanValue;

        return true;
    }

    $(document).ready(function() {
        // Verifica si hay un mensaje de éxito en la URL y muestra el mensaje CANCELADO
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('message') && urlParams.get('message') === 'cancelado') {
            //alert('La operación se realizó con éxito.');
            Swal.fire({
                icon: 'success',
                title: 'Retiro cancelado',
                text: 'El retiro se cancelo exitosamente.',
            });
        }

        // Verifica si hay un mensaje de éxito en la URL y muestra el mensaje APROBADO
        if (urlParams.has('message') && urlParams.get('message') === 'aprobado') {
            //alert('La operación se realizó con éxito.');
            Swal.fire({
                icon: 'success',
                title: 'Retiro aprobado',
                text: 'El retiro fue aprobado exitosamente.',
            });
        }

        $('#tesoreria_retiros').DataTable({
            "language": {
                "url": "{{ asset('/json/i18n/es_es.json') }}"
            }
        });

        // MUESTRA EL MODAL PARA CONFIRMAR EL RETIRO
        $(document).on('click', '.show_modal', function() {
            var dataId = $(this).data('id');
            var ruta = '{{ route('admin.tesoreria.retiro.show', ':id') }}';
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
                        const montoFormateado = new Intl.NumberFormat('es-MX', {
                            style: 'currency',
                            currency: 'MXN'
                        }).format(v.monto_retiro);
                        $('#retiro_id').val(v.id);
                        $('#monto_retiro').val(v.monto_retiro);
                        $('#socio').text(v.nombre_completo);
                        $('#fecha').text(v.fecha_captura);
                        $('#monto').text(montoFormateado);
                        $('#saldo_aprobado').attr('max', v.monto_retiro);
                    });
                },
                error: function(response) {
                    console.log('error:', JSON.stringify(response));
                    $(".myModal").modal('hide');
                },
            });

            $(".myModal").modal('show');
        });

        // MUESTRA EL MODAL PARA CANCELAR EL RETIRO
        $(document).on('click', '.show_modal_cancelar', function() {
            var dataId = $(this).data('id');
            var ruta = '{{ route('admin.tesoreria.retiro.show', ':id') }}';
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
                        const montoFormateado2 = new Intl.NumberFormat('es-MX', {
                            style: 'currency',
                            currency: 'MXN'
                        }).format(v.monto_retiro);

                        $('#retiro_id2').val(v.id);
                        $('#socio2').text(v.nombre_completo);
                        $('#fecha2').text(v.fecha_captura);
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

        // VALIDAMOS Y ENVIAMOS EL MONTO RETIRO, PARA AFIRMAR EL RETIRO
        $(document).on('click', '.btn-aprobar-retiro', function() {
            console.log('valiar los datos y enviar el fomulario');
            var retiroId = $('#retiro_id').val();
            var saldoAprobado = $('#saldo_aprobado').val();
            var metodoPago = $('#forma_pago').val();
            switch (true) {
                case $('#forma_pago').val() === '-1' || $('#saldo_aprobado').val() <= 0:
                    console.log('case 1');
                    Swal.fire({
                        icon: 'error',
                        title: 'Hay datos pendientes por requisitar',
                        text: 'Por favor verifique la información.',
                    });
                    break;
                case $('#saldo_aprobado').val() > $('#monto_retiro').val():
                    console.log('case 2');
                    Swal.fire({
                        icon: 'error',
                        title: 'El monto aprobado es mayor al monto solicitado',
                        text: 'Por favor verifique la información.',
                    });
                    break;
                default:
                    //$("#submitBtn").attr("disabled", true);
                    //document.getElementById('form_prestamos').submit();
                    $(".btn-aprobar-retiro").attr("disabled", true);
                    console.log('enviaria el formulario');
                    apruebaRetiro(retiroId, saldoAprobado, metodoPago);
                    break;
            }
        });

        // VALIDAMOS Y ENVIAMOS EL MONTO RETIRO, PARA CANCELAR EL RETIRO
        $(document).on('click', '.btn-cancelar-retiro', function() {
            var retiroId = $('#retiro_id2').val();
            var motivo = $('#motivo').val();
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
                    //$("#submitBtn").attr("disabled", true);
                    console.log('enviaria el formulario');
                    cancelaRetiro(retiroId, motivo);
                    break;
            }
        });

        function apruebaRetiro(retiroId, saldoAprobado, metodoPago) {
            const ajaxData = {
                "_token": "{{ csrf_token() }}",
                id: retiroId,
                saldo_aprobado: saldoAprobado,
                forma_pago: metodoPago,
            };

            $.ajax({
                url: "{{ route('admin.tesoreria.retiro.store') }}",
                type: "POST",
                dataType: 'json',
                data: ajaxData,
                success: function(response) {
                    //console.log('success:', JSON.stringify(response));
                    setTimeout(function() {
                        // Abre el enlace externo en una nueva ventana o pestaña
                        window.open("{{ url('/recibo-retiro-aprobado') }}/" + response,
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

        function cancelaRetiro(retiroId, motivo) {
            const ajaxData = {
                "_token": "{{ csrf_token() }}",
                id: retiroId,
                comentarios: motivo,
            };

            var elimina = '{{ route('admin.tesoreria.retiro.destroy', ':quit') }}';
            var ruta = elimina.replace(':quit', retiroId)

            $.ajax({
                url: ruta,
                type: "DELETE",
                dataType: 'json',
                data: ajaxData,
                success: function(response) {
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
