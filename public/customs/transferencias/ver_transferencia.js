$(document).ready(function () {
  // Mostrar "Cargando..." antes de inicializar la tabla
  $('#loading').text('Cargando...').fadeIn();

  // Función para inicializar DataTable
  function initDataTable() {
      if (!$.fn.DataTable.isDataTable('#simple-table')) {
          $('#simple-table').DataTable({
              "paging": true,
              "ordering": true,
              "language": {
                  "emptyTable": "No hay datos disponibles en la tabla"
              },
              "initComplete": function () {
                  // Ocultar "Cargando..." y mostrar la tabla cuando DataTable esté listo
                  $('#loading').fadeOut(function () {
                      $('#table-show').fadeIn();
                  });
              }
          });
      }
  }

  // Inicializar DataTable
  initDataTable();

  // Asignar el evento de clic una sola vez
  $('#simple-table tbody').off('click', 'a.detalleTransferencia').on('click', 'a.detalleTransferencia', function () {
      console.log('bien');
      let registro = $(this).data('registro');
      let codigo = $(this).data('codigo');
      let nombre = $(this).data('nombre');
      let cantidad = $(this).data('cantidad');
      let sucursal_origen = $(this).data('sucursal_origen');
      let sucursal_destino = $(this).data('sucursal_destino');
      let comentario = $(this).data('comentario');
      let usuario = $(this).data('usuario');

      // Asignar datos a los elementos de la modal
      $('#registro').text(registro);
      $('#codigoProducto').text(codigo);
      $('#nombreProducto').text(nombre);
      $('#cantidadTransferida').text(cantidad);
      $('#origenDestino').text(sucursal_origen + ' - ' + sucursal_destino);
      $('#comentario').text(comentario);
      $('#gestor').text(usuario);

      // Abrir la modal
      $('#verDetalleTransferencia').modal('show');
  });
});