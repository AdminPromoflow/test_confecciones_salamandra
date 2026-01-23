<div>
  <?php include VIEWS_PATH . 'backview/componentes/breadcrumb.php' ?>

  <?php include VIEWS_PATH . 'backview/componentes/table.php' ?>
</div>

<!-- Modal para registrar nueva requisicion -->
<div id="modal-nuevaRequisicion" class="modal fade" role="dialog" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLiveLabel">Creando nueva requisición</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="px-2">
          <div class=" row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="fecha">Fecha entrega</label>
                <input type="date" class="form-control" id="fecha">
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="producto">Producto</label>
                <select class="form-control select2-simple" id="id_producto">
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="cantidad">Cantidad requerida</label>
                <input type="number" class="form-control" id="cantidad">
              </div>
            </div>
          </div>
          
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="guardar">Guardar</button>
      </div>
    </div>
  </div>
</div>