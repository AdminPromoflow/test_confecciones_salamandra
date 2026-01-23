<div>
    <?php include VIEWS_PATH . 'backview/componentes/breadcrumb.php' ?>
</div>

<!-- Buscador -->
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <?php if (adminAccess()) : ?>
                    <div id="btn-header">

                    </div>
                <?php endif ?>
                <div class="card-header-right">
                    <div class="btn-group card-option">
                        <button type="button" class="btn dropdown-toggle btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="feather icon-more-horizontal"></i>
                        </button>
                        <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                            <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize"></i> Maximizar</span><span style="display:none"><i class="feather icon-minimize"></i> Restaurar</span></a>
                            </li>
                            <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> Colapsar</span><span style="display:none"><i class="feather icon-plus"></i> Expandir</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="px-2">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="numero-venta" class="mx-sm-3 mb-2">Número de venta</label>
                            <div class="form-inline">
                                <div class="form-group mx-sm-3 mb-2">
                                    <input type="number" class="form-control" id="numero-venta" autocomplete="off">
                                </div>
                                <button type="button" class="btn btn-primary mb-2" id="buscar-venta"><i class="feather icon-search"></i>Buscar</button>
                            </div>
                        </div>

                        <?php if (adminAccess() || sellerAccess()) : ?>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <div class="col-8">
                                            <label for="cliente-venta" class="mb-2">Cliente</label>
                                            <select class="form-control select2-simple" id="cliente-venta">
                                                <option>Seleccione cliente</option>
                                                <?php foreach ($clientes as $cliente) : ?>
                                                    <option value="<?= $cliente->id_cliente ?>"><?= $cliente->cedula . ' - ' .  $cliente->nombre . ' ' . $cliente->apellidos ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label for="cliente-venta" class="mb-2" style="color: white;">buscar</label>
                                            <button type="button" class="btn btn-primary mb-2" id="buscar-cliente"><i class="feather icon-search"></i>Buscar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (adminAccess() || sellerAccess()) : ?>
    <div id="contenedor-ventas" class="row" style="display: none;">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Resultado de la busqueda por cliente</h4>
                </div>
                <div class="card-body shadow-1">
                    <div class="px-2">
                        <div class="row">
                            <div class="col-md-12">
                                <table id="" class="table table-striped table-sm table-hover text-sm" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Número de venta</th>
                                            <th>Registro</th>
                                            <th>Tipo de Venta</th>
                                            <th>Sucursal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="content-body">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif ?>

<div id="contenedor-venta" class="row" style="display: none;">
    <div class="col-sm-12">
        <div class="card">
            <?php if (adminAccess() || sellerAccess()) : ?>
                <div class="card-header">
                    <div id="enlaceContainer"></div>
                </div>
                <div class="card-body">
                    <div class="px-2">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h5>Datos de la venta</h5>
                                <table class="table text-sm mb-0 table-striped table-sm">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Fecha:</th>
                                            <td id="fecha-venta" class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"># de venta:</th>
                                            <td id="factura-venta" class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Sucursal:</th>
                                            <td id="sucursal-venta" class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Tipo de venta:</th>
                                            <td id="tipo-venta" class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row" id="val_estado">Estado:</th>
                                            <td id="estado-venta" class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Total:</th>
                                            <td id="total-venta" class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Ajuste:</th>
                                            <td id="descuento-venta" class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row" id="val_abono">Abonos:</th>
                                            <td id="abono-venta" class="text-right">
                                                <span class="badge badge-primary px-3">+ Abono</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" id="val_saldo">Saldo:</th>
                                            <td id="saldo-venta" class="text-right text-danger"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row" id="val_entregado">Total entregado:</th>
                                            <td id="entregado-venta" class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Cajero:</th>
                                            <td id="cajero-venta" class="text-right"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h5>Datos del cliente</h5>
                                    <table class="table text-sm mb-0 table-striped table-sm">
                                        <tbody>
                                            <tr>
                                                <th scope="row">Cliente:</th>
                                                <td id="nombre-cliente" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Dirección</th>
                                                <td id="direccion-cliente" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Teléfono:</th>
                                                <td id="telefono-cliente" class="text-right"></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">E-mail:</th>
                                                <td id="email-cliente" class="text-right"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mb-3">
                                    <h5>Nota adicional</h5>
                                    <table class="table text-sm mb-0 table-striped table-sm">
                                        <tbody>
                                            <tr>
                                                <td id="nota-venta"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <div class="card-body shadow-1">
                <div class="px-2">
                    <div class="row">
                        <h5 class="titulo-venta ml-3"></h5>
                        <div class="col-md-12 table-responsive">
                            <table id="" class="table table-striped table-sm table-hover text-sm" width="100%">
                                <thead id="table-head">

                                </thead>
                                <tbody id="table-body">

                                </tbody>
                            </table>
                        </div>
                        <h5 class="titulo-venta ml-3"></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (adminAccess() || sellerAccess()) : ?>
    <div id="modal-actualizarEstado" class="modal fade" role="dialog" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLiveLabel">Actualizar estado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="px-2">
                        <input type="hidden" id="id-producto">
                        <input type="hidden" id="rolUsuario">
                        <div class=" row">
                            <div class="col-md-12">
                                <input type="hidden" id="id-venta">
                                <input type="hidden" id="id-detalle">
                                <input type="hidden" id="precio">
                                <input type="hidden" id="anteriorEstado">
                                <div class="form-group">
                                    <label for="estado-detalle">Estado</label>
                                    <select class="form-control select2-simple" id="estado-detalle">
                                        <option value="">Seleccione una opción</option>
                                        <option value="0">Pendiente</option>
                                        <option value="1">Urgente</option>
                                        <option value="2">Listo</option>
                                        <option value="3">Bolsa</option>
                                        <option value="4">Entregado</option>
                                        <option value="5">Cancelado</option>
                                        <option value="6">Retorno</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <p id="mensaje" class="text-danger" style="display:none;">Este estado no se puede elegir.</p>
                            </div>
                        </div>
                        
                        <div class="form-group" id="contenedor-cajero" style="display: none;">
                            <label for="id-cajero">Vendedor Solicitante</label>
                            <select class="form-control select2-simple" id="id-cajero">
                                <option value="">Seleccione un vendedor</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="aceptar" style="display:none;">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>
<div id="modal-registrarAbono" class="modal fade" role="dialog" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLiveLabel">Realizar abono</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="px-2">
                    <input type="hidden" id="id-venta">
                    <input type="hidden" id="id-cliente">
                    <input type="hidden" id="abonado">
                    <div class=" row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="estado-detalle">Saldo</label>
                                <input type="text" class="form-control" id="saldo" readonly>
                            </div>
                        </div>
                    </div>
                    <div class=" row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado-detalle">Valor del abono</label>
                                <input type="number" class="form-control" id="valor-abono">
                                <small class="form-text text-muted">El valor de abono debe ser menor o igual al saldo.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado-detalle">Medio de pago</label>
                                <select id="mediopago-abono" class="form-control select2-simple">
                                    <option value="1">Efectivo</option>
                                    <option value="2">Nequi</option>
                                    <option value="3">DaviPlata</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="estado-detalle">Nota adicional</label>
                                <textarea class="form-control" id="nota-abono" cols="30" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardar-abono">Aceptar</button>
            </div>
        </div>
    </div>
</div>