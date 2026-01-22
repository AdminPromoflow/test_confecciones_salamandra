
// Variables
//

const urlController = window.location.pathname;
const urlPath = urlController.slice(0, urlController.lastIndexOf("/"));

const currentDate = new Date().toISOString().split("T")[0];

// Funciones
//

function formatCurrency(value) {
    return parseFloat(value).toLocaleString('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    })
};

function obtenerCierre(inicio, fin) {
    $.ajax({
        url: urlPath + "/cierre/obtenerCierre/",
        type: "POST",
        data: { fechaInicio: inicio, fechaFin: fin }
    })
        .done(function (response) {
            // console.log('Respuesta completa:', response); // Para depuración

            // Limpiar el contenedor de tablas antes de mostrar nuevas tablas
            $('#tablasContainer').empty();

            if (response && response.respuesta) {
                let data = response.respuesta.data;
                // console.log(data);

                if (response.respuesta.success && data) {
                    if (data.cajero) {
                        delete data['cajero'];
                        mostrarTabla1(data);
                    } else {
                        mostrarTabla2(data);
                    }
                } else {
                    console.log(response.respuesta.message || "No se encontraron resultados");
                    $('#tablasContainer').html('<div class="alert alert-info">No se encontraron resultados para el período seleccionado.</div>');
                }
            } else {
                console.log("Respuesta inesperada del servidor");
                $('#tablasContainer').html('<div class="alert alert-warning">Se recibió una respuesta inesperada del servidor. Por favor, inténtelo de nuevo.</div>');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            $('#tablasContainer').html('<div class="alert alert-danger">Ocurrió un error al obtener los datos. Por favor, inténtelo de nuevo más tarde.</div>');
        });
}

function mostrarTabla1(data) {
    // Agregar un contenedor para las tablas si no existe
    if (!$('#tablasContainer').length) {
        $('<div></div>').attr('id', 'tablasContainer').appendTo('body');
    }

    var totalesPorUsuario = {};

    // Iterar sobre los elementos de data
    $.each(data, function (index, item) {
        // Obtener el nombre de la sucursal
        var nombreSucursal = item.nombre_sucursal;

        // Crear una nueva tabla para esta sucursal
        var tabla = $('<table></table>').addClass('table table-bordered table-striped');
        var tablaId = 'tablaSucursal_' + nombreSucursal.replace(/\s+/g, '_');
        tabla.attr('id', tablaId);

        // Agregar un título para esta tabla usando <caption>
        var caption = $('<caption></caption>').addClass('titulo-general').text('Sucursal: ' + nombreSucursal);

        // Agregar encabezados de la tabla
        var encabezados = [
            'Cajero',
            'Total Efectivo',
            'Total Nequi',
            'Total Daviplata',
            'Total Devoluciones',
            'Gran Total'
        ];
        var thead = $('<thead class="thead-light"></thead>');
        var filaEncabezados = $('<tr></tr>');

        $.each(encabezados, function (index, encabezado) {
            filaEncabezados.append($('<th class="text-center" scope="col"></th>').text(encabezado));
        });
        thead.append(filaEncabezados);
        tabla.append(thead);

        // Agregar la fila de datos
        var fila = $('<tr></tr>');
        fila.append('<th class="text-left" scope="row">' + item.nombre_usuario.toUpperCase() + '</th>');
        fila.append('<td class="text-right">' + formatCurrency(item.TotalEfectivo) + '</td>');
        fila.append('<td class="text-right">' + formatCurrency(item.TotalNequi) + '</td>');
        fila.append('<td class="text-right">' + formatCurrency(item.TotalDaviplata) + '</td>');
        fila.append('<td class="text-right">' + formatCurrency(item.TotalDevolucion) + '</td>');

        // Calcular el total para esta fila y actualizar el objeto totalesPorUsuario
        var total = parseFloat(item.TotalEfectivo) + parseFloat(item.TotalNequi) + parseFloat(item.TotalDaviplata);
        totalesPorUsuario[item.nombre_usuario] = (totalesPorUsuario[item.nombre_usuario] || 0) + total;
        fila.append('<td class="text-right font-weight-bold">' + formatCurrency(total) + '</td>');
        tabla.append(fila);

        // Agregar la fila de totales por usuario
        var filaTotales = $('<tr></tr>');
        filaTotales.append('<td class="font-weight-bold" colspan="5">Total por Usuario</td>');
        filaTotales.append('<td class="text-right font-weight-bold">' + formatCurrency(totalesPorUsuario[item.nombre_usuario]) + '</td>');
        tabla.append(filaTotales);

        // Agregar la tabla a la página
        $('#tablasContainer').append(tabla);

        // Agregar un espacio entre las tablas
        $('#tablasContainer').append($('<hr>').addClass('my-4'));
    });
}

function mostrarTabla2(data) {
    // console.log('Datos recibidos en mostrarTabla2:', data); // Para depuración

    // Limpiar el contenedor de tablas antes de mostrar nuevas tablas
    $('#tablasContainer').empty();

    // Iterar sobre las sucursales
    $.each(data, function (sucursal, usuarios) {
        // Crear un contenedor para la sucursal
        var containerSucursal = $('<div></div>').addClass('sucursal-container');
        
        // Crear un título para la sucursal
        var sucursalTitle = $('<h3></h3>').text('Sucursal: ' + sucursal).addClass('sucursal-title');
        containerSucursal.append(sucursalTitle);

        // Crear una tabla para los cajeros en esta sucursal
        var tabla = $('<table></table>').addClass('table table-bordered table-striped');
        var tablaId = 'tablaSucursal_' + sucursal.replace(/\s+/g, '_');
        tabla.attr('id', tablaId);

        // Agregar encabezados de la tabla
        var encabezados = [
            'Cajero',
            'Total Efectivo',
            'Total Nequi',
            'Total Daviplata',
            'Total Devoluciones',
            'Gran Total'
        ];
        var thead = $('<thead class="thead-light"></thead>');
        var filaEncabezados = $('<tr></tr>');

        $.each(encabezados, function (index, encabezado) {
            filaEncabezados.append($('<th class="text-center" scope="col"></th>').text(encabezado));
        });
        thead.append(filaEncabezados);
        tabla.append(thead);
                
        i = 0;
        // Iterar sobre los usuarios de esta sucursal
        $.each(usuarios, function (index, usuario) {
            if (usuario && ((usuario.entrada && usuario.entrada.length > 0) || (usuario.salida && usuario.salida.length > 0))) {
                var fila = $('<tr></tr>');
                var entradaIndex = usuario.entrada && usuario.entrada.length > 0 ? 0 : -1;
                var salidaIndex = usuario.salida && usuario.salida.length > 0 ? 0 : -1;
                
                var nombreUsuario = (entradaIndex >= 0 && usuario.entrada[entradaIndex].nombre_usuario) || 
                                    (salidaIndex >= 0 && usuario.salida[salidaIndex].nombre_usuario) || 
                                    'Usuario Desconocido';
                fila.append('<th class="text-left" scope="row">' + nombreUsuario.toUpperCase() + '</th>');
        
                // Verificar y formatear los valores antes de mostrarlos
                var totalEfectivo = entradaIndex >= 0 && usuario.entrada[entradaIndex].TotalEfectivo ? parseFloat(usuario.entrada[entradaIndex].TotalEfectivo).toFixed(2) : '0';
                var totalNequi = entradaIndex >= 0 && usuario.entrada[entradaIndex].TotalNequi ? parseFloat(usuario.entrada[entradaIndex].TotalNequi).toFixed(2) : '0';
                var totalDaviplata = entradaIndex >= 0 && usuario.entrada[entradaIndex].TotalDaviplata ? parseFloat(usuario.entrada[entradaIndex].TotalDaviplata).toFixed(2) : '0';
                var totalDevolucion = salidaIndex >= 0 && usuario.salida[salidaIndex].TotalDevolucion ? parseFloat(usuario.salida[salidaIndex].TotalDevolucion).toFixed(2) : '0';
        
                fila.append('<td class="text-right">' + formatCurrency(totalEfectivo) + '</td>');
                fila.append('<td class="text-right">' + formatCurrency(totalNequi) + '</td>');
                fila.append('<td class="text-right">' + formatCurrency(totalDaviplata) + '</td>');
                fila.append('<td class="text-right">' + formatCurrency(totalDevolucion) + '</td>');
        
                // Calcular el total para esta fila
                var granTotal = parseFloat(totalEfectivo) + parseFloat(totalNequi) + parseFloat(totalDaviplata) - parseFloat(totalDevolucion);
                fila.append('<td class="text-right font-weight-bold">' + formatCurrency(granTotal.toFixed(2)) + '</td>');
                tabla.append(fila);
            }
        });

        // Agregar la tabla al contenedor de la sucursal
        containerSucursal.append(tabla);

        // Agregar la tabla a la página
        $('#tablasContainer').append(containerSucursal);

        // Agregar un espacio entre las tablas de sucursales
        $('#tablasContainer').append($('<hr>').addClass('my-4'));
    });
}


function getValues() {
    const searchType = document.getElementById("searchType").value;
    let values = {};

    switch (searchType) {
        case 'today':
            values['start'] = currentDate;
            values['end'] = currentDate;
            break;
        case 'range':
            // Obtenga ambas fechas si se ha seleccionado el intervalo
            values['start'] = document.getElementById("startDate").value;
            values['end'] = document.getElementById("endDate").value;
            break;
        default:
            console.error(`No existe ninguna acción definida para ${searchType}`);
            break;
    }

    return values;
}

// Acciones
//

$("#searchType").on("change", function () {
    if ($("#searchType option:selected").val() === "range") {
        $("#dateRangeContainer, #endDateContainer").removeClass("d-none");
    } else {
        $("#dateRangeContainer, #endDateContainer").addClass("d-none");
    }
}).trigger('change');

$("#searchBtn").click(function (event) {
    event.preventDefault(); // Evita que el formulario envíe automáticamente
    const data = getValues();

    // Limpiar el contenedor de tablas antes de mostrar nuevas tablas
    $('#tablasContainer').empty();

    obtenerCierre(data.start, data.end);
});