 <!-- Modal -->
 <div class="modal modal-lg fade modalCancelar" tabindex="-1" aria-labelledby="modalCancelarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCancelarLabel">Cancelar ahorro</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="col">
                            <h6><strong>SOCIO</strong></h6>
                            <h6><span id="socio2"></span></h6>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="col">
                            <h6><strong>FECHA DE CAPTURA</strong></h6>
                            <h6><span id="fecha2"></span></h6>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="col">
                            <h6><strong>AHORRO</strong></h6>
                            <h6><span id="monto2"></span></h6>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="col">
                            <h6><strong>MOTIVO DE LA CANCELACIÓN</strong></h6>
                            {{ Form::hidden('prestamo_id2', null, ['id' => 'prestamo_id2', 'class' => 'prestamo_id2']) }}
                            <textarea class="form-control uppercase" id="motivo_cancelacion" rows="4" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary btn-cancelar-prestamo">Cancelar ahorro</button>
            </div>
        </div>
    </div>
</div>
