$(document).ready(function () {
    function formatearFecha(fechaObj) {
        const fecha = new Date(fechaObj);

        if (isNaN(fecha.getTime())) {
            console.log("La fecha original no es válida");
            return "-"; // Retorna un valor predeterminado si la fecha no es válida
        }

        const year = fecha.getFullYear();
        const month = (fecha.getMonth() + 1).toString().padStart(2, "0");
        const day = fecha.getDate().toString().padStart(2, "0");

        return `${year}-${month}-${day}`;
    }

    async function obtenerRequisiciones() {
        try {
            const response = await fetch("requisiciones/obtenerRequisiciones", {
                method: "GET",
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log("Datos recibidos:", data);

            if (data && Array.isArray(data.respuesta)) {
                renderizarTabla(data.respuesta);
            } else {
                console.warn("No se encontraron datos en la respuesta.");
            }
        } catch (error) {
            console.error("Error en la solicitud Fetch:", error);
        }
    }

    function renderizarTabla(requisiciones) {
        // Destruir la instancia de DataTable antes de actualizar la tabla
        if ($.fn.DataTable.isDataTable("#simple-table")) {
            $("#simple-table").DataTable().destroy();
        }

        // Construir encabezado de la tabla
        const tableHead = `
            <tr>
                <th>#</th>
                <th>Fecha programada</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Estado</th>
            </tr>
        `;
        $("#table-head").html(tableHead);

        // Construir cuerpo de la tabla
        $("#table-body").empty();
        if (Array.isArray(requisiciones) && requisiciones.length > 0) {
            requisiciones.forEach((item, index) => {
                const filaRequisicion = `
                    <tr data-id="${index + 1}">
                        <td>${index + 1}</td>
                        <td>${formatearFecha(item.fecha)}</td>
                        <td>${item.codigo}</td>
                        <td>${item.cantidad}</td>
                        <td>${item.estado == 0 ? "Pendiente" : "Lista"}</td>
                    </tr>
                `;
                $("#table-body").append(filaRequisicion);
            });
        }

        // Inicializar DataTable solo una vez, después de renderizar la tabla
        initdataTable();
    }

    function initdataTable() {
        if (!$.fn.DataTable.isDataTable("#simple-table")) {
            $("#simple-table").DataTable({
                language: {
                    emptyTable: "No hay datos disponibles para la tabla",
                },
                columnDefs: [
                    {
                        targets: [0], // Indica la primera columna
                        className: "text-center", // Clase para centrar el contenido
                    },
                ],
                order: [[0, "asc"]], // Orden ascendente para la columna 0
            });

            $("#table-show").fadeIn();
        }
    }

    async function guardarRequisicion(requisicion) {
        try {
            const response = await fetch(`${urlPath}/requisiciones/guardarRequisicion`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requisicion), // Convertir el objeto a JSON
            });
    
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
    
            const data = await response.json();
    
            if (data.respuesta) {
                alert('Requisición creada con éxito.');
                obtenerRequisiciones();
            } else {
                alert('Error al crear la requisición.');
            }
        } catch (error) {
            console.error('Error en la solicitud Fetch:', error);
        }
    }    

    async function obtenerProductos() {
        try {
            const response = await fetch(`${urlPath}/productos/obtenerProductosActivos`, {
                method: 'GET',
            });
    
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status} ${response.statusText}`);
            }
    
            const data = await response.json();
    
            if (data && Array.isArray(data.respuesta)) {
                // Limpiar el select antes de agregar nuevas opciones
                selectProducto.empty();
    
                // Recorrer productos y agregarlos al select
                data.respuesta.forEach(producto => {
                    const option = $("<option>")
                        .val(producto.id_producto)
                        .text(producto.codigo);

                    // Agregar la opción al select
                    selectProducto.append(option);
                });
            } else {
                console.warn('No se encontraron productos en la respuesta.');
            }
        } catch (error) {
            console.error('Error en la solicitud Fetch:', error);
        }
    } 
    
    const urlPath = window.location.pathname.split('/').slice(0, -1).join('/');

    // Crear un botón para nueva requisición
    let nuevaRequisicionBtn = $("<button>", {
        text: "Nueva Requisicion",
        id: "btnNuevaRequisicion",
        class: "btn btn-primary btn-sm",
        "data-bs-toggle": "modal",
        "data-bs-target": "#modal-nuevaRequisicion",
    });

    let selectProducto = $("#id_producto"); 

    $("#btn-header").append(nuevaRequisicionBtn);

    nuevaRequisicionBtn.on('click', function () {

        obtenerProductos();
    
        // Limpiar opciones existentes
        selectProducto.empty();
    
        // Agregar la opción predeterminada a cada select
        selectProducto.append($("<option>").val("").text("Seleccione una opción"));
    
        $('#cantidad-producto').val('');
    
        $('#modal-nuevaRequisicion').modal('show');
    });

    $('#guardar').on('click', function () {
        let id_producto = $('#id_producto').val();
        let cantidad = $('#cantidad').val();
        let fecha = formatearFecha($('#fecha').val());

        // Crear un objeto con los datos a enviar
        let requisicion = {
            id_producto: parseInt(id_producto),
            cantidad: parseInt(cantidad),
            fecha: fecha
        }
        if (!id_producto || !cantidad || !fecha) {
            alert('Por favor, complete todos los campos.');
            return;
        }
        
        $('#modal-nuevaRequisicion').modal('hide');
    
        guardarRequisicion(requisicion);
    });

    // Inicializar datos al cargar la página
    obtenerRequisiciones();
});
