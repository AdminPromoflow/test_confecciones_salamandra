<div class="page-wrapper">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10"><?php echo ucfirst($description) ?></h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo url_path('backend/dashboard') ?>"><i class="feather icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="<?php echo url_path('backend/' . $controller) ?>"><?php echo ucfirst($controller) ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- [ breadcrumb ] end -->
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-md-7 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Hello card</h5>
                    <div class="card-header-right">
                        <div class="btn-group card-option">
                            <button type="button" class="btn dropdown-toggle btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="feather icon-more-horizontal"></i>
                            </button>
                            <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                                <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize"></i> maximize</span><span style="display:none"><i class="feather icon-minimize"></i> Restore</span></a>
                                </li>
                                <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> collapse</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>
                                <li class="dropdown-item reload-card"><a href="#!"><i class="feather icon-refresh-cw"></i> reload</a></li>
                                <li class="dropdown-item close-card"><a href="#!"><i class="feather icon-trash"></i> remove</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                  <form action="<?php echo url_path('backend/transferencias/registrarTransferencia') ?>" method="post" class="settings-form" id="formularioTransferir">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="origen" class="form-label">Sucursal de Origen</label>
                        <select class="form-control select2-simple" id="origen" name="origen" required >
                          <option value=""></option>
                          <?php foreach ($sucursales as $sucursal): ?>
                            <option value="<?php echo $sucursal->id_sucursal ?>"><?php echo $sucursal->nombre_sucursal ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="destino" class="form-label">Sucursal de Destino</label>
                        <select class="form-control select2-simple" id="destino" name="destino" required >
                          <option value=""></option>
                        <?php foreach ($sucursales as $sucursal): ?>
                          <option value="<?php echo $sucursal->id_sucursal ?>"><?php echo $sucursal->nombre_sucursal ?></option>
                        <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="codigo" class="form-label">Código Producto</label>
                        <select class="form-control select2-simple" id="codigo" name="codigo" disabled >
                          <option value=""></option>
                          <?php foreach ($productos as $producto): ?>
                            <option value="<?php echo $producto->idProducto ?>" data-stock="<?php echo $producto->stock ?>"><?php echo $producto->codigo . ' - ' . $producto->nombre ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="cantidad" class="form-label">Cantidad a Transferir</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad" value="" >
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-12 mb-3">
                        <label class="form-label">Anotación</label>
                        <textarea  id="anotacion" class="form-control" name="anotacion" rows="10" cols="80" style="height: 100px;"></textarea>
                      </div>
                    </div>

                    <div class="col-md-12 card-footer">
                      <div class="row">
                        <button type="submit" class="btn btn-primary" name="registrar" id="registrar" >Guardar transferencia</button>
                      </div>
                    </div>
                  </form>
                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->

        <div class="col-md-5 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Últimas 7 transferencias</h5>
                    <div class="card-header-right">
                        <div class="btn-group card-option">
                            <button type="button" class="btn dropdown-toggle btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="feather icon-more-horizontal"></i>
                            </button>
                            <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                                <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize"></i> maximize</span><span style="display:none"><i class="feather icon-minimize"></i> Restore</span></a>
                                </li>
                                <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> collapse</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>
                                <li class="dropdown-item reload-card"><a href="#!"><i class="feather icon-refresh-cw"></i> reload</a></li>
                                <li class="dropdown-item close-card"><a href="#!"><i class="feather icon-trash"></i> remove</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                  <table class="table" id="infoSucursalTable">
                    <thead>
                      <tr>
                        <th>Codigo</th>
                        <th class="text-center">Cantidad</th>
                        <th>Origen/Destino</th>
                      </tr>
                    </thead>
                    <tbody >
                      <?php if ($ultimasTransferencias > 0): ?>
                        <?php foreach ($ultimasTransferencias as $transferencia): ?>
                          <tr>
                            <td class="text-sm-start"><?php echo $transferencia->codigo ?></td>
                            <td class="text-center"><?php echo $transferencia->cantidad ?></td>
                            <td class="text-wrap">de <?php echo $transferencia->sucursal_origen . ' a ' . $transferencia->sucursal_destino ?></td>
                          </tr>
                        <?php endforeach ?>
                      <?php else: ?>
                        <tr>
                          <td class="text-center" colspan="3">No hay transferencias aún.</td>
                        </tr>
                      <?php endif ?>
                    </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
