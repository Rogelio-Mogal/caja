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

        .custom-p {
            display: inline-block;
            padding: .375rem .75rem;
            margin-bottom: 0;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
            text-transform: uppercase;
        }

        .custom-p:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
@stop

<br />
@csrf
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Detallalles del socio</h3>
    </div>

    <div class="card-body">
        <div class="register-box-body">
            <div class="row mb-4">
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="col">
                        <label for="campo1" class="form-label fw-bold">NÚMERO DE SOCIO</label>
                        <p id="campo1" class="form-control custom-p">
                            {{ $socios->num_socio }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="col">
                        <label for="campo2" class="form-label fw-bold">APELLIDO PATERNO</label>
                        <p id="campo2" class="form-control custom-p">
                            {{ $socios->apellido_paterno }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="col">
                        <label for="campo3" class="form-label fw-bold">APELLIDO MATERNO</label>
                        <p id="campo3" class="form-control custom-p">
                            {{ $socios->apellido_materno }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="col">
                        <label for="campo4" class="form-label fw-bold">NOMBRE(S)</label>
                        <p id="campo4" class="form-control custom-p">
                            {{ $socios->nombre }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="col">
                        <label for="campo5" class="form-label fw-bold">RFC</label>
                        <p id="campo5" class="form-control custom-p">
                            {{ $socios->rfc }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="col">
                        <label for="campo6" class="form-label fw-bold">INGRESO A CAJA</label>
                        <p id="campo6" class="form-control custom-p">
                            {{ $socios->fecha_alta }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="col">
                        <label for="campo7" class="form-label fw-bold">TELÉFONO</label>
                        <p id="campo7" class="form-control custom-p">
                            {{ $socios->telefono }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="col">
                        <label for="campo8" class="form-label fw-bold">DOMICILIO</label>
                        <p id="campo8" class="form-control custom-p">
                            {{ $socios->domicilio }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="col">
                        <label for="campo9" class="form-label fw-bold">CURP</label>
                        <p id="campo9" class="form-control custom-p">
                            {{ $socios->curp }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="col">
                        <label for="campo10" class="form-label fw-bold">CUIP</label>
                        <p id="campo10" class="form-control custom-p">
                            {{ $socios->cuip }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="col">
                        <label for="campo11" class="form-label fw-bold">ESTADO CIVIL</label>
                        <p id="campo11" class="form-control custom-p">
                            {{ $socios->estado_civil }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="col">
                        <label for="campo12" class="form-label fw-bold">EMERGENCIAS, COMUNICARSE CON</label>
                        <p id="campo12" class="form-control custom-p">
                            {{ $socios->contacto_emergencia }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="col">
                        <label for="campo13" class="form-label fw-bold">TELÉFONO DE EMERGENCIA</label>
                        <p id="campo13" class="form-control custom-p">
                            {{ $socios->telefono_emergencia }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="col">
                        <label for="campo14" class="form-label fw-bold">TIPO DE SANGRE</label>
                        <p id="campo14" class="form-control custom-p">
                            {{ $socios->tipo_sangre }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="col">
                        <label for="campo15" class="form-label fw-bold">LUGAR DE ORIGEN</label>
                        <p id="campo15" class="form-control custom-p">
                            {{ $socios->lugar_origen }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <div class="col">
                        <label for="campo16" class="form-label fw-bold">ALTA A LA COORPORACIÓN</label>
                        <p id="campo16" class="form-control custom-p">
                            {{ $socios->alta_coorporacion }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="col">
                        <label for="campo17" class="form-label fw-bold">COMPAÑIA</label>
                        <p id="campo17" class="form-control custom-p">
                            {{ $socios->compania }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="col">
                        <label for="campo18" class="form-label fw-bold">BATALLÓN</label>
                        <p id="campo18" class="form-control custom-p">
                            {{ $socios->batallon }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="col">
                        <label for="campo19" class="form-label fw-bold">SECTOR</label>
                        <p id="campo19" class="form-control custom-p">
                            {{ $socios->sector->sector }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="col">
                        <label for="campo20" class="form-label fw-bold">CATEGORÍA</label>
                        <p id="campo20" class="form-control custom-p">
                            {{ $socios->categoria->categoria }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- !! Form::open(['method' => 'DELETE', 'route' => ['admin.socios.destroy', $socios->id]]) !! --}}
             
            <form method="POST" action="{{ route('admin.socios.destroy', $socios->id) }}">
                @csrf
                @method('DELETE')
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-4">
                        <div class="col mb-3">
                            <div class="form-outline">
                                {{ Form::hidden('tipos', old('tipo', $socios->tipo)) }}

                                {!! Form::select('tipo', array_combine($tipoValues, $tipoValues), old('tipo', $socios->tipo), [
                                    'class' => 'select mb-2',
                                    'id' => 'tipo',
                                    'name' => 'tipo',
                                    'data-mdb-filter' => 'true',
                                    'required' => 'true',
                                ]) !!}
                                <label class="form-label select-label" for="tipo">ESTATUS</label>
                            </div>
                            @error('tipo')
                                <p class="error-message text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4">
                        <div class="col mb-3">
                            <div class="form-outline datepicker-translated" data-mdb-toggle-button="false">
                                {{ Form::text('fecha_baja', old('fecha_baja', $socios->fecha_baja ? Carbon\Carbon::createFromFormat('Y-m-d', $socios->fecha_baja)->format('d/m/Y') : ''), ['id' => 'fecha_baja', 'data-mdb-toggle' => 'datepicker', 'class' => 'form-control uppercase', 'placeholder' => 'FECHA BAJA', 'required' => 'true']) }}
                                <label class="form-label" for="fecha_baja">FECHA BAJA</label>
                            </div>
                            <div class="invalid-feedback">
                                Please provide a valid input.
                            </div>
                            @error('fecha_baja')
                                <p class="error-message text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-4">
                        <div class="col mb-3">
                            <div class="form-outline">
                                {{ Form::textarea('observaciones', old('observaciones', $socios->observaciones), ['id' => 'observaciones', 'name' => 'observaciones', 'class' => 'form-control', 'rows' => '3']) }}
                                <label class="form-label" for="fecha_baja">OBSERVACIONES</label>
                            </div>
                            @error('observaciones')
                                <p class="error-message text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-12 col-md-12 col-sm-12" align="center">
                        <div class="col-2">

                            <div class="form-group">
                                {!! Form::submit('Elimina Registro', [
                                    'class' => 'btn btn-raised btn-danger',
                                    'id' => 'btn-delete',
                                    'data-id' => $socios->id,
                                ]) !!}
                            </div>

                        </div>
                    </div>
                </div>
            </form>
            {{-- !! Form::close() !! --}}
        </div>
    </div>
</div>


@section('js')
    <script>

        $(document).ready(function() {

            // CALENDARIO
            const datepickerElements = document.querySelectorAll('.datepicker-translated');
            datepickerElements.forEach(function(element) {
                new mdb.Datepicker(element, {
                    disableFuture: true,
                    confirmDateOnSelect: true,
                    title: 'Seleccione una fecha',
                    monthsFull: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio',
                        'Agosto',
                        'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                    ],
                    monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep',
                        'Oct', 'Nov',
                        'Dic'
                    ],
                    weekdaysFull: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes',
                        'Sábado'
                    ],
                    weekdaysShort: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                    weekdaysNarrow: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
                    okBtnText: 'Ok',
                    clearBtnText: 'Limpiar',
                    cancelBtnText: 'Cancelar',
                });
            });

            // Evitar entrada de datos en el campo fecha_baja
            var inputFechaBaja = $('#fecha_baja');
            inputFechaBaja.on('keydown', function(e) {
                e.preventDefault();
            });

            // Manejar el clic en la opción "Eliminar"
            $('#btn-delete').on('click', function(e) {
                e.preventDefault();
                var id = $(this).data('id');

                // Utilizar SweetAlert2 para mostrar un mensaje de confirmación
                Swal.fire({
                    title: '¿Estás seguro de eliminar el registro?',
                    text: 'No podrás revertir esto',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminarlo'
                }).then((result) => {
                    if (result.value) {
                        // Solicitud AJAX para eliminar el elemento
                        $.ajax({
                            url: "{{ route('admin.socios.destroy', ':id') }}"
                                .replace(':id', id),
                            type: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "_method": "DELETE",
                                "tipo": $('#tipo').val(),
                                "fecha_baja": $('#fecha_baja').val(),
                                "observaciones": $('#observaciones').val(),
                            },
                            success: function(response) {
                                Swal.fire(response.swal).then(() => {
                                    window.location.href = response.redirect;
                                });
                            },
                            error: function(xhr, status, error) {
                                //console.error(xhr.responseText);
                                if (xhr.status === 400) {
                                    var swalData = xhr.responseJSON.swal;
                                    Swal.fire({
                                        icon: 'error',
                                        title: swalData.title || 'Error',
                                        text: swalData.text ||
                                            'Hubo un error durante el proceso.',
                                    });
                                } else {
                                    console.error(xhr.responseText);
                                }
                            }
                        });
                    }
                });
            });


        });
    </script>


@stop
