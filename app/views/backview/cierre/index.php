<div>
  <?php include VIEWS_PATH . 'backview/componentes/breadcrumb.php' ?>

  <?php include VIEWS_PATH . 'backview/componentes/board.php' ?>
</div>

<div class="card">
  <div class="card-body table-border-style">

    <!-- Formulario de búsqueda -->
    <div class="mb-4">
      <div class="row align-items-center gx-2">
        <div class="col-auto mb-3">
          <label for="searchType" class="">Tipo de búsqueda:</label>
          <select id="searchType" name="searchType" class="form-control">
            <option value="today">Hoy</option>
            <option value="range">Rango de fechas</option>
          </select>
        </div>
        <div class="col-auto mb-3 d-none" id="dateRangeContainer">
          <label for="startDate" class="">Fecha inicial:</label>
          <input type="date" id="startDate" name="startDate" class="form-control" required />
        </div>
        <div class="col-auto mb-3 d-none" id="endDateContainer">
          <label for="endDate" class="">Fecha final:</label>
          <input type="date" id="endDate" name="endDate" class="form-control" required />
        </div>
        <div class="col-auto mb-3">
          <label for="searchType" class=""></label>
          <button type="button" class="btn btn-primary" id="searchBtn">Buscar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card mt-4">
  <div class="card-body">
    <div id="tablasContainer"></div>
  </div>
</div>