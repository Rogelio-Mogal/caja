 <!-- Modal -->
 <div class="modal modal-lg fade modalPrestamos" tabindex="-1" aria-labelledby="exampleModalPrestamos" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalPrestamos">Préstamos activos</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <h6><strong>SOCIO</strong></h6>   
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9">
                        <h6><span id="socio"> </span></h6>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h6 class="uppercase"><strong>SALDO AL {{$day}} DE {{$mesEnEspanol}} DE {{$year}} </strong></h6>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h6><strong>AHORROS: </strong> <span id="ahorro"> </span></h6>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-12 col-md-12 col-sm-12" id="listPrestamos">
                        
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <h6><strong>DEUDA TOTAL: </strong> <span id="deuda"> </span></h6>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <h6><strong>DISPONIBLE: </strong> <span id="disponible"> </span></h6>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <h6><strong>ES AVAL: </strong> <span id="aval"> </span></h6>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="btnImprimir" target="_blank" class="btn btn-success">
                        Imprimir
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
