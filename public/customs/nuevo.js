$(document).ready(function () {

    // ----------
    // Variables
    // ----------

    const notyf = new Notyf();
    const urlController = window.location.pathname;
    const urlPath = urlController.slice(0, urlController.lastIndexOf("/"));

    // ----------
    // Funciones Generales
    // ----------

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

    function handleErrorResponse(jqXHR, textStatus, errorThrown) {
        handleErrorResponse(jqXHR, textStatus, errorThrown);
    }

    function showMessage(type, contentMessage) {
        const commonOptions = {
            message: contentMessage,
            dismissible: true,
            duration: 2000,
            position: {
                x: 'center',
                y: 'top',
            },
        };

        const successOptions = {
            ...commonOptions,
            type: 'success',
        };

        if (type === 'success') {
            notyf.success(successOptions).on('dismiss', handleDismiss);
        } else if (type === 'error') {
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

    // ----------
    // Funciones 
    // ----------

    function guardarNuevoCliente(datosCliente) {
        $.ajax({
            url: `${urlPath}/clientes/crearNuevoCliente`,
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
                    showMessage('error', 'Cliente nuevo no fue guardado correctamente.');
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                handleErrorResponse(jqXHR, textStatus, errorThrown);
            });
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
                            let estado;

                            switch (producto.estado) {
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

                            var fila = `<tr>
                                <td>${index + 1}</td>
                                <td data-label="Name">
                                    <div class="d-flex py-1 align-items-center">
                                        <div class="flex-fill">
                                            <div class="font-weight-bold">${producto.producto_codigo}</div>
                                            <div class="text-secondary">${producto.producto_nombre}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div>${formatCurrency(producto.precio)}</div>
                                </td>
                                <td class="text-center">${producto.cantidad}</td>
                                <td class="text-center">${estado}</td>
                                <td class="text-end">${formatCurrency(total)}</td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="javascript:void(0)" class="btn btn-info btn-sm opciones" data-idCarrito="${producto.id_carrito}" data-idCliente="${idCliente}" data-cantidad="${producto.cantidad}" data-estado="${producto.estado}" data-anotacion="${producto.nota}" data-fecha="${producto.fecha_entrega}">
                                            Opciones
                                        </a>
                                        <a href="javascript:void(0)" class="btn btn-danger btn-sm quitar" data-idCarrito="${producto.id_carrito}" data-idCliente="${idCliente}" data-idProducto="${producto.producto_id}">
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

                const subtotal = $("#table-cart td:nth-child(6)");
                const sumaSubtotal = subtotal.toArray().reduce((sum, td) => sum + parseFloat($(td).text().replace(/\D/g, '')) || 0, 0);

                const subtotalFormatted = formatCurrency(sumaSubtotal);
                $('#subtotal, #total').val(subtotalFormatted);

                actualizarTotal();
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                handleErrorResponse(jqXHR, textStatus, errorThrown);
            });
    };

    function actualizarTotal() {
        const subtotal = parseInt($('#subtotal').val().replace(/\D/g, ''), 10) || 0;
        const descuento = parseInt($('#descuento').val().replace(/\D/g, ''), 10) || 0;

        const total = subtotal - descuento;
        $('#total').val(formatCurrency(total));

        actualizarCambio();
    }

    function actualizarCambio() {
        const total = parseInt($('#total').val().replace(/\D/g, ''), 10) || 0;
        const pagoCon = parseInt($('#pagacon').val().replace(/\D/g, ''), 10) || 0;

        const cambio = pagoCon - total;
        $('#cambio').val(formatCurrency(cambio));
    }

    //****** ******/

    function obtenerDetallesProducto(idProducto) {
        $.ajax({
            url: `${urlController}/obtenerInventario`,
            type: 'POST',
            data: { id_producto: idProducto }
        })
            .done(function (response) {
                if (response.success) {
                    const { nombre, precio, stock, id_sucursal } = response.respuesta;

                    const cantidadProducto = stock < 1 ? 'Sin existencias' : stock;
                    $('#contenedorFechas').toggle(stock < 1);

                    $('#idSucursal').val(id_sucursal);
                    $('#nombreProducto').val(nombre);
                    $('#precioProducto').val(formatCurrency(precio));
                    $('#cantidadProducto').val(cantidadProducto);
                } else {
                    showMessage('error', 'No se encontró información del producto.');
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                handleErrorResponse(jqXHR, textStatus, errorThrown);
            });
    }

    function obrtenerStockSucursales(idProducto) {
        if (idProducto) {
            $.ajax({
                url: urlPath + '/inventarioProductos/obtenerStockByProducto/' + idProducto,
                type: 'GET'
            })
                .done(function (response) {
                    if (response.success) {
                        let datos = response.respuesta;
                        let tabla = $('#tabla-container').empty();

                        for (var i = 0; i < datos.length; i++) {
                            tabla += '<tr>';
                            tabla += '<td>' + datos[i].codigo + '</td>';
                            tabla += '<td class="text-center">' + datos[i].stock + '</td>';
                            tabla += '<td>' + datos[i].nombre_sucursal + '</td>';
                            tabla += '</tr>';
                        }

                        // Cerrar la tabla en el HTML
                        tabla += '</tbody></table>';

                        // Agregar la tabla al elemento con el id "tabla-container" (debes tener un elemento con ese id en tu HTML)
                        $('#tabla-container').html(tabla);
                        $('#modal-inventarioSucursales').modal('show');
                    } else {
                        showMessage('error', 'En ninguna sucursal hay existencias de este producto.');
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    handleErrorResponse(jqXHR, textStatus, errorThrown);
                });
        } else {
            showMessage('error', 'No se haseleccionado un producto.');
        }
    }

    function verificarEstado(cantidadProducto) {
        let estadoProducto;

        if (cantidadProducto > 0) {
            estadoProducto = 2;
        } else {
            estadoProducto = 0;
        }

        return estadoProducto;
    }

    function restarCantidadProducto() {
        const cantidadProductoElement = $('#cantidadProducto');
        let nuevaCantidad = parseInt(cantidadProductoElement.val(), 10);
        nuevaCantidad = isNaN(nuevaCantidad) ? 0 : nuevaCantidad;
        nuevaCantidad--;

        cantidadProductoElement.val(nuevaCantidad);
    }

    function agregarProductoAlCarrito(datosProducto) {
        restarCantidadProducto();
        datosProducto.estado = verificarEstado(datosProducto.cantidad);

        $.ajax({
            url: `${urlPath}/carrito/agregarProductoCarrito`,
            type: 'POST',
            data: datosProducto,
        })
            .done(function (response) {
                if (response.success) {
                    carrito(datosProducto.id_cliente);
                    showMessage('success', 'Producto agregado al carrito correctamente.');
                    $('#fechasProduccion').val($('#fechasProduccion option:first').val()).trigger('change.select2');
                } else {
                    showMessage('error', 'Ocurrió un problema y el producto no se agregó al carrito.');
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                handleErrorResponse(jqXHR, textStatus, errorThrown);
            });
    }



    //****** ******/


    // ----------
    // Acciones
    // ----------

    // Agregar nuevo cliente
    $('#nuevoCliente').off('click').on('click', function () {
        $('#cedula').val('');
        $('#nombre').val('');
        $('#apellidos').val('');
        $('#direccion').val('');
        $('#barrio').val('');
        $('#email').val('');
        $('#telefono').val('');

        $('#modal-nuevoCliente').modal('show');
    });

    $('#guardarCliente').off('click').on('click', function () {
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

    //****** ******/

    // Selecciona un cliente y se escanea el carrito para ver si hay productos asociados al cliente
    $('.escanearCarrito').on("change", function () {
        let idCliente = $('#idCliente').val();

        if (!idCliente) {
            showMessage('error', 'No se ha seleccionado un cliente.');
            return
        }

        carrito(idCliente);
    });

    // Obtenemos la informacion completa del producto
    $('#idProducto').on("change", function () {
        let productoSeleccionado = $(this).find(":selected");

        if (productoSeleccionado.val() > 0) {
            obtenerDetallesProducto(productoSeleccionado.val());
        }
    });

    // Obtenemos el stock del producto existente en otras sucursales
    $('#mostrarSucrsales').off('click').on('click', function () {
        let idProducto = $('#idProducto').val();

        obrtenerStockSucursales(idProducto);
    });

    $('#agregarAlCarrito').off('click').on("click", function () {
        const idCliente = parseInt($('#idCliente').val());
        const idSucursal = $('#idSucursal').val();
        const idProducto = $('#idProducto').val();
        let cantidadProducto = $('#cantidadProducto').val();
        const precioProducto = parseInt($('#precioProducto').val().replace(/[^0-9-]/g, '') || 0);
        const idFecha = $('#fechaEntrega').val();

        if (!idCliente) {
            showMessage('error', 'No se ha seleccionado un cliente.');
            return;
        }

        if (!idFecha) {
            showMessage('error', 'No se ha seleccionado una fecha de entrega.');
            return;
        }

        if (cantidadProducto == 'Sin existencias') {
            cantidadProducto = 0;
        }

        if (verificarEstado(cantidadProducto)) {
            const datosProducto = {
                id_sucursal: parseInt(idSucursal),
                id_producto: parseInt(idProducto),
                cantidad: 1,
                precio: precioProducto,
                estado: parseInt(estadoProducto),  // Asegúrate de definir 'estadoProducto' antes de usarlo
                nota: "",
                fecha_entrega: idFecha === "" ? null : idFecha,
                id_cliente: idCliente
            };

            agregarProductoAlCarrito(datosProducto);
        }
    });

    //****** ******/

    $('#guardarOpciones').on('click', function () {
        const estadoActualStr = $('#estadoActual').val();
        const clienteId = $('#clienteId').val();
        const fechaActual = $('#fechaActual').val();
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
            default:
                estadoActual = null; // Handle unexpected state values
        }

        const datosOpciones = {
            idCarrito: $('#carritoId').val(),
            estado: $('#nuevoEstado').val() === "" ? estadoActual : $('#nuevoEstado').val(),
            cantidad: $('#cantidadActual').val(),
            nota: $('#anotacionActual').val(),
            fecha_entrega: nuevaFechaEntrega !== "" ? nuevaFechaEntrega : fechaActual
        };

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
})