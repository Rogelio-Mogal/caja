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

    @php
        use Carbon\Carbon;
        $today = Carbon::now();

        //if ($today->day >= 15 && $today->day < $today->endOfMonth()->day) {
        //    $desiredDate = $today->copy()->day(15); // Si hoy es mayor o igual al 15 y menor al último día del mes, muestra el día 15.
        //} else {
        //    $desiredDate = $today->copy()->endOfMonth(); // Si hoy es menor al 15, muestra el último día del mes anterior.
        //}
        // Obtener el día, mes y año de $desiredDate
        //$day = $desiredDate->day;
        //$month = $desiredDate->month;
        //$year = $desiredDate->year;
        // Obtener el nombre completo del mes en español
        //$desiredDate = $desiredDate->locale('es'); // Establecer la configuración local en español
        //$mesEnEspanol = $desiredDate->formatLocalized('%B');

        if ($today->day > 15) {
            $desiredDate = $today->copy()->endOfMonth(); // Último día del mes actual
        } else {
            $desiredDate = $today->copy()->day(15); // Día 15 del mes actual
        }
        $day = $desiredDate->day;
        $year = $desiredDate->year;

        $mesEnEspanol = $desiredDate->locale('es')->translatedFormat('F');
    @endphp

    <br />
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Socios</h3>
            <div class="card-tools">
                <!-- Buttons, labels, and many other things can be placed here! -->
            </div>
            <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            {{-- @if (isset($socios)) --}}
            <div class="register-box-body">
                <div class="card border border-primary shadow-0 mb-3" style="max-width: 100%;">
                    <div class="card-body text-dark">
                        <table id="socios" class="table table-striped table-hover display responsive nowrap"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Apellido Paterno</th>
                                    <th>Apellido Materno</th>
                                    <th>Nombre(s)</th>
                                    <th>Saldo</th>
                                    <th>CUIP</th>
                                    <th>RFC</th>
                                    <th>Estatus</th>
                                    <th>Fundador</th>
                                    <th>Opc</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- @else
                <h1>No hay información por mostrar.</h1>
            @endif --}}
        </div>
        <br />
        <br />

        <!-- PRUEBA SUBIR IMAGEN -->
        @include('socios._modal_toma_foto')
        @include('socios._modal_tipo_socio')
        @include('socios._modal_historial_prestamo')

        <br />
        <br />

    </div>
@stop


