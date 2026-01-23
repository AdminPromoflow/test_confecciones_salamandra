<div>
  <?php include VIEWS_PATH . 'backview/componentes/breadcrumb.php' ?>

  <?php include VIEWS_PATH . 'backview/componentes/table.php' ?>
</div>


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
                  <th width="30%"> Sucursal </th>
                  <th class="text-center"> Inventario Total </th>
                  <th> &nbsp; </th>
                </tr>
              </thead>
              <tbody>
                <?php if (isset($sucursales)) : ?>
                  <?php $no = 1;
                  foreach ($sucursales as $sucursal) : ?>
                    <tr>
                      <td class="cell"><?php echo $no++ ?></td>
                      <td class="cell"><b><?php echo ucfirst($sucursal->nombre_sucursal) ?></b></td>
                      <td class="cell text-center">
                        <?php echo $sucursal->total_stock == 0 ? '<span class="text-danger">Sin existencias</span>' : $sucursal->total_stock . ' productos' ?>
                      </td>
                      <td class="cell text-center">
                        <!-- <a class="btn-sm app-btn-secondary mx-1 float-end" href="<?php echo url_path('backend/sucursales/editar/' . $sucursal->id_sucursal) ?>">Editar</a> -->
                        <?php if ($sucursal->total_stock > 0) : ?>
                          <a class="btn-sm app-btn-secondary mx-1 float-end" href="<?php echo url_path('backend/sucursales/ver/' . $sucursal->id_sucursal) ?>">Ver Sucursal</a>
                        <?php endif ?>
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