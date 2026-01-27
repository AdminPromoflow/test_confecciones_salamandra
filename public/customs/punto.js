$(document).ready(function () {

  // Variables
  //Buenas
  const notyf = new Notyf();
  const urlController = window.location.pathname;
  const urlPath = urlController.slice(0, urlController.lastIndexOf("/"));

  let result = {};

  // Funciones Generales
  //

  function formatDate(dateString, includeTime) {
    const date = dateString ? new Date(dateString) : new Date();

    if (!includeTime) {
      // Caso 1: Solo fecha en formato "Y-m-d"
      const formattedDate = date.toISOString().split('T')[0];
      return formattedDate;
    }

    const options = {
      year: 'numeric',
      month: 'numeric',
      day: 'numeric',
      hour: 'numeric',
      minute: 'numeric',
      second: 'numeric',
      hour12: true
    };

    const formattedDateTime = new Intl.DateTimeFormat('es-ES', options).format(date);

    if (!dateString) {
      // Caso 2: Solo hora en formato "H:i:s"
      const formattedTime = formattedDateTime.split(' ')[1];
      return formattedTime;
    }

    // Caso 3: Fecha en formato "d-m-Y" y Caso 4: Fecha y hora en formato "d-m-Y h:i:s A"
    return formattedDateTime;
  }

  function formatCurrency(value) {
    return parseFloat(value).toLocaleString('es-CO', {
      style: 'currency',
      currency: 'COP',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    })
  };

  function showMessage(type, contentMessage) {
    const commonOptions = {
      message: contentMessage,
      dismissible: true,
      duration: 3500,
      position: {
        x: 'right',
        y: 'top',
      },
    };

    const successOptions = {
      ...commonOptions,
      type: 'success',
    };

    if (type == 'success') {
      notyf.success(successOptions).on('dismiss', handleDismiss);
    } else if (type == 'error') {
      const errorOptions = {
        ...commonOptions,
        type: 'error',
      };
      notyf.error(errorOptions).on('dismiss', handleDismiss);
    }

    function handleDismiss({ target, event }) {
      // Add your dismissal handling logic here
      foobar.retry();
    }
  }

  function handleErrorResponse(jqXHR, textStatus, errorThrown) {
    handleErrorResponse(jqXHR, textStatus, errorThrown);
  }

  // Funciones

  function obtenerIdSucursal() {
    let sucursal = $('#sucursal').attr('data-idSucursal');
    if (!sucursal) {
      showMessage('error', 'Ocurrió un problema al cargar, refresque la pagina con Ctrl + F5.');
      return;
    }
    return sucursal;
  }

  // Obtenemos los productos de la base de datos y los mostramos en el select productos cuando seleccionamos un cliente
  function obtenerProductos() {
    let id_sucursal = obtenerIdSucursal();

    // Mostrar un indicador de carga (puedes implementar esto según tu interfaz de usuario)
    // ...

    // Realizar la solicitud AJAX utilizando Promesas
    $.ajax({
      url: urlPath + '/inventarioproductos/obtenerStockBySucursal/' + id_sucursal,
      type: 'GET'
    })
      .then(function (response) {
        // Verificar que la respuesta tiene la estructura esperada
        if (response && response.respuesta) {
          // Agregar la clase select2-simple después de inicializar Select2
          $('#idProducto').addClass('select2-simple');

          // Manipular los datos obtenidos y actualizar el select
          actualizarSelect(response.respuesta);
        } else {
          console.error('Error: La respuesta no tiene la estructura esperada.');
          // Puedes mostrar un mensaje de error al usuario si es necesario
        }
      })
      .catch(function (error) {
        // Manejar el error de una manera más informativa
        handleErrorResponse(error);
      });
  }

  // Función para actualizar el select con los datos obtenidos
  function actualizarSelect(datos) {
      // Inicializar Select2
      $(".select2-simple").select2();

      console.log(typeof datos); // Verificar el tipo de datos

      // Convertir el objeto en un array si no lo es
      if (typeof datos === 'object' && !Array.isArray(datos)) {
          datos = Object.values(datos); // Convierte el objeto en un array
      }

      // Verificar que datos sea un array y no esté vacío
      if (!Array.isArray(datos) || datos.length === 0) {
          console.error('Error: Los datos proporcionados no son válidos.');
          return;
      }

      // Limpiar el select antes de agregar nuevas opciones
      $('#idProducto').empty();

      // Agregar la opción predeterminada
      $('#idProducto').append('<option value="" disabled selected>Seleccione un producto</option>');

      // Agregar opciones desde los datos
      $.each(datos, function (index, producto) {
          // Verificar que cada producto tenga las propiedades esperadas
          if (producto && producto.id_producto && producto.codigo && producto.nombre_producto) {
              // Construir la opción y agregar al select
              $('#idProducto').append('<option value="' + producto.id_producto + '">' + producto.codigo + ' - ' + producto.nombre_producto + '</option>');
          } else {
              console.warn('Advertencia: El objeto en datos[' + index + '] no tiene las propiedades esperadas y será omitido.');
          }
      });

      // Disparar el evento 'change' después de actualizar el select
      $('#idProducto').trigger('change');
  }

  function carrito(idCliente, init) {
    $.ajax({
      url: urlPath + '/carrito/obtenerCarrito',
      type: 'POST',
      data: { id_cliente: idCliente }
    })
      .done(function (response) {
        if (response.success) {
          let carrito = response.respuesta;

          if ($.isArray(carrito)) {
            $("#table-cart").empty();
            carrito.forEach(function (producto, index) {
              let total = producto.cantidad * producto.precio;
              let estado = parseInt(producto.estado);

              switch (estado) {
                case 0:
                  estado = '<span class="badge badge-danger pt-1 px-3"> Pendiente </span>';
                  break;
                case 1:
                  estado = '<span class="badge badge-danger pt-1 px-3"> Urgente </span>';
                  break;
                case 2:
                  estado = '<span class="badge badge-primary pt-1 px-3"> Listo </span>';
                  break;
                case 3:
                  estado = '<span class="badge badge-warning pt-1 px-3"> En Bolsa </span>';
                  break;
                case 4:
                  estado = '<span class="badge badge-success pt-1 px-3"> Entregado </span>';
                  break;
                default:
                  estado = 'Estado Desconocido';
                  break;
              }

              let oculto = 'block';

              if (producto.estado == 4) {
                // oculto = 'none';
              }

              // Verificar si la fecha de entrega es null
              let fechaEntrega = producto.fecha_entrega ? producto.fecha_entrega : "Sin fecha de entrega";

              var fila = `<tr>
                  <td>${index + 1}</td>
                  <td data-label="Name">
                      <div class="d-flex py-1 align-items-center">
                          <div class="flex-fill">
                              <div class="font-weight-bold">${producto.producto_codigo}</div>
                              <div class="text-secondary">${producto.producto_nombre + ' - Talla: ' + producto.producto_talla}</div>
                          </div>
                      </div>
                  </td>
                  <td class="text-center">${producto.cantidad}</td>
                  <td class="text-center">
                      <div>${formatCurrency(producto.precio)}</div>
                  </td>
                  <td class="text-center">${estado}</td>
                  <td class="text-center">${fechaEntrega}</td>
                  <td>
                      <div class="btn-list flex-nowrap">
                          <a href="javascript:void(0)" class="btn btn-info btn-sm btn-block opciones" data-idCarrito="${producto.id_carrito}" data-idCliente="${idCliente}" data-cantidad="${producto.cantidad}" data-estado="${producto.estado}" data-anotacion="${producto.nota}" data-fecha="${producto.fecha_entrega}" style="display:${oculto};">
                            Opciones
                          </a>
                          <a href="javascript:void(0)" class="btn btn-danger btn-sm btn-block quitar" data-idCarrito="${producto.id_carrito}" data-idCliente="${idCliente}" data-idProducto="${producto.producto_id}" data-estado="${producto.estado}">
                            Quitar
                          </a>
                      </div>
                  </td>
              </tr>`;
              $("#table-cart").append(fila);
            });

            if (init == 1) {
              showMessage('success', 'Los productos fueron cargados correctamente.');
            }
          } else {
            $("#table-cart").empty();
            showMessage('error', 'No hay productos asociados a este cliente en el carrito.');
          }
        }

        let subtotal = $("#table-cart td:nth-child(4)");
        let sumaSubtotal = 0;

        subtotal.each(function () {
          sumaSubtotal += parseFloat($(this).text().replace(/\D/g, '')) || 0;
        });

        subtotal = formatCurrency(sumaSubtotal);
        let total = formatCurrency(sumaSubtotal);

        $('#subtotal').val(subtotal);
        $('#total').val(total);

        actualizarTotal();
        actualizarCambio()
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        handleErrorResponse(jqXHR, textStatus, errorThrown);
      });
  };

  function actualizarTotal() {
    let subtotal = $('#subtotal').val().replace(/\D/g, '') || 0;
    let descuento = $('#descuento').val().replace(/\D/g, '') || 0;

    if (!descuento) {
      $('#total').val(formatCurrency(subtotal))
    } else {
      var total = subtotal - descuento;
      $('#total').val(formatCurrency(total))
    }

    actualizarCambio()
  }

  function actualizarCambio() {
    let total = $('#total').val().replace(/\D/g, '') || 0;
    let pagoCon = $('#pagacon').val().replace(/\D/g, '') || 0;

    let cambio = pagoCon - total;

    $('#cambio').val(formatCurrency(cambio))
  }

  function guardarNuevoCliente(datosCliente) {
    $.ajax({
      url: urlPath + '/clientes/crearNuevoCliente',
      type: 'POST',
      data: datosCliente
    })
      .done(function (response) {
        if (response.success) {

          let nuevoClienteID = response.respuesta.idCliente;
          let nuevoClienteNombre = datosCliente.cedula + ' - ' + datosCliente.nombre + ' ' + datosCliente.apellidos;

          // Agregar temporalmente la nueva opción al select
          $("#idCliente").append('<option value="' + nuevoClienteID + '">' + nuevoClienteNombre + '</option>');

          $('#modal-nuevoCliente').modal('hide');

          showMessage('success', 'Cliente nuevo fue guardado correctamente.');
        } else {
          $('#modal-nuevoCliente').modal('hide');
          showMessage('error', 'Cliente ya registrado');
        }
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        handleErrorResponse(jqXHR, textStatus, errorThrown);
      });
  }

  function verificarEstado(cantidadProducto) {
    if (parseInt(cantidadProducto) > 0) {
      estadoProducto = parseInt(2)
    } else {
      estadoProducto = parseInt(0)
    }
    return result
  };

  function evaluarTipoVenta(total, pagacon) {
    let hayProductosPendientes = false;

    $('#table-cart tr').each(function() {
      let estadoBadge = $(this).find('td:nth-child(5) .badge');
      if (estadoBadge.text().trim() === 'Pendiente' || estadoBadge.text().trim() === 'Urgente') {
        hayProductosPendientes = true;
        return false;
      }
    });

    return (hayProductosPendientes || pagacon < total) ? 2 : 1;
  }

  function limpiarCampos() {
    $('#idCliente').val($('#idCliente option:first').val()).trigger('change.select2');
    $('#idProducto').val($('#idProducto option:first').val()).trigger('change.select2');
    $('#tipoVenta').val($('#tipoVenta option:first').val()).trigger('change.select2');
    $('#nombreProducto').val('');
    $('#cantidadProducto').val('');
    $('#precioProducto').val('');
    $('#fechasProduccion').val($('#fechasProduccion option:first').val()).trigger('change.select2');
    $('#fechaEntrega').val('');
    $('#subtotal').val('');
    $('#descuento').val('');
    $('#total').val('');
    $('#mediopago').val($('#mediopago option:first').val()).trigger('change.select2');
    $('#pagacon').val('');
    $('#cambio').val('');
    $('#notaVenta').val('');
    $('#contenedorFechas').hide();
  }

// ****************************




function obtenerDetallesProducto(idProducto) {
  alert("2. punto.js public/customs/punto.js linea 376 url: " + urlController + '/obtenerInventario');

  $.ajax({
      url: urlController + '/obtenerInventario',
      type: 'POST',
      data: { id_producto: idProducto }
  })
  .done(function (response) {
    alert("3. Resputesta punto.js linea 384" + JSON.stringify(response));
      if (response.success) {
          const { nombre, talla, precio, stock, id_sucursal } = response.respuesta;

          // Asignar los valores a los elementos del DOM
          $('#idSucursal').val(id_sucursal);
          $('#nombreProducto').text(nombre + ' TALLA ' + talla);
          $('#precioProducto').text(formatCurrency(precio));
          $('#precioProducto').attr('data-precioProducto', precio);

          // Resaltar en rojo si no hay existencias
          if (stock <= 0) {
              $('#cantidadProducto').attr('data-cantidadProducto', stock);
              $('#cantidadProducto')
                  .text('Sin existencias')
                  .removeClass('text-success')
                  .addClass('text-danger text-bold');

              $('#contenedorFechas').show();
          } else {
              $('#cantidadProducto').attr('data-cantidadProducto', stock);
              $('#cantidadProducto')
                  .text(stock)
                  .removeClass('text-danger')
                  .addClass('text-success text-bold');

              $('#contenedorFechas').hide();
          }

          // Obtener y mostrar las sucursales con stock disponible
          obrtenerStockSucursales(idProducto, id_sucursal);

          // Mostrar la caja de información del producto
          $('#infoProducto').show();
      } else {
          showMessage('error', 'No se encontró información del producto.');
          $('#infoProducto').hide();
      }
  })
  .fail(function (jqXHR, textStatus, errorThrown) {
      handleErrorResponse(jqXHR, textStatus, errorThrown);
      $('#infoProducto').hide();
  });
}

function obrtenerStockSucursales(idProducto, idSucursalActual) {
  if (idProducto && idSucursalActual) {
      $.ajax({
          url: urlPath + '/inventarioproductos/obtenerStockByProducto/' + idProducto,
          type: 'GET'
      })
      .done(function (response) {
          if (response.success) {
              let datos = response.respuesta; // Asegúrate de que la respuesta tenga la estructura correcta
              console.log(datos);
              let sucursalesStock = $('#sucursalesStock').empty();
              let hayStockEnOtrasSucursales = false; // Bandera para verificar si hay stock en otras sucursales
              let stockEnSucursalActual = 0; // Variable para almacenar el stock en la sucursal actual

              // Obtener el stock en la sucursal actual
              for (var i = 0; i < datos.length; i++) {
                  if (datos[i].id_sucursal === idSucursalActual) {
                      stockEnSucursalActual = datos[i].stock;
                      break;
                  }
              }

              // Mostrar stock en otras sucursales
              for (var i = 0; i < datos.length; i++) {
                  // Filtrar la sucursal actual y mostrar solo sucursales con stock > 0
                  if (datos[i].id_sucursal !== idSucursalActual && datos[i].stock > 0) {
                      hayStockEnOtrasSucursales = true; // Hay stock en al menos una sucursal que no es la actual

                      // Determinar si es "unidad" o "unidades"
                      let unidadTexto = (datos[i].stock === 1) ? "unidad" : "unidades";

                      // Mostrar sucursales con stock en el nuevo formato
                      sucursalesStock.append(
                          `<li class="list-group-item text-primary">
                              <i class="fas fa-store-alt"></i> ${datos[i].nombre_sucursal}:
                              <strong class="text-bold text-danger">${datos[i].stock + ' ' + unidadTexto}</strong>
                          </li>`
                      );

                      // Mostrar el boton con id agregarAlCarrito
                      $('#agregarAlCarrito').show();
                  }
              }
              let stock = $('#cantidadProducto').attr('data-cantidadProducto');

              // Mostrar mensajes condicionales
              if (stock <= 0 && hayStockEnOtrasSucursales) {
                  // Mostrar mensaje de recomendación si el stock en la sucursal actual es menor a 3 y hay stock en otras sucursales
                  sucursalesStock.append(
                    `<li class="list-group-item text-info">
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> Solicite traslado al administrador.
                        </div>
                    </li>`
                );

                //  Necesito ocultar el boton con id agregarAlCarrito
                $('#agregarAlCarrito').hide();
              } else if (!hayStockEnOtrasSucursales) {
                  // Mostrar mensaje en rojo si no hay stock en otras sucursales
                  sucursalesStock.append(
                      `<li class="list-group-item text-danger">
                          <div class="alert alert-danger" role="alert">
                              <i class="fas fa-exclamation-circle"></i> No hay stock disponible en otras sucursales.
                          </div>
                      </li>`
                  );

                  // Mostrar el boton con id agregarAlCarrito
                  $('#agregarAlCarrito').show();
              } else {
                // remover el ultimo li
                $('#sucursalesStock li:last-child').remove();
              }
          } else {
              showMessage('error', 'En ninguna sucursal hay existencias de este producto.');
          }
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
          handleErrorResponse(jqXHR, textStatus, errorThrown);
      });
  } else {
      showMessage('error', 'No se ha seleccionado un producto o la sucursal actual.');
  }
}

function agregarProductoAlCarrito(datosProducto) {
  // Verificar que id_sucursal esté presente y no sea 0
  if (!datosProducto.id_sucursal || datosProducto.id_sucursal == 0) {
      showMessage('error', 'La sucursal no está definida o no es válida.');
      return; // Detener la ejecución si id_sucursal no es válido
  }

  // Enviar los datos al controlador
  $.ajax({
      url: urlPath + '/carrito/agregarProductoCarrito',
      type: 'POST',
      data: datosProducto,
  })
  .done(function (response) {
      if (response.success) {
          // Producto agregado correctamente
          showMessage('success', 'Producto agregado al carrito correctamente.');

          // Actualizar el carrito
          carrito(datosProducto.id_cliente);

          // Obtener el stock actual y restar 1
          let stock = $('#cantidadProducto').attr('data-cantidadProducto') - 1;

          // Determinar el texto y el valor de cantidadProducto
          let cantidadProducto = (stock <= 0) ? 'Sin existencias' : stock;

          // Actualizar el texto y el atributo data-cantidadProducto
          $('#cantidadProducto').text(cantidadProducto);
          $('#cantidadProducto').attr('data-cantidadProducto', stock);

          // Cambiar el estilo del texto según el stock
          if (cantidadProducto === 'Sin existencias') {
              $('#cantidadProducto')
                  .removeClass('text-success') // Remover color verde
                  .addClass('text-danger text-bold'); // Aplicar color rojo y negrita
          } else {
              $('#cantidadProducto')
                  .removeClass('text-danger text-bold') // Remover color rojo y negrita
                  .addClass('text-success'); // Aplicar color verde
          }

          // Mostrar u ocultar el contenedor de fechas según el stock
          if (stock <= 0) {
              $('#contenedorFechas').show(); // Mostrar contenedor de fechas
          } else {
              $('#contenedorFechas').hide(); // Ocultar contenedor de fechas
          }

          // Actualizar el total del carrito
          actualizarTotal();
      } else {
          // Mostrar mensaje de error y solicitar actualizar la página
          showMessage('error', 'Ocurrio un problema y el producto no se agregó al carrito.');
          setTimeout(() => {
              location.reload(); // Recargar la página después de un error
          }, 3000);
      }
  })
  .fail(function (jqXHR, textStatus, errorThrown) {
      // Manejar errores de la solicitud AJAX
      showMessage('error', 'Error en la solicitud. Por favor, intente nuevamente.');
      console.error('Error en la solicitud:', textStatus, errorThrown);
  });
}

function quitarProducto(datosProducto) {
  $.ajax({
      url: `${urlPath}/carrito/quitarProductoCarrito`,
      type: 'POST',
      data: datosProducto
  })
  .done(function (response) {
      if (response.success) {
          // Mostrar mensaje de éxito
          showMessage('success', 'Producto quitado del carrito.');

          // Obtener el ID del producto seleccionado actualmente en la interfaz
          let idProductoSeleccionado = $('#idProducto').val();

          // Verificar si el producto quitado es el mismo que el seleccionado
          if (parseInt(datosProducto.id_producto) == parseInt(idProductoSeleccionado)) {
              // Actualizar el stock en la interfaz
              let stockActual = parseInt($('#cantidadProducto').attr('data-cantidadProducto')) + 1;

              // Actualizar el atributo data-cantidadProducto
              $('#cantidadProducto').attr('data-cantidadProducto', stockActual);

              // Verificar si el stock era 0 y ahora tiene 1 unidad
              if (stockActual > 0) {
                  $('#cantidadProducto')
                      .text(stockActual)
                      .removeClass('text-success')
                      .addClass('text-success text-bold');

                  // Ocultar el contenedor de fechas
                  $('#contenedorFechas').hide();
              } else if (stockActual <= 0) {
                  $('#cantidadProducto')
                      .text('Sin existencias')
                      .removeClass('text-success')
                      .addClass('text-danger text-bold');
              }
          }

          // Actualizar el carrito
          carrito(datosProducto.id_cliente);
      } else {
          showMessage('error', 'No se pudo quitar el producto del carrito.');
      }
  })
  .fail(function (jqXHR, textStatus, errorThrown) {
      handleErrorResponse(jqXHR, textStatus, errorThrown);
  });
}

$('#idProducto').on('change', function() {

  let idProducto = $(this).val();
  if (idProducto) {
    alert("1. punto.js public/customs/punto.js linea 631  (1)");

      obtenerDetallesProducto(idProducto);
  } else {
      $('#infoProducto').hide();
  }
});

$('#agregarAlCarrito').off("click").on("click", function () {
  let idCliente = parseInt($('#idCliente').val());
  let tipoVenta = parseInt($('#tipoVenta').val());
  let idSucursal = parseInt($('#sucursal').attr('data-idSucursal'));
  let idProducto = $('#idProducto').val();
  let cantidadProducto = parseInt($('#cantidadProducto').text()); // Obtener la cantidad actual del DOM
  let stock = parseInt($('#cantidadProducto').attr('data-cantidadProducto'));
  let precioProducto = parseInt($('#precioProducto').attr('data-precioProducto'));
  let idFecha = $('#fechaEntrega').val();

  // Validaciones
  if (!idFecha && stock <= 0) {
      showMessage('error', 'No se ha seleccionado una fecha de entrega.');
      return;
  }

  if (tipoVenta == 1 && stock < 1) {
      showMessage('error', 'Este producto no puede agregarse al carrito, revise el tipo de transacción.');
      return;
  }

  if (!idCliente) {
      showMessage('error', 'No se ha seleccionado un cliente.');
      return;
  }

  if (verificarEstado(cantidadProducto)) {
      let estadoProducto = (stock > 0) ? 2 : 0; // Definir el estado del producto

      const datosProducto = {
          id_sucursal: idSucursal,
          id_producto: idProducto,
          stock: stock,
          cantidad: 1, // Cantidad fija, puedes ajustarla según tu lógica
          precio: precioProducto, // Usar el precio convertido a número
          estado: estadoProducto,
          nota: "",
          fecha_entrega: idFecha == "" ? null : idFecha,
          id_cliente: idCliente
      };

      // Agregar el producto al carrito
      agregarProductoAlCarrito(datosProducto);
  }
});

// Quitar el producto del carrito
$('#table-cart').on('click', 'a.quitar', function () {
  const datosProducto = {
      id_carrito: $(this).attr('data-idCarrito'),
      id_cliente: $(this).attr('data-idCliente'),
      id_producto: $(this).attr('data-idProducto'),
      estado: $(this).attr('data-estado')
  };

  // Llamar a la función para quitar el producto del carrito
  quitarProducto(datosProducto);
});




  // **************************

  // Eventos  //

  // Agregar un evento de cambio al select
  $('#tipoVenta').on('change', function () {
    // Obtener el valor seleccionado
    var valorSeleccionado = $(this).val();

    // Actualizar el texto del label según la opción seleccionada
    if (valorSeleccionado == 2) {
      $('#labelSaldoPendiente').text('Saldo pendiente');
    } else if (valorSeleccionado == 1) {
      $('#labelSaldoPendiente').text('Cambio');
    }
  });

  $('#idCliente').on('change', function () {
    obtenerProductos();
  });

  // Selecciona un cliente y se escanea el carrito para ver si hay productos asociados al cliente
  $('.escanearCarrito').on("change", function () {
    let idCliente = $('#idCliente').val();

    if (!idCliente) {
      showMessage('error', 'No se ha seleccionado un cliente.');
      return
    }

    // El segundo parametro se establece en 1 para indicar que es la primera verz que se busca a este cliente
    carrito(idCliente, 0);
  });

  $('#guardarCliente').on('click', function () {
    const datosCliente = {
      cedula: $('#cedula').val(),
      nombre: $('#nombre').val(),
      apellidos: $('#apellidos').val(),
      direccion: $('#direccion').val(),
      barrio: $('#barrio').val(),
      email: $('#email').val(),
      telefono: $('#telefono').val(),
      registro: formatDate()
    };

    guardarNuevoCliente(datosCliente);
  });

  $('#nuevoCliente').on('click', function () {
    $('#cedula').val('');
    $('#nombre').val('');
    $('#apellidos').val('');
    $('#direccion').val('');
    $('#barrio').val('');
    $('#email').val('');
    $('#telefono').val('');

    $('#modal-nuevoCliente').modal('show');
  });

  $('#table-cart').on('click', 'a.opciones', function () {
    const idCarrito = $(this).data('idcarrito');
    const idCliente = $(this).data('idcliente');
    const cantidad = $(this).data('cantidad');
    const estado = +$(this).data('estado'); // Convert to number
    const fecha = $(this).data('fecha');
    const anotacion = $(this).data('anotacion');

    $('#clienteId').val(idCliente);
    $('#carritoId').val(idCarrito);
    $('#cantidadActual').val(cantidad);

    let estadoActual = '';
    let estadoDisabled = false;
    let fechaDisabled = false;

    switch (estado) {
      case 0:
        estadoActual = 'Pendiente';
        estadoDisabled = true;
        fechaDisabled = false;
        break;
      case 1:
        estadoActual = 'Urgente';
        estadoDisabled = true;
        fechaDisabled = false;
        break;
      case 2:
        estadoActual = 'Listo';
        estadoDisabled = false;
        fechaDisabled = true;
        break;
      case 3:
        estadoActual = 'Bolsa';
        estadoDisabled = false;
        fechaDisabled = true;
        break;
      case 4:
        estadoActual = 'Entregado';
        estadoDisabled = false;
        fechaDisabled = true;
        break;
      case 5:
        estadoActual = 'Cancelado';
        estadoDisabled = true;
        fechaDisabled = true;
        break;
      case 6:
        estadoActual = 'Retorno';
        estadoDisabled = true;
        fechaDisabled = true;
        break;
      default:
        break;
    }

    $('#nuevoEstado').prop('disabled', estadoDisabled);
    $('#nuevaFechaEntrega').prop('disabled', fechaDisabled);

    $('#estadoActual').val(estadoActual);
    $('#fechaActual').val(fecha);
    $('#anotacionActual').val(anotacion);

    // Limpiar las opciones
    $('#nuevoEstado option').remove();

    // Añadir opciones según el estado
    if (estadoActual == 'Pendiente') {
      $('#nuevoEstado').append('<option value="">Seleccione nuevo estado</option>');
      $('#nuevoEstado').append('<option value="1">Urgente</option>');
    } else if (estadoActual == 'Urgente') {
      $('#nuevoEstado').append('<option value="">Seleccione nuevo estado</option>');
      $('#nuevoEstado').append('<option value="1">Urgente</option>');
    } else if (estadoActual == 'Listo') {
      $('#nuevoEstado').append('<option value="">Seleccione nuevo estado</option>');
      $('#nuevoEstado').append('<option value="3">Bolsa</option>');
      $('#nuevoEstado').append('<option value="4">Entregar</option>');
    } else if (estadoActual == 'Bolsa') {
      $('#nuevoEstado').append('<option value="">Seleccione nuevo estado</option>');
      $('#nuevoEstado').append('<option value="2">Listo</option>');
      $('#nuevoEstado').append('<option value="4">Entregar</option>');
    } else if (estadoActual == 'Entregado') {
      $('#nuevoEstado').append('<option value="">Seleccione nuevo estado</option>');
      $('#nuevoEstado').append('<option value="2">Listo</option>');
      $('#nuevoEstado').append('<option value="3">Bolsa</option>');
    }
    else if (estadoActual == 'Cancelado' || estadoActual == 'Retorno') {
      $('#nuevoEstado').append('<option value="">No editable</option>');
    }

    $('#modal-opciones').modal('show');
  });

  $('#guardarOpciones').on('click', function () {
    const estadoActualStr = $('#estadoActual').val();
    const clienteId = $('#clienteId').val();
    const fechaActual = formatDate($('#fechaActual').val());
    const nuevaFechaEntrega = $('#nuevaFechaEntrega').val();

    let estadoActual;

    switch (estadoActualStr) {
      case 'Pendiente':
        estadoActual = 0;
        break;
      case 'Urgente':
        estadoActual = 1;
        break;
      case 'Listo':
        estadoActual = 2;
        break;
      case 'Bolsa':
        estadoActual = 3;
        break;
      case 'Entregado':
        estadoActual = 4;
        break;
      case 'Cancelado':
        estadoActual = 5;
        break;
      case 'Retorno':
        estadoActual = 6;
        break;
      default:
        estadoActual = null; // Handle unexpected state values
    }

    const datosOpciones = {
      idCarrito: $('#carritoId').val(),
      estado: $('#nuevoEstado').val() == "" ? estadoActual : $('#nuevoEstado').val(),
      cantidad: $('#cantidadActual').val(),
      nota: $('#anotacionActual').val(),
      // fecha_entrega: nuevaFechaEntrega != "" ? nuevaFechaEntrega : fechaActual
    };

    // necesito evaluar si la nuevafecha de entrega esta vacia no hacer nada
    if (nuevaFechaEntrega != "") {
      datosOpciones.fecha_entrega = formDate(nuevaFechaEntrega);
    } else {
      datosOpciones.fecha_entrega = fechaActual;
    }


    $.ajax({
      url: urlController + '/guardarOpciones',
      method: 'POST',
      data: datosOpciones
    })
      .done(function (response) {
        if (response.nuevasOpciones) {
          // Clear input values and hide modal
          $('#clienteId, #carritoId, #cantidadActual, #estadoActual, #nuevoEstado, #fechaActual, #nuevaFechaEntrega, #anotacionActual').val('');
          $('#modal-opciones').modal('hide');

          // Show success message and update cart
          showMessage('success', 'Las opciones adicionales se guardaron.');
          carrito(clienteId);
        }
      })
      .fail(function (error) {
        console.error('Error en la solicitud AJAX:', error);
      });
  });

  $('#descuento').on('keyup', function () {
    actualizarTotal()
  });

  $('#pagacon').on('keyup', function () {
    actualizarCambio()
  });

  $('#guardarVenta').on('click', function () {
    // Obtener valores de los campos
    let idCliente = parseInt($('#idCliente').val());
    let descuento = parseInt($('#descuento').val()) || 0;
    let total = parseInt($('#total').val().replace(/\D/g, '')) || 0;
    let mediopago = parseInt($('#mediopago').val());
    let pagacon = parseInt($('#pagacon').val()) || 0;
    let cambio = parseInt($('#cambio').val().replace(/\D/g, ''));
    let notaVenta = $('#notaVenta').val();
    let abono = 0;
    let saldo = 0;

    // Evaluar el tipo de venta correcto
    let tipoVenta = evaluarTipoVenta(total, pagacon);

    if (total == 0) {
      showMessage('error', 'No se puede guardar venta porque no hay productos en el carrito.');
      return;
    }

    if (isNaN(mediopago)) {
      showMessage('error', 'No se ha elegido el medio de pago.');
      return;
    }

    if (descuento > total) {
      showMessage('error', 'El monto de descuento debe ser menor al total de la venta.');
      return;
    }

    // Validaciones y asignación de valores según el tipo de venta
    if (tipoVenta == 2) { // Apartado
      abono = pagacon;
      saldo = total - pagacon;
      cambio = 0;
    } else { // Venta
      if (pagacon < total) {
        showMessage('error', 'El monto recibido debe ser mayor o igual al total para ventas completas.');
        return;
      }
      abono = 0;
      saldo = 0;
    }

    const datosVenta = {
      tipoventa: tipoVenta,
      total: total,
      descuento: descuento,
      devolucion: 0,
      pagacon: pagacon,
      cambio: cambio,
      abono: abono,
      saldo: saldo,
      mediopago: mediopago,
      estado: tipoVenta,
      nota: notaVenta,
      id_cliente: idCliente
    };

    $.ajax({
      url: urlPath + '/ventas/guardarVenta',
      type: 'POST',
      data: datosVenta
    })
      .done(function (response) {
        if (response.success) {
          showMessage('success', 'Venta guardada correctamente.');
          // carrito(idCliente);
          limpiarCampos();

          let urlImpresion = urlPath + '/ventas/imprimirTiquet/' + response.respuesta;

          // Abrir una nueva ventana
          let ventanaImpresion = window.open(urlImpresion, '_blank');

          // Esperar a que la ventana de impresión se cargue
          ventanaImpresion.onload = function () {
            // Activar la impresión automáticamente
            ventanaImpresion.print();
          };

          location.reload();
        }
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        handleErrorResponse(jqXHR, textStatus, errorThrown);
      });
  });

  $('#limpiarVenta').on('click', function () {
    let idCliente = $('#idCliente').val();

    if (idCliente == 0) {
      showMessage('success', 'El Punto de venta ya está limpio.');
    }

    $.ajax({
      url: urlPath + '/carrito/vaciarCarrito',
      type: 'POST',
      data: { id_cliente: idCliente }
    })
      .done(function (response) {
        if (response.success) {
          showMessage('success', 'Punto de venta limpio.');
          limpiarCampos();
          idCliente = 0;
          carrito(idCliente);
        }
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        handleErrorResponse(jqXHR, textStatus, errorThrown);
      });
  });

});
