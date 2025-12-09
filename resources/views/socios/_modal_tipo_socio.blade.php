 <!-- Modal -->
 <div class="modal modal-lg fade modalTipoSocio" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cambiar tipo de socio</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="col">
                           <h6><strong>SOCIO</strong></h6>
                           <h6><span id="fullNameSocio"></span></h6>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                       <div class="col">
                           <h6><strong>TIPO DE SOCIO</strong></h6>
                           <input type="hidden" id="idTypeSocio" name="idTypeSocio">
                           {!! Form::select(
                            'type_user',
                            [
                                'SOCIO' => 'SOCIO',
                                'EJECUTIVO' => 'EJECUTIVO',
                                'FINANZAS' => 'FINANZAS',
                                'ADMINISTRADOR' => 'ADMINISTRADOR',
                            ],
                            null,
                            ['id' => 'type_user', 'class' => 'select'],
                        ) !!}
                       </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-success save-tipo-socio">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
