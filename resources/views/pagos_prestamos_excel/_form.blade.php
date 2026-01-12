@section('css')
    <style type="text/css">
        .btn-uno {
            background-color: #ecbe14 !important;
            color: #000;
        }

        .btn-dos {
            background-color: #6fa84b !important;
            color: #000;
        }

        .btn-tres {
            background-color: #34b5aa !important;
            color: #000;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .custom-center-align {
            /*display: flex !important;*/
            justify-content: center !important;
        }
    </style>
@stop

<br />
<meta name="csrf-token" content="{{ csrf_token() }}">
@csrf
<div class="card card-outline card-primary">
    <div class="card-header">
        @if (\Request::route()->getName() === 'admin.pago.prestamos.create')
            <h3 class="card-title">PAGO DE PRÉSTAMOS</h3>
        @elseif (\Request::route()->getName() === 'admin.pago.prestamos.edit')
            <h3 class="card-title">EDITAR SOCIO</h3>
        @endif
    </div>

    <div class="card-body">
        <div class="register-box-body">
            <div class="card border border-primary shadow-0 mb-3">
                <div class="card-header">
                    <h4>CARGAR PAGOS DE PRÉSTAMOS DESDE ARCHIVO EXCEL</h4>
                </div>
                <div class="card-body text-dark custom-center-align">
                    <div class="row mb-1">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="col">
                                <label class="form-label" for="customFile">CARGAR EL ARCHIVO EXCEL CON LOS
                                    DATOS</label>
                                <input type="file" class="form-control" id="customFile" />

                                @error('num_socio')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <div class="col">
                                <label class="form-label" for="fecha_pago">FECHA DE PAGO</label>
                                <div class="form-outline datepicker-translated" data-mdb-toggle-button="false">
                                    {{ Form::text('fecha_pago', null, ['id' => 'fecha_pago', 'name' => 'fecha_pago', 'data-mdb-toggle' => 'datepicker', 'class' => 'form-control', 'placeholder' => 'FECHA DE PAGO']) }}
                                    <label for="fecha_pago" class="form-label">FECHA DE PAGO</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <label class="form-label" for="btnExcel">ANALIZAR DATOS</label>
                            <div class="col">
                                {!! Form::button('Analizar', [
                                    'type' => 'button',
                                    'class' => 'btn btn-success form-control',
                                    'id' => 'btnExcel',
                                ]) !!}
                                @error('nombre_completo')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row mb-1">
                <div class="col-12">
                    <div class="card border border-success shadow-0 mb-3">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-lg-12 col-md-12">
                                    <h5 class="card-title">PAGO DE PRÉSTAMOS CORRECTOS <span style="color: green;" id="importe_total"></span></h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-dark">
                            <div class="row mb-4">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="card-body table-responsive p-0">
                                        <input type="hidden" name="pagos_json" id="pagos_json">
                                        <table id="tblOk"
                                            class="table table-striped table-hover dataTable dtr-inline display responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Rfc</th>
                                                    <th>Nombre completo</th>
                                                    <th>Serie</th>
                                                    <th>Importe</th>
                                                </tr>
                                            </thead>
                                            <tbody id="body_details_tblOk">
                                                <!-- Aquí se agregarán las filas de los registros -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border border-danger shadow-0 mb-3">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-lg-12 col-md-12">
                                    <h5 class="card-title">PAGO DE PRÉSTAMOS NO ENCONTRADOS EN LA BASE DE DATOS</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-dark">
                            <div class="row mb-4">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="card-body table-responsive p-0">
                                        <table id="tblNoDb"
                                            class="table table-striped table-hover dataTable dtr-inline display responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Rfc</th>
                                                    <th>Nombre completo</th>
                                                    <th>Serie</th>
                                                    <th>Importe</th>
                                                </tr>
                                            </thead>
                                            <tbody id="body_details_tblNoDb">
                                                <!-- Aquí se agregarán las filas de los registros -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border border-danger shadow-0 mb-3">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-lg-12 col-md-12">
                                    <h5 class="card-title">PAGO DE PRÉSTAMOS NO ENCONTRADOS EN ARCHIVO EXCEL</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-dark">
                            <div class="row mb-4">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="card-body table-responsive p-0">
                                        <table id="tblNoExcel"
                                            class="table table-striped table-hover dataTable dtr-inline display responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Rfc</th>
                                                    <th>Nombre completo</th>
                                                    <th>Importe</th>
                                                </tr>
                                            </thead>
                                            <tbody id="body_details_tblNoExcel">
                                                <!-- Aquí se agregarán las filas de los registros -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-12 col-md-12 col-sm-12" align="center">
                    <div class="col-2 custom-center-align">
                        <br />
                        {!! Form::button('Guardar', ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'submitBtn']) !!}
                    </div>
                </div>
            </div>
            <br />

        </div>
    </div>
