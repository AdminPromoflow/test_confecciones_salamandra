<div>
  <?php include VIEWS_PATH . 'backview/componentes/breadcrumb.php' ?>

  <?php include VIEWS_PATH . 'backview/componentes/table.php' ?>
</div>

<div id="modal-editarCliente" class="modal fade" role="dialog" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLiveLabel">Editando cliente</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="px-2">
          <input type="hidden" id="id-cliente">
          <div class=" row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="cedula">Cédula</label>
                <input type="text" class="form-control" id="cedula-cliente">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre-cliente">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="apellidos">Apellidos</label>
                <input type="text" class="form-control" id="apellidos-cliente">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" class="form-control" id="direccion-cliente">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="barrio">Barrio</label>
                <input type="text" class="form-control" id="barrio-cliente">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" class="form-control" id="telefono-cliente">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="text" class="form-control" id="email-cliente">
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


<div id="modal-nuevoCliente" class="modal fade" role="dialog" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLiveLabel">Creando nuevo cliente</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="px-2">
          <div class=" row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="cedula">Cédula</label>
                <input type="number" class="form-control" id="nuevo-cedula-cliente">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nuevo-nombre-cliente">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="apellidos">Apellidos</label>
                <input type="text" class="form-control" id="nuevo-apellidos-cliente">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="categoria">Direccion</label>
                <input type="text" class="form-control" id="nuevo-categoria-cliente">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="barrio">Barrio</label>
                <input type="text" class="form-control" id="nuevo-barrio-cliente">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" class="form-control" id="nuevo-telefono-cliente">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="text" class="form-control" id="nuevo-email-cliente">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="crear-cliente">Guardar Cambios</button>
      </div>
    </div>
  </div>
</div>