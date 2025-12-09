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
        @if (\Request::route()->getName() === 'admin.socios.create')
            <h3 class="card-title">NUEVO SOCIO</h3>
        @elseif (\Request::route()->getName() === 'admin.socios.edit')
            <h3 class="card-title">EDITAR SOCIO</h3>
        @endif
    </div>

    <div class="card-body">
        @if($socios == false)
            <div class="alert alert-dismissible fade show" role="alert" data-mdb-color="warning">
                <strong>Ya se han insertado datos a travéz de un excel.</strong>
                Por favor ingrese los nuevos socios a rtavéz del módulo
                <strong> <a href="{{ route('admin.socios.create') }}" class="navbar-text">Nuevo socio.</a> </strong>
                <button type="button" class="btn-close" data-mdb-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="register-box-body">
            <div class="card border border-primary shadow-0 mb-3">
                <div class="card-header">
                    <h4>CARGAR SOCIOS DESDE ARCHIVO EXCEL</h4>
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
                <div class="col-6">
                    <div class="card border border-success shadow-0 mb-3">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-lg-6 col-md-12">
                                    <h4 class="card-title">DATOS CORRECTOS</h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-dark">
                            <div class="row mb-4">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="card-body table-responsive p-0">
                                        <table id="resultadoOk"
                                            class="table table-striped table-hover dataTable dtr-inline display responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Apellido Paterno</th>
                                                    <th>Apellido Materno</th>
                                                    <th>Nombre completo</th>
                                                    <th>Rfc</th>
                                                    <th>Teléfono</th>
                                                    <th>Domicilio</th>
                                                    <th>Curp</th>
                                                    <th>Cuip</th>
                                                    <th>Estado civil</th>
                                                    <th>Contacto emergerncia</th>
                                                    <th>Tel emergencia</th>
                                                    <th>Tipo de sangre</th>
                                                    <th>Lugar de origen</th>
                                                    <th>Compañia</th>
                                                    <th>Batallón</th>
                                                    <th>Sector</th>
                                                    <th>Categoría</th>
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
                                <div class="col-lg-6 col-md-12">
                                    <h4 class="card-title">DATOS CON ERRORES DE CAMPOS</h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-dark">
                            <div class="row mb-4">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="card-body table-responsive p-0">
                                        <table id="resultadoFail"
                                            class="table table-striped table-hover dataTable dtr-inline display responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Apellido Paterno</th>
                                                    <th>Apellido Materno</th>
                                                    <th>Nombre completo</th>
                                                    <th>Rfc</th>
                                                    <th>Teléfono</th>
                                                    <th>Domicilio</th>
                                                    <th>Curp</th>
                                                    <th>Cuip</th>
                                                    <th>Estado civil</th>
                                                    <th>Contacto emergerncia</th>
                                                    <th>Tel emergencia</th>
                                                    <th>Tipo de sangre</th>
                                                    <th>Lugar de origen</th>
                                                    <th>Compañia</th>
                                                    <th>Batallón</th>
                                                    <th>Sector</th>
                                                    <th>Categoría</th>
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

            @if($socios)
                <div class="row mb-3">
                    <div class="col-lg-12 col-md-12 col-sm-12" align="center">
                        <div class="col-2 custom-center-align">
                            <br />
                            {!! Form::button('Guardar', ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'submitBtn']) !!}
                        </div>
                    </div>
                </div>
                <br />
            @endif
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

                // Verificar si hay datos en la tabla #resultadoOk
                const resultadoOkTable = document.querySelector('#resultadoOk');

                if (resultadoOkTable && resultadoOkTable.rows.length > 1) {
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
                            document.getElementById('form_excel_socios').submit();
                            console.log('enviaria el formulario');
                            $("#submitBtn").attr("disabled", true);
                        }
                    });
                }
            });

            //BLOQUEA EL BOTON PARA QUE EL FORMULARIO SE ENVIE UNA VEZ
            $('#form_excel_socios').submit(function() {
                $("#submitBtn").attr("disabled", true);
            });	
            
            // ENVIAMOS LOS DATOS DEL EXCEL
            // SE GUARDAN EN UNA TABLA TEMPORAL
            $('#btnExcel').click(function(e) {
                e.preventDefault();

                var formData = new FormData();
                formData.append('archivo', $('#customFile')[0].files[0]);

                $.ajax({
                    url: "{{ route('leer.excel.socios') }}",
                    type: 'POST',
                    data: formData,
                    contentType: false, // No configurar contentType aquí
                    processData: false, // No procesar los datos aquí
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // Incluir el token CSRF en los encabezados
                    },

                    /*

                    <table class="table item_table" id="tblBeneficiario">
                                    <thead>
                                        <tr>
                                            <th>NOMBRE</th>
                                            <th>DOMICILIO</th>
                                            <th>TELÉFONO</th>
                                            <th>PORCENTAJE</th>
                                            <th>OPC</th>
                                        </tr>
                                    </thead>
                                    <tbody id="body_details_table">

                                    </tbody>
                                </table>

                    */
                    /*success: function (response) {
                        //console.log('Response:', JSON.stringify(response));
                        // Por ejemplo, mostrar la información en una tabla en la página
                        if (response.length > 0) {
                            var table = '<table class="table item_table" id="resultTable">';
                            
                            // Generar la cabecera de la tabla usando el primer registro de datos
                            table += '<thead><tr>';
                            for (var j = 0; j < response[0].length; j++) {
                                table += '<th>' + response[0][j] + '</th>';
                            }
                            table += '</tr></thead>';
                            
                            // Generar el cuerpo de la tabla
                            table += '<tbody>';
                            for (var i = 1; i < response.length; i++) {
                                table += '<tr>';
                                for (var j = 0; j < response[i].length; j++) {
                                    table += '<td>' + response[i][j] + '</td>';
                                }
                                table += '</tr>';
                            }
                            table += '</tbody>';

                            table += '</table>';
                            $('#tablaResultado').html(table);

                            // Inicializa el DataTable
                            $('#resultTable').DataTable();
                        }
                    }*/
                    success: function(response) {
                        //console.log('success:', JSON.stringify(response));
                        console.log('success');

                        // VALORES CORRECTOS - INSERTAFOS EN LA TABLA
                        if (response.data && response.data.length > 0) {
                            // Realiza aquí la lógica para manejar los registros correctos
                            const tabla = document.getElementById('resultadoOk');
                            const tbody = tabla.querySelector('tbody');

                            // Recorre los registros y agrega una fila por cada uno
                            response.data.forEach(registro => {
                                const fila = document.createElement('tr');

                                // Agrega celdas con los datos del registro
                                const name = document.createElement('td');
                                name.textContent = registro.nombre;
                                fila.appendChild(name);

                                const paterno = document.createElement('td');
                                paterno.textContent = registro.apellido_paterno;
                                fila.appendChild(paterno);

                                const materno = document.createElement('td');
                                materno.textContent = registro.apellido_materno;
                                fila.appendChild(materno);

                                const fullName = document.createElement('td');
                                fullName.textContent = registro.nombre_completo;
                                fila.appendChild(fullName);

                                const rfc = document.createElement('td');
                                rfc.textContent = registro.rfc;
                                fila.appendChild(rfc);

                                const tel = document.createElement('td');
                                tel.textContent = registro.telefono;
                                fila.appendChild(tel);

                                const dom = document.createElement('td');
                                dom.textContent = registro.domicilio;
                                fila.appendChild(dom);

                                const curp = document.createElement('td');
                                curp.textContent = registro.curp;
                                fila.appendChild(curp);

                                const cuip = document.createElement('td');
                                cuip.textContent = registro.cuip;
                                fila.appendChild(cuip);

                                const edoCivil = document.createElement('td');
                                edoCivil.textContent = registro.estado_civil;
                                fila.appendChild(edoCivil);

                                const emergencia = document.createElement('td');
                                emergencia.textContent = registro.contacto_emergencia;
                                fila.appendChild(emergencia);

                                const telEmergencia = document.createElement('td');
                                telEmergencia.textContent = registro
                                .telefono_emergencia;
                                fila.appendChild(telEmergencia);

                                const tipoSangre = document.createElement('td');
                                tipoSangre.textContent = registro.tipo_sangre;
                                fila.appendChild(tipoSangre);

                                const origen = document.createElement('td');
                                origen.textContent = registro.lugar_origen;
                                fila.appendChild(origen);

                                const compania = document.createElement('td');
                                compania.textContent = registro.compania;
                                fila.appendChild(compania);

                                const batallon = document.createElement('td');
                                batallon.textContent = registro.batallon;
                                fila.appendChild(batallon);

                                const sector = document.createElement('td');
                                sector.textContent = registro.sector_id;
                                fila.appendChild(sector);

                                const category = document.createElement('td');
                                category.textContent = registro.categoria_id;
                                fila.appendChild(category);

                                // Agrega más celdas según tus datos

                                // Agrega la fila a la tabla
                                tbody.appendChild(fila);
                            });

                            // inicializa DataTable
                            $('#resultadoOk').DataTable({
                                "language": {
                                    "url": "{{ asset('/json/i18n/es_es.json') }}"
                                }
                            });
                        }

                        // VALORES DUPLICADOS - NO INSERTADOS
                        if (response.erroresUnicidad && response.erroresUnicidad.length > 0) {
                            // Realiza aquí la lógica para manejar los registros con errores
                            const tabla = document.getElementById('resultadoFail');
                            const tbody = tabla.querySelector('tbody');

                            // Recorre los registros y agrega una fila por cada uno
                            response.erroresUnicidad.forEach(registro => {
                                const fila = document.createElement('tr');

                                // Agrega celdas con los datos del registro
                                const name = document.createElement('td');
                                name.textContent = registro.nombre;
                                fila.appendChild(name);

                                const paterno = document.createElement('td');
                                paterno.textContent = registro.apellido_paterno;
                                fila.appendChild(paterno);

                                const materno = document.createElement('td');
                                materno.textContent = registro.apellido_materno;
                                fila.appendChild(materno);

                                const fullName = document.createElement('td');
                                fullName.textContent = registro.nombre_completo;
                                fila.appendChild(fullName);

                                const rfc = document.createElement('td');
                                rfc.textContent = registro.rfc;
                                fila.appendChild(rfc);

                                const tel = document.createElement('td');
                                tel.textContent = registro.telefono;
                                fila.appendChild(tel);

                                const dom = document.createElement('td');
                                dom.textContent = registro.domicilio;
                                fila.appendChild(dom);

                                const curp = document.createElement('td');
                                curp.textContent = registro.curp;
                                fila.appendChild(curp);

                                const cuip = document.createElement('td');
                                cuip.textContent = registro.cuip;
                                fila.appendChild(cuip);

                                const edoCivil = document.createElement('td');
                                edoCivil.textContent = registro.estado_civil;
                                fila.appendChild(edoCivil);

                                const emergencia = document.createElement('td');
                                emergencia.textContent = registro.contacto_emergencia;
                                fila.appendChild(emergencia);

                                const telEmergencia = document.createElement('td');
                                telEmergencia.textContent = registro
                                .telefono_emergencia;
                                fila.appendChild(telEmergencia);

                                const tipoSangre = document.createElement('td');
                                tipoSangre.textContent = registro.tipo_sangre;
                                fila.appendChild(tipoSangre);

                                const origen = document.createElement('td');
                                origen.textContent = registro.lugar_origen;
                                fila.appendChild(origen);

                                const compania = document.createElement('td');
                                compania.textContent = registro.compania;
                                fila.appendChild(compania);

                                const batallon = document.createElement('td');
                                batallon.textContent = registro.batallon;
                                fila.appendChild(batallon);

                                const sector = document.createElement('td');
                                sector.textContent = registro.sector_id;
                                fila.appendChild(sector);

                                const category = document.createElement('td');
                                category.textContent = registro.categoria_id;
                                fila.appendChild(category);

                                // Agrega más celdas según tus datos

                                // Agrega la fila a la tabla
                                tbody.appendChild(fila);
                            });

                            // inicializa DataTable
                            $('#resultadoFail').DataTable({
                                "language": {
                                    "url": "{{ asset('/json/i18n/es_es.json') }}"
                                }
                            });
                        }
                    },
                    error: function(response) {
                        console.log('error:', JSON.stringify(response));
                    }
                });
            });

            // Función para comprobar duplicados en columnas específicas (7, 8, 12, 13)
            function checkForDuplicates(row) {
                var columnsToCheck = [7, 8, 12, 13]; // Índices de columnas a comprobar

                for (var i = 0; i < columnsToCheck.length; i++) {
                    for (var j = i + 1; j < columnsToCheck.length; j++) {
                        if (row[columnsToCheck[i]] === row[columnsToCheck[j]]) {
                            return true; // Hay un duplicado en las columnas
                        }
                    }
                }
                return false; // No hay duplicados en las columnas
            }

        });
    </script>


@stop
