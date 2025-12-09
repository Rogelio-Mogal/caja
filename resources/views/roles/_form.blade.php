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
    </style>
@stop

<br />
@csrf
<div class="card card-outline card-primary">
    <div class="card-header">
        @if (\Request::route()->getName() === 'admin.roles.create')
            <h3 class="card-title">NUEVO ROL Y PERMISOS</h3>
        @elseif (\Request::route()->getName() === 'admin.roles.edit')
            <h3 class="card-title">EDITAR ROL Y PERMISOS</h3>
        @endif
    </div>
    <div class="card-body">
        <div class="register-box-body">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <div class="col mb-3">
                        <div class="form-outline">
                            {{ Form::text('name', old('name', $role->name), ['id' => 'name', 'class' => 'form-control uppercase', 'placeholder' => 'NOMBRE DEL ROL', 'tabindex' => '1', 'required']) }}
                            <label class="form-label" for="name">NOMBRE DEL ROL</label>
                        </div>

                        @error('name')
                            <p class="error-message text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                @foreach ($permission as $value)
                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <div class="col mb-3">
                            <div class="form-check">
                                {{-- Form::checkbox('permission[]', $value->id, old('permission', $rolePermissions->permission_id), array('class'=>'form-check-input mr-1 name')) --}}
                                {{ Form::checkbox('permission[]', $value->id, in_array($value->id, $rolePermissions), array('class'=>'form-check-input mr-1 name')) }}
                                <label class="form-check-label" for="permission">{{ $value->name }}</label>
                            </div>
                        </div>
                    </div>
                @endforeach
                @error('permission')
                    <p class="error-message text-danger">{{ $message }}</p>
                @enderror

                {{--
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="col mb-3">
                        @foreach ($permission as $value)
                            <div class="form-check">
                                <input class="form-check-input mr-1" type="checkbox" value="{{$value->id}}" id="permission[]" />
                                <label class="form-check-label" for="permission">{{ $value->name }}</label>
                            </div>
                        @endforeach
                        @error('monto_retiro')
                            <p class="error-message text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                --}}
            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12" align="center">
                    <div class="col-2">
                        <br />
                        {!! Form::button('Guardar', ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'submitBtn']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@section('js')
    <script>
        $(document).ready(function() {
            // Evitar entrada de datos en el campo saldo
            var inputPagoQuincenal = $('#saldo');
            inputPagoQuincenal.on('keydown', function(e) {
                e.preventDefault();
            });
        });
    </script>


@stop
