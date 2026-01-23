<div>
  <input type="hidden" id="metodo" value="<?= $metodo ?>"></input>
  <?php include VIEWS_PATH . 'backview/componentes/breadcrumb.php' ?>
 
  <button id="exportar-pendientes" class="btn btn-primary btn-sm mb-3">Exportar producciónes pendientes</button>
  <button id="exportar-listos" class="btn btn-primary btn-sm mb-3">Exportar producciones listas</button>

  <?php include VIEWS_PATH . 'backview/componentes/table.php' ?>
</div>