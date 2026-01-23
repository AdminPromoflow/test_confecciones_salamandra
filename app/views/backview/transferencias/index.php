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
                        <li class="breadcrumb-item"><a href="#!"><?php echo ucfirst($controller) ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- [ breadcrumb ] end -->
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                  <a href="<?php echo url_path('backend/transferencias/registrarTransferencia') ?>" class="btn btn-sm btn-primary">Registrar nueva transferencia</a>
                </div>
                <div class="card-body">
                  <div id="loading" class="text-center">Cargando...</div>
                  <div id="table-show" class="table-responsive mb-4 mt-2">
                    <table id="simple-table" class="table table-striped nowrap dataTable order-table" style="width:100%">
                        <thead>
                          <tr>
                              <th> # </th>
                            <th> Fecha </th>
                            <th> Código </th>
                            <th class="text-center"> Cantidad transferida </th>
                            <th> Origen - Destino </th>
                            <th class="text-center">  </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if ($transferencias > 0): ?>
                            <?php $no = 1; foreach ($transferencias as $transferencia): ?>
                                <tr>
                                    <td class=""><?php echo $transferencia->id_transferencia ?></td>
                                  <td class=""><?php echo formatearFecha($transferencia->registro) ?></td>
                                  <td class=""><b><?php echo $transferencia->codigo ?></b></td>
                                  <td class="text-center"><?php echo $transferencia->cantidad ?></td>
                                  <td class="">de <?php echo '<strong>' . $transferencia->sucursal_origen . '</strong>' . ' a ' . '<b>' . $transferencia->sucursal_destino . '</b>' ?></td>
                                  <td class="text-center">
                                    <a href="javascript:void(0)" class="detalleTransferencia"
                                         data-registro="<?php echo formatearFecha($transferencia->registro) ?>"
                                         data-codigo="<?php echo $transferencia->codigo ?>"
                                         data-nombre="<?php echo $transferencia->nombre_producto ?>"
                                         data-cantidad="<?php echo $transferencia->cantidad ?>"
                                         data-sucursal_origen="<?php echo $transferencia->sucursal_origen ?>"
                                         data-sucursal_destino="<?php echo $transferencia->sucursal_destino ?>"
                                         data-comentario="<?php echo $transferencia->anotacion ?>"
                                         data-usuario="<?php echo $transferencia->usuario ?>">
                                      Ver Transferencia
                                    </a>
                                  </td>
                                </tr>
                            <?php endforeach; ?>
                          <?php else : ?>
                            <tr>
                              <td class="text-center" colspan="6">No hay transferencias registradas.</td>
                            </tr>
                          <?php endif ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
              </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
    <!-- [ Main Content ] end -->
</div>

<div class="modal fade" id="verDetalleTransferencia" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalles de la transferencia</h5>
      </div>
      <div class="modal-body">
        <!-- Tabla para mostrar los detalles -->
        <table class="table table-striped">
          <tbody>
            <tr>
              <th>Fecha</th>
              <td id="registro"></td>
            </tr>
            <tr>
              <th>Codigo Producto</th>
              <td id="codigoProducto"></td>
            </tr>
            <tr>
              <th>Nombre Producto</th>
              <td id="nombreProducto"></td>
            </tr>
            <tr>
              <th>Cantidad Transferida</th>
              <td id="cantidadTransferida"></td>
            </tr>
            <tr>
              <th>Origen/Destino</th>
              <td id="origenDestino"></td>
            </tr>
            <tr>
              <th>Comentario</th>
              <td id="comentario"></td>
            </tr>
            <tr>
              <th>Gestor</th>
              <td id="gestor"></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary">Cerrar</button> -->
      </div>
    </div>
  </div>
</div>