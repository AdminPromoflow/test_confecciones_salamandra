<div class="page-wrapper">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10"><?php echo ucfirst($description) . ' ' . $sucursal[0]->nombre_sucursal ?></h5>
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
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">

                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                      <table id="simpletable" class="table table-striped nowrap dataTable" role="grid" aria-describedby="simpletable_info" style="width: 100%">
                        <thead>
                          <tr>
                            <th> # </th>
                            <th width="30%"> Código Producto </th>
                            <th class="text-center"> Cantidad Actual </th>
                            <th> &nbsp; </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if (isset($sucursal)): ?>
                            <?php $no = 1; foreach ($sucursal as $item): ?>
                                <tr>
                                  <td class="cell"><?php echo $no++ ?></td>
                                  <td class="cell"><b><?php echo ucfirst($item->codigo) ?></b></td>
                                  <td class="cell text-center">
                                    <?php echo $item->stock <= 0 ? '<span class="text-danger">Sin existencias</span>' : $item->stock . ' productos' ?>
                                  </td>
                                  <td class="cell text-center">
                                    <a class="btn-sm app-btn-secondary" href="javascript:void(0)"> Hacer Transferencia </a>
                                  </td>
                                </tr>
                            <?php endforeach; ?>
                          <?php else : ?>
                            <tr>
                              <td class="text-center" colspan="3">No hay sucursales registradas.</td>
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
