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
        @if (\Request::route()->getName() === 'admin.excel.ahorros.create')
            <h3 class="card-title">APORTACIÓN QUINCENAL</h3>
        @elseif (\Request::route()->getName() === 'admin.excel.ahorros.edit')
            <h3 class="card-title">EDITAR SOCIO</h3>
        @endif
    </div>

    <div class="card-body">
        <div class="register-box-body">
            <div class="card border border-primary shadow-0 mb-3">
                <div class="card-header">
                    <h4>CARGAR APORTACIÓN QUINCENAL DESDE ARCHIVO EXCEL</h4>
                </div>
                <div class="card-body text-dark custom-center-align">
                    <div class="row mb-1">
                        <div class="col-lg-8 col-md-8 col-sm-8">
                            <div class="col">
                                <label class="form-label" for="customFile">CARGAR EL ARCHIVO EXCEL CON LOS
                                    SOCIOS</label>
                                <input type="file" class="form-control" id="customFile" />

                                @error('num_socio')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
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
                                    <h5 class="card-title">SOCIOS CORRECTOS <span id="total_correcto"></span> </h5>
                                    <input type="hidden" name="total_aportacion" id="total_aportacion">
                                    <input type="hidden" name="num_socio" id="num_socio">
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-dark">
                            <div class="row mb-4">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="card-body table-responsive p-0">
                                        <table id="tblCorrecto"
                                            class="table table-striped table-hover dataTable dtr-inline display responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Nombre completo</th>
                                                    <th>Rfc</th>
                                                </tr>
                                            </thead>
                                            <tbody>
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

            <div class="row mb-1">
                <div class="col-6">
                    <div class="card border border-danger shadow-0 mb-3">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-lg-12 col-md-12">
                                    <h5 class="card-title">SOCIOS NO ENCONTRADOS EN LA BASE DE DATOS</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-dark">
                            <div class="row mb-4">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="card-body table-responsive p-0">
                                        <table id="tblDB"
                                            class="table table-striped table-hover dataTable dtr-inline display responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Nombre completo</th>
                                                    <th>Rfc</th>
                                                </tr>
                                            </thead>
                                            <tbody>
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
                                    <h5 class="card-title">SOCIOS NO ENCONTRADOS EN FINANCIEROS</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-dark">
                            <div class="row mb-4">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="card-body table-responsive p-0">
                                        <table id="tblFinanciero"
                                            class="table table-striped table-hover dataTable dtr-inline display responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Nombre completo</th>
                                                    <th>Rfc</th>
                                                </tr>
                                            </thead>
                                            <tbody>
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

                // Verificar si hay datos en la tabla #tblDB
                const resultadoOkTable = document.querySelectorAll('#tblDB tbody tr');
                // Filtra las filas que no son "vacías"
                const filasDatos = Array.from(resultadoOkTable).filter(tr => !tr.classList.contains('dataTables_empty'));

                //if (resultadoOkTable && resultadoOkTable.length > 1) {
                if (filasDatos.length > 1) {
                    // Mostrar la alerta de confirmación
                    Swal.fire({
                        title: 'Hay datos con errores en diferentes campos',
                        text: '¿Estás seguro de guardar la infomación?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, enviar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Si el usuario confirmó, enviar el formulario
                            document.getElementById('form_excel_ahorro').submit();
                            console.log('enviaria el formulario');
                            $("#submitBtn").attr("disabled", true);
                        }
                    });
                }else {
                    const montoTotal  = Number($("#total_aportacion").val()) || 0;
                    const totalSocios = $("#num_socio").val();

                    const montoFormateado = new Intl.NumberFormat('es-MX', {
                        style: 'currency',
                        currency: 'MXN'
                    }).format(montoTotal);
                    // No hay datos → confirmar envío
                    Swal.fire({
                        title: 'Confirmar envío',
                        html: `
                            <p><b>Total de socios:</b> ${totalSocios}</p>
                            <p><b>Monto total:</b> ${montoFormateado}</p>
                            <br>
                            <p>¿Deseas continuar?</p>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, enviar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('form_excel_ahorro').submit();
                            $("#submitBtn").attr("disabled", true);
                        }
                    });
                }
            });

            //BLOQUEA EL BOTON PARA QUE EL FORMULARIO SE ENVIE UNA VEZ
            $('#form_excel_ahorro').submit(function() {
                $("#submitBtn").attr("disabled", true);
            });

            // ENVIAMOS LOS DATOS DEL EXCEL
            // SE GUARDAN EN UNA TABLA TEMPORAL
            $('#btnExcel').click(function(e) {
                e.preventDefault();
                obtenerResultados();
            });

            function obtenerResultados2() {
                var dataNoDb, dataNoFinanciero, obtenerDatosCorrectos;

                return new Promise(function(resolve, reject) {
                    // Realizar la solicitud AJAX para obtener los resultados
                    var formData = new FormData();
                    formData.append('archivo', $('#customFile')[0].files[0]);

                    $.ajax({
                        url: "{{ route('leer.excel.ahorros') }}",
                        type: 'POST',
                        data: formData,
                        contentType: false, // No configurar contentType aquí
                        processData: false, // No procesar los datos aquí
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                'content') // Incluir el token CSRF en los encabezados
                        },
                        success: function(response) {
                            resolve(response); // Resuelve la promesa con los resultados

                            // VALORES NO EN BASE DE DATOS
                            if (response == 'success') {
                                // Hacer la primera llamada AJAX para obtener datos de datos_temporales
                                obtenerDatosNoDb()
                                    .then(function(dataNoDb) {
                                        // Hacer la segunda llamada AJAX para obtener datos de socios
                                        return obtenerDatosNoFinanciero();
                                    })
                                    .then(function(dataNoFinanciero) {
                                        // Hacer la segunda llamada AJAX para obtener datos de noFinanciero
                                        //return obtenerDatosNoFinanciero();
                                        //console.log('Datos No DB:', dataNoDb);
                                        //console.log('Datos No Financiero:', dataNoFinanciero);
                                    })
                                    .then(function(obtenerDatosCorrectos) {
                                    })
                                    .catch(function(error) {
                                        console.error(error);
                                    });
                            }
                        },
                        error: function(response) {
                            reject(error); // Rechaza la promesa en caso de error
                            console.log('error:', JSON.stringify(response));
                        }
                    });
                });
            }

            function obtenerResultados() {
                return new Promise(function(resolve, reject) {
                    var formData = new FormData();
                    formData.append('archivo', $('#customFile')[0].files[0]);

                    $.ajax({
                        url: "{{ route('leer.excel.ahorros') }}",
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response === 'success') {
                                // Ejecutar las 3 funciones en orden
                                obtenerDatosNoDb()
                                    .then(obtenerDatosNoFinanciero)
                                    .then(obtenerDatosCorrectos)
                                    .then(() => {
                                        resolve('Todas las funciones ejecutadas correctamente');
                                    })
                                    .catch(error => {
                                        console.error('Error al ejecutar las funciones:', error);
                                        reject(error);
                                    });
                            } else {
                                reject('La respuesta no fue success');
                            }
                        },
                        error: function(error) {
                            console.log('Error en leer.excel.ahorros:', error);
                            reject(error);
                        }
                    });
                });
            }

            function obtenerDatosNoDb() {
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: "{{ route('obtener.noDb.ahorros') }}",
                        type: 'POST',
                        contentType: false, // No configurar contentType aquí
                        processData: false, // No procesar los datos aquí
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                'content') // Incluir el token CSRF en los encabezados
                        },
                        success: function(response) {
                            resolve(response); // Resuelve la promesa con los resultados

                            // VALORES NO EN BASE DE DATOS
                            try {
                                // Analiza la respuesta JSON en un objeto
                                const responseData = JSON.parse(response);

                                // Verifica si la respuesta tiene la clave "data"
                                if (responseData.hasOwnProperty('data')) {
                                    const dataArray = responseData.data;

                                    const tabla = document.getElementById('tblDB');
                                    const tbody = tabla.querySelector('tbody');

                                    // Limpia el contenido previo de la tabla si es necesario
                                    tbody.innerHTML = '';

                                    // Itera sobre los registros y crea filas en la tabla
                                    dataArray.forEach(registro => {
                                        const fila = document.createElement('tr');

                                        // Crea celdas y agrega contenido
                                        const nombreCompletoCell = document.createElement('td');
                                        nombreCompletoCell.textContent = registro.nombre_completo;
                                        fila.appendChild(nombreCompletoCell);

                                        const rfcCell = document.createElement('td');
                                        rfcCell.textContent = registro.rfc;
                                        fila.appendChild(rfcCell);

                                        // Agrega la fila a la tabla
                                        tbody.appendChild(fila);
                                    });

                                    // Inicializa DataTable si es necesario
                                    if (!$.fn.dataTable.isDataTable('#tblDB')) {
                                        $('#tblDB').DataTable({
                                            "language": {
                                                "url": "{{ asset('/json/i18n/es_es.json') }}"
                                            }
                                        });
                                    }
                                } else {
                                    console.error("La respuesta no contiene la clave 'data':", responseData);
                                }
                            } catch (error) {
                                console.error("Error al analizar la respuesta JSON:", error);
                            }
                        },
                        error: function(response) {
                            reject(error); // Rechaza la promesa en caso de error
                            console.log('error:', JSON.stringify(response));
                        }
                    });
                });
            }

            function obtenerDatosNoFinanciero() {
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: "{{ route('obtener.noFinanciero.ahorros') }}",
                        type: 'POST',
                        contentType: false, // No configurar contentType aquí
                        processData: false, // No procesar los datos aquí
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                'content') // Incluir el token CSRF en los encabezados
                        },
                        success: function(response) {
                            resolve(response); // Resuelve la promesa con los resultados
                            try {
                                // Analiza la respuesta JSON en un objeto
                                const responseData = JSON.parse(response);

                                // Verifica si la respuesta tiene la clave "data"
                                if (responseData.hasOwnProperty('data')) {
                                    const dataArray = responseData.data;

                                    const tabla = document.getElementById('tblFinanciero');
                                    const tbody = tabla.querySelector('tbody');

                                    // Limpia el contenido previo de la tabla si es necesario
                                    tbody.innerHTML = '';

                                    // Itera sobre los registros y crea filas en la tabla
                                    dataArray.forEach(registro => {
                                        const fila = document.createElement('tr');

                                        // Crea celdas y agrega contenido
                                        const nombreCompletoCell = document.createElement('td');
                                        nombreCompletoCell.textContent = registro.nombre_completo;
                                        fila.appendChild(nombreCompletoCell);

                                        const rfcCell = document.createElement('td');
                                        rfcCell.textContent = registro.rfc;
                                        fila.appendChild(rfcCell);

                                        // Agrega la fila a la tabla
                                        tbody.appendChild(fila);
                                    });

                                    // Inicializa DataTable si es necesario
                                    if (!$.fn.dataTable.isDataTable('#tblFinanciero')) {
                                        $('#tblFinanciero').DataTable({
                                            "language": {
                                                "url": "{{ asset('/json/i18n/es_es.json') }}"
                                            }
                                        });
                                    }
                                } else {
                                    console.error("La respuesta no contiene la clave 'data':", responseData);
                                }
                            } catch (error) {
                                console.error("Error al analizar la respuesta JSON:", error);
                            }
                        },
                        error: function(response) {
                            reject(error); // Rechaza la promesa en caso de error
                            console.log('error:', JSON.stringify(response));
                        }
                    });
                });
            }

            function obtenerDatosCorrectos() {
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: "{{ route('obtener.socios.ahorros.excel') }}",
                        type: 'POST',
                        contentType: false, // No configurar contentType aquí
                        processData: false, // No procesar los datos aquí
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                'content') // Incluir el token CSRF en los encabezados
                        },
                        success: function(response) {
                            resolve(response); // Resuelve la promesa con los resultados
                            try {
                                // Analiza la respuesta JSON en un objeto
                                const responseData = JSON.parse(response);

                                // Aquí accedes al total
                                const montoTotal = responseData.monto_total;
                                const totalSocios = responseData.total_socios;

                                $("#total_aportacion").val(montoTotal);
                                $("#num_socio").val(totalSocios);

                                // Verifica si la respuesta tiene la clave "data"
                                if (responseData.hasOwnProperty('data')) {
                                    const dataArray = responseData.data;

                                    const tabla = document.getElementById('tblCorrecto');
                                    const tbody = tabla.querySelector('tbody');

                                    // Limpia el contenido previo de la tabla si es necesario
                                    tbody.innerHTML = '';

                                    // Itera sobre los registros y crea filas en la tabla
                                    dataArray.forEach(registro => {
                                        const fila = document.createElement('tr');

                                        // Crea celdas y agrega contenido
                                        const nombreCompletoCell = document.createElement('td');
                                        nombreCompletoCell.textContent = registro.nombre_completo;
                                        fila.appendChild(nombreCompletoCell);

                                        const rfcCell = document.createElement('td');
                                        rfcCell.textContent = registro.rfc;
                                        fila.appendChild(rfcCell);

                                        // Agrega la fila a la tabla
                                        tbody.appendChild(fila);
                                    });

                                    //MOSTRAR EL TOTAL total_correcto
                                    $('#total_correcto').text(montoTotal.toLocaleString('es-MX', {
                                        style: 'currency',
                                        currency: 'MXN'
                                    }));

                                    // Inicializa DataTable si es necesario
                                    if (!$.fn.dataTable.isDataTable('#tblCorrecto')) {
                                        $('#tblCorrecto').DataTable({
                                            "language": {
                                                "url": "{{ asset('/json/i18n/es_es.json') }}"
                                            }
                                        });
                                    }
                                } else {
                                    console.error("La respuesta no contiene la clave 'data':", responseData);
                                }
                            } catch (error) {
                                console.error("Error al analizar la respuesta JSON:", error);
                            }
                        },
                        error: function(response) {
                            reject(error); // Rechaza la promesa en caso de error
                            console.log('error:', JSON.stringify(response));
                        }
                    });
                });
            }


        });
    </script>


@stop
