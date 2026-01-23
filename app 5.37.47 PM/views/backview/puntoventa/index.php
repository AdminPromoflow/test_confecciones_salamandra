<div class="page-wrapper">
  <!-- [ Main Content ] start -->
  <div class="row">
    <!-- [ sample-page ] start -->
    <input type="hidden" id="sucursal" data-idSucursal="<?= $idSucursal ?>">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <button type="button" class="btn btn-info btn-sm" id="nuevoCliente">
            <i class="feather icon-user-plus"></i>Registrar cliente nuevo
          </button>
        </div>

        <div class="card-body">
          <div class="row">
            <input type="hidden" id="tipoVenta" value="2">
            <div class="col-md-6 mb-3">
              <label for="clienteInput" class="form-label">Buscar cliente:</label>
              <select class="form-control select2-simple escanearCarrito" id="idCliente">
                <option value="">Seleccione un cliente</option>
                <?php foreach ($clientes as $cliente): ?>
                  <option value="<?php echo $cliente->id_cliente ?>"><?php echo $cliente->cedula . ' - ' . $cliente->nombre . ' ' . $cliente->apellidos ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="productoInput" class="form-label">Buscar producto:</label>
                <select class="form-control" id="idProducto"></select>
            </div>
          </div>

          <!-- Caja para mostrar la información del producto -->
<div class="row">
    <div class="col-md-12">
        <div class="card mt-3 shadow-sm border" id="infoProducto" style="display: none;">
            <div class="card-body">
                <!-- Título del producto -->
                <h5 class="card-title font-weight-bold text-primary" id="nombreProducto">
                    <i class="fas fa-box"></i> Información del producto
                </h5>
                <hr>

                <!-- Cantidad disponible -->
                <p class="card-text">
                    <strong><i class="fas fa-cubes"></i> Cantidad disponible:</strong>
                    <span id="cantidadProducto" data-cantidadProducto="cantidadProducto" class="font-weight-bold"></span>
                </p>

                <!-- Precio de venta -->
                <p class="card-text">
                    <strong><i class="fas fa-tag"></i> Precio de venta:</strong>
                    <span id="precioProducto" data-precioProducto="precioProducto" class="font-weight-bold text-info"></span>
                </p>

                <!-- Sucursales con stock -->
                <p class="card-text">
                    <strong><i class="fas fa-store"></i> Sucursales con stock:</strong>
                </p>
                <ul id="sucursalesStock" class="list-group list-group-flush">
                    <!-- Las sucursales se agregarán dinámicamente aquí -->
                </ul>
            </div>
        </div>
    </div>
</div>

          <div class="row" id="contenedorFechas" style="display:none;">
            <div class="col-md-6 mb-3">
              <label class="form-label">Fechas de producción</label>
              <select class="form-control select2-simple" id="fechasProduccion">
                <option value="">Seleccione una opción</option>
                <?php foreach ($programaciones as $programacion): ?>
                  <option value="<?php echo $programacion->id_programacion ?>">
                    <?php echo $programacion->produccion . ' - ' . $programacion->fecha ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <!-- <div class="col-md-4 mb-3">
                      <label class="form-label">Producto seleccionado</label>
                      <input type="text" class="form-control" id="nombreProducto" disabled>
                    </div> -->
            <div class="col-md-6 mb-3">
              <label class="form-label">Programar fecha entrega</label>
              <input type="text" id="fechaEntrega" class="form-control" placeholder="Seleccione una fecha" data-dtp="dtp_TWKUh">
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <button type="button" class="btn btn-primary btn-block" id="agregarAlCarrito">
                <i class="feather icon-plus-circle"></i>Agregar al carrito
              </button>
            </div>
          </div>
        </div>
        <hr>
        <div class="card-body">
          <div class="row mb-3">
            <div class="table-responsive">
              <table class="table table-vcenter table-mobile-md card-table">
                <thead>
                  <tr>
                    <th width="5%"> # </th>
                    <th width="30%"> Producto </th>
                    <th class="text-center"> Cantidad </th>
                    <th class="text-center"> Precio </th>
                    <th class="text-center"> Estado </th>
                    <th class="text-center"> Fecha Entrega </th>
                    <th class="w-1"></th>
                  </tr>
                </thead>
                <tbody id="table-cart">

                </tbody>
              </table>
            </div>
          </div>
        </div>
        <hr>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-4 mb-3">
              <label class="form-label">Subtotal</label>
              <input type="text" class="form-control text-end" id="subtotal" name="subtotal" placeholder="$ 0" disabled>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Descuento</label>
              <input type="number" class="form-control text-end" id="descuento" name="descuento" min="0" value="" placeholder="$ 0" autocomplete="off" readonly>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Total</label>
              <input type="text" class="form-control text-end" id="total" name="total" placeholder="$ 0" disabled>
            </div>
          </div>
          <div class="row align-items-center">
            <div class="col-md-4 mb-3">
              <label class="form-label">Medio de pago</label>
              <select class="form-control select2-simple" id="mediopago" name="mediopago">
                <option value="">Seleccione un medio de pago</option>
                <option value="1">Efectivo</option>
                <option value="2">Nequi</option>
                <option value="3">Daviplata</option>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Se recibe</label>
              <input type="number" class="form-control text-end" id="pagacon" name="pagacon" min="0" value="" placeholder="$ 0" autocomplete="off">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label" id="labelSaldoPendiente">Saldo pendiente</label>
              <input type="text" class="form-control text-end" id="cambio" name="cambio" placeholder="$ 0" disabled>
            </div>
          </div>
          <div class="row align-items-center">
            <div class="col">
              <label class="form-label">Nota adicional a la factura</label>
              <textarea class="form-control" id="notaVenta" name="notaVenta" rows="3" cols="80"></textarea>
            </div>
          </div>
        </div>

        <div class="card-footer btn-toolbar sw-toolbar sw-toolbar-bottom justify-content-end">
          <a href="javascript:void(0)" class="btn btn-secondary btn-sm" id="limpiarVenta">
            <i class="feather icon-x-circle"></i>Limpiar punto de venta
          </a>

          <?php if ($this->session->getUserData('userSession', 'usuarioRol') == 2): ?>
            <a href="javascript:void(0)" class="btn btn-primary btn-sm" id="guardarVenta" name="guardarVenta">
              <i class="feather icon-save"></i>Guardar venta
            </a>
          <?php endif ?>
        </div>
      </div>
    </div>
  </div>
  <!-- [ sample-page ] end -->
