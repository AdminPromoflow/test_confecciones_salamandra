"use strict";

$(document).ready(function () {
  // const urlTransferencias = window.location.pathname;

  const notyfy = new Notyf({
    types: [{
      type: 'info',
      background: 'blue',
      icon: false
    }]
  });

  var stockActual;

  $('#origen').on("change", function () {
    $('#codigo').attr('disabled', false);
  });

  $('#codigo').on("change", function () {
    const cadena = window.location.pathname;
    const ultimaParte = cadena.slice(cadena.lastIndexOf("/") + 1);
    const urlTransferencias = cadena.slice(0, cadena.lastIndexOf("/"));

    let idSucursal = $('#origen').val();

    if (idSucursal != '') {
      let idProducto = $('#codigo').val();

      $.ajax({
        type: 'POST',
        url: urlTransferencias + '/obtenerStock',
        data: { id_producto: idProducto, id_sucursal: idSucursal },
        success: function (response) {
          stockActual = parseInt(response.respuesta.stock);

          if (stockActual === 0) {
            let message = 'Este producto No puede transferirse porque no tiene existencias.';
            messageError(message);
          }

        },
        error: function (error) {
          let message = 'Ocurrió un ERROR. Contactese con el administrador.';
          messageError(message);
        }
      });
    } else {
      let message = 'Debe seleccionar la sucursal de origen';
      messageError(message);
    }

  });

  $('#formularioTransferir').submit(function (event) {
    // Evita que se envíe el formulario por defecto
    event.preventDefault();

    if (stockActual === 0) {
      let message = 'Este producto NO puede transferirse porque no tiene existencias.';
      messageError(message);
    } else {

      let urlTransferencias = window.location.pathname;

      // Obtiene los valores de los campos
      var sucursal_origen = $('#origen').val();
      var sucursal_destino = $('#destino').val();
      var producto = $('#codigo').val();
      var cantidad = $('#cantidad').val();
      var anotacion = $('#anotacion').val();
      var registrar = $('#registrar').val();

      // Realiza las validaciones
      if (sucursal_origen === sucursal_destino) {
        let message = 'Las sucursales NO deben ser iguales.';
        messageError(message);
      }
      else if (producto === '') {
        let message = 'Debe elegir un producto';
        messageError(message);
      }
      else if (cantidad === '') {
        let message = 'La cantidad NO debe estar vacía.';
        messageError(message);
      }
      else {
        const transferenciaData = {
          sucursal_origen: sucursal_origen,
          sucursal_destino: sucursal_destino,
          producto: producto,
          cantidad: cantidad,
          anotacion: anotacion,
          registrar: registrar
        };

        $.ajax({
          type: 'POST',
          url: urlTransferencias,
          data: transferenciaData,
          success: function (respuesta) {
            if (respuesta.success) {
              goBack();
            } else {
              reloadPage();
            }
          },
          error: function (error) {
            let message = 'Ocurrió un ERROR, intente nuevamente.';
            messageError(message);
          }
        });
      }
    }
  });

  function messageError(message) {
    notyfy.error({
      message: message,
      dismissible: true,
      duration: 6000,
      position: {
        x: 'right',
        y: 'top'
      }
    }).on('dismiss', (target, event) => foobar.retry());
  }

  function goBack() {
    history.go(-1);
  }

  function reloadPage() {
    history.go(0);
  }
});
