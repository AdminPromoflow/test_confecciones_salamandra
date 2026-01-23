
// Variables

const notyf = new Notyf();
const urlController = window.location.pathname;
const urlPath = urlController.slice(0, urlController.lastIndexOf("/"));

// Funciones

function initdataTable() {
    if (!$.fn.DataTable.isDataTable('#simple-table')) {
        $('#simple-table').DataTable({
            "language": {
                "emptyTable": "No hay datos disponibles en la tabla" // Mensaje personalizado
            },
            "columnDefs": [
                {
                    "targets": [1], // Indica la primera columna
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
    // Verificar si la fecha es válida
    if (!fechaActual || isNaN(new Date(fechaActual).getTime())) {
        return "Fecha inválida"; // Mensaje de error si la fecha no es válida
    }

    var opciones = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    };

    if (mostrarHora) {
        opciones.hour = '2-digit';
        opciones.minute = '2-digit';
        opciones.second = '2-digit';
        opciones.timeZone = 'America/Bogota'; // Asegúrate de que la zona horaria sea correcta
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

    if (type == 'success') {
        notyf.success(notyfOptions).on('dismiss', ({ target, event }) => foobar.retry());
    } else if (type == 'error') {
        notyf.error(notyfOptions).on('dismiss', ({ target, event }) => foobar.retry());
    }
}

function obtenerCarrito() {
    $.ajax({
        url: urlPath + '/carrito/obtenerAll',
        method: 'GET'
    })
        .done(function (response) {
            let carrito = response.respuesta;
            renderizarTabla(carrito);
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
        });
}

function renderizarTabla(carrito) {
    // Destruir la instancia de DataTable antes de actualizar la tabla
    if ($.fn.DataTable.isDataTable('#simple-table')) {
        $('#simple-table').DataTable().destroy();
    }

    // Limpiar la tabla
    $('#table-head').empty();
    $('#table-body').empty();

    // Construir el encabezado de la tabla (siempre debe estar presente)
    const tableHead = `
        <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Código</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Sucursal</th>
            <th>Usuario</th>
        </tr>
    `;
    $('#table-head').html(tableHead);

    // Verificar que carrito sea un array y tenga datos
    if (Array.isArray(carrito) && carrito.length > 0) {
        // Construir el cuerpo de la tabla
        carrito.forEach(function (carrito, index) {
            const carritoData = {
                indice: parseInt(index + 1),
                fecha: carrito.create_at ? formatearFecha(carrito.create_at, true) : "Fecha no disponible",
                sucursal_nombre: carrito.sucursal_nombre,
                producto_codigo: carrito.producto_codigo,
                producto_nombre: carrito.producto_nombre,
                cantidad: carrito.cantidad,
                cliente_nombre: carrito.cliente_nombre,
                cliente_apellido: carrito.cliente_apellido,
                usuario_nombre: carrito.usuario_nombre
            };

            const filaCarrito = `
                <tr data-id="${carrito.id_carrito}">
                    <td>${carritoData.indice}</td>
                    <td>${carritoData.fecha}</td>
                    <td>${carritoData.cliente_nombre} ${carritoData.cliente_apellido}</td>
                    <td>${carritoData.producto_codigo}</td>
                    <td>${carritoData.producto_nombre}</td>
                    <td>${carritoData.cantidad}</td>
                    <td>${carritoData.sucursal_nombre}</td>
                    <td>${carritoData.usuario_nombre}</td>
                </tr>
            `;
            $('#table-body').append(filaCarrito);
        });
    } else {
        // Si no hay datos, mostrar un mensaje en el cuerpo de la tabla
        const mensaje = `
            <tr>
                <td colspan="7" class="text-center">No hay registros para mostrar</td>
            </tr>
        `;
        $('#table-body').html(mensaje);
    }

    // Inicializar DataTables (solo si la tabla tiene una estructura válida)
    if ($('#table-head th').length > 0) {
        initdataTable();
    }
}

$(document).ready(function () {
    obtenerCarrito();
})