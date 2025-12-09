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
            display: flex !important;
            justify-content: center !important;
        }
    </style>
@stop

<br />
@csrf
<div class="card card-outline card-primary">
    <div class="card-header">
        @if (\Request::route()->getName() === 'admin.sector.categoria.create')
            <h3 class="card-title">NUEVO SECTOR / CATEGORÍA</h3>
        @elseif (\Request::route()->getName() === 'admin.sector.categoria.edit')
            <h3 class="card-title">EDITAR SECTOR / CATEGORÍA</h3>
        @endif
    </div>

    <div class="card-body">
        <div class="register-box-body">
            <div class="card border border-primary shadow-0 mb-3">
                <div class="card-body text-dark">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('nombre', old('nombre', $sectorCategoria->nombre), ['id' => 'nombre', 'class' => 'form-control uppercase', 'placeholder' => 'Nombre', 'autofocus' => 'true', 'tabindex' => '1', 'required']) }}
                                    <label class="form-label" for="nombre">NOMBRE</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('nombre')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::hidden('sectorId', old('sectorId', $sectorCategoria->sector_id)) }}

                                    {{--!! Form::select(
                                        'sector_id',
                                        ['-1' => '- SECTOR -'] + $tipoValues->pluck('nombre', 'id')->all(),
                                        old('sector_id', $sectorCategoria->sector_id),
                                        [
                                            'class' => 'select mb-2',
                                            'id' => 'sector_id',
                                            'data-mdb-filter' => 'true',
                                            'tabindex' => '19',
                                        ],
                                    ) !!--}}

                                    {!! Form::select(
                                        'tipo',
                                        ['-1' => '- SELECCIONE -'] + array_combine($tipoValues, $tipoValues), // Combina el array para que sea usable en el select
                                        old('tipo', null),
                                        [
                                            'class' => 'select mb-2',
                                            'id' => 'tipo',
                                            'data-mdb-filter' => 'true',
                                            'tabindex' => '2',
                                        ]
                                    ) !!}
                                    <label class="form-label select-label" for="tipo">SECTOR/CATEGORÍA</label>
                                    <div class="form-helper" id="sector_feedback" style="color: red; display: none;">
                                        Este
                                        campo es requerido.</div>
                                </div>
                                @error('tipo')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
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
            var oldValues = @json(old()); // Obtén los valores old en formato JSON







            // Función para actualizar el campo nombre_completo
            function actualizarNombreCompleto() {
                var apellidoPaterno = $('#apellido_paterno').val();
                var apellidoMaterno = $('#apellido_materno').val();
                var nombre = $('#nombre').val();

                // Construye el nombre completo
                var nombreCompleto = apellidoPaterno + ' ' + apellidoMaterno + ' ' + nombre;
                console.log('nombre completo: ' + nombreCompleto);

                // Actualiza el campo nombre_completo
                $('#nombre_completo').val(nombreCompleto);
            }

        });
    </script>


@stop
