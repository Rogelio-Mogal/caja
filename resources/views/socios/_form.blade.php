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
        @if (\Request::route()->getName() === 'admin.socios.create')
            <h3 class="card-title">NUEVO SOCIO</h3>
        @elseif (\Request::route()->getName() === 'admin.socios.edit')
            <h3 class="card-title">EDITAR SOCIO</h3>
        @endif
    </div>

    <div class="card-body">
        <div class="register-box-body">
            <div class="card border border-primary shadow-0 mb-3">
                <div class="card-header">
                    <h4>DATOS GENERALES</h4>
                </div>
                <div class="card-body text-dark">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('num_socio', old('num_socio', $socios->num_socio), ['id' => 'num_socio', 'class' => 'form-control uppercase', 'placeholder' => 'NÚMERO DE SOCIO', 'autofocus' => 'false', 'tabindex' => '1', 'aria-label' => 'readonly input', 'readonly']) }}
                                    <label class="form-label" for="num_socio">NÚMERO DE SOCIO</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('num_socio')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('apellido_paterno', old('apellido_paterno', $socios->apellido_paterno), ['id' => 'apellido_paterno', 'class' => 'form-control uppercase nombre', 'autofocus' => 'true', 'placeholder' => 'APELLIDO PATERNO', 'tabindex' => '2']) }}
                                    <label class="form-label" for="apellido_paterno">APELLIDO PATERNO</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('apellido_paterno')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                                @error('nombre_completo')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('apellido_materno', old('apellido_materno', $socios->apellido_materno), ['id' => 'apellido_materno', 'class' => 'form-control uppercase nombre', 'placeholder' => 'APELLIDO MATERNO', 'tabindex' => '3']) }}
                                    <label class="form-label" for="apellido_materno">APELLIDO MATERNO</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('apellido_materno')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                                @error('nombre_completo')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('nombre', old('nombre', $socios->nombre), ['id' => 'nombre', 'class' => 'form-control uppercase nombre', 'placeholder' => 'NOMBRE(S)', 'tabindex' => '4', 'required']) }}
                                    <label class="form-label" for="nombre">NOMBRE(S)</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('nombre')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                                @error('nombre_completo')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                                {{ Form::hidden('nombre_completo', old('nombre_completo', $socios->nombre_completo), ['id' => 'nombre_completo', 'class' => 'uppercase']) }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('rfc', old('rfc', $socios->rfc), ['id' => 'rfc', 'class' => 'form-control uppercase', 'placeholder' => 'RFC', 'tabindex' => '5', 'required']) }}
                                    <label class="form-label" for="rfc">RFC</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('rfc')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('fecha_alta', old('fecha_alta', $socios->fecha_alta), ['id' => 'fecha_alta', 'class' => 'form-control uppercase', 'placeholder' => 'INGRESO A CAJA', 'tabindex' => '6', 'aria-label' => 'readonly input', 'readonly']) }}
                                    <label class="form-label" for="fecha_alta">INGRESO A CAJA</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('fecha_alta')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('telefono', old('telefono', $socios->telefono), ['id' => 'telefono', 'class' => 'form-control uppercase', 'placeholder' => 'TELÉFONO', 'tabindex' => '7', 'required']) }}
                                    <label class="form-label" for="telefono">TELÉFONO</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('telefono')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('domicilio', old('domicilio', $socios->domicilio), ['id' => 'domicilio', 'class' => 'form-control uppercase', 'placeholder' => 'DOMICILIO', 'tabindex' => '8', 'required']) }}
                                    <label class="form-label" for="domicilio">DOMICILIO</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('domicilio')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('curp', old('curp', $socios->curp), ['id' => 'curp', 'class' => 'form-control uppercase', 'placeholder' => 'CURP', 'tabindex' => '9', 'required']) }}
                                    <label class="form-label" for="curp">CURP</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('curp')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('cuip', old('cuip', $socios->cuip), ['id' => 'cuip', 'class' => 'form-control uppercase', 'placeholder' => 'CUIP', 'tabindex' => '10']) }}
                                    <label class="form-label" for="cuip">CUIP</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('cuip')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <div class="col mb-3">                                
                                <div class="form-outline">
                                    {{ Form::hidden('tipos_a1', old('tipo', $socios->tipo)) }}

                                    {!! Form::select('estado_civil', array_combine($estadoCivil, $estadoCivil), old('estado_civil', $socios->estado_civil), [
                                        'class' => 'select mb-2',
                                        'id' => 'estado_civil',
                                        'name' => 'estado_civil',
                                        'data-mdb-filter' => 'true',
                                        'required' => 'true',
                                        'tabindex' => '11'
                                    ]) !!}
                                    <label class="form-label select-label" for="estado_civil">ESTADO CIVIL</label>
                                </div>
                                @error('estado_civil')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror

                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('contacto_emergencia', old('contacto_emergencia', $socios->contacto_emergencia), ['id' => 'contacto_emergencia', 'class' => 'form-control uppercase', 'placeholder' => 'EMERGENCIAS, COMUNICARSE CON', 'tabindex' => '12', 'required']) }}
                                    <label class="form-label" for="contacto_emergencia">
                                        EMERGENCIAS, COMUNICARSE CON
                                    </label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('contacto_emergencia')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('telefono_emergencia', old('telefono_emergencia', $socios->telefono_emergencia), ['id' => 'telefono_emergencia', 'class' => 'form-control uppercase', 'placeholder' => 'TELÉFONO DE EMERGENCIA', 'tabindex' => '13', 'required']) }}
                                    <label class="form-label" for="telefono_emergencia">TELÉFONO DE EMERGENCIA</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('telefono_emergencia')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::hidden('tipoSangre', old('tipoSangre', $socios->tipo_sangre)) }}

                                    {!! Form::select(
                                        'tipo_sangre',
                                        [
                                            '-1' => '- TIPO DE SANGRE -',
                                            'A+' => 'A+',
                                            'A-' => 'A-',
                                            'B+' => 'B+',
                                            'B-' => 'B-',
                                            'AB' => 'AB+',
                                            'AB' => 'AB-',
                                            'O+' => 'O+',
                                            'O-' => 'O-',
                                        ],
                                        old('tipo_sangre', $socios->tipo_sangre),
                                        ['id' => 'tipo_sangre', 'class' => 'select', 'required' => 'required', 'tabindex' => '14'],
                                    ) !!}
                                    <label class="form-label select-label" for="tipo_sangre">TIPO DE SANGRE</label>
                                    <div class="form-helper" id="sangre_feedback" style="color: red; display: none;">
                                        Este
                                        campo es requerido.</div>
                                </div>
                                @error('tipo_sangre')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('lugar_origen', old('lugar_origen', $socios->lugar_origen), ['id' => 'lugar_origen', 'class' => 'form-control uppercase', 'placeholder' => 'LUGAR DE ORIGEN', 'tabindex' => '15', 'required']) }}
                                    <label class="form-label" for="lugar_origen">LUGAR DE ORIGEN</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('lugar_origen')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <div class="col mb-3">
                                <div class="form-outline datepicker-translated" data-mdb-toggle-button="false">
                                    {{ Form::text('alta_coorporacion', old('alta_coorporacion', $socios->alta_coorporacion ? Carbon\Carbon::createFromFormat('Y-m-d', $socios->alta_coorporacion)->format('d/m/Y') : ''), ['id' => 'alta_coorporacion', 'data-mdb-toggle' => 'datepicker', 'class' => 'form-control uppercase', 'placeholder' => 'ALTA A LA COORPORACIÓN', 'tabindex' => '16', 'required']) }}
                                    <label class="form-label" for="alta_coorporacion">ALTA A LA COORPORACIÓN</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('alta_coorporacion')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('compania', old('compania', $socios->compania), ['id' => 'compania', 'class' => 'form-control uppercase', 'placeholder' => 'COMPAÑIA', 'tabindex' => '17', 'required']) }}
                                    <label class="form-label" for="compania">COMPAÑIA</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('compania')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('batallon', old('batallon', $socios->batallon), ['id' => 'batallon', 'class' => 'form-control uppercase', 'placeholder' => 'BATALLÓN', 'tabindex' => '18', 'required']) }}
                                    <label class="form-label" for="batallon">BATALLÓN</label>
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid input.
                                </div>
                                @error('batallon')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::hidden('sectorId', old('sectorId', $socios->sector_id)) }}

                                    {!! Form::select(
                                        'sector_id',
                                        ['-1' => '- SECTOR -'] + $sectores->pluck('nombre', 'id')->all(),
                                        old('sector_id', $socios->sector_id),
                                        [
                                            'class' => 'select mb-2',
                                            'id' => 'sector_id',
                                            'data-mdb-filter' => 'true',
                                            'tabindex' => '19',
                                        ],
                                    ) !!}
                                    <label class="form-label select-label" for="sector_id">SECTOR</label>
                                    <div class="form-helper" id="sector_feedback" style="color: red; display: none;">
                                        Este
                                        campo es requerido.</div>
                                </div>
                                @error('sector_id')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::hidden('categoriaId', old('categoriaId', $socios->categoria_id)) }}

                                    {!! Form::select(
                                        'categoria_id',
                                        ['-1' => '- CATEGORÍA -'] + $categorias->pluck('nombre', 'id')->all(),
                                        old('categoria_id', $socios->categoria_id),
                                        [
                                            'class' => 'select mb-2',
                                            'id' => 'categoria_id',
                                            'data-mdb-filter' => 'true',
                                            'tabindex' => '20',
                                        ],
                                    ) !!}
                                    <label class="form-label select-label" for="categoria_id">CATEGORÍA</label>
                                    <div class="form-helper" id="categoria_feedback"
                                        style="color: red; display: none;">Este
                                        campo es requerido.</div>
                                </div>
                                @error('categoria_id')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- MUESTRA EL CAMBIO DE TIPO --}}
                    @if (\Request::route()->getName() === 'admin.socios.edit')
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-4">
                                <div class="col mb-3">
                                    <div class="form-outline">
                                        {{ Form::hidden('tipos', old('id', $socios->tipo)) }}

                                        {!! Form::select(
                                            'tipo',
                                            array_combine($tipoValues, $tipoValues),
                                            old('tipo', $socios->tipo),
                                            [
                                                'class' => 'select mb-2',
                                                'id' => 'tipo',
                                                'name' => 'tipo',
                                                'data-mdb-filter' => 'true',
                                                'tabindex' => '20',
                                            ],
                                        ) !!}
                                        <label class="form-label select-label" for="tipo">ESTATUS</label>
                                        <div class="form-helper" id="tipo_feedback"
                                            style="color: red; display: none;">Este
                                            campo es requerido.</div>
                                    </div>
                                    @error('tipo')
                                        <p class="error-message text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4">
                                <div class="col mb-3">
                                    <div class="form-outline datepicker-translated" data-mdb-toggle-button="false">
                                        {{ Form::text('fecha_baja', old('fecha_baja', $socios->fecha_baja ? Carbon\Carbon::createFromFormat('Y-m-d', $socios->fecha_baja)->format('d/m/Y') : ''), ['id' => 'fecha_baja', 'data-mdb-toggle' => 'datepicker', 'class' => 'form-control uppercase', 'placeholder' => 'FECHA BAJA']) }}
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
                                        {{ Form::textarea('observaciones', old('lugar_origen', $socios->observaciones), ['id' => 'observaciones','name' =>'observaciones', 'class' => 'form-control', 'rows' => '3']) }}
                                        <label class="form-label" for="fecha_baja">OBSERVACIONES</label>
                                    </div>
                                    @error('observaciones')
                                        <p class="error-message text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card border border-success shadow-0 mb-3">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-md-12">
                            <h4 class="card-title">BENEFICIARIOS</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body text-dark">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('beneficiario', null, ['id' => 'beneficiario', 'class' => 'form-control uppercase', 'placeholder' => 'NOMBRE', 'tabindex' => '21']) }}
                                    <label class="form-label" for="beneficiario">NOMBRE</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('beneficiario_domicilio', null, ['id' => 'beneficiario_domicilio', 'class' => 'form-control uppercase', 'placeholder' => 'DOMICILIO', 'tabindex' => '22']) }}
                                    <label class="form-label" for="beneficiario_domicilio">DOMICILIO</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::text('beneficiario_telefono', null, ['id' => 'beneficiario_telefono', 'class' => 'form-control uppercase', 'placeholder' => 'TELÉFONO', 'tabindex' => '23']) }}
                                    <label class="form-label" for="beneficiario_telefono">TELÉFONO</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <div class="col mb-3">
                                <div class="form-outline">
                                    {{ Form::number('beneficiario_porcentaje', null, ['id' => 'beneficiario_porcentaje', 'min' => '1', 'max' => '100', 'class' => 'form-control uppercase', 'placeholder' => 'PORCENTAJE', 'tabindex' => '24']) }}
                                    <label class="form-label" for="beneficiario_porcentaje">PORCENTAJE</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <div class="col custom-center-align">
                                {!! Form::button('Agregar beneficiario', [
                                    'type' => 'button',
                                    'class' => 'btn btn-success',
                                    'id' => 'btnBeneficiario',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card-body table-responsive p-0 border border-primary">
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
                                        @foreach (old('nombre_beneficiario', []) as $index => $nombre)
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="socios_id[]"
                                                        value="{{ old('socios_id.' . $index) }}" />
                                                    <input type="text" name="nombre_beneficiario[]"
                                                        class="form-control uppercase" value="{{ $nombre }}"
                                                        required />
                                                </td>
                                                <td>
                                                    <input type="text" name="domicilio_beneficiario[]"
                                                        class="form-control uppercase"
                                                        value="{{ old('domicilio_beneficiario.' . $index) }}"
                                                        required />
                                                </td>
                                                <td>
                                                    <input type="text" name="telefono_beneficiario[]"
                                                        class="form-control uppercase"
                                                        value="{{ old('telefono_beneficiario.' . $index) }}"
                                                        required />
                                                </td>
                                                <td>
                                                    <input type="number" name="porcentaje_beneficiario[]"
                                                        class="form-control uppercase"
                                                        value="{{ old('porcentaje_beneficiario.' . $index) }}"
                                                        min="1" max="100" required />
                                                </td>
                                                <td>
                                                    <button type="button" name="remove"
                                                        class="btn btn-danger btn-fab btn-fab-mini btn-round remove"><i
                                                            class="material-icons far fa-trash-alt"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach


                                        {{-- Datos de la relacion --}}
                                        @if (isset($beneficiarios))
                                            @foreach ($beneficiarios as $beneficiario)
                                                <tr>
                                                    <td>
                                                        <input type="hidden" name="socios_id[]"
                                                            value="{{ $beneficiario->id }}" />
                                                        <input type="text" name="nombre_beneficiario[]"
                                                            class="form-control uppercase"
                                                            value="{{ $beneficiario->nombre_beneficiario }}"
                                                            required />

                                                    </td>
                                                    <td>
                                                        <input type="text" name="domicilio_beneficiario[]"
                                                            class="form-control uppercase"
                                                            value="{{ $beneficiario->domicilio_beneficiario }}"
                                                            required />
                                                    </td>
                                                    <td>
                                                        <input type="text" name="telefono_beneficiario[]"
                                                            class="form-control uppercase"
                                                            value="{{ $beneficiario->telefono_beneficiario }}"
                                                            required />
                                                    </td>
                                                    <td>
                                                        <input type="number" name="porcentaje_beneficiario[]"
                                                            class="form-control uppercase"
                                                            value="{{ $beneficiario->porcentaje_beneficiario }}"
                                                            min="1" max="100" required />
                                                    </td>
                                                    <td>
                                                        <button type="button" name="remove"
                                                            class="btn btn-danger btn-fab btn-fab-mini btn-round remove"><i
                                                                class="material-icons far fa-trash-alt"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
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

            // Evitar entrada de datos en el campo alta_coorporacion
            var inputPagoQuincenal = $('#alta_coorporacion');
            inputPagoQuincenal.on('keydown', function(e) {
                e.preventDefault();
            });

            // Evitar entrada de datos en el campo fecha_baja
            var inputFechaBaja = $('#fecha_baja');
            inputFechaBaja.on('keydown', function(e) {
                e.preventDefault();
            });

            // NOMBRE COMPLETO
            $(document).on('change', '.nombre', function() {

                actualizarNombreCompleto();
            });

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

            // INSERTA EN LA TABLA DINAMICA DE BENEFICIARIO
            $('#btnBeneficiario').click(function(e) {
                console.log('click');
                //e.preventDefault();
                var currentIndex = Date.now(); // Puedes usar un índice único para cada fila
                var beneficiario = $('#beneficiario').val();
                var domicilio = $('#beneficiario_domicilio').val();
                var telefono = $('#beneficiario_telefono').val();
                //var porcentaje = $('#beneficiario_porcentaje').val();
                var porcentaje = parseFloat($('#beneficiario_porcentaje').val());

                // Validaciones básicas
                if (!beneficiario || !domicilio || !telefono || isNaN(porcentaje)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Mensaje de advertencia',
                        html: 'Todos los campos del beneficiario son obligatorios.',
                    });
                    return;
                }

                if (porcentaje <= 0 || porcentaje > 100) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Mensaje de advertencia',
                        html: 'El porcentaje debe ser un número entre 1 y 100.',
                    });
                    return;
                }

                // Suma total de porcentaje actual
                let sumaTotal = porcentaje;
                $('input[name="porcentaje_beneficiario[]"]').each(function () {
                    sumaTotal += parseFloat($(this).val());
                });

                if (sumaTotal > 100) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Mensaje de advertencia',
                        html: 'La suma de porcentajes no puede exceder 100%.',
                    });
                    return;
                }

                // Si pasa validaciones, inserta en la tabla
                var html = '';

                // Recupera los valores old o los valores ingresados anteriormente
                var oldBeneficiario = oldValues['nombre_beneficiario.' + currentIndex] || beneficiario;
                var oldDomicilio = oldValues['domicilio_beneficiario.' + currentIndex] || domicilio;
                var oldTelefono = oldValues['telefono_beneficiario.' + currentIndex] || telefono;
                var oldPorcentaje = oldValues['porcentaje_beneficiario.' + currentIndex] || porcentaje;


                html += '<tr>';

                html += '<td>';
                html +=
                    '<input type="hidden" name="socios_id[]" value="0"/>' +
                    '<input type="text" name="nombre_beneficiario[]" class="form-control uppercase" value="' +
                    oldBeneficiario + '" required/>';
                html += '</td>';

                html += '<td>';
                html +=
                    '<input type="text" name="domicilio_beneficiario[]" class="form-control uppercase" value="' +
                    oldDomicilio + '" required/>';
                html += '</td>';

                html += '<td>';
                html +=
                    '<input type="text" name="telefono_beneficiario[]" class="form-control uppercase" value="' +
                    oldTelefono + '" required/>';
                html += '</td>';

                html += '<td>';
                html +=
                    '<input type="number" name="porcentaje_beneficiario[]" class="form-control uppercase" value="' +
                    oldPorcentaje + '" min="1" max="100" required/>';
                html += '</td>';

                html += '<td>';
                html +=
                    '<button type="button" name="remove" class="btn btn-danger btn-fab btn-fab-mini btn-round remove"><i class="material-icons far fa-trash-alt"></i></button>';
                html += '</td>';

                html += '</tr>';

                $('#tblBeneficiario').append(html);

                // Limpiar campos
                $('#beneficiario').val('');
                $('#beneficiario_domicilio').val('');
                $('#beneficiario_telefono').val('');
                $('#beneficiario_porcentaje').val('');

                $('.remove').off().click(function(e) {
                    console.log('1');
                    $(this).parent('td').parent('tr').remove();
                });

            });

            $('.remove').off().click(function(e) {
                console.log('2');
                $(this).parent('td').parent('tr').remove();
            });

            // VALIDAR QUE LA SUMA DE PORCENTAJES SEA 100% ANTES DE ENVIAR EL FORMULARIO
            $('form').on('submit', function (e) {
                let total = 0;
                let count = 0;

                $('input[name="porcentaje_beneficiario[]"]').each(function () {
                    const valor = parseFloat($(this).val());
                    if (!isNaN(valor)) {
                        total += valor;
                        count++;
                    }
                });

                // Solo validar si hay beneficiarios agregados
                if (count > 0 && total !== 100) {
                    e.preventDefault(); // Detiene el envío
                    Swal.fire({
                        icon: 'warning',
                        title: 'Mensaje de advertencia',
                        html: 'La suma total de los porcentajes de beneficiarios debe ser exactamente 100%.<br>Actualmente tienes: ' + total + '%.',
                    });
                }
            });

        });
    </script>


@stop
