<div>
  <?php include VIEWS_PATH . 'backview/componentes/breadcrumb.php' ?>

  <?php include VIEWS_PATH . 'backview/componentes/table.php' ?>
</div>

<!-- Modal para editar producto -->
<div id="modal-editarProducto" class="modal fade" role="dialog" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLiveLabel">Editando producto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="px-2">
          <input type="hidden" id="id-producto">
          <div class=" row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="barras">Código de Barras</label>
                <input type="text" class="form-control" id="barras-producto">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="codigo">Código</label>
                <input type="text" class="form-control" id="codigo-producto" disabled>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="nombre">Producto</label>
                <input type="text" class="form-control" id="nombre-producto">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="descripcion">Descripción</label>
                <input type="text" class="form-control" id="descripcion-producto">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="talla">Talla</label>
                <input type="text" class="form-control" id="talla-producto">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="categoria">Categoría</label>
                <select class="form-control select2-simple" id="categoria-producto"></select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="subcategoria">Sub Categoría</label>
                <select class="form-control select2-simple" id="subcategoria-producto"></select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="institucion">Institución</label>
                <select class="form-control select2-simple" id="institucion-producto"></select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="proveedor">Proveedor</label>
                <select class="form-control select2-simple" id="proveedor-producto"></select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="costo">Costo</label>
                <input type="text" class="form-control" id="costo-producto">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="precio">Precio de Venta</label>
                <input type="text" class="form-control" id="precio-producto">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="estado">Estado</label>
                <select class="form-control select2-simple" id="estado-producto">
                  <option value="0">Inactivo</option>
                  <option value="1">Activo</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="registro">Fecha de Registro</label>
                <input type="text" class="form-control" id="registro-producto" disabled>
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

<!-- Modal para registrar nuevo producto -->
<div id="modal-nuevoProducto" class="modal fade" role="dialog" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLiveLabel">Creando nuevo producto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="px-2">
          <div class=" row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="barras">Código de Barras</label>
                <input type="number" class="form-control" id="nuevo-barras-producto">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="codigo">Código</label>
                <input type="text" class="form-control" id="nuevo-codigo-producto">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="nombre">Producto</label>
                <input type="text" class="form-control" id="nuevo-nombre-producto">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="descripcion">Descripción</label>
                <input type="text" class="form-control" id="nuevo-descripcion-producto">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="talla">Talla</label>
                <input type="text" class="form-control" id="nuevo-talla-producto">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="categoria">Categoría</label>
                <select class="form-control select2-simple" id="nuevo-categoria-producto">
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="subcategoria">Sub Categoría</label>
                <select class="form-control select2-simple" id="nuevo-subcategoria-producto"></select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="institucion">Institución</label>
                <select class="form-control select2-simple" id="nuevo-institucion-producto"></select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="proveedor">Proveedor</label>
                <select class="form-control select2-simple" id="nuevo-proveedor-producto"></select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="costo">Costo</label>
                <input type="number" class="form-control" id="nuevo-costo-producto">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="precio">Precio de Venta</label>
                <input type="number" class="form-control" id="nuevo-precio-producto">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="estado">Estado</label>
                <select class="form-control select2-simple" id="nuevo-estado-producto">
                  <option value="0">Inactivo</option>
                  <option value="1">Activo</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="crear-producto">Guardar Cambios</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para ver el stock del producto en los diferentes inventarios -->
<div id="modal-inventarioProducto" class="modal fade" role="dialog" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLiveLabel">Distribución del inventario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="px-2">
          <div class=" row">
            <table id="infoProducto" class="table table-striped">
              <thead>
                <tr>
                  <th>Código</th>
                  <th class="text-center">Producto</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="modal-body">
        <div class="px-2">
          <div class=" row">
            <table id="infoSucursal" class="table table-striped">
              <thead>
                <tr>
                  <th>Sucursal</th>
                  <th class="text-center">Cantidad disponible</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>