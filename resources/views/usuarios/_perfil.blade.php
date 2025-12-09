@csrf
<div class="container py-2 h-500">
    <div class="row d-flex justify-content-center align-items-center h-500">
        <div class="col col-lg-12 mb-6 mb-lg-0">
            <div class="card mb-3" style="border-radius: .5rem;">
                <div class="row g-0">
                    <div class="col-md-4 gradient-custom text-center text-white"
                        style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
                        <!--<img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava1-bg.webp"
                        alt="Avatar" class="img-fluid my-5" style="width: 80px;" /> -->
                        <h5 class="img-fluid my-5 negritas-negro">Información de perfil</h5>
                        <p class="px-4 negritas-negro" style="text-align: justify; text-align-last: left;">Actualice la
                            información de su cuenta y la dirección de correo electrónico.</p>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body p-4">
                            <h6>Información</h6>
                            <input type="hidden" name="formulario" value="user.informacion">
                            <input type="hidden" name="formularioPerfil" value="{{$perfil}}">
                            <hr class="mt-0 mb-4">
                            <div class="row pt-1">
                                <div class="col-12 mb-3">
                                    <h6>Apellido Paterno</h6>
                                    <input type="text" name="apellido_paterno" id="apellido_paterno"
                                        value="{{ old('apellido_paterno', $socio->apellido_paterno) }}" class="form-control uppercase mb-0" placeholder=""
                                        >
                                </div>
                                @error('apellido_paterno')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="row pt-1">
                                <div class="col-12 mb-3">
                                    <h6>Apellido Materno</h6>
                                    <input type="text" name="apellido_materno" id="apellido_materno"
                                        value="{{ old('apellido_materno', $socio->apellido_materno) }}" class="form-control uppercase mb-0" placeholder=""
                                        >
                                </div>
                                @error('apellido_materno')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="row pt-1">
                                <div class="col-12 mb-3">
                                    <h6>Nombre(s)</h6>
                                    <input type="text" name="nombre" id="nombre"
                                        value="{{ old('nombre', $socio->nombre) }}" class="form-control uppercase mb-0" placeholder=""
                                        required>
                                </div>
                                @error('nombre')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="row pt-1">
                                <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                    <h6>Correo electrónico</h6>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                        maxlength="60" class="form-control mb-0" placeholder="" required />
                                </div>
                                @error('email')
                                    <p class="error-message text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="row pt-1">
                                <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                    <h6>Rol</h6>
                                    @if ($user && $user->roles->count() > 0)
                                        {!! Form::select('roles', ['' => 'SELECCIONA UN ROL'] + $roles, old('roles', $user->roles->pluck('name')->toArray()), ['class' => 'select', 'required']) !!}
                                        <label class="form-label select-label">ROLES</label>
                                    @else
                                        {!! Form::select('roles', ['' => 'SELECCIONA UN ROL'] + $roles, old('roles'), array('class' => 'select', 'required')) !!}
                                        <label class="form-label select-label">ROLES</label>
                                    @endif
                                    @error('roles')
                                        <p class="error-message text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary btn-rounded btn-lg">
                                    Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
