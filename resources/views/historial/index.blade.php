@extends('layouts.app')

@section('content')

@section('css')
    <style>
        /* Ajustar la altura de la caja del select2 */
        .select2-container .select2-selection--single {
            height: 35px;
            /* Ajusta la altura según tus necesidades */
            line-height: 35px;
            /* Asegura que el texto se alinee verticalmente al centro */
            display: flex;
            /* Flexbox para alinear el contenido */
            align-items: center;
            /* Centra el contenido verticalmente */
            position: relative;
            /* Necesario para posicionar el ícono */
        }

        /* Ajustar la altura del dropdown */
        .select2-container .select2-dropdown {
            max-height: 300px;
            /* Ajusta el alto máximo del dropdown */
            overflow-y: auto;
            /* Permite el scroll si el contenido es más alto */
        }

        /* Centrar el texto en el elemento seleccionado */
        .select2-container .select2-selection__rendered {
            line-height: 35px;
            /* Asegura que el texto seleccionado esté centrado */
            display: flex;
            align-items: center;
            /* Centra el texto verticalmente */
        }

        /* Posicionar el ícono de "limpiar" a la derecha */
        .select2-container .select2-selection__clear {
            position: absolute;
            /* Posicionamiento absoluto */
            right: 10px;
            /* Ajusta según sea necesario para alejarlo del borde derecho */
            top: 40%;
            /* Posición vertical en el centro */
            transform: translateY(-55%);
            /* Asegura que esté centrado verticalmente */
            cursor: pointer;
            /* Establece un puntero de cursor para indicar que es clickeable */
        }
    </style>

@stop

@php
    use Carbon\Carbon;
@endphp

