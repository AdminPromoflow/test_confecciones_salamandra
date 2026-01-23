
// Variables

const notyf = new Notyf();
const urlController = window.location.pathname;
const urlPath = urlController.slice(0, urlController.lastIndexOf("/"));

// Funciones

function initdataTable() {
    if (!$.fn.DataTable.isDataTable('#simple-table')) {
        $('#simple-table').DataTable({
            "language": {
                "emptyTable": "No hay datos disponibles en la tabla",
                "search": "Buscar:",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
                "infoFiltered": "(filtrado de _MAX_ entradas totales)",
                "lengthMenu": "Mostrar _MENU_ entradas",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "columnDefs": [
                {
                    "targets": [1],
                    "className": "text-center"
                }
            ],
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                   '<"row"<"col-sm-12"tr>>' +
                   '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            "initComplete": function(settings, json) {
                this.api().columns(2).every(function() {
                    var column = this;
                    var select = $('<select><option value="">Buscar por código</option></select>')
                        .appendTo($(column.header()))
                        .on('change', function() {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column
                                .search(val ? '^'+val+'$' : '', true, false)
                                .draw();
                        });
                    column.data().unique().sort().each(function(d, j) {
                        select.append('<option value="'+d+'">'+d+'</option>')
                    });
                });
            }
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

function obtenerDetalle() {
    $.ajax({
        url: urlPath + '/detalleventa/obtenerDetalles',
        method: 'GET'
    })
        .done(function (response) {
            let detalles = response.respuesta;
            renderizarTabla(detalles);
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
        });
}

function renderizarTabla(detalles) {
    // Destruir la instancia de DataTable antes de actualizar la tabla
    if ($.fn.DataTable.isDataTable('#simple-table')) {
        $('#simple-table').DataTable().destroy();
    }
    $('#table-head').empty();

    const tableHead = `
        <tr>
            <th>#</th>
            <th># Venta</th>
            <th>Producto</th>
            <th>Estado</th>
            <th>Anotación</th>
            <th>Registrado el</th>
        </tr>
    `;

    $('#table-head').html(tableHead);
    $('#table-body').empty();

    let estadoBadge = '';
    let estadoLabel = '';
    let msgFeacha = '';

    if (Array.isArray(detalles) && detalles.length > 0) {
        detalles.forEach(function (detalle, index) {
            const detalleData = {
                indice: parseInt(index + 1),
                id_detalle: parseInt(detalle.id_detalle),
                id_venta: parseInt(detalle.id_venta),
                id_producto: detalle.id_producto,
                codigo: detalle.codigo,
                nombre: detalle.nombre,
                estado: parseInt(detalle.estado),
                nota: detalle.nota,
                fecha_entrega: detalle.fecha_entrega,
                registro: formatearFecha(detalle.registro)
            };

            switch (detalleData.estado) {
                case 0:
                    estadoBadge = 'badge-danger';
                    estadoLabel = 'Pendiente';
                    msgFeacha = 'Entregar el ';
                    break;
                case 1:
                    estadoBadge = 'badge-dark';
                    estadoLabel = 'Urgente';
                    msgFeacha = 'Entregar el ';
                    break;
                case 2:
                    estadoBadge = 'badge-primary';
                    estadoLabel = 'Listo';
                    msgFeacha = 'Entregar el ';
                    break;
                case 3:
                    estadoBadge = 'badge-warning';
                    estadoLabel = 'Bolsa';
                    msgFeacha = 'Entregar el ';
                    break;
                case 4:
                    estadoBadge = 'badge-success';
                    estadoLabel = 'Entregado';
                    msgFeacha = 'Entregado el ';
                    break;
                case 5:
                    estadoBadge = 'badge-default';
                    estadoLabel = 'Cancelado';
                    break;
                case 6:
                    estadoBadge = 'badge-secondary';
                    estadoLabel = 'Retorno';
                    msgFeacha = 'Retornado el ';
                    break;
                case 7:
                    estadoBadge = 'badge-default';
                    estadoLabel = 'Corte';
                    break;
            }

            const estadoSpan = `<span class="badge ${estadoBadge} pt-1 px-3">${estadoLabel}</span>`;


            const filaDetalle = `
                <tr data-id="${detalleData.id_detalle}">
                    <td class="">${detalleData.id_detalle}</td>
                    <td class="font-weight-bold">${detalleData.id_venta}</td>
                    <td class="font-weight-bold">${detalleData.codigo}</td>
                    <td class="">${estadoSpan}</td>
                    <td class="">${detalleData.nota}</td>
                    <td class="">${detalleData.registro}</td>
                </tr>
                `;
            $('#table-body').append(filaDetalle);
        });

        initdataTable();
    }
};

// Acciones
//


$(document).ready(function () {
    obtenerDetalle();
})