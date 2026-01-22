$(document).ready(function () {

    // Variables

    const notyf = new Notyf();
    const urlController = window.location.pathname;
    const urlPath = urlController.slice(0, urlController.lastIndexOf("/"));

    // Crear un botón para Nueva Sucursal
    let nuevaSucursalBtn = $('<button>', {
        text: 'Nueva Sucursal',
        id: 'btnNuevaSucursal',
        class: 'btn btn-primary btn-sm',
        'data-bs-toggle': 'modal',
        'data-bs-target': '#modal-nuevaSucursal',
    });

    // Funciones

    function initDataTable() {
        if (!$.fn.DataTable.isDataTable('#simple-table')) {
            $('#simple-table').DataTable({
                "language": {
                    "emptyTable": "No hay datos disponibles en la tabla"
                },
                "columnDefs": [
                    {
                        "targets": [2, 4],
                        "className": "text-center"
                    }
                ]
            });
            $('#table-show').fadeIn();
        }
    }

    function obtenerDateTime() {
        var fecha = new Date();

        var year = fecha.getFullYear();
        var month = ('0' + (fecha.getMonth() + 1)).slice(-2);  // Se suma 1 porque los meses van de 0 a 11
        var day = ('0' + fecha.getDate()).slice(-2);
        var hours = ('0' + fecha.getHours()).slice(-2);
        var minutes = ('0' + fecha.getMinutes()).slice(-2);
        var seconds = ('0' + fecha.getSeconds()).slice(-2);

        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
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

    function obtenerSucursales() {
        $.ajax({
            url: urlController + '/obtenerSucursalesInventario',
            method: 'GET'
        })
            .done(function (response) {
                let sucursales = response.respuesta;
                renderizarTabla(sucursales);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            });
    }

    function renderizarTabla(sucursales) {
        // Destruir la instancia de DataTable antes de actualizar la tabla
        if ($.fn.DataTable.isDataTable('#simple-table')) {
            $('#simple-table').DataTable().destroy();
        }

        $('#table-head').empty();

        const tableHead = `
            <tr role="row">
                <th>#</th>
                <th>Sucursal</th>
                <th>Cantidad actual</th>
                <th>Tipo de Permiso</th>
                <th>&nbsp;</th>
            </tr>
        `;

        $('#table-head').html(tableHead);
        $('#table-body').empty();

        if (Array.isArray(sucursales) && sucursales.length > 0) {
            sucursales.forEach(function (sucursal, index) {
                const sucursalData = {
                    indice: parseInt(index + 1),
                    id_sucursal: parseInt(sucursal.id_sucursal),
                    nombre: sucursal.nombre,
                    permiso: parseInt(sucursal.permiso),
                    stock: sucursal.total_stock,
                    registro: formatearFecha(sucursal.registro)
                };

                let permiso = '';

                switch (sucursalData.permiso) {
                    case 1:
                        permiso = 'Administrador';
                        break;
                    case 2:
                        permiso = 'Cajero / Vendedor';
                        break;
                    default:
                        permiso = 'Operario';
                        break;
                }

                let stockActual = sucursalData.stock < 1 || sucursalData.stock == NaN ? '<span class="text-danger font-weight-bold">Sin existencias</span>' : '<span class="font-weight-bold">' + sucursalData.stock + '</span>';

                const filaSucursal = `
                    <tr data-id="${sucursalData.id_sucursal}">
                        <td>${sucursalData.indice}</td>
                        <td class="font-weight-bold">${sucursalData.nombre}</td>
                        <td class="inventario" style="cursor: pointer">${stockActual}</td>
                        <td>${permiso}</td>
                        <td class="editar" data-sucursal='${JSON.stringify(sucursalData)}'>
                            <span class="badge badge-primary pt-1 px-3" style="cursor: pointer">Ver / Editar</span>
                        </td>
                    </tr>
                    `;
                $('#table-body').append(filaSucursal);
            });

            // Obtener los datos del sucursal
            $('.editar').off('click').on('click', function () {
                const sucursal = $(this).data('sucursal');

                editarSucursal(sucursal);
            });

            $('.inventario').off('click').on('click', function () {
                let trElement = $(this).closest('tr');
                let idSucursal = trElement.data('id');

                verInventario(idSucursal);
            });

            initDataTable();
        }
    };

    function editarSucursal(sucursal) {

        $('#id-sucursal').val(sucursal.id_sucursal);
        $('#nombre-sucursal').val(sucursal.nombre);
        $("#permiso-sucursal").val(sucursal.permiso).trigger('change');
        $('#registro-sucursal').val(sucursal.registro);

        $('#modal-editarSucursal').modal('show');
    }

    function verInventario(idSucursal) {
        $.ajax({
            url: urlPath + '/inventarioproductos/obtenerStockBySucursal/' + idSucursal,
            type: 'GET'
        })
            .done(function (response) {
                if (response.success) {
                    let inventario = response.respuesta;
                    console.log(inventario);
                    return;
                    // Limpiar el contenido actual de las tablas en el modal
                    $('#infoProducto tbody').empty();
                    $('#infoSucursal tbody').empty();

                    // Agregar información del producto a la primera tabla
                    productoRow = '<tr>' +
                        '<td>' + inventario[0].codigo + '</td>' +
                        '<td class="text-center">' + inventario[0].nombre_producto + '</td>' +
                        '</tr>';
                    $('#infoProducto tbody').append(productoRow);

                    // Agregar información de inventario a la segunda tabla
                    $.each(inventario, function (index, sucursal) {
                        stockCell = sucursal.stock <= 0 ? '<span class="badge badge-danger pt-1 px-3">Sin existencias</span>' : sucursal.stock;

                        sucursalRow = '<tr>' +
                            '<td>' + sucursal.nombre_sucursal + '</td>' +
                            '<td class="text-center">' + stockCell + ' </td>' +
                            '</tr>';
                        $('#infoSucursal tbody').append(sucursalRow);
                    });

                    // Mostrar el modal
                    $('#modal-inventarioProducto').modal('show');
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:' + textStatus + ' ' + errorThrown);
            });
    }

    function guardarSucursal(sucursal) {
        $.ajax({
            url: urlController + '/guardarSucursal',
            method: 'POST',
            data: sucursal
        })
            .done(function (response) {
                // Manejar la respuesta del servidor
                if (response.success) {
                    showMessage('success', 'La sucursal se guardó correctamente.');
                    obtenerSucursales();
                } else {
                    showMessage('error', 'Ocurrió un problema y no se logró guardar la sucursal.');
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            });
    }

    // Acciones

    $('#btn-header').append(nuevaSucursalBtn);

    $('#guardar-cambios').on('click', function () {
        let id_sucursal = $('#id-sucursal').val();
        let nombre = $('#nombre-sucursal').val();
        let permiso = $('#permiso-sucursal').val();

        // Crear un objeto con los datos a enviar
        let sucursal = {
            id_sucursal: parseInt(id_sucursal),
            nombre: nombre,
            permiso: parseInt(permiso)
        };

        $("#modal-editarSucursal").modal('hide');

        guardarSucursal(sucursal);
    });

    nuevaSucursalBtn.on('click', function () {
        $('#nuevo-nombre-sucursal').val('');
        $('#nuevo-permiso-sucursal').val('');

        $('#modal-nuevaSucursal').modal('show');
    });

    $('#crear-sucursal').on('click', function () {
        let nombre = $('#nuevo-nombre-sucursal').val();
        let permiso = $('#nuevo-permiso-sucursal').val();

        const sucursal = {
            nombre: nombre,
            permiso: parseInt(permiso),
            registro: obtenerDateTime()
        };

        $("#modal-nuevaSucursal").modal('hide');

        guardarSucursal(sucursal);
    });

    obtenerSucursales();
});