<br />

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Historial</h3>
        <div class="card-tools">
        </div>
    </div>

    <div class="card-body">
        <div class="register-box-body">
            <div class="card border border-success shadow-0 mb-0">
                <div class="card-header">
                    <h4>HISTORIAL DE SOCIO</h4>
                </div>
                <div class="card-body text-dark">
                    <form method="GET" action="{{ route('admin.historial.index') }}"
                        class="row g-2 mb-0 align-items-end">
                        <div class="row mb-0">
                            {{--
                                    <select class="select mb-2" name="socios_id" id="socios_id" data-mdb-filter="true"
                                        data-mdb-option-height="50" required="true">
                                        <option value="-1" hidden selected>-- Socios --</option>
                                    </select>
                                --}}
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="col mb-3">
                                    <div class="form-outline">
                                        {{ Form::hidden('hidde', null) }}
                                        <select id="socios_id" name="socios_id" class="form-control select2"
                                            style="width: 100%;">
                                        </select>
                                        <label class="form-label select-label form-control-lg"
                                            for="socios_id">&nbsp;</label>
                                    </div>
                                </div>
                                @error('socios_id')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="col mb-3">
                                    <div class="form-outline datepicker-translated" data-mdb-toggle-button="false">
                                        {{-- Form::text('fecha_inicio', request('fecha_inicio', \Carbon\Carbon::now()->startOfMonth()->toDateString()) , ['id' => 'fecha_inicio', 'data-mdb-toggle' => 'datepicker', 'class' => 'form-control', 'placeholder' => 'FECHA INICIAL', 'tabindex' => '2']) --}}
                                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control"
                                            value="{{ request('fecha_inicio', \Carbon\Carbon::now()->startOfMonth()->toDateString()) }}">
                                        <label class="form-label" for="fecha_inicio">FECHA INICIAL</label>
                                    </div>
                                </div>
                                @error('fecha_inicio')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="col mb-3">
                                    <div class="form-outline datepicker-translated2" data-mdb-toggle-button="false">
                                        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control"
                                            value="{{ request('fecha_fin', \Carbon\Carbon::now('America/Mexico_City')->toDateString()) }}">
                                        {{-- Form::text('fecha_fin', request('fecha_fin', \Carbon\Carbon::now()->toDateString()), ['id' => 'fecha_fin', 'data-mdb-toggle' => 'datepicker', 'class' => 'form-control', 'placeholder' => 'FECHA FINAL', 'tabindex' => '3']) --}}
                                        <label class="form-label" for="fecha_fin">FECHA FINAL</label>
                                    </div>
                                </div>
                                @error('fecha_fin')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="col mb-3 d-flex justify-content-center">
                                    {!! Form::button('Historial', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-success',
                                        'id' => 'btn-historial',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if (!$fechaInicio || !$fechaFin)
        <div class="alert alert-info shadow-0 mb-0 g-3">
            Por favor selecciona un rango de fechas para ver el reporte de {{ $tipo }}.
        </div>
    @elseif($datos->isEmpty())
        <div class="alert alert-warning shadow-0 mb-0 g-3">
            No se encontraron resultados para el rango de fechas seleccionado.
        </div>
    @else
        @if ($tipo == 'historial')
            @include('historial.partials.historial', ['datos' => $datos])
        @endif
    @endif
</div>
@stop


@section('js')
<script>
    $(document).ready(function() {

        // Estilos para el select2 con BoostrapMD
        // Inicializar Select2
        $('#socios_id').select2({
            placeholder: "-- Socios --",
            allowClear: true,
            minimumInputLength: 3,
            width: '100%',
        });

        // ACTIVA LA BUSQUEDA
        $(document).on('select2:open', () => {
            let allFound = document.querySelectorAll('.select2-container--open .select2-search__field');
            $(this).one('mouseup keyup', () => {
                setTimeout(() => {
                    allFound[allFound.length - 1].focus();
                }, 0);
            });
        });


        socios();

        /*
        $("#btn-historial").click(function() {
            var socio = $('#socios_id').val();
            var inicio = $('#fecha_inicial').val();
            var fin = $('#fecha_final').val();
            switch (true) {
                case socio === '-1' || inicio === '' || fin === '':
                    console.log('case 1');
                    Swal.fire({
                        icon: 'error',
                        title: 'Hay datos pendientes por requisitar',
                        text: 'Por favor verifique la información.',
                    });
                    break;
                default:
                    muestraHistorial(socio, inicio, fin);
                    console.log('enviaria el formulario');
                    break;
            }

        });
        */

        // CALENDARIO
        const datepickerTranslated = document.querySelector('.datepicker-translated');
        new mdb.Datepicker(datepickerTranslated, {
            disableFuture: true,
            confirmDateOnSelect: true,
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
        });

        const datepickerTranslated2 = document.querySelector('.datepicker-translated2');
        new mdb.Datepicker(datepickerTranslated2, {
            disableFuture: true,
            confirmDateOnSelect: true,
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
        });

        // Evitar entrada de datos en el campo fecha_inicial
        var inputPagoQuincenal = $('#fecha_inicial');
        inputPagoQuincenal.on('keydown', function(e) {
            e.preventDefault();
        });

        // Evitar entrada de datos en el campo fecha_final
        var inputPagoQuincenal = $('#fecha_final');
        inputPagoQuincenal.on('keydown', function(e) {
            e.preventDefault();
        });

        // FUNCION PARA OBTENER LOS SOCIOS
        /*
        function socios(id, tipo, accion) {
            //console.log('aval id: ' + id);
            $.ajax({
                url: "{{ route('all.socios.ahorro') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    //console.log('success:', JSON.stringify(response));
                    // Recorrer los datos obtenidos y agregar opciones al select
                    var select = $('#socios_id');
                    $.each(response, function(index, socio) {
                        var option = $('<option>', {
                            value: socio.id,
                            text: socio.nombre_completo,
                            'data-mdb-secondary-text': 'RFC: ' + socio.rfc +
                                '. CUIP: ' + socio.cuip,
                        });
                        select.append(option);
                    });
                },
                error: function(response) {
                    console.log('error:', JSON.stringify(response));
                },
            });
        }
        */

        function socios(id) {

            $('#socios_id').select2({
                placeholder: '-- Socios--',
                allowClear: true,
                minimumInputLength: 3,
                width: '100%',
                ajax: {
                    url: "{{ route('get.socios.ajax.by.select') }}", // Ruta de tu controlador
                    type: "POST",
                    dataType: 'json',
                    delay: 250, // Retraso para evitar muchas solicitudes
                    data: function(params) {
                        return {
                            search: params.term, // Término de búsqueda introducido por el usuario
                            _token: "{{ csrf_token() }}" // Token CSRF para seguridad
                        };
                    },
                    processResults: function(data) {
                        console.log('data: ' + data);
                        return {
                            results: $.map(data, function(socio) {
                                return {
                                    id: socio.id || '',
                                    text: socio.nombre_completo || 'Sin nombre',
                                    rfc: socio.rfc || 'N/A',
                                    cuip: socio.cuip || 'N/A'
                                };
                            })
                        };
                    },
                    cache: true
                },
                //minimumInputLength: 3, // Mínimo de caracteres para iniciar la búsqueda
                language: {
                    inputTooShort: function() {
                        return 'Por favor, introduzca 3 o más caracteres';
                    },
                    noResults: function() {
                        return "No se encontraron resultados";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                },
                escapeMarkup: function(markup) {
                    return markup; // Permitir HTML en los resultados
                },
                templateResult: function(data) {
                    // Renderizar cada opción con datos adicionales
                    if (data.loading) {
                        return data.text;
                    }
                    var markup = `
                            <div>
                                <strong>${data.text}</strong>
                                <br>
                                <small>RFC: ${data.rfc || 'N/A'} | CUIP: ${data.cuip || 'N/A'}</small>
                            </div>`;
                    return markup;
                },
                templateSelection: function(data) {
                    // Mostrar solo el nombre seleccionado
                    return data.text || '-- Socios--';
                }
            });




            // SI HAY UN ID, CARGAR LA OPCIÓN AUTOMÁTICAMENTE
            if (id) {
                $.ajax({
                    url: "{{ route('get.socios.by.id') }}", // Nueva ruta para obtener socio por ID
                    type: "POST",
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        var socio = {
                            id: data.id,
                            text: data.nombre_completo,
                            rfc: data.rfc,
                            cuip: data.cuip
                        };

                        // Añadir la opción al select y seleccionarla
                        var newOption = new Option(socio.text, socio.id, true, true);
                        $('#socios_id').append(newOption).trigger('change');
                    }
                });
            }
        }

        function muestraHistorial(socio, inicio, fin) {
            const ajaxData = {
                "_token": "{{ csrf_token() }}",
                socios_id: socio,
                f_inicia: inicio,
                f_termina: fin,
            };

            var dataId = $(this).data('id');
            var ruta = '{{ route('admin.historial.show', ':id') }}';
            ruta = ruta.replace(':id', socio);
            var html = '';

            // Destruir la instancia DataTable existente
            if ($.fn.DataTable.isDataTable('#tbl_historial')) {
                $('#tbl_historial').DataTable().destroy();
            }

            $.ajax({
                url: ruta,
                type: "GET",
                dataType: 'json',
                data: ajaxData,
                success: function(response) {
                    //console.log('success:', JSON.stringify(response));
                    $("#body_details").empty();
                    $(response).each(function(i, v) { // indice, valor
                        html += '<tr>';
                        html += '<td>';
                        html += v.fecha;
                        html += '</td>';
                        html += '<td>';
                        html += v.folio;
                        html += '</td>';
                        html += '<td>';
                        html += v.movimiento;
                        html += '</td>';
                        html += '<td>';
                        html += v.tipo_movimiento;
                        html += '</td>';
                        html += '<td>';
                        html += formatCurrencyMXN(v.saldo_anterior);
                        html += '</td>';
                        html += '<td>';
                        html += formatCurrencyMXN(v.monto);
                        html += '</td>';
                        html += '<td>';
                        html += formatCurrencyMXN(v.saldo_actual);
                        html += '</td>';
                        html += '<td>';
                        html += v.estatus;
                        html += '</td>';
                        html += '</tr>';
                        $('#body_details').html(html);
                    });

                    // Inicializar el DataTable aquí
                    $('#tbl_historial').DataTable({
                        "language": {
                            "url": "{{ asset('/json/i18n/es_es.json') }}"
                        }
                    });
                },
                error: function(response) {
                    console.log('error:', JSON.stringify(response));
                    $("#body_details").empty();
                    Swal.fire({
                        type: 'error',
                        title: 'Advertencia.',
                        text: 'Hubo un error durante el proceso, por favor intente de nuevo.',
                    });
                },
            });
        }
    });

    //FUNCION MONEDA MX
    function formatCurrencyMXN(value) {
        return new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN'
        }).format(value || 0);
    }
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
@stop
