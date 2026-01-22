<div>
  <?php include VIEWS_PATH . 'backview/componentes/breadcrumb.php' ?>

  <?php include VIEWS_PATH . 'backview/componentes/table.php' ?>
</div>

<div id="modal-editarSucursal" class="modal fade" role="dialog" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLiveLabel">Editando sucursal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="px-2">
          <input type="hidden" id="id-sucursal">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="nombre">Nombre de la Sucursal</label>
                <input type="text" class="form-control" id="nombre-sucursal" autocomplete="off">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="permiso">Permisos</label>
                <select class="form-control select2-simple" id="permiso-sucursal">
                  <option value="">Seleccione una opción</option>
                  <option value="1">Administradores</option>
                  <option value="2">Cajeros / Vendedores</option>
                  <option value="3">Operarios</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="registro">Fecha de Registro</label>
                <input type="text" class="form-control" id="registro-sucursal" disabled>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="guardar-cambios">Guardar Cambios</button>
      </div>
    </div>
  </div>
</div>

<div id="modal-nuevaSucursal" class="modal fade" role="dialog" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLiveLabel">Creando nueva sucursal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="px-2">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="nombre">Nombre de la Sucursal</label>
                <input type="text" class="form-control" id="nuevo-nombre-sucursal" autocomplete="off">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="permiso">Permisos</label>
                <select class="form-control select2-simple" id="nuevo-permiso-sucursal">
                  <option value="">Seleccione una opción</option>
                  <option value="1">Administradores</option>
                  <option value="2">Cajeros / Vendedores</option>
                  <option value="3">Operarios</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="crear-sucursal">Guardar</button>
      </div>
    </div>
  </div>
</div>