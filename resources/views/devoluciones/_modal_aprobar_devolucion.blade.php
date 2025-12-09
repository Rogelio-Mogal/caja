 <!-- Modal -->
 <div class="modal modal-lg fade modalApruebaPrestamo" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLabel">Finalizar devolución</h5>
                 <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <div class="row mb-3">
                     <div class="col-lg-7 col-md-7 col-sm-12">
                         <div class="col">
                             <h6><strong>SOCIO</strong></h6>
                             <h6><span id="socio"></span></h6>
                         </div>
                     </div>
                     <div class="col-lg-3 col-md-3 col-sm-12">
                         <div class="col">
                             <h6 class="text-align: text-center"><strong>FECHA DE CAPTURA</strong></h6>
                             <h6 class="text-align: text-center"><span id="fecha"></span></h6>
                         </div>
                     </div>
                     <div class="col-lg-2 col-md-2 col-sm-12">
                        <div class="col">
                            <h6><strong>MONTO</strong></h6>
                            <h6>$<span id="monto"></span></h6>
                        </div>
                    </div>
                 </div>

                 <div class="row mb-3">
                     <div class="col-lg-12 col-md-12 col-sm-12">
                         <h6 class="text-align: text-center"><strong>NOTA ADICIONAL</strong></h6>
                         {{ Form::textarea('nota', null, ['id' => 'nota','name' =>'nota', 'class' => 'form-control uppercase', 'rows' => 2]) }}
                         {{ Form::hidden('prestamo_id', null, ['id' => 'prestamo_id', 'class' => 'prestamo_id']) }}
                     </div>
                 </div>
                 <div class="row mb-3">
                     <div class="col-lg-8 col-md-8 col-sm-12">
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
                                 ['id' => 'forma_pago', 'class' => 'select', 'required' => 'true', 'tabindex' => '1'],
                             ) !!}
                         </div>
                     </div>
                     <div class="col-lg-4 col-md-4 col-sm-12">
                         <div class="col mb-3">
                             <h6><strong>FECHA DE PAGO</strong></h6>
                             <div class="form-outline datepicker-translated" data-mdb-toggle-button="false"
                                 data-mdb-format="dd/mm/yyyy">
                                 {{ Form::text('fecha_primer_pago', null, ['id' => 'fecha_primer_pago', 'data-mdb-toggle' => 'datepicker', 'class' => 'form-control', 'placeholder' => 'FECHA DE PAGO', 'tabindex' => '2']) }}
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">
                     Cancelar
                 </button>
                 <button type="button" class="btn btn-primary btn-aprobar-prestamo">Aprobar devolución</button>
             </div>
         </div>
     </div>
 </div>