@section('js')
    <script>
        $(document).ready(function() {

            const postData = {
                _token: $('input[name=_token]').val()
            };
            var ruta = "{{ Request::url() }}";
            $('#socios').DataTable({
                "language": {
                    "url": "{{ asset('/json/i18n/es_es.json') }}"
                },
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('ajax.socios') }}", // Debes especificar la ruta correcta de tu controlador
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
                    /*{
                        "data": "imagen",
                        "render": function(data, type, row) {
                            var img = window.location.origin + data;
                            if (data != null) {
                                if (data.match(/\.(gif|jpg|jpeg|tiff|png)$/i)) {
                                    return '<img src="' + img + '" width="125" height="auto"  />';
                                } else if (data.match(/\.*$/i)) {
                                    return '<img src="{{ url('/foto_socios/socio.png') }}" width="65" height="auto"  />';
                                }
                            } else {
                                return '<img src="{{ url('/foto_socios/socio.png') }}" width="65" height="auto"  />';
                            }
                        }
                    },*/
                    //{
                    //    "data": "imagen",
                    //    "render": function(data, type, row) {
                    //        var img = window.location.origin + data;
                    //        return '<img src="'+row.imagen+'" width="100" height="auto"  />';
                            /*if (data != null) {
                                if (data.match(/\.(gif|jpg|jpeg|tiff|png)$/i)) {
                                    return '<img src="' + img + '" width="125" height="auto"  />';
                                } else if (data.match(/\.*$/i)) {
                                    return '<img src="{{ url('/foto_socios/socio.png') }}" width="65" height="auto"  />';
                                }
                            } else {
                                return '<img src="{{ url('/foto_socios/socio.png') }}" width="65" height="auto"  />';
                            }*/
                    //    }
                    //},
                    //{
                    //    data: 'nombre_completo'
                    //},
                    {
                        data: 'apellido_paterno'
                    },
                    {
                        data: 'apellido_materno'
                    },
                    {
                        data: 'nombre'
                    },
                    {
                        data: 'saldo',
                        render: function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(data || 0);
                            }
                            return data; // para ordenamiento y otros usos internos
                        }
                    },
                    {
                        data: 'cuip'
                    },
                    {
                        data: 'rfc'
                    },
                    {
                        data: 'tipo'
                    },
                    {
                        data: 'is_fundador',
                        "render": function(data, type, row) {
                            if (data == 1) {
                                return "<strong>FUNDADOR</strong>";
                            } else if (data == 0) {
                                return "HONORARIOS";
                            } else {
                                return data; // Si es otro valor, mantenerlo igual
                            }
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {  
                            var cuenta = ''; 
                            if(row.users_id == 0){
                                cuenta = '<li>' +
                                    '<a type="button" class="dropdown-item" href="usuarios/create?id=' + row.id + '">' +
                                    'Crear cuenta de acceso'+
                                    '</a>' +
                                '</li>' ;
                            }  
                                                    
                            return '<div class="btn-group">' +
                                '<button type="button" class="btn btn-primary dropdown-toggle"' +
                                'data-mdb-toggle="dropdown" aria-expanded="false">' +
                                'Acciones' +
                                '</button>' +
                                '<ul class="dropdown-menu">' +
                                '<li>' +
                                '<button type="button" class="dropdown-item show_modal_prestamos"' +
                                'data-id="' + row.id + '" data-socio="' + row.nombre_completo +
                                '">' +
                                'Ver préstamos' +
                                '</button>' +
                                '</li>' +
                                //'<li>' +
                                //'<button type="button" class="dropdown-item show_modal_foto"' +
                                //'data-id="' + row.id + '" data-socio="' + row.nombre_completo +
                                //'">' +
                                //'Tomar foto' +
                                //'</button>' +
                                //'</li>' +
                                cuenta +
                                '<li>' +
                               /* '<button type="button" class="dropdown-item show_modal_tipo_socio"' +
                                'data-id="' + row.id + '" data-socio="' + row.nombre_completo +
                                '" data-tipo="' + row.tipo_usuario + '" >' +
                                'Cambiar tipo de socio' +
                                '</button>' +
                                '</li>' +*/
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

            // PRUEBA PARA SUBIR IMAGEN
            let cameraActive = false;
            let fotoCapturada = false;
            let photoDataURL;

            // MUESTRO EL MODAL DEL HISTORIAL DE PRÉSTAMOS
            $(document).on('click', '.show_modal_prestamos', function() {
                var dataId = $(this).data('id');
                var socio = $(this).data('socio');
                //$('.socio_id').val(dataId);
                console.log('Id: '+dataId);
                console.log('socio: '+socio);

                // CONSULTO LOS DATOS POR AJAX PARA EL RECIBO
                if (dataId) {
                    var ruta = '{{ route('recibo.socios.prestamos', ':id') }}';
                    ruta = ruta.replace(':id', dataId);
                    $.ajax({
                        url: ruta,
                        type: "POST",
                        dataType: 'json',
                        data: {
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            //console.log('success:', JSON.stringify(response));
                            $(".modalPrestamos").modal('show');
                            if (response.result === "success") {
                                // DATOS DEL SOCIO
                                var socio = response['socio'];
                                var detalle = response['prestamo-detalle'];
                                var deudaTotal = 0;
                                var disponible = 0;
                                var aval = 'NO';
                                var montoAval = detalle;
                                disponible = parseFloat( socio.saldo );
                                $('#socio').text(socio.nombre_completo);
                                $('#ahorro').text(formatToCurrency(socio.saldo));
                                if(socio.is_aval > 0){
                                    aval = 'SI'+' ( '+formatToCurrency(montoAval)+')';
                                }

                                // Asigna el enlace con el ID del socio
                                $('#btnImprimir').attr('href', '/ticket-saldo/' + socio.id);


                                // DATOS DE LOS PRÉSTAMOS
                                var prestamos = response['prestamos'];
                                var html = '';
                                var deudaTotal = 0;
                                html += ' <ul class="list-unstyled"> ';
                                for (var i = 0; i < prestamos.length; i++) {
                                    var item = prestamos[i];
                                    //var totalQuincenas = parseInt(item.total_quincenas);
                                    //var serie = parseInt(item.serie);
                                    
                                    var capitalPendiente = parseFloat(item.capital_pendiente);
                                    var serie = item.ultima_serie_pagada ? parseInt(item.ultima_serie_pagada.serie_pago) : 0;
                                    var totalQuincenas = item.ultimo_pago_pendiente ? parseInt(item.ultimo_pago_pendiente.serie_final) : item.total_quincenas;
                                    deudaTotal += capitalPendiente;

                                    let fechaOriginal = item.ultimo_pago_pendiente.fecha_tabla; // "2025-09-15"
                                    let partes = fechaOriginal.split('-'); // ["2025", "09", "15"]
                                    let ultimaFecha = `${partes[2]}/${partes[1]}/${partes[0]}`;

                                    //deudaTotal = parseFloat(deudaTotal) + parseFloat(item.capital_pendiente);
                                    html += ' <li class="mb-3"> ';
                                    html += '   <i class="fas fa-long-arrow-alt-right me-2 text-info"></i> ';
                                    html += '       PRÉSTAMO ' +(i+1)+': '+formatToCurrency(capitalPendiente)+' ('+serie+'/'+totalQuincenas+'. '+ultimaFecha+')';
                                    html += ' </li> ';
                                }
                                html += ' </ul> ';
                                let montoDeuda = parseFloat(deudaTotal) + parseFloat(montoAval);
                                let resultado = parseFloat(disponible) - parseFloat(montoDeuda);
                                
                                $('#listPrestamos').html(html);
                                $('#deuda').html(formatToCurrency(deudaTotal));
                                $('#disponible').html(formatToCurrency(Math.max(0, resultado)));
                                $('#aval').html(aval);

                            }
                        },
                        error: function(response) {
                            console.log('error:', JSON.stringify(response));
                        },
                    });
                }else {
                    console.log("dataId no tiene un valor válido.");
                    // Puedes manejar este caso de acuerdo a tus necesidades.
                }
            });

            // MUESTRO EL MODAL PARA LA FOTO
            $(document).on('click', '.show_modal_foto', function() {
                var dataId = $(this).data('id');
                var socio = $(this).data('socio');
                $('.socio_id').val(dataId);
                $(".modalTomaFoto").modal('show');
                // Llama a tomaFoto() después de que el modal se haya mostrado
                $(".modalTomaFoto").on('shown.bs.modal', function() {
                    if (!cameraActive) {
                        tomaFoto(socio);
                        cameraActive = true; // Marca la cámara como activa
                    }
                });
            });

            // MUESTRO EL MODAL PARA CAMBIAR EL TIPO DE SOCIO
            $(document).on('click', '.show_modal_tipo_socio', function() {
                console.log('muestra modal?');
                var dataId = $(this).data('id');
                var dataSocio = $(this).data('socio');
                var dataTipo = $(this).data('tipo');
                const singleSelect = document.querySelector('#type_user');
                const singleSelectInstance = mdb.Select.getInstance(singleSelect);
                singleSelectInstance.setValue(dataTipo);
                $('#idTypeSocio').val(dataId);
                $('#fullNameSocio').text(dataSocio);
                $(".modalTipoSocio").modal('show');
            });

            // Escucha el clic en el botón con el atributo data-mdb-dismiss
            $(document).on('click', '[data-mdb-dismiss="modal"]', function() {
                // Cierra el modal
                $(this).closest('.modal').modal('hide');
                if (cameraActive) {
                    // Detén la cámara solo si está activa
                    stopCamera();
                    cameraActive = false; // Marca la cámara como inactiva
                }
            });

            // ENVIARIA LA FOTO
            $(document).on('click', '.save-foto', function() {
                if (fotoCapturada) {
                    // Aquí puedes agregar la lógica para guardar la foto
                    console.log('La foto se ha capturado y se va a guardar.');
                    guardaFoto();
                } else {
                    console.log('No se ha capturado ninguna foto.');
                }
            });

            // GUARDAMOS EL CAMBIO DE TIPO DE USUARIO
            $(document).on('click', '.save-tipo-socio', function() {
                $.ajax({
                    url: "{{ route('cambio.tipo.socio') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        socios_id: $('#idTypeSocio').val(),
                        tipo_usuario: $('#type_user').val()
                    },
                    success: function(response) {
                        //console.log('success:', JSON.stringify(response));
                        if (response.tipo === 'aprobado') {
                            $('.modalTipoSocio').modal('hide');
                            Swal.fire({
                                icon: response.icon,
                                title: response.title,
                                text: response.text,
                            }).then(function() {
                                // Recarga la página actual después de mostrar el mensaje
                                location.reload();
                            });
                        }
                    },
                    error: function(response) {
                        //console.log('error:', JSON.stringify(response));
                        if (response.tipo === 'error') {
                            Swal.fire({
                                icon: response.icon,
                                title: response.title,
                                text: response.text,
                            }).then(function() {
                                // Recarga la página actual después de mostrar el mensaje
                                location.reload();
                            });
                        }
                    },
                });
            });
            // CAPTURA LA FOTO
            function tomaFoto(socio) {
                var nameSocio = socio;
                const videoElement = document.querySelector('.webcam-feed');
                const captureButton = document.querySelector('.capture-button');
                const photoCanvas = document.querySelector('.photo-canvas');
                const capturedPhoto = document.querySelector('.captured-photo');
                const constraints = {
                    video: true
                };

                // Acceder a la cámara web
                navigator.mediaDevices.getUserMedia(constraints)
                    .then(function(stream) {
                        videoElement.srcObject = stream;
                    })
                    .catch(function(error) {
                        console.error('Error al acceder a la cámara web:', error);
                    });

                // Tomar una foto cuando se hace clic en el botón
                captureButton.addEventListener('click', function() {
                    const context = photoCanvas.getContext('2d');
                    photoCanvas.width = videoElement.videoWidth;
                    photoCanvas.height = videoElement.videoHeight;
                    context.drawImage(videoElement, 0, 0, photoCanvas.width, photoCanvas.height);

                    // Mostrar la foto capturada en una imagen
                    photoDataURL = photoCanvas.toDataURL('image/png');
                    capturedPhoto.src = photoDataURL;
                    capturedPhoto.style.display = 'block';

                    // Marcar que se capturó una foto
                    fotoCapturada = true;

                    // Agregar la lógica de descarga de la foto con jQuery
                    nameSocio = nameSocio.replace(/ /g, '_');
                    var nombreFoto = 'foto_' + nameSocio + '.png';
                    const downloadButton = $('<a>Descargar Foto</a>');
                    downloadButton.attr('href', photoDataURL);
                    downloadButton.attr('download', nombreFoto);
                    downloadButton.addClass('download-button');
                    $('.foto').show();
                    $('.download-container').empty().append(downloadButton);

                });
            }

            // DETINE LA CAMARA EN VIVO
            function stopCamera() {
                // Detener la cámara y liberar los recursos aquí
                const videoElement = document.querySelector('.webcam-feed');
                const stream = videoElement.srcObject;
                const tracks = stream.getTracks();
                tracks.forEach(track => track.stop());
                videoElement.srcObject = null;
            }

            // AJAX ENVIA FOTO
            function guardaFoto() {
                // Verifica si se ha capturado una foto
                if (!fotoCapturada) {
                    alert("Por favor, primero capture una foto.");
                    return;
                }
                // Crear un objeto FormData
                var formData = new FormData();

                // Agregar los datos al objeto FormData
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("usuario_id", $('.socio_id').val());

                // Obtener el archivo de imagen desde el input file
                var inputFile = document.getElementById('img_socio');

                // Verificar si se ha seleccionado un archivo
                if (!inputFile || !inputFile.files || inputFile.files.length === 0) {
                    alert("Por favor, seleccione un archivo.");
                    return;
                }

                var file = inputFile.files[0];

                // Verificar si el archivo es de tipo PNG
                if (file.type !== 'image/png') {
                    alert("Por favor, seleccione un archivo de tipo PNG.");
                    return;
                }

                formData.append("photo", file);

                $.ajax({
                    url: "{{ route('guardar.foto') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        //console.log('success:', JSON.stringify(response));
                        if (response.tipo === 'aprobado') {
                            $('.modalTomaFoto').modal('hide');
                            Swal.fire({
                                icon: response.icon,
                                title: response.title,
                                text: response.text,
                            }).then(function() {
                                // Recarga la página actual después de mostrar el mensaje
                                location.reload();
                            });
                        }
                    },
                    error: function(response) {
                        //console.log('error:', JSON.stringify(response));
                        if (response.tipo === 'error') {
                            Swal.fire({
                                icon: response.icon,
                                title: response.title,
                                text: response.text,
                            }).then(function() {
                                // Recarga la página actual después de mostrar el mensaje
                                location.reload();
                            });
                        }
                    },
                });
            }

            // FORMATO MONEDA
            function formatToCurrency(data) {
                return '$' + Intl.NumberFormat('es-MX', {
                    minimumFractionDigits: 2,
                }).format(data);
            }
        });
    </script>

    @if (Session::has('id'))
        <script type="text/javascript">
            var id = {{ session('id') }};
            setTimeout(function() {
                window.open("{{ url('/recibo-ahorro-voluntario') }}/" + id, '_blank');
            }, 200);
            <?php Session::forget('id'); ?>
        </script>
    @endif

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
