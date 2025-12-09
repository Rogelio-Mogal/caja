 <!-- Modal -->
 <div class="modal modal-lg fade myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLabel">Finalizar retiro</h5>
                 <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <div class="row mb-3">
                     <div class="col-lg-12 col-md-12 col-sm-12">
                         <div class="col">
                             <h6><strong>SOCIO</strong></h6>
                             <h6><span id="socio"></span></h6>
                         </div>
                     </div>
                 </div>
                 <div class="row mb-3">
                     <div class="col-lg-6 col-md-6 col-sm-12">
                         <div class="col">
                             <h6><strong>FECHA DE CAPTURA</strong></h6>
                             <h6><span id="fecha"></span></h6>
                         </div>
                     </div>
                     <div class="col-lg-6 col-md-6 col-sm-12">
                         <div class="col">
                             <h6><strong>MONTO SOLICITADO</strong></h6>
                             <h6><span id="monto"></span></h6>
                         </div>
                     </div>
                 </div>
                 <div class="row mb-3">
                     <div class="col-lg-6 col-md-6 col-sm-12">
                         <div class="col">
                             <h6><strong>MONTO APROBADO</strong></h6>
                             {{ Form::text('pp_display', null, ['id' => 'pp_display', 'oninput' => 'formatNumber(this)', 'onblur' => 'fixDecimals(this)','tabindex' => '1', 'class' => 'form-control uppercase', 'placeholder' => 'MONTO APROBADO', 'required']) }}
                             {{ Form::hidden('saldo_aprobado', null, ['id' => 'saldo_aprobado', 'class' => 'form-control', 'placeholder' => 'MONTO APROBADO']) }}
                             {{ Form::hidden('retiro_id', null, ['id' => 'retiro_id', 'class' => 'retiro_id']) }}
                             {{ Form::hidden('monto_retiro', null, ['id' => 'monto_retiro', 'class' => 'monto_retiro']) }}
                         </div>
                     </div>
                     <div class="col-lg-6 col-md-6 col-sm-12">
                         <div class="col">
                             <h6><strong>METODO DE PAGO</strong></h6>
                             {{ Form::hidden('tipoSangre') }}

                             {!! Form::select(
                                 'forma_pago',
                                 [
                                     '-1' => '- FORMA DE PAGO -',
                                     'EFECTIVO' => 'EFECTIVO',
                                     'TRANSFERENCIA ELECTRÓNICA' => 'TRANSFERENCIA ELECTRÓNICA',
                                     'CHEQUE' => 'CHEQUE',
                                 ],
                                 null,
                                 ['id' => 'forma_pago', 'class' => 'select', 'required' => 'true', 'tabindex' => '2'],
                             ) !!}
                         </div>
                     </div>
                 </div>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">
                     Cancelar
                 </button>
                 <button type="button" class="btn btn-primary btn-aprobar-retiro">Aprobar retiro</button>
             </div>
         </div>
     </div>
 </div>
