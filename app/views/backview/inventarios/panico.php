<div>
  <?php include VIEWS_PATH . 'backview/componentes/breadcrumb.php' ?>
</div>

<div>
  <form method="post" action="<?= url_path('backend/Inventarioproductos/modificarStock') ?>" class="form-control">
    <div class="row align-items-end">
      <div class="col-md-4">
        <label>Producto <span class="text-danger">*</span></label>
        <select class="form-control select2-simple" name="producto" required>
          <option>Seleccione un producto</option>
          <?php foreach ($productos as $producto) : ?>
            <option value="<?= $producto->id_producto ?>"><?= $producto->codigo ?></option>
          <?php endforeach ?>
        </select>
      </div>
      <div class="col-md-4">
        <label>Sucursal <span class="text-danger">*</span></label>
        <select class="form-control select2-simple" name="sucursal" required>
          <option>Seleccione una sucursal</option>
          <?php foreach ($sucursales as $sucursal) : ?>
            <option value="<?= $sucursal->id_sucursal ?>"><?= $sucursal->nombre ?></option>
          <?php endforeach ?>
        </select>
      </div>
      <div class="col-md-2">        
        <label>Cantidad <span class="text-danger">*</span></label>
        <input type="number" name="cantidad" class="form-control" required>
      </div>
      <div class="col-md-2">   
        <button type="submit" class="btn btn-primary btn-sm" name="aceptar">Aceptar</button>
      </div>
    </div>
  </form>
</div>
