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