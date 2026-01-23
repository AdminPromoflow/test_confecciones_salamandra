$(document).ready(function () {

    // Variables
    // *****************************

    let dataVenta = {};
    const notyf = new Notyf();
    const urlController = window.location.pathname;
    const urlPath = urlController.slice(0, urlController.lastIndexOf("/"));

    // Funciones
    // *****************************

    function formatCurrency(value) {
        return parseFloat(value).toLocaleString('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    function formatDate(dateString, time) {
        let date = new Date(dateString);
        let day = date.getDate();
        let month = date.getMonth() + 1;
        let year = date.getFullYear();
        let hora = '';

        if (time) {
            let hours = date.getHours();
            let minutes = date.getMinutes();
            let seconds = date.getSeconds();
            let ampm = hours >= 12 ? 'pm' : 'am';

            hours = hours % 12;
            hours = hours ? hours : 12; // handle midnight

            // Utilizamos métodos específicos para obtener la hora en la zona horaria local
            hours = date.toLocaleTimeString('es-CO', { hour: 'numeric', hour12: true });
            hora = ' - ' + hours + ':' + minutes + ':' + seconds + ' ' + ampm;
        }

        let formattedDate = day + '/' + month + '/' + year + hora;

        return formattedDate;
    }

    function showMessage(type, contentMessage) {
        const notyfOptions = {
            message: contentMessage,
            dismissible: true,
            duration: 2000,
            position: {
                x: 'center',
                y: 'top',
            },
        };

        const notyfInfo = {
            message: contentMessage,
            dismissible: true,
            duration: 2000,
            position: {
                x: 'center',
                y: 'top',
            },
            type: 'info',
            background: 'blue',
        };

        if (type == 'success') {
            notyf.success(notyfOptions).on('dismiss', ({ target, event }) => foobar.retry());
        } else if (type == 'error') {
            notyf.error(notyfOptions).on('dismiss', ({ target, event }) => foobar.retry());
        }

    }

    function buscarVenta(idVenta) {
        $.ajax({
            url: urlPath + '/ventas/obtenerVentaById',
            method: 'POST',
            data: { id_venta: idVenta }
        })
            .done(function (response) {
                if (response.success) {

                    $('#enlaceContainer').empty();

                    dataVenta.id_sucursal = response.respuesta.id_sucursal;

                    vistaVenta(response.respuesta);
                    obtenerDetalle(response.respuesta.id_venta);

                    $('html, body').animate({
                        scrollTop: $('#contenedor-venta').offset().top
                    }, 1000);
                } else {
                    showMessage('error', 'No se encontró la venta buscada.');
                    $('#numero-venta').val('');
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            });
    }

    function buscarCliente(idCliente) {
        $('#numero-venta').val('');

        $.ajax({
            url: urlPath + '/ventas/obtenerVentaByCliente',
            method: 'POST',
            data: { id_cliente: idCliente }
        })
            .done(function (response) {
                if (response.success) {
                    $('#content-body').empty();
                    $('#contenedor-ventas').hide();
                    $('#contenedor-venta').hide();

                    dataVenta.id_sucursal = response.respuesta.id_sucursal;

                    vistaVentas(response.respuesta);

                    $('html, body').animate({
                        scrollTop: $('#contenedor-ventas').offset().top
                    }, 1000);
                } else {
                    showMessage('error', 'No se encontró la venta buscada.');
                    $('#cliente-venta').val('');
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            });
    }

    function vistaVenta(resultado) {
        $('#fecha-venta').text(formatDate(resultado.registro, false));
        $('#factura-venta').text(resultado.id_venta);
        $('#sucursal-venta').text('Sucursal ' + resultado.nombre_sucursal);
        $('#tipo-venta').text(resultado.tipoventa == 1 ? 'Venta' : 'Apartado');
        switch (parseInt(resultado.estado)) {
            case 1:
                $('#estado-venta').html('<span class="badge badge-success pt-1 px-3">Pagada</span>');
                break;
            case 2:
                $('#estado-venta').html('<span class="badge badge-danger pt-1 px-3">Pendiente</span>');
                break;
            case 3:
                $('#estado-venta').html('<span class="badge badge-warning pt-1 px-3">Cancelada</span>');
                break;
            default:
                break;
        }
        $('#total-venta').text(formatCurrency(resultado.total));
        $('#descuento-venta').text(formatCurrency(resultado.descuento));
        $('#abono-venta').html(resultado.saldo > 0 ? `<span id="abonar" class="badge badge-primary pt-1 px-3" data-venta='${JSON.stringify(resultado)}' style="cursor:pointer;">+ Abono</span> ${formatCurrency(resultado.abono)}` : formatCurrency(resultado.abono));
        $('#saldo-venta').text(formatCurrency(resultado.saldo));
        $('#entregado-venta').text(formatCurrency(resultado.entregado));
        $('#cajero-venta').text(resultado.usuario);

        $('#nombre-cliente').text(resultado.cliente_nombre + resultado.cliente_apellidos);
        $('#direccion-cliente').text(resultado.cliente_direccion);
        $('#telefono-cliente').text(resultado.cliente_telefono);
        $('#email-cliente').text(resultado.cliente_email);

        $('#nota-venta').text(resultado.nota);
        $('.titulo-venta').text("Detalle de la venta #" + $('#factura-venta').text());
        $('#contenedor-venta').show();
    }

    // Mostrando todas las ventas asociadas a un cliente especifico
    function vistaVentas(resultado) {
        // Limpiar la tabla antes de agregar nuevas filas
        $('#content-body').empty();

        $.each(resultado, function (index, venta) {
            let tipoventa = venta.tipoventa == 1 ? 'Venta' : 'Apartado';
            var row = "<tr>" +
                "<td>" + venta.id_venta + "</td>" +
                "<td>" + formatDate(venta.registro, true) + "</td>" +
                "<td>" + tipoventa + "</td>" +
                "<td>" + venta.nombre_sucursal + "</td>" +
                "</tr>";
            $('#content-body').append(row);
        });

        $('#contenedor-ventas').show();
    }

    function modalActualizar(idVenta, idDetalle, estado, precio, rolUsuario) {
        $('#id-venta').val(idVenta);
        $('#rolUsuario').val(rolUsuario);
        $('#id-detalle').val(idDetalle);
        $('#precio').val(precio);
        $('#anteriorEstado').val(estado);
        $('#estado-detalle').val(estado).trigger('change');

        $('#modal-actualizarEstado').modal('show');
    }

    // Solicitud de obtener detalle de venta
    function obtenerDetalle(idVenta) {
        $.ajax({
            url: urlPath + '/detalleventa/obtenerDetalle',
            method: 'POST',
            data: { id_venta: idVenta }
        })
            .done(function (response) {
                if (response.success) {
                    // Limpiar la tabla antes de agregar nuevas filas
                    $('#enlaceContainer').empty();

                    // Crear el enlace con el ID de venta
                    let enlace = $('<a>', {
                        class: 'btn btn-outline-dark btn-sm',
                        href: 'https://www.confeccionessalamandra.com/backend/ventas/imprimirTiquet/' + idVenta,
                        html: '<i class="feather icon-printer"></i> Imprimir factura',
                        target: '_blank'
                    });


                    // Agregar el enlace al contenedor
                    $('#enlaceContainer').append(enlace);

                    var detalles = [];
                    var data = response.respuesta;

                    for (var key in data) {
                        if (key !== 'rolUsuario') {
                            detalles[key] = data[key];
                        }
                    }

                    $('#table-head').empty();

                    const tableHead = `
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>&nbsp;</th>
                        </tr>
                    `;

                    $('#table-head').html(tableHead);
                    $('#table-body').empty();

                    if (Array.isArray(detalles) && detalles.length > 0) {
                        detalles.forEach(function (producto, index) {
                            const detalleData = {
                                id_detalle: parseInt(producto.id_detalle),
                                id_venta: parseInt(producto.id_venta),
                                id_producto: parseInt(producto.id_producto),
                                codigo: producto.codigo,
                                nombre: producto.nombre_producto,
                                cantidad: parseInt(producto.cantidad),
                                estado: parseInt(producto.estado),
                                precio_producto: producto.precio,
                                precio: formatCurrency(producto.precio),
                                fecha_entrega: formatDate(producto.fecha_entrega, false),
                                fecha_actualizada: formatDate(producto.fecha_actualizada, false)
                            };

                            let estadoBadge = '';
                            let estadoLabel = '';
                            let msgFecha = '';

                            switch (detalleData.estado) {
                                case 0:
                                    estadoBadge = 'badge-danger';
                                    estadoLabel = 'Pendiente';
                                    msgFecha = 'Entregar el ';
                                    break;
                                case 1:
                                    estadoBadge = 'badge-dark';
                                    estadoLabel = 'Urgente';
                                    msgFecha = 'Entregar el ';
                                    break;
                                case 2:
                                    estadoBadge = 'badge-primary';
                                    estadoLabel = 'Listo';
                                    msgFecha = 'Enlistado el ';
                                    break;
                                case 3:
                                    estadoBadge = 'badge-warning';
                                    estadoLabel = 'Bolsa';
                                    msgFecha = 'Embolsado el ';
                                    break;
                                case 4:
                                    estadoBadge = 'badge-success';
                                    estadoLabel = 'Entregado';
                                    msgFecha = 'Entregado el ';
                                    break;
                                case 5:
                                    estadoBadge = 'badge-default';
                                    estadoLabel = 'Cancelado';
                                    msgFecha = 'Cancelado el';
                                    break;
                                case 6:
                                    estadoBadge = 'badge-secondary';
                                    estadoLabel = 'Retorno';
                                    msgFecha = 'Retornado el ';
                                    break;
                                case 7:
                                    estadoBadge = 'badge-default';
                                    estadoLabel = 'Corte';
                                    break;
                            }

                            const estadoSpan = `<span class="badge ${estadoBadge} pt-1 px-3" style="cursor: pointer">${estadoLabel}</span>`;

                            let dataFecha = detalleData.estado >= 2 ? detalleData.fecha_actualizada : detalleData.fecha_entrega;

                            const filaDetalle = `
                                <tr data-id="${detalleData.id_detalle}">
                                    <td class="font-weight-bold">${detalleData.codigo}</td>
                                    <td class="">${detalleData.nombre}</td>
                                    <td class="">${detalleData.cantidad}</td>
                                    <td class="">${detalleData.precio}</td>
                                    <td class="actualizar-estado" data-id-venta="${detalleData.id_venta}" data-precio="${detalleData.precio_producto}" data-estado="${detalleData.estado}" data-id-producto="${detalleData.id_producto}"  data-rol="${data.rolUsuario}">${estadoSpan}</td>
                                    <td class="">${msgFecha} ${dataFecha}</td>
                                </tr>
                                `;
                            $('#table-body').append(filaDetalle);
                        });

                        // Agregar evento de clic a la columna de estado
                        $('.actualizar-estado').off('click').on('click', function () {
                            // Encuentra el elemento tr más cercano
                            let trElement = $(this).closest('tr');
                            let idDetalle = trElement.data('id');
                            let idVenta = $(this).attr('data-id-venta');
                            let precio = $(this).attr('data-precio');
                            let estado = $(this).attr('data-estado');
                            let rolUsuario = $(this).attr('data-rol');
                            dataVenta.id_producto = $(this).attr('data-id-producto');

                            if (estado < 5) {
                                modalActualizar(idVenta, idDetalle, estado, precio, rolUsuario);
                            }

                        });
                    } else {
                        console.log('error');
                    }
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            });
    }

    function actualizarEstado(idVenta, estadoVenta, idDetalle, anteriorEstado, nuevoEstado, precio, rolUsuario) {
        // Validar si el usuario tiene permisos para realizar la acción
        if (!validarPermisos(rolUsuario, anteriorEstado, nuevoEstado)) {
            showMessage('error', 'No tienes permisos para realizar esta acción.');
            return; // Detener la ejecución si no tiene permisos
        }
    
        // Enviar solicitud AJAX al servidor
        $.ajax({
            url: urlPath + '/detalleventa/actualizarEstadoDetalle',
            method: 'POST',
            data: {
                id_venta: idVenta,
                estadoVenta: estadoVenta,
                id_detalleventa: idDetalle,
                anteriorEstado: anteriorEstado,
                nuevoEstado: nuevoEstado,
                precio: precio,
                usuarioRol: rolUsuario // Enviar el rol del usuario al servidor
            }
        })
        .done(function (response) {
            if (response.success && response.respuesta) {
                // Actualización exitosa
                // actualizarInterfazUsuario(idDetalle, nuevoEstado);
                showMessage('success', response.mensaje || 'Estado del producto se actualizó correctamente.');
    
                // Limpiar campos del formulario
                $('#id-detalle').val('');
                $('#anteriorEstado').val('');
                $('#modal-actualizarEstado').modal('hide');
    
                // Si se cancela o retorna, actualizar el inventario
                if (nuevoEstado == 5 || nuevoEstado == 6) {
                    actualizarInventario(dataVenta);
                }
            } else {
                // Error en la respuesta del servidor
                showMessage('error', response.mensaje || 'No se pudo actualizar el estado del producto.');
            }
            buscarVenta(idVenta);
            obtenerDetalle(idVenta);
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            showMessage('error', 'Error al realizar la solicitud AJAX.');
        });
    }
    
    // Función para validar permisos según el rol del usuario
    function validarPermisos(rolUsuario, anteriorEstado, nuevoEstado) {
        // Solo el administrador puede cancelar (5) o retornar (6)
        if (nuevoEstado == 5 || nuevoEstado == 6) {
            if (rolUsuario == 1) { // Solo administrador
                // Validar estados permitidos para cancelar o retornar
                if (nuevoEstado == 5) {
                    // Cancelar: solo desde pendiente (0) o urgente (1)
                    return [0, 1].includes(anteriorEstado);
                } else if (nuevoEstado == 6) {
                    // Retornar: solo desde listo (2), en bolsa (3) o entregado (4)
                    return [2, 3, 4].includes(anteriorEstado);
                }
            }
            return false; // Si no es administrador, no tiene permisos
        }
    
        // Validar acciones permitidas para el cajero
        if (rolUsuario == 2) {
            // Cajero solo puede cambiar entre estados 2 (Listo), 3 (Bolsa) y 4 (Entregado)
            const estadosPermitidosCajero = [2, 3, 4];
            return estadosPermitidosCajero.includes(anteriorEstado) && estadosPermitidosCajero.includes(nuevoEstado);
        }
    
        // Si no es administrador ni cajero, no tiene permisos
        return false;
    }

    // Función auxiliar para obtener el nombre del estado
    function obtenerNombreEstado(estado) {
        const estados = {
            0: 'Pendiente',
            1: 'Urgente',
            2: 'Listo',
            3: 'En Bolsa',
            4: 'Entregado',
            5: 'Cancelado',
            6: 'Retorno'
        };
        return estados[estado] || 'Desconocido';
    }

    // Función para actualizar el inventario
    function actualizarInventario(dataVenta) {
        $.ajax({
            url: urlPath + '/inventarioproductos/actualizarStock',
            method: 'POST',
            data: { id_producto: parseInt(dataVenta.id_producto), id_sucursal: parseInt(dataVenta.id_sucursal) }
        })
        .done(function (response) {
            if (response.success) {
                showMessage('success', 'El inventario se actualizó correctamente.');
            } else {
                showMessage('error', response.mensaje || 'No se pudo actualizar el inventario.');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            showMessage('error', 'Error al actualizar el inventario.');
        });
    }

    function guardarAbono(abonoData) {
        $('#id-venta').val('');
        $('#id-cliente').val('');
        $('#saldo').val('');
        $('#valor-abono').val('');
        $('#mediopago-abono').val('').trigger('change');
        $('#nota-abono').val('');

        $('#modal-registrarAbono').modal('hide');

        $.ajax({
            url: `${urlPath}/abonos/guardarAbono`,
            method: 'POST',
            data: abonoData
        })
            .done(function (response) {
                if (response.success) {
                    showMessage('success', 'Abono registrado a la venta correctamente.');
                    if (abonoData.nuevoEstado == 1) {
                        // Cambiar la clase y el texto del span dentro de la celda
                        $('#estado-venta span').removeClass('badge-danger').addClass('badge-success').text('Pagada');
                    }
                    $('#abono-venta').html(abonoData.nuevoSaldo > 0 ? `<span id="abonar" class="badge badge-primary pt-1 px-3" style="cursor:pointer;">+ Abono</span> ${formatCurrency(abonoData.nuevoAbono)}` : formatCurrency(abonoData.nuevoAbono));
                    $('#saldo-venta').text(formatCurrency(abonoData.nuevoSaldo));
                }


            })

            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            });

    }


    // Acciones
    // *****************************

    $('#buscar-venta').off('click').on('click', function () {
        var numeroVenta = parseInt($('#numero-venta').val());

        buscarVenta(numeroVenta);
    });

    $('#buscar-cliente').off('click').on('click', function () {
        var clienteVenta = parseInt($('#cliente-venta').val());

        buscarCliente(clienteVenta);
    });

    $('#aceptar').off('click').on('click', function () {
        // Mostrar confirmación al usuario
        if (!confirm("¿Está seguro de cambiar el estado de este producto?")) {
            return; // Si el usuario cancela, no hacer nada
        }
        let rolUsuario = parseInt($('#rolUsuario').val());
        let estadoVenta = $('#estado-venta').text() == 'Pagada' ? 1 : 2;
        let idVenta = parseInt($('#id-venta').val());
        let precio = parseInt($('#precio').val());
        let idDetalle = parseInt($('#id-detalle').val());
        let anteriorEstado = parseInt($('#anteriorEstado').val());
        let nuevoEstado = parseInt($('#estado-detalle').val());

        actualizarEstado(idVenta, estadoVenta, idDetalle, anteriorEstado, nuevoEstado, precio, rolUsuario);
    });

    $('#estado-detalle').on('change', function () {
        let estado = parseInt($('#anteriorEstado').val());
        let nuevoEstado = parseInt($(this).val());
        let rolUsuario = parseInt($('#rolUsuario').val());

        // Definir las transiciones permitidas para cada rol
        const transicionesPermitidas = {
            1: [0, 1, 2, 3, 4, 5, 6], // Administrador
            2: { // Cajero
                0: [1, 5],
                1: [0, 5],
                2: [3],
                3: [4]
            }
        };

        // Verificar si el rol tiene permitida la transición
        if (!(rolUsuario == 1 && transicionesPermitidas[1].includes(nuevoEstado)) &&
            !(rolUsuario == 2 && transicionesPermitidas[2][estado] && transicionesPermitidas[2][estado].includes(nuevoEstado))) {
            $('#mensaje').show();
            $('#aceptar').hide();
            return;
        }

        // Reiniciar estado de los elementos
        $('#mensaje').hide();
        $('#aceptar').hide();

        // Mostrar botón de aceptar si la transición es válida
        $('#aceptar').show();
    });

    $('#abono-venta').on('click', '#abonar', function () {
        // Obtiene el valor del atributo 'data-venta'
        const dataVenta = $(this).data('venta');

        $('#id-venta').val(dataVenta.id_venta);
        $('#id-cliente').val(dataVenta.id_cliente);
        $('#abonado').val(dataVenta.abono);
        $('#saldo').val(formatCurrency(dataVenta.saldo));

        $('#modal-registrarAbono').modal('show');
    });

    $('#guardar-abono').on('click', function () {
        let abonoData = {};
        let saldo = parseInt($('#saldo').val().replace(/\D/g, '') || 0);
        let abonado = parseInt($('#abonado').val());

        abonoData.id_venta = parseInt($('#id-venta').val());
        abonoData.id_cliente = parseInt($('#id-cliente').val());
        abonoData.abono = parseInt($('#valor-abono').val());
        abonoData.nota = $('#nota-abono').val();
        abonoData.mediopago = $('#mediopago-abono').val();

        if (abonoData.abono > saldo) return

        abonoData.nuevoSaldo = saldo - abonoData.abono;

        if (abonoData.nuevoSaldo == 0) {
            abonoData.nuevoEstado = 1;
        }

        abonoData.nuevoAbono = abonado + abonoData.abono;

        guardarAbono(abonoData);

        let urlImpresion = urlPath + '/abonos/imprimirTiquet/' + abonoData.id_venta;
        let ventanaImpresion = window.open(urlImpresion, '_blank');
        ventanaImpresion.onload = function () {
            ventanaImpresion.print();
        };
    });
})