<div>
  <?php include VIEWS_PATH . 'backview/componentes/breadcrumb.php' ?>
  <?php include VIEWS_PATH . 'backview/componentes/table.php' ?>
</div>

<!-- Modal Editar -->
<div id="modal-editarUsuario" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Usuario</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="id-usuario">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Nombre</label>
              <input type="text" class="form-control" id="nombre-usuario">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Apellidos</label>
              <input type="text" class="form-control" id="apellidos-usuario">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Usuario (login)</label>
              <input type="text" class="form-control" id="login-usuario" readonly>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Email</label>
              <input type="email" class="form-control" id="email-usuario">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Rol</label>
              <select class="form-control" id="rol-usuario">
                <option value="1">Administrador</option>
                <option value="2">Vendedor</option>
                <option value="3">Cajero</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="guardar-usuario">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Nuevo -->
<div id="modal-nuevoUsuario" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nuevo Usuario</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Nombre</label>
              <input type="text" class="form-control" id="nuevo-nombre-usuario">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Apellidos</label>
              <input type="text" class="form-control" id="nuevo-apellidos-usuario">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Usuario (login)</label>
              <input type="text" class="form-control" id="nuevo-login-usuario">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Email</label>
              <input type="email" class="form-control" id="nuevo-email-usuario">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Contraseña</label>
              <input type="password" class="form-control" id="nuevo-password-usuario" placeholder="Mín. 6 caracteres">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Rol</label>
              <select class="form-control" id="nuevo-rol-usuario">
                <option value="1">Administrador</option>
                <option value="2">Vendedor</option>
                <option value="3">Cajero</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="crear-usuario">Crear Usuario</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Cambiar Contraseña -->
<div id="modal-cambiarPassword" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cambiar Contraseña</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="id-usuario-password">
        <div class="form-group">
          <label>Nueva Contraseña</label>
          <input type="password" class="form-control" id="nueva-password" placeholder="Mínimo 6 caracteres">
        </div>
        <div class="form-group">
          <label>Confirmar Contraseña</label>
          <input type="password" class="form-control" id="confirmar-password">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-warning" id="btn-cambiar-password">Cambiar Contraseña</button>
      </div>
    </div>
  </div>
</div>