</div>


@section('js')
    <script>
        $(document).ready(function() {
            // SWEET ALERT
            var submitBtn = document.getElementById('submitBtn');

            // Agrega un evento click al botón de envío
            submitBtn.addEventListener('click', function(event) {
                // Prevenir el envío del formulario por defecto
                event.preventDefault();

                Swal.fire({
                    title: '¿Confirmar envío?',
                    text: 'Se enviarán todos los registros obtenidos en el Excel.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, enviar',
                    cancelButtonText: 'Cancelar',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        enviarFormulario();
                    }
                });


                // Verificar si hay datos en la tabla #tblDB
                //const resultadoOkTable = document.querySelector('#tblDB');

                //if (resultadoOkTable && resultadoOkTable.rows.length > 1) {
                    // Mostrar la alerta de confirmación
                    //Swal.fire({
                        //title: 'Hay datos con errores en diferentes campos',
                        //text: '¿Estás seguro de guardar la infomación?',
                        //icon: 'warning',
                        //showCancelButton: true,
                        //confirmButtonText: 'Sí, enviar',
                        //cancelButtonText: 'Cancelar'
                    //}).then((result) => {
                        //if (result.isConfirmed) {
                            // Si el usuario confirmó, enviar el formulario
                            //--document.getElementById('form_excel_pagos').submit();
                            //--console.log('enviaria el formulario');
                            //--$("#submitBtn").attr("disabled", true);
                        //}
                    //});
                //}
            });

            function enviarFormulario() {

                var table = $('#tblOk').DataTable();

                // 1️⃣ Quitar name a inputs visibles (evita duplicados)
                $('#tblOk tbody input').each(function () {
                    $(this).data('original-name', $(this).attr('name'));
                    $(this).removeAttr('name');
                });

                // 2️⃣ Elimina clones anteriores
                $('#form_excel_pagos .dt-hidden').remove();

                // 3️⃣ Clona inputs de TODAS las filas
                table.rows({ page: 'all' }).every(function () {
                    var row = $(this.node());

                    row.find('input').each(function () {
                        let clone = $(this).clone();
                        clone.attr('name', $(this).data('original-name'));
                        clone.addClass('dt-hidden');
                        clone.appendTo('#form_excel_pagos');
                    });
                });

                // 4️⃣ Enviar
                document.getElementById('form_excel_pagos').submit();
                $("#submitBtn").attr("disabled", true);
            }


            //BLOQUEA EL BOTON PARA QUE EL FORMULARIO SE ENVIE UNA VEZ
            $('#form_excel_pagos').submit(function() {
                $("#submitBtn").attr("disabled", true);
            });

            // ENVIAMOS LOS DATOS DEL EXCEL
            // SE GUARDAN EN UNA TABLA TEMPORAL
            $('#btnExcel').click(function(e) {
                e.preventDefault();
                if ($('#customFile')[0].files.length === 0 || $('#fecha_pago').val() == '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Hay datos pendientes por requisitar.',
                        text: 'Por favor verifique la información..',
                    });
                } else {
                    obtenerResultados();
                }

            });

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
                disablePast: false,
                title: 'Seleccione la fecha del primer pago',
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

            // Evitar entrada de datos en el campo al escribir
            var inputPrestamosIntereses = $('#fecha_pago');
            inputPrestamosIntereses.on('keydown',
                function(e) {
                    e.preventDefault();
                });

            // FORMATO MONEDA
            function formatToCurrency(data) {
                return '$' + Intl.NumberFormat('es-MX', {
                    minimumFractionDigits: 2,
                }).format(data);
            }


            function obtenerResultados() {
                var dataNoDb, dataNoFinanciero;

                return new Promise(function(resolve, reject) {
                    // Realizar la solicitud AJAX para obtener los resultados
                    var formData = new FormData();
                    formData.append('archivo', $('#customFile')[0].files[0]);
                    formData.append('fecha_pago', $('#fecha_pago').val());

                    $.ajax({
                        url: "{{ route('leer.excel.pagos') }}",
                        type: 'POST',
                        data: formData,
                        contentType: false, // No configurar contentType aquí
                        processData: false, // No procesar los datos aquí
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                'content') // Incluir el token CSRF en los encabezados
                        },
                        success: function(response) {
                            if (response.debug) {
                                console.log('DEBUG:', response);
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Debug activo',
                                    text: JSON.stringify(response, null, 2)
                                });
                                return;
                            }

                            resolve(response); // Resuelve la promesa con los resultados
                            //console.log('asdasda');
                            //console.log(response);
                            // VALORES NO EN BASE DE DATOS
                            if (response.result == 'success') {
                                // OBTENGO LAS SERIES DEL EXCEL QUE NO ESTÁN EN LA BD
                                if (response.result === "success" && response['serie-no-db'].length > 0) {
                                    var serie_no_db = response['serie-no-db'];
                                    var html = '';
                                    for (var i = 0; i < serie_no_db.length; i++) {
                                        var item = serie_no_db[i];
                                        if (item && item.rfc && item.nombre_completo && item.serie && item.importe) {
                                            html += '<tr>';
                                            html +=     '<td>';
                                            html +=         item.rfc;
                                            html +=     '</td>';
                                            html +=     '<td>';
                                            html +=         item.nombre_completo;
                                            html +=     '</td>';
                                            html +=     '<td>';
                                            html +=         item.serie;
                                            html +=     '</td>';
                                            html +=     '<td>';
                                            html +=         formatToCurrency(item.importe);
                                            html +=     '</td>';
                                            html += '</tr>';
                                        }
                                    }
                                    $('#body_details_tblNoDb').html(html);
                                    // Inicializa DataTable si es necesario
                                    if (!$.fn.dataTable.isDataTable('#tblNoDb')) {
                                        $('#tblNoDb').DataTable({
                                            "language": {
                                                "url": "{{ asset('/json/i18n/es_es.json') }}"
                                            }
                                        });
                                    }
                                }

                                // OBTENGO LAS SERIES DE LA BD QUE NO ESTÁN EN EL EXCEL
                                if (response.result === "success" && response['serie-no-excel'].length > 0) {
                                    var serie_no_excel = response['serie-no-excel'];
                                    var html = '';
                                    for (var i = 0; i < serie_no_excel.length; i++) {
                                        var item = serie_no_excel[i];
                                        if (item && item.rfc && item.nombre_completo && item.importe) {
                                            html += '<tr>';
                                            html +=     '<td>';
                                            html +=         item.rfc;
                                            html +=     '</td>';
                                            html +=     '<td>';
                                            html +=         item.nombre_completo;
                                            html +=     '</td>';
                                            html +=     '<td>';
                                            html +=         formatToCurrency(item.importe);
                                            html +=     '</td>';
                                            html += '</tr>';
                                        }
                                    }
                                    $('#body_details_tblNoExcel').html(html);
                                    // Inicializa DataTable si es necesario
                                    if (!$.fn.dataTable.isDataTable('#tblNoExcel')) {
                                        $('#tblNoExcel').DataTable({
                                            "language": {
                                                "url": "{{ asset('/json/i18n/es_es.json') }}"
                                            }
                                        });
                                    }
                                }

                                // OBTENGO LAS SERIES CORRECTAS
                                if (response.result === "success" && response['serie-ok']) {
                                    //var importe_total = response['importe_total'];
                                    //$('#importe_total').text(formatToCurrency(importe_total));

                                    var serie_ok = response['serie-ok'];
                                    var html = '';
                                    var importe_total = 0; // 👈 inicializa
                                    let pagosJson = []; // ← GLOBAL

                                    for (var i = 0; i < serie_ok.length; i++) {
                                        var item = serie_ok[i];
                                        if (item) {

                                            // Guardar en JSON
                                            pagosJson.push({
                                                prestamos_id: item.prestamos_id,
                                                socios_id: item.socios_id,
                                                fecha_pago: item.fecha_pago,
                                                fecha_captura: item.fecha_captura,
                                                serie_pago: item.serie_pago,
                                                serie_final: item.serie_final,
                                                importe: item.importe
                                            });
                                            //Sumar importe (forzar a número)
                                            importe_total += parseFloat(item.importe) || 0;

                                            html += '<tr>';
                                            html +=     '<td>';
                                            html +=         item.rfc;
                                            //html +=         '<input type="hidden" name="prestamos_id[]" value="'+item.prestamos_id+'">';
                                            //html +=         '<input type="hidden" name="socios_id[]" value="'+item.socios_id+'">';
                                            //html +=         '<input type="hidden" name="fecha_pago[]" value="'+item.fecha_pago+'">';
                                            //html +=         '<input type="hidden" name="fecha_captura[]" value="'+item.fecha_captura+'">';
                                            //html +=         '<input type="hidden" name="serie_pago[]" value="'+item.serie_pago+'">';
                                            //html +=         '<input type="hidden" name="serie_final[]" value="'+item.serie_final+'">';
                                            //html +=         '<input type="hidden" name="importe[]" value="'+item.importe+'">';
                                            html +=     '</td>';
                                            html +=     '<td>';
                                            html +=         item.nombre_completo;
                                            html +=     '</td>';
                                            html +=     '<td>';
                                            html +=         item.serie;
                                            html +=     '</td>';
                                            html +=     '<td>';
                                            html +=         formatToCurrency(item.importe);
                                            html +=     '</td>';
                                            html += '</tr>';
                                        }
                                    }
                                    // Mostrar total
                                    $('#importe_total').text(formatToCurrency(importe_total));

                                    $('#body_details_tblOk').html(html);
                                    $('#pagos_json').val(JSON.stringify(pagosJson));
                                    // Inicializa DataTable si es necesario
                                    if (!$.fn.dataTable.isDataTable('#tblOk')) {
                                        $('#tblOk').DataTable({
                                            "language": {
                                                "url": "{{ asset('/json/i18n/es_es.json') }}"
                                            }
                                        });
                                    }
                                }

                                // SI NO HAY ERRORES MUESTRO EL BOTÓN PARA GUARDAR
                                const tblserieNoDb = document.querySelector('#tblNoDb');
                                const tblserieOk = document.querySelector('#tblOk');
                                const tblnoExcel = document.querySelector('#tblNoExcel');
                                console.log(tblserieNoDb.rows.length);
                                console.log(tblserieOk.rows.length);
                                console.log(tblnoExcel.rows.length);

                                if(tblserieNoDb.rows.length == 1 && tblserieOk.rows.length > 1 && tblnoExcel.rows.length == 1){
                                    $('#submitBtn').show();
                                }
                            }
                        },
                        error: function(response) {
                            console.log('error:', JSON.stringify(response));
                            reject(error); // Rechaza la promesa en caso de error
                        }
                    });
                });
            }


        });
    </script>


@stop
