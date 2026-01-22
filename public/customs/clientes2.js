$(document).ready(function () {
    // Definir la URL base para las solicitudes AJAX

    // Manejar el envío del formulario de búsqueda
    $('#form-buscar-cliente').on('submit', function (e) {
        e.preventDefault();

        const cedula = $('#buscar-cedula').val();
        const nombre = $('#buscar-nombre').val();

        if (!cedula && !nombre) {
            showMessage('error', 'Por favor, ingrese una cédula o un nombre para buscar.');
            return;
        }

        buscarClientes(cedula, nombre);
    });

    // Resto del código...
});

function formatearNombre(nombre) {
    return nombre.toLowerCase()
                 .split(' ')
                 .map(palabra => palabra.charAt(0).toUpperCase() + palabra.slice(1))
                 .join(' ');
}

function buscarClientes(cedula, nombre) {
    $.ajax({
        url: urlPath + '/clientes/buscarCliente',
        method: 'POST',
        data: {
            cedula: cedula,
            nombre: nombre
        }
    })
    .done(function (response) {
        if (response.respuesta && response.respuesta.length > 0) {
            // Si hay resultados, limpiar y mostrar la lista de clientes
            $('#lista-clientes').empty(); // Limpiar resultados anteriores
            mostrarListaClientes(response.respuesta);
        } else {
            // Si no hay resultados, mostrar un mensaje de notificación
            showMessage('error', 'No se encontraron clientes con los datos proporcionados.');
        }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
        console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
        showMessage('error', 'Ocurrió un error al realizar la búsqueda.');
    });
}

function mostrarListaClientes(clientes) {
    const lista = clientes.map(cliente => {
        const nombreFormateado = formatearNombre(cliente.nombre);
        const apellidosFormateados = formatearNombre(cliente.apellidos);

        return `
            <div class="list-group-item list-group-item-action" data-cliente='${JSON.stringify(cliente)}'>
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">${nombreFormateado} ${apellidosFormateados}</h6>
                    <small>Cédula: ${cliente.cedula}</small>
                </div>
                <p class="mb-1">${cliente.direccion}</p>
                <small>Teléfono: ${cliente.telefono}</small>
                <div class="mt-2">
                    ${cliente.estado === 'activo' ? 
                        '<span class="badge bg-success text-white">Activo</span>' : 
                        '<span class="badge bg-danger">Inactivo</span>'
                    }
                </div>
            </div>
        `;
    }).join('');

    $('#lista-clientes').html(lista);
    $('#resultados-cliente').show();

    // Reconectar los eventos de clic para los nuevos elementos de la lista
    $('.list-group-item').off('click').on('click', function () {
        const cliente = $(this).data('cliente');
        obtenerEstadisticasCliente(cliente, cliente.id_cliente);
    });
}

function mostrarDetallesCliente(cliente, ventas, otrasEstadisticas) {
    // Formatear el nombre y los apellidos
    const nombreFormateado = formatearNombre(cliente.nombre);
    const apellidosFormateados = formatearNombre(cliente.apellidos);

    // Avatar por defecto (SVG)
    const avatarSVG = `
        <svg xmlns="http://www.w3.org/2000/svg" width="150" height="150" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="img-fluid rounded-circle mb-3">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
        </svg>
    `;

    // Estructura de los detalles del cliente
    const detalles = `
        <div class="row">
            <!-- Columna de la imagen y datos básicos -->
            <div class="col-md-4">
                <div class="text-center">
                    ${avatarSVG}
                    <h4 class="mb-2">${nombreFormateado} ${apellidosFormateados}</h4>
                    <p class="text-muted mb-1">Cédula: ${cliente.cedula}</p>
                    <p class="text-muted mb-1">Teléfono: ${cliente.telefono}</p>
                    <p class="text-muted mb-1">Correo: ${cliente.email}</p>
                    <div class="mt-3">
                        ${cliente.estado === 'activo' ? 
                            '<span class="badge bg-success text-white">Activo</span>' : 
                            '<span class="badge bg-danger">Inactivo</span>'
                        }
                    </div>
                </div>
            </div>

            <!-- Columna de estadísticas y gráficos -->
            <div class="col-md-8">
                <h5 class="mb-3">Estadísticas del Cliente</h5>

                <!-- Ventas realizadas -->
                <div class="mb-4">
                    <h6>Ventas realizadas</h6>
                    <canvas id="ventasChart" width="400" height="200"></canvas>
                </div>

                <!-- Otras estadísticas -->
                <div class="mb-4">
                    <h6>Otras estadísticas</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light p-3">
                                <p class="mb-0"><strong>Total comprado:</strong> ${formatearMonedaCOP(otrasEstadisticas.total_comprado)}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light p-3">
                                <p class="mb-0"><strong>Última compra:</strong> ${formatearFecha(otrasEstadisticas.ultima_compra)}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Mostrar los detalles en el contenedor
    $('#detalles-cliente').html(detalles).show();
    $('#lista-clientes').hide();

    // Agregar un botón para volver a la lista de resultados
    $('#detalles-cliente').append(`
        <button id="volver-a-lista" class="btn btn-secondary mt-3">Volver a la lista</button>
    `);

    // Manejar el clic en el botón "Volver a la lista"
    $('#volver-a-lista').on('click', function () {
        $('#detalles-cliente').hide().empty(); // Ocultar y limpiar los detalles
        $('#lista-clientes').show(); // Mostrar la lista de resultados
    });

    // Inicializar el gráfico de ventas (usando Chart.js)
    inicializarGraficoVentas(ventas);
}

function formatearMonedaCOP(valor) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0, // No mostrar decimales
    }).format(valor);
}

function obtenerNombreMesYAnio(fecha) {
    const nombresMeses = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];
    const fechaObj = new Date(fecha);
    const mes = nombresMeses[fechaObj.getMonth()]; // Nombre del mes
    const anio = fechaObj.getFullYear(); // Año
    return `${mes} ${anio}`;
}

function obtenerNombreMes(mes) {
    const nombresMeses = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];
    return nombresMeses[mes - 1]; // Los meses en JavaScript van de 0 a 11
}

function inicializarGraficoVentas(ventas) {
    const ctx = document.getElementById('ventasChart').getContext('2d');
    const ventasChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ventas.map(v => `Año ${v.anio}`), // Año
            datasets: [{
                label: 'Compras realizadas',
                data: ventas.map(v => v.total), // Datos de ventas
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function obtenerEstadisticasCliente(cliente, idCliente) {
    $.ajax({
        url: urlPath + '/clientes/obtenerEstadisticasCliente', // Endpoint en el backend
        method: 'POST',
        data: { id_cliente: idCliente }
    })
    .done(function (response) {
        if (response.respuesta) {
            const { ventas, otrasEstadisticas } = response.respuesta;
            mostrarDetallesCliente(cliente, ventas, otrasEstadisticas);
        } else {
            showMessage('error', 'No se pudieron obtener las estadísticas del cliente.');
        }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
        console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
        showMessage('error', 'Ocurrió un error al obtener las estadísticas del cliente.');
    });
}