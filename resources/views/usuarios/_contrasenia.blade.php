@csrf
<div class="container py-1 h-500">
    <div class="row d-flex justify-content-center align-items-center h-500">
        <div class="col col-lg-12 mb-6 mb-lg-0">
            <div class="card mb-3" style="border-radius: .5rem;">
                <div class="row g-0">
                    <div class="col-md-4 gradient-custom text-center text-white"
                        style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
                        <h5 class="img-fluid my-5 negritas-negro">Actualizar contraseña</h5>
                        <p class="px-4 negritas-negro" style="text-align: justify; text-align-last: left;">Asegúrese que
                            su cuenta esté usando una contraseña larga y aleatoria para mantenerse seguro.</p>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body p-4">
                            <h6>Contraseña</h6>
                            <input type="hidden" name="formulario" value="user.contrasena">
                            <hr class="mt-0 mb-4">
                            <div class="row pt-1">
                                <div class="col-12 mb-3">
                                    <h6>Contraseña actual</h6>
                                    <input type="password" name="current_password" id="current_password"
                                        class="form-control mb-0" placeholder="" required>
                                    @error('current_password')
                                        <p class="error-message text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <h6>Nueva Contraseña</h6>
                                    <input type="password" name="new_password" id="new_password" maxlength="60"
                                        class="form-control mb-0" placeholder="" required />
                                    @error('new_password')
                                        <p class="error-message text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <h6>Confirmar contraseña</h6>
                                    <input type="password" name="confirm_password" id="confirm_password" maxlength="60"
                                        class="form-control mb-0" placeholder="" required />
                                    @error('confirm_password')
                                        <p class="error-message text-danger">{{ $message }}</p>
                                    @enderror
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
</div>
