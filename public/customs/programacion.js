$(document).ready(function () {

    // Variables

    const notyf = new Notyf();
    const urlController = window.location.pathname;
    const urlPath = urlController.slice(0, urlController.lastIndexOf("/"));
    // console.log(urlController, urlPath);

    // Funciones

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

    function initdataTable() {
        if (!$.fn.DataTable.isDataTable('#simple-table')) {
            $('#simple-table').DataTable({
                "language": {
                    "emptyTable": "No hay datos disponibles en la tabla"
                },
                "columnDefs": [
                    {
                        "targets": [0],
                        "className": "text-center"
                    },
                    {
                        "targets": [2],
                        "width": "50%"
                    }
                ],
                "order": [
                    [2, 'asc']
                ]
            });

            $('#table-show').fadeIn();
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

    function obtenerProgramacion() {
        $.ajax({
            url: urlPath + '/programaciones/obtenerProgramacion',
            type: 'GET'
        })
            .done(function (response) {
                let programacion = response.respuesta;
                renderizarTabla(programacion);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            });
    }

    function renderizarTabla(programacion) {
        if ($.fn.DataTable.isDataTable('#simple-table')) {
            $('#simple-table').DataTable().destroy();
        }

        $('#table-head').empty();

        const tableHead = `
            <tr>
                <th>#</th>
                <th>Producción</th>
                <th>Fecha de producción</th>
                <th>&nbsp;</th>
            </tr>
        `;

        $('#table-head').html(tableHead);
        $('#table-body').empty();

        if (Array.isArray(programacion) && programacion.length > 0) {
            programacion.forEach(function (produccion, index) {
                const programacionData = {
                    indice: parseInt(index + 1),
                    id_programacion: parseInt(produccion.id_programacion),
                    produccion: produccion.produccion,
                    fecha: formatearFecha(produccion.fecha),
                    estado: produccion.estado
                };


                const filaPrograma = `
                    <tr data-id="${programacionData.id_programacion}">
                        <td class="">${programacionData.indice}</td>
                        <td class="">${programacionData.produccion}</td>
                        <td class="">${programacionData.fecha}</td>
                        <td class="actualizar" data-programacion='${JSON.stringify(programacionData)}'>
                            <span class="badge badge-primary pt-1 px-3" style="cursor: pointer">Completar</span>
                        </td>
                    </tr>
                    `;
                $('#table-body').append(filaPrograma);
            });

            // Obtener los datos del programacion
            $('.actualizar').off('click').on('click', function () {
                const programacion = $(this).data('programacion');

                actualizarEstado(programacion);
            });

            initdataTable();
        }
    };

    function actualizarEstado(programacion) {

        programacion.nuevoEstado = 0;

        $.ajax({
            url: urlPath + '/programaciones/actualizarEstado',
            type: 'POST',
            data: programacion
        })
            .done(function (response) {
                if (response.success) {
                    obtenerProgramacion();
                    showMessage('success', 'Se completo la producción programada correctamente.');
                } else {
                    showMessage('error', 'No se completo la producción programada..');
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            });
    }

    function crearProgramacion(programacionData) {
        $.ajax({
            url: urlPath + '/programaciones/crearProgramacion',
            type: 'POST',
            data: programacionData
        })
            .done(function (response) {
                if (response.success) {
                    showMessage('success', 'La producción programada se guardó correctamente.');
                    obtenerProgramacion();
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            });
    }

    // Acciones

    $('#crear-programacion').on('click', function () {
        $('#modal-nuevaProgramacion').modal('show');
    });

    $('#guardar-programacion').on('click', function () {

        const programacionData = {
            produccion: $('#nueva-produccion').val(),
            fecha: $('#nueva-fecha').val(),
            estado: 1
        };

        $('#modal-nuevaProgramacion').modal('hide');

        crearProgramacion(programacionData);
    });

    obtenerProgramacion();

});