$(document).ready(function () {
    setTimeout(function () {
        // Verificar si la DataTable ya está inicializada
        if (!$.fn.DataTable.isDataTable('#simple-table')) {
            $('#simple-table').DataTable();
        }
        // Muestra la tabla con una transición suave
        // $('#table-show').fadeIn();

    }, 350);
});

