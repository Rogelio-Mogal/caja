 <!-- Modal -->
 <div class="modal modal-lg fade modalTomaFoto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLabel">Tomar foto</h5>
                 <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <div class="row mb-3">
                     <div class="col-lg-6 col-md-6 col-sm-6">
                         <div class="col">
                            <h6><strong>CAMARA EN VIVO</strong></h6>
                             <video id="webcam-feed" class="webcam-feed" autoplay width="380" height="auto"></video>
                             <canvas id="photo-canvas" class="photo-canvas" style="display: none;"></canvas>
                         </div>
                     </div>
                     <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="col">
                            <h6><strong>FOTO</strong></h6>
                            <img id="captured-photo" class="captured-photo" alt="Foto Capturada" width="380" height="auto">
                            <input type="hidden" id="socio_id" class="socio_id" value="">
                        </div>
                     </div>
                 </div>
                 <div class="row mb-3 foto" style="display: none;">
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <div class="download-container">
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9">
                        <input type="file" class="form-control" id="img_socio" name="img_socio">
                    </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">
                         Cancelar
                     </button>
                     <button type="button" class="btn btn-primary capture-button">Tomar foto</button>
                     <button type="button" class="btn btn-success save-foto">Guardar</button>
                 </div>
             </div>
         </div>
     </div>
 </div>
