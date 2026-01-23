<div>
  <?php include VIEWS_PATH . 'backview/componentes/breadcrumb.php' ?>
  <?php include VIEWS_PATH . 'backview/componentes/board.php' ?>
</div>
<hr>
<div>
  <button id="crear-programacion" class="btn btn-primary btn-sm mb-3">Crear nueva programación</button>
</div>
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div>
          <h5>Lista de producciones programadas</h5>
        </div>
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
        <div id="table-show" class="table-responsive mb-4 mt-2">
          <table id="simple-table" class="table table-striped nowrap dataTable" style="width:100%">
            <thead id="table-head">
            </thead>
            <tbody id="table-body">
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="modal-nuevaProgramacion" class="modal fade" role="dialog" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLiveLabel">Creando nueva programación</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="px-2">
          <div class=" row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="barras">Código del producto</label>
                <input type="text" class="form-control" id="codigo-produccion">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="barras">Cantidad a producir</label>
                <input type="number" class="form-control" id="cantidad-produccion">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="nombre">Fecha de producción</label>
                <input type="date" class="form-control" id="nueva-fecha">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="guardar-programacion">Guardar</button>
      </div>
    </div>
  </div>
</div>