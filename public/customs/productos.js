
// Variables

const notyf = new Notyf();
const urlController = window.location.pathname;
const urlPath = urlController.slice(0, urlController.lastIndexOf("/"));

let Categorias;
let Subcategorias;
let Instituciones;
let Proveedores;

// Crear un botón para Nuevo Producto
let nuevoProductoBtn = $('<button>', {
    text: 'Nuevo Producto',
    id: 'btnNuevoProducto',
    class: 'btn btn-primary btn-sm',
    'data-bs-toggle': 'modal',
    'data-bs-target': '#modal-nuevoProducto',
});

// Funciones

function initdataTable() {
    if (!$.fn.DataTable.isDataTable('#simple-table')) {
        $('#simple-table').DataTable({
            "language": {
                "emptyTable": "No hay datos disponibles en la tabla"
            },
            "columnDefs": [
                {
                    "targets": [3, 4, 5], // Indica la primera columna
                    "className": "text-center" // Clase para centrar el contenido
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

function formatCurrency(value) {
    return parseFloat(value).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0, maximumFractionDigits: 0 });
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

    if (type == 'success') {
        notyf.success(notyfOptions).on('dismiss', ({ target, event }) => foobar.retry());
    } else if (type == 'error') {
        notyf.error(notyfOptions).on('dismiss', ({ target, event }) => foobar.retry());
    }
}

function obtenerProductos() {
    $.ajax({
        url: urlController + '/obtenerProductos',
        method: 'GET'
    })
        .done(function (response) {
            let productos = response.respuesta;
            renderizarTabla(productos);
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
        });
}

async function obtenerDatosAsync() {
    try {
        Productos = await obtenerDatos('/productos/obtenerProductos');
        Categorias = await obtenerDatos('/categorias/obtenerCategorias');
        Subcategorias = await obtenerDatos('/subcategorias/obtenerSubcategorias');
        Instituciones = await obtenerDatos('/instituciones/obtenerInstituciones');
        Proveedores = await obtenerDatos('/proveedores/obtenerProveedoresByTipo/1');
    } catch (error) {
        console.log(error);
    }
}

function obtenerDatos(url) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: urlPath + url,
            method: 'GET'
        })
            .done(function (response) {
                if (response.success) {
                    resolve(response.respuesta);
                } else {
                    reject('No se logró obtener los datos.');
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                reject('Error en la solicitud AJAX:' + textStatus + ' ' + errorThrown);
            });
    });
}

