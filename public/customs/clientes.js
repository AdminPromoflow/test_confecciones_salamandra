
// Variables

const notyf = new Notyf();
const urlController = window.location.pathname;
const urlPath = urlController.slice(0, urlController.lastIndexOf("/"));

// Crear un botón para Nuevo Cliente
let nuevoClienteBtn = $('<button>', {
    text: 'Nuevo Cliente',
    id: 'btnNuevoCliente',
    class: 'btn btn-primary btn-sm',
    'data-bs-toggle': 'modal',
    'data-bs-target': '#modal-nuevoCliente',
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

async function obtenerClientes() {
    try {
        const response = await $.ajax({
            url: urlController + '/obtenerClientes',
            method: 'GET'
        });
        renderizarTabla(response.respuesta);
    } catch (error) {
        console.error('Error al obtener clientes:', error);
    }
}

function renderizarTabla(clientes) {
    // Destruir la instancia de DataTable antes de actualizar la tabla
    if ($.fn.DataTable.isDataTable('#simple-table')) {
        $('#simple-table').DataTable().destroy();
    }
    $('#table-head').empty();

    const tableHead = `
        <tr>
            <th>#</th>
            <th>Cedula</th>
            <th>Nombre</th>
            <th>Correo Electrónico</th>
            <th>Teléfono</th>
            <th>Estado</th>
            <th>&nbsp;</th>
        </tr>
    `;

    $('#table-head').html(tableHead);
    $('#table-body').empty();

    if (Array.isArray(clientes) && clientes.length > 0) {
        clientes.forEach(function (cliente, index) {
            const clienteData = {
                indice: parseInt(index + 1),
                id_cliente: parseInt(cliente.id_cliente),
                cedula: cliente.cedula,
                nombre: cliente.nombre,
                apellidos: cliente.apellidos,
                nombreCompleto: `${ cliente.nombre || ''} ${cliente.apellidos || ''}`.trim(),
                email: cliente.email,
                telefono: cliente.telefono,
                estado: cliente.estado,
                registro: formatearFecha(cliente.registro)
            };

            let estadoBadge = '';
            let estadoLabel = '';

            // Evaluar estado (puede ser 1, '1', 'activo', etc.)
            const esActivo = clienteData.estado == 1 || clienteData.estado === '1' || clienteData.estado === 'activo';
            
            if (esActivo) {
                estadoBadge = 'badge-success';
                estadoLabel = 'Activo';
            } else {
                estadoBadge = 'badge-danger';
                estadoLabel = 'Inactivo';
            }

            const estadoSpan = `<span class="badge ${estadoBadge} pt-1 px-3" style="cursor: pointer">${estadoLabel}</span>`;

            const filacliente = `
                <tr data-id="${clienteData.id_cliente}">
                    <td class="">${clienteData.indice}</td>
                    <td class="">${clienteData.cedula}</td>
                    <td class="font-weight-bold">${clienteData.nombreCompleto}</td>
                    <td class="">${clienteData.email}</td>
                    <td class="">${clienteData.telefono}</td>
                    <td class="actualizar-estado" data-estado="${clienteData.estado}">${estadoSpan}</td>
                    <td class="editar" data-cliente='${JSON.stringify(clienteData)}'>
                        <span class="badge badge-primary pt-1 px-3" style="cursor: pointer">Ver / Editar</span>
                    </td>
                </tr>
                `;
            $('#table-body').append(filacliente);
        });

        // Obtener los datos del cliente
        $('.editar').off('click').on('click', function () {
            const cliente = $(this).data('cliente');

            editarCliente(cliente);
        });

        // Agregar evento de clic a la columna de estado
        $('.actualizar-estado').off('click').on('click', function () {
            // Encuentra el elemento tr más cercano
            let trElement = $(this).closest('tr');
            let idCliente = trElement.data('id');
            let estado = $(this).attr('data-estado');

            actualizarEstado(idCliente, estado);
        });

        initdataTable();
    }
};

function actualizarEstado(idCliente, estado) {
    let nuevoEstado = estado == 'activo' ? 'inactivo' : 'activo';

    console.log('Estado: ' + estado, 'Nuevo estado: ' + nuevoEstado);

    $.ajax({
        url: urlController + '/actualizarEstado',
        method: 'POST',
        data: { id_cliente: idCliente, estado: nuevoEstado }
    })
        .done(function (response) {
            if (response.success) {
                // Encuentra la fila en la tabla con el ID del cliente y actualiza sus celdas
                let fila = $("#table-body").find("[data-id='" + idCliente + "']");

                if (fila.length > 0) {
                    fila.find(".actualizar-estado").attr('data-estado', nuevoEstado);
                    fila.find(".actualizar-estado").html(nuevoEstado == 'inactivo' ? '<span class="badge badge-danger pt-1 px-3" style="cursor: pointer">Inactivo</span>' : '<span class="badge badge-success pt-1 px-3" style="cursor: pointer">Activo</span>');
                }
                showMessage('success', 'Estado del cliente se actualizó correctamente.');
            } else {
                showMessage('error', 'No se actualizó el estado del preducto.');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
        });
}

function editarCliente(cliente) {
    $('#id-cliente').val(cliente.id_cliente);
    $('#cedula-cliente').val(cliente.cedula);
    $('#nombre-cliente').val(cliente.nombre);
    $('#apellidos-cliente').val(cliente.apellidos);
    $('#email-cliente').val(cliente.email);
    $('#telefono-cliente').val(cliente.telefono);
    $('#direccion-cliente').val(cliente.direccion || '');
    $('#barrio-cliente').val(cliente.barrio || '');
    $('#estado-cliente').val(cliente.estado).trigger('change');
    $('#registro-cliente').val(cliente.registro);

    $('#modal-editarCliente').modal('show');
}

function guardarCliente() {
    const clienteData = {
        id_cliente: $('#id-cliente').val(),
        cedula: $('#cedula-cliente').val(),
        nombre: $('#nombre-cliente').val(),
        apellidos: $('#apellidos-cliente').val(),
        email: $('#email-cliente').val(),
        telefono: $('#telefono-cliente').val(),
        direccion: $('#direccion-cliente').val(),
        barrio: $('#barrio-cliente').val()
    };

    console.log('Datos a enviar:', clienteData);

    $.ajax({
        url: urlController + '/guardarCliente',
        method: 'POST',
        data: clienteData
    })
    .done(function(response) {
        console.log('Respuesta del servidor:', response);
        if (response.success) {
            $('#modal-editarCliente').modal('hide');
            obtenerClientes();
            showMessage('success', 'Cliente actualizado correctamente.');
        } else {
            showMessage('error', 'Error al actualizar el cliente.');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('Error AJAX:', textStatus, errorThrown);
        console.error('Respuesta completa:', jqXHR.responseText);
        showMessage('error', 'Error en la solicitud.');
    });
}


$(document).ready(function () {
    obtenerClientes();
    
    // Evento para guardar cliente editado
    $('#guardar-cambios').off('click').on('click', function() {
        guardarCliente();
    });
})