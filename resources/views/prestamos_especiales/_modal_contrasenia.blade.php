 <!-- Modal -->
 <div class="modal fade modal-lg modalContrasenia" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="staticBackdropLabel">Validación de aval</h5>
                 <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <div class="row mb-3">
                     <div class="col-lg-12 col-md-12 col-sm-12">
                         <div class="col">
                             <h6><strong>SOCIO</strong></h6>
                             <h6><span id="socio"></span></h6>
                             <input type="hidden" class="modalSocio">
                             <input type="hidden" class="modalIdSocio">
                         </div>
                     </div>
                 </div>
                 <div class="row mb-3">
                     <div class="col-lg-12 col-md-12 col-sm-12">
                         <div class="col">
                             <h6><strong>INGRESE SU CONTRASEÑA DE INICIO DE SESIÓN</strong></h6>
                             <input type="password" id="passwordInput" placeholder="Contraseña"
                                 class="form-control modalPassword" autocomplete="off">
                             <p class="error-message text-danger" id="errorPass" style="display: none">La contraseña es
                                 incorrecta</p>
                         </div>
                     </div>
                 </div>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">
                     Cancelar
                 </button>
                 <button type="button" class="btn btn-primary btn-aprobar-aval">Comprobar</button>
             </div>
         </div>
     </div>
 </div>