function renderizarTabla(productos) {
    // Destruir la instancia de DataTable antes de actualizar la tabla
    if ($.fn.DataTable.isDataTable('#simple-table')) {
        $('#simple-table').DataTable().destroy();
    }
    $('#table-head').empty();

    const tableHead = `
        <tr>
            <th>#</th>
            <th>Código</th>
            <th>Producto</th>
            <th>Cantidad actual</th>
            <th>Estado</th>
            <th>&nbsp;</th>
        </tr>
    `;

    $('#table-head').html(tableHead);
    $('#table-body').empty();

    if (Array.isArray(productos) && productos.length > 0) {
        productos.forEach(function (producto, index) {
            const productoData = {
                indice: parseInt(index + 1),
                id_producto: parseInt(producto.id_producto),
                barras: producto.barras == '' ? '' : parseInt(producto.barras),
                codigo: producto.codigo,
                nombre: producto.nombre_producto,
                descripcion: producto.descripcion,
                id_categoria: parseInt(producto.id_categoria),
                id_subcategoria: parseInt(producto.id_subcategoria),
                id_institucion: parseInt(producto.id_institucion),
                id_proveedor: parseInt(producto.id_proveedor),
                categoria: producto.nombre_categoria,
                subcategoria: producto.nombre_subcategoria,
                institucion: producto.nombre_institucion,
                proveedor: producto.nombre_proveedor,
                costo: formatCurrency(producto.costo),
                precio: formatCurrency(producto.precio),
                stock: parseInt(producto.cantidad_total),
                estado: parseInt(producto.estado),
                registro: formatearFecha(producto.registro)
            };

            let estadoBadge = '';
            let estadoLabel = '';

            switch (productoData.estado) {
                case 1:
                    estadoBadge = 'badge-success';
                    estadoLabel = 'Activo';
                    break;
                default:
                    estadoBadge = 'badge-danger';
                    estadoLabel = 'Inactivo';
                    break;
            }

            const estadoSpan = `<span class="badge ${estadoBadge} pt-1 px-3" style="cursor: pointer">${estadoLabel}</span>`;

            let stockActual = productoData.stock < 1 ? '<span class="text-danger font-weight-bold">Sin existencias</span>' : '<span class="font-weight-bold">' + productoData.stock + '</span>';

            const filaProducto = `
                <tr data-id="${productoData.id_producto}">
                    <td class="">${productoData.indice}</td>
                    <td class="font-weight-bold">${productoData.codigo}</td>
                    <td class="">${productoData.nombre}</td>
                    <td class="inventario" style="cursor: pointer">${stockActual}</td>
                    <td class="actualizar-estado" data-estado="${productoData.estado}">${estadoSpan}</td>
                    <td class="editar" data-producto='${JSON.stringify(productoData)}'>
                        <span class="badge badge-primary pt-1 px-3" style="cursor: pointer">Ver / Editar</span>
                    </td>
                </tr>
                `;
            $('#table-body').append(filaProducto);
        });

        // Obtener los datos del producto
        $('.editar').off('click').on('click', function () {
            const producto = $(this).data('producto');

            editarProducto(producto);
        });

        $('.inventario').off('click').on('click', function () {
            let trElement = $(this).closest('tr');
            let idProducto = trElement.data('id');

            verInventario(idProducto);
        });

        // Agregar evento de clic a la columna de estado
        $('.actualizar-estado').off('click').on('click', function () {
            // Encuentra el elemento tr más cercano
            let trElement = $(this).closest('tr');
            let idProducto = trElement.data('id');
            let estado = $(this).attr('data-estado');

            actualizarEstado(idProducto, estado);
        });

        initdataTable();
    }
};

function editarProducto(producto) {
    let selectCategoria = $("#categoria-producto");
    let selectSubcategoria = $("#subcategoria-producto");
    let selectInstituciones = $("#institucion-producto");
    let selectProveedores = $("#proveedor-producto");

    // Limpiar opciones existentes
    selectCategoria.empty();
    selectSubcategoria.empty();
    selectInstituciones.empty();
    selectProveedores.empty();

    // Recorrer categorías y agregarlas al select
    $.each(Categorias, function (index, categoria) {
        // Crear una opción para cada categoría
        let option = $("<option>").val(categoria.id_categoria).text(categoria.nombre);

        // Marcar la opción como seleccionada si coincide con la categoría del producto
        if (categoria.id_categoria == producto.id_categoria) {
            option.prop("selected", true);
        }

        // Agregar la opción al select
        selectCategoria.append(option);
    });

    $.each(Subcategorias, function (index, subcategoria) {
        // Crear una opción para cada categoría
        let option = $("<option>").val(subcategoria.id_subcategoria).text(subcategoria.nombre);

        // Marcar la opción como seleccionada si coincide con la categoría del producto
        if (subcategoria.id_subcategoria == producto.id_subcategoria) {
            option.prop("selected", true);
        }

        // Agregar la opción al select
        selectSubcategoria.append(option);
    });

    $.each(Instituciones, function (index, institucion) {
        // Crear una opción para cada categoría
        let option = $("<option>").val(institucion.id_institucion).text(institucion.nombre);

        // Marcar la opción como seleccionada si coincide con la categoría del producto
        if (institucion.id_institucion == producto.id_institucion) {
            option.prop("selected", true);
        }

        // Agregar la opción al select
        selectInstituciones.append(option);
    });

    $.each(Proveedores, function (index, proveedor) {
        // Crear una opción para cada categoría
        let option = $("<option>").val(proveedor.id_proveedor).text(proveedor.nombre);

        // Marcar la opción como seleccionada si coincide con la categoría del producto
        if (proveedor.id_proveedor == producto.id_proveedor) {
            option.prop("selected", true);
        }

        // Agregar la opción al select
        selectProveedores.append(option);
    });

    $('#id-producto').val(producto.id_producto);
    $('#barras-producto').val(producto.barras);
    $('#codigo-producto').val(producto.codigo);
    $('#nombre-producto').val(producto.nombre);
    $('#descripcion-producto').val(producto.descripcion);
    $('#costo-producto').val(producto.costo);
    $('#precio-producto').val(producto.precio);
    $("#estado-producto").val(producto.estado).trigger('change');
    $('#registro-producto').val(producto.registro);

    $('#modal-editarProducto').modal('show');
}