</div>
<!-- [ Main Content ] end -->
</div>

<div id="modal-opciones" class="modal fade" role="dialog" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Opciones del producto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="carritoId">
        <input type="hidden" id="clienteId">
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Estado Actual</label>
            <input type="text" class="form-control" id="estadoActual" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Nuevo estado</label>
            <select class="form-control" id="nuevoEstado">
            </select>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Cantidad actual</label>
            <input type="number" class="form-control" id="cantidadActual" disabled>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Fecha programada para entregar</label>
            <input type="text" class="form-control" id="fechaActual" disabled>
          </div>

          <div class="col-md-6">
            <label class="form-label">Nueva fecha de entrega</label>
            <input type="text" class="form-control" placeholder="Seleccione nueva fecha" id="nuevaFechaEntrega" data-dtp="dtp_TWKUh">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col">
            <label class="form-label">Nota adicional al producto</label>
            <textarea class="form-control" id="anotacionActual" rows="3"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="feather icon-x-circle"></i>Cerrar
        </button>
        <button type="button" class="btn btn-primary" id="guardarOpciones">
          <i class="feather icon-save"></i>Guardar cambios
        </button>
      </div>
    </div>
  </div>
</div>

<div id="modal-inventarioSucursales" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Inventario en otras sucursales</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="modal-body mb-3">
          <div class="card-body table-border-style">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th> Código producto </th>
                    <th> Cantidad disponible </th>
                    <th> Sucursal </th>
                  </tr>
                </thead>
                <tbody id="tabla-container">

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="feather icon-x-circle"></i>Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

<div id="modal-nuevoCliente" class="modal fade" role="dialog" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crear nuevo cliente salamandra</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Cédula</label>
            <input type="text" class="form-control" id="cedula" autocomplete="false">
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" autocomplete="false">
          </div>
          <div class="col-md-6">
            <label class="form-label">Apellidos</label>
            <input type="text" class="form-control" id="apellidos" autocomplete="false">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-8">
            <label class="form-label">Dirección</label>
            <input type="text" class="form-control" id="direccion" autocomplete="false">
          </div>
          <div class="col-md-4">
            <label class="form-label">Barrio</label>
            <input type="text" class="form-control" id="barrio" autocomplete="false">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" autocomplete="false">
          </div>
          <div class="col-md-8">
            <label class="form-label">Correo electrónico</label>
            <input type="text" class="form-control" id="email" autocomplete="false">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="feather icon-x-circle"></i>Cerrar
          </button>
          <button type="button" class="btn btn-primary" id="guardarCliente">
            <i class="feather icon-save"></i>Guardar nuevo cliente
          </button>
        </div>
      </div>
    </div>
  </div>