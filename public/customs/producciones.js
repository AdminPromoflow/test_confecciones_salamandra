$(document).ready(function () {

    // Variables

    const notyf = new Notyf();
    const urlController = window.location.pathname;
    const urlPath = urlController.slice(0, urlController.lastIndexOf("/"));

    let metodo = $("#metodo").val();

    // Funciones

    if (metodo == "requisiciones") {
        obtenerRequisicionesPendientes();
    } else {
        obtenerProduccionPendiente();
    }

    function initdataTable() {
        if (!$.fn.DataTable.isDataTable('#simple-table')) {
            $('#simple-table').DataTable({
                "language": {
                    "emptyTable": "No hay datos disponibles en la tabla"
                },
                "columnDefs": [
                    {
                        "targets": [0], // Indica la primera columna
                        "className": "text-center" // Clase para centrar el contenido
                    }
                ],
                "order": [
                    [0, 'asc'] // Orden ascendente para la columna 3
                ]
            });

            $('#table-show').fadeIn();
        }
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

        if (type === 'success') {
            notyf.success(notyfOptions).on('dismiss', ({ target, event }) => foobar.retry());
        } else if (type === 'error') {
            notyf.error(notyfOptions).on('dismiss', ({ target, event }) => foobar.retry());
        }
    }

    function formatearFecha(fechaActual, mostrarHora = false) {
        var opciones = {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        };

        if (mostrarHora) {
            opciones.hour = '2-digit';
            opciones.minute = '2-digit';
            opciones.second = '2-digit';
            opciones.timeZone = 'America/Bogota';
        }

        var fechaFormateada = new Date(fechaActual);
        return fechaFormateada.toLocaleString('es-CO', opciones);
    }

    function obtenerProduccionPendiente() {
        $.ajax({
            url: urlPath + '/obtenerProduccionPendiente',
            type: 'GET'
        })
            .done(function (response) {
                let produccion = response.respuesta;
                renderizarTabla(produccion);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            });
    }

    function obtenerRequisicionesPendientes() {
        $.ajax({
            url: urlPath + '/obtenerRequisicionesPendientes',
            type: 'GET'
        })
            .done(function (response) {
                let requisiciones = response.respuesta;
                renderizarTablaRequisiciones(requisiciones);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            });
    }


    function renderizarTablaRequisiciones(requisiciones) {
        // Destruir la instancia de DataTable antes de actualizar la tabla
        if ($.fn.DataTable.isDataTable('#simple-table')) {
            $('#simple-table').DataTable().destroy();
        }
        $('#table-head').empty();

        const tableHead = `
            <tr>
                <th>#</th>
                <th>Fecha de entrega</th>
                <th>Código</th>
                <th class="text-center">Cantidad</th>
                <th>&nbsp;</th>
            </tr>
        `;

        $('#table-head').html(tableHead);
        $('#table-body').empty();

        if (Array.isArray(requisiciones) && requisiciones.length > 0) {
            requisiciones.forEach(function (item, index) {
                const requisicionData = {
                    indice: parseInt(index + 1),
                    id_requisicion: parseInt(item.id_requisicion),
                    id_producto: parseInt(item.id_producto),
                    id_sucursal: parseInt(item.id_sucursal),
                    codigo: item.codigo,
                    nombre: item.nombre,
                    cantidad: parseInt(item.cantidad),
                    fecha: formatearFecha(item.fecha)
                };


                const filaRequisicion = `
                    <tr data-id="${requisicionData.indice}">
                        <td class="">${requisicionData.id_requisicion}</td>                        
                        <td class="">${requisicionData.fecha}</td>
                        <td class="font-weight-bold">${requisicionData.codigo} - ${requisicionData.nombre}</td>
                        <td class="text-center">${requisicionData.cantidad}</td>
                        <td class="actualizar" data-requisicion='${JSON.stringify(requisicionData)}'>
                            <span class="badge badge-primary pt-1 px-3 badge-custom" style="cursor: pointer">Pasar a Listo</span>
                        </td>
                    </tr>
                    `;
                $('#table-body').append(filaRequisicion);
            });

            // Obtener los datos del produccion
            $('.actualizar').off('click').on('click', function () {

                // Mostrar confirmación al usuario
                if (!confirm("¿Está seguro de cambiar el estado de esta requisición?")) {
                    return; // Si el usuario cancela, no hacer nada
                }

                const requisicion = $(this).data('requisicion');

                actualizarEstadoRequisicion(requisicion);
            });

            initdataTable();
        }
    };

    function actualizarEstadoRequisicion(requisicion) {

        requisicion.nuevoEstado = 1; // Pasa a listo

        $.ajax({
            url: urlPath + '/actualizarEstadoRequisicion',
            type: 'POST',
            data: requisicion
        })
            .done(function (response) {
                if (response.success) {
                    // obtenerProduccionPendiente();

                    // Eliminar la fila correspondiente al producto actualizado
                    $(`#table-body tr[data-id="${requisicion.indice}"]`).remove();
                    showMessage('success', 'Estado del producto se actualizó correctamente.');
                } else {
                    showMessage('error', 'No se actualizó el estado del preducto.');
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            });
    }
       

    function renderizarTabla(produccion) {
        // Destruir la instancia de DataTable antes de actualizar la tabla
        if ($.fn.DataTable.isDataTable('#simple-table')) {
            $('#simple-table').DataTable().destroy();
        }
        $('#table-head').empty();

        const tableHead = `
            <tr>
                <th>Factura</th>
                <th>Código</th>
                <th>Producto</th>
                <th>Nota</th>
                <th>Fecha entrega</th>
                <th>&nbsp;</th>
            </tr>
        `;

        $('#table-head').html(tableHead);
        $('#table-body').empty();

        if (Array.isArray(produccion) && produccion.length > 0) {
            produccion.forEach(function (producto, index) {
                const produccionData = {
                    indice: parseInt(index + 1),
                    id_produccion: parseInt(producto.id_produccion),
                    id_detalleventa: parseInt(producto.id_detalle),
                    codigo: producto.codigo_producto,
                    nombre: producto.nombre_producto,
                    cantidad: parseInt(producto.cantidad),
                    estado: parseInt(producto.estado),
                    nota: producto.nota,
                    id_venta: parseInt(producto.id_venta),
                    id_producto: parseInt(producto.id_producto),
                    id_sucursal: parseInt(producto.id_sucursal),
                    fecha_entrega: formatearFecha(producto.fecha_entrega)
                };


                const filaProducto = `
                    <tr data-id="${produccionData.indice}">
                        <td class="">${produccionData.id_venta}</td>
                        <td class="font-weight-bold">${produccionData.codigo}</td>
                        <td class="">${produccionData.nombre}</td>
                        <td class="">${produccionData.nota}</td>
                        <td class="">${produccionData.fecha_entrega}</td>
                        <td class="actualizar" data-produccion='${JSON.stringify(produccionData)}'>
                            <span class="badge badge-primary pt-1 px-3 badge-custom" style="cursor: pointer">Pasar a Listo</span>
                        </td>
                    </tr>
                    `;
                $('#table-body').append(filaProducto);
            });

            // Obtener los datos del produccion
            $('.actualizar').off('click').on('click', function () {

                // Mostrar confirmación al usuario
                if (!confirm("¿Está seguro de cambiar el estado de este producto?")) {
                    return; // Si el usuario cancela, no hacer nada
                }

                const produccion = $(this).data('produccion');

                actualizarEstado(produccion);
            });

            initdataTable();
        }
    };

    function actualizarEstado(produccion) {

        produccion.nuevoEstado = 2; // Pasa a listo

        $.ajax({
            url: urlPath + '/actualizarEstado',
            type: 'POST',
            data: produccion
        })
            .done(function (response) {
                if (response.success) {
                    // obtenerProduccionPendiente();

                    // Eliminar la fila correspondiente al producto actualizado
                    $(`#table-body tr[data-id="${produccion.indice}"]`).remove();
                    showMessage('success', 'Estado del producto se actualizó correctamente.');
                } else {
                    showMessage('error', 'No se actualizó el estado del preducto.');
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            });
    }

    // Acciones

    $('#exportar-pendientes').on('click', function (event) {
        $.ajax({
            url: urlPath + '/exportarPendientes',
            type: 'POST'
        })
            .done(function (response) {

                if (response) {
                    showMessage('success', 'Datos exportados correctamente.');
                    var blob = new Blob([response], { type: 'text/csv' });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'Produccion_Pendiente.csv';
                    link.click();
                } else {
                    showMessage('error', 'No se exportó ningún dato.');
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            });
    });

    $('#exportar-listos').on('click', function (event) {
        $.ajax({
            url: urlPath + '/exportarListos',
            type: 'POST'
        })
            .done(function (response) {
                if (response) {
                    showMessage('success', 'Datos exportados correctamente.');
                    var blob = new Blob([response], { type: 'text/csv' });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'Produccion_Lista.csv';
                    link.click();
                } else {
                    showMessage('error', 'No se exportó ningún dato.');
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            });
    });
});