function verInventario(idProducto) {
    $.ajax({
        url: urlPath + '/inventarioproductos/obtenerStockByProducto/' + idProducto,
        type: 'GET'
    })
        .done(function (response) {
            if (response.success) {
                let inventario = response.respuesta;
                console.log(inventario);
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

function actualizarEstado(idProducto, estado) {
    let nuevoEstado = estado == 1 ? 0 : 1;

    console.log('Estado: ' + estado, 'Nuevo estado: ' + nuevoEstado);

    $.ajax({
        url: urlController + '/actualizarEstado',
        method: 'POST',
        data: { id_producto: idProducto, estado: nuevoEstado }
    })
        .done(function (response) {
            if (response.success) {
                // Encuentra la fila en la tabla con el ID del producto y actualiza sus celdas
                let fila = $("#table-body").find("[data-id='" + idProducto + "']");

                if (fila.length > 0) {
                    fila.find(".actualizar-estado").attr('data-estado', nuevoEstado);
                    fila.find(".actualizar-estado").html(nuevoEstado == 0 ? '<span class="badge badge-danger pt-1 px-3" style="cursor: pointer">Inactivo</span>' : '<span class="badge badge-success pt-1 px-3" style="cursor: pointer">Activo</span>');
                }
                showMessage('success', 'Estado del producto se actualizó correctamente.');
            } else {
                showMessage('error', 'No se actualizó el estado del preducto.');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
        });
}

function guardarProducto(producto) {
    $.ajax({
        url: urlController + '/guardarProducto',
        method: 'POST',
        data: producto
    })
        .done(function (response) {
            // Manejar la respuesta del servidor
            if (response.success) {
                showMessage('success', 'El producto se guardó correctamente.');
                // actualizarFilaEnTabla(datos);
                obtenerProductos();
            } else {
                showMessage('error', 'Ocurrió un problema y no se logró guardar el producto.');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
        });
}

// Acciones

$('#btn-header').append(nuevoProductoBtn);

$('#guardar-cambios').on('click', function () {
    let id_producto = $('#id-producto').val();
    let barras = $('#barra-producto').val();
    let codigo = $('#codigo-producto').val();
    let nombre = $('#nombre-producto').val();
    let descripcion = $('#descripcion-producto').val();
    let talla = $('#talla-producto').val();
    let id_categoria = $('#categoria-producto').val();
    let id_subcategoria = $('#subcategoria-producto').val();
    let id_institucion = $('#institucion-producto').val();
    let id_proveedor = $('#proveedor-producto').val();
    let costo = $('#costo-producto').val().replace(/\D/g, '') || 0;
    let precio = $('#precio-producto').val().replace(/\D/g, '') || 0;
    let estado = $('#estado-producto').val();

    // Crear un objeto con los datos a enviar
    let producto = {
        id_producto: parseInt(id_producto),
        barras: barras == '' ? '' : parseInt(barras),
        codigo: codigo.toUpperCase(),
        nombre: nombre.toUpperCase(),
        descripcion: descripcion.toUpperCase(),
        talla: talla.toUpperCase(),
        id_categoria: parseInt(id_categoria),
        id_subcategoria: parseInt(id_subcategoria),
        id_institucion: parseInt(id_institucion),
        id_proveedor: parseInt(id_proveedor),
        costo: parseInt(costo),
        precio: parseInt(precio),
        estado: parseInt(estado),
    };

    $("#modal-editarProducto").modal('hide');

    guardarProducto(producto);
});

nuevoProductoBtn.on('click', function () {
    let selectCategoria = $("#nuevo-categoria-producto");
    let selectSubcategoria = $("#nuevo-subcategoria-producto");
    let selectInstituciones = $("#nuevo-institucion-producto");
    let selectProveedores = $("#nuevo-proveedor-producto");

    // Limpiar opciones existentes
    selectCategoria.empty();
    selectSubcategoria.empty();
    selectInstituciones.empty();
    selectProveedores.empty();

    // Agregar la opción predeterminada a cada select
    selectCategoria.append($("<option>").val("").text("Seleccione una opción"));
    selectSubcategoria.append($("<option>").val("").text("Seleccione una opción"));
    selectInstituciones.append($("<option>").val("").text("Seleccione una opción"));
    selectProveedores.append($("<option>").val("").text("Seleccione una opción"));

    // Recorrer categorías y agregarlas al select
    $.each(Categorias, function (index, categoria) {
        // Crear una opción para cada categoría
        let option = $("<option>").val(categoria.id_categoria).text(categoria.nombre);

        // Agregar la opción al select
        selectCategoria.append(option);
    });

    $.each(Subcategorias, function (index, subcategoria) {
        // Crear una opción para cada categoría
        let option = $("<option>").val(subcategoria.id_subcategoria).text(subcategoria.nombre);

        // Agregar la opción al select
        selectSubcategoria.append(option);
    });

    $.each(Instituciones, function (index, institucion) {
        // Crear una opción para cada categoría
        let option = $("<option>").val(institucion.id_institucion).text(institucion.nombre);

        // Agregar la opción al select
        selectInstituciones.append(option);
    });

    $.each(Proveedores, function (index, proveedor) {
        // Crear una opción para cada categoría
        let option = $("<option>").val(proveedor.id_proveedor).text(proveedor.nombre);

        // Agregar la opción al select
        selectProveedores.append(option);
    });

    $('#nuevo-barras-producto').val('');
    $('#nuevo-codigo-producto').val('');
    $('#nuevo-nombre-producto').val('');
    $('#nuevo-descripcion-producto').val('');
    $('#nuevo-talla-producto').val('');
    $('#nuevo-costo-producto').val('');
    $('#nuevo-precio-producto').val('');
    $('#nuevo-estado-producto').val('');

    $('#modal-nuevoProducto').modal('show');
});

$('#crear-producto').on('click', function () {
    let barras = $('#nuevo-barras-producto').val();
    let codigo = $('#nuevo-codigo-producto').val();
    let nombre = $('#nuevo-nombre-producto').val();
    let descripcion = $('#nuevo-descripcion-producto').val();
    let talla = $('#nuevo-talla-producto').val();
    let id_categoria = $('#nuevo-categoria-producto').val();
    let id_subcategoria = $('#nuevo-subcategoria-producto').val();
    let id_institucion = $('#nuevo-institucion-producto').val();
    let id_proveedor = $('#nuevo-proveedor-producto').val();
    let costo = $('#nuevo-costo-producto').val();
    let precio = $('#nuevo-precio-producto').val();
    let estado = $('#nuevo-estado-producto').val();

    const producto = {
        barras: parseInt(barras),
        codigo: codigo.toUpperCase(),
        nombre: nombre.toUpperCase(),
        descripcion: descripcion.toUpperCase(),
        talla: talla.toUpperCase(),
        id_categoria: parseInt(id_categoria),
        id_subcategoria: parseInt(id_subcategoria),
        id_institucion: parseInt(id_institucion),
        id_proveedor: parseInt(id_proveedor),
        costo: parseInt(costo),
        precio: parseInt(precio),
        estado: parseInt(estado),
        registro: obtenerDateTime()
    };

    $("#modal-nuevoProducto").modal('hide');

    guardarProducto(producto);
});

$(document).ready(function () {
    obtenerProductos();
    obtenerDatosAsync();
})
