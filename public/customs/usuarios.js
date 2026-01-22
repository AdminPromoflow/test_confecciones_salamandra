const notyf = new Notyf({
    duration: 2500,
    position: { x: 'center', y: 'top' }
});

const urlController = window.location.pathname;

function showMessage(type, message) {
    if (type === 'success') {
        notyf.success(message);
    } else {
        notyf.error(message);
    }
}

function initDataTable() {
    if ($.fn.DataTable.isDataTable('#simple-table')) {
        $('#simple-table').DataTable().destroy();
    }
    $('#simple-table').DataTable({
        language: {
            emptyTable: "No hay usuarios registrados",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            }
        },
        columnDefs: [
            { targets: [4, 5], className: "text-center" }
        ]
    });
    $('#table-show').fadeIn();
}

function renderizarTabla(usuarios) {
    $('#table-head').html(`
        <tr>
            <th>#</th>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    `);

    $('#table-body').empty();

    const roles = { '1': 'Admin', '2': 'Vendedor', '3': 'Cajero' };

    usuarios.forEach((u, i) => {
        const nombreCompleto = (u.nombre || '') + ' ' + (u.apellidos || '');
        const rol = roles[u.rol] || 'Desconocido';
        const esActivo = ['activo', '1', 1].includes(u.estado);
        const estadoBadge = esActivo
            ? '<span class="badge badge-success">Activo</span>'
            : '<span class="badge badge-danger">Inactivo</span>';

        const fila = `
            <tr data-id="${u.id_usuario}">
                <td>${i + 1}</td>
                <td>${u.usuario}</td>
                <td>${nombreCompleto.trim()}</td>
                <td>${u.email}</td>
                <td>${rol}</td>
                <td class="toggle-estado" data-estado="${u.estado}" style="cursor:pointer">${estadoBadge}</td>
                <td>
                    <span class="badge badge-primary editar-usuario" style="cursor:pointer" data-usuario='${JSON.stringify(u)}'>Editar</span>
                    <span class="badge badge-warning ml-1 cambiar-password" style="cursor:pointer" data-id="${u.id_usuario}">Contraseña</span>
                </td>
            </tr>
        `;
        $('#table-body').append(fila);
    });

    // Eventos
    $('.editar-usuario').off('click').on('click', function () {
        const u = $(this).data('usuario');
        $('#id-usuario').val(u.id_usuario);
        $('#nombre-usuario').val(u.nombre);
        $('#apellidos-usuario').val(u.apellidos);
        $('#login-usuario').val(u.usuario);
        $('#email-usuario').val(u.email);
        $('#rol-usuario').val(u.rol).trigger('change');
        $('#modal-editarUsuario').modal('show');
    });

    $('.cambiar-password').off('click').on('click', function () {
        const id = $(this).data('id');
        $('#id-usuario-password').val(id);
        $('#nueva-password, #confirmar-password').val('');
        $('#modal-cambiarPassword').modal('show');
    });

    $('.toggle-estado').off('click').on('click', function () {
        const id = $(this).closest('tr').data('id');
        const estadoActual = $(this).data('estado');
        actualizarEstado(id, estadoActual);
    });

    initDataTable();
}

function obtenerUsuarios() {
    $.ajax({
        url: urlController + '/obtenerUsuarios',
        method: 'GET',
        success: function (res) {
            if (res.success && res.respuesta) {
                renderizarTabla(res.respuesta);
            }
        },
        error: function () {
            showMessage('error', 'Error al cargar usuarios.');
        }
    });
}

function actualizarEstado(id, estadoActual) {
    const esActivo = ['1', 1, 'activo'].includes(estadoActual);
    const estadoParaEnviar = esActivo ? 1 : 0;

    $.ajax({
        url: urlController + '/actualizarEstado',
        method: 'POST',
        data: { id_usuario: id, estado: estadoParaEnviar },
        success: function (res) {
            if (res && res.success && res.respuesta && typeof res.respuesta.estado !== 'undefined') {
                const nuevoEstado = res.respuesta.estado;
                const fila = $(`tr[data-id="${id}"]`);
                fila.find('.toggle-estado').data('estado', nuevoEstado);
                fila.find('.toggle-estado').html(
                    nuevoEstado == 1
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>'
                );
                showMessage('success', 'Estado actualizado correctamente.');
            } else {
                const msg = res?.respuesta || 'No se pudo actualizar el estado.';
                showMessage('error', typeof msg === 'string' ? msg : 'Error desconocido.');
            }
        },
        error: function () {
            showMessage('error', 'Error de conexión. Intente nuevamente.');
        }
    });
}

function guardarUsuario() {
    const data = {
        id_usuario: $('#id-usuario').val(),
        nombre: $('#nombre-usuario').val().trim(),
        apellidos: $('#apellidos-usuario').val().trim(),
        usuario: $('#login-usuario').val().trim(),
        email: $('#email-usuario').val().trim(),
        rol: $('#rol-usuario').val()
    };

    if (!data.nombre || !data.usuario || !data.email) {
        showMessage('error', 'Complete todos los campos obligatorios.');
        return;
    }

    $.ajax({
        url: urlController + '/guardarUsuario',
        method: 'POST',
        data: data,
        success: function (res) {
            if (res.success) {
                $('#modal-editarUsuario').modal('hide');
                obtenerUsuarios();
                showMessage('success', 'Usuario actualizado correctamente.');
            } else {
                showMessage('error', res.respuesta || 'Error al guardar.');
            }
        },
        error: function () {
            showMessage('error', 'Error en la solicitud.');
        }
    });
}

function crearUsuario() {
    const data = {
        nombre: $('#nuevo-nombre-usuario').val().trim(),
        apellidos: $('#nuevo-apellidos-usuario').val().trim(),
        usuario: $('#nuevo-login-usuario').val().trim(),
        email: $('#nuevo-email-usuario').val().trim(),
        password: $('#nuevo-password-usuario').val(),
        rol: $('#nuevo-rol-usuario').val()
    };

    if (!data.nombre || !data.usuario || !data.email || !data.password) {
        showMessage('error', 'Todos los campos son obligatorios.');
        return;
    }

    if (data.password.length < 6) {
        showMessage('error', 'La contraseña debe tener al menos 6 caracteres.');
        return;
    }

    $.ajax({
        url: urlController + '/guardarUsuario',
        method: 'POST',
        data: data,
        success: function (res) {
            if (res.success) {
                $('#modal-nuevoUsuario').modal('hide');
                obtenerUsuarios();
                showMessage('success', 'Usuario creado correctamente.');
                $('#modal-nuevoUsuario input').val('');
            } else {
                showMessage('error', res.respuesta || 'Error al crear.');
            }
        },
        error: function () {
            showMessage('error', 'Error en la solicitud.');
        }
    });
}

function cambiarPassword() {
    const pass1 = $('#nueva-password').val();
    const pass2 = $('#confirmar-password').val();
    const id = $('#id-usuario-password').val();

    if (!pass1 || pass1.length < 6) {
        showMessage('error', 'La contraseña debe tener al menos 6 caracteres.');
        return;
    }
    if (pass1 !== pass2) {
        showMessage('error', 'Las contraseñas no coinciden.');
        return;
    }

    $.ajax({
        url: urlController + '/cambiarPassword',
        method: 'POST',
        data: {
            id_usuario: id,
            password: pass1  // ← debe llamarse "password"
        },
        success: function (res) {
            if (res.success) {
                $('#modal-cambiarPassword').modal('hide');
                showMessage('success', 'Contraseña actualizada.');
            } else {
                showMessage('error', res.respuesta || 'Error al cambiar la contraseña.');
            }
        },
        error: function () {
            showMessage('error', 'Error de conexión.');
        }
    });
}

$(document).ready(function () {
    // Agregar botón "Nuevo Usuario" al contenedor del breadcrumb o toolbar
    const nuevoBtn = $('<button>', {
        text: 'Nuevo Usuario',
        class: 'btn btn-primary btn-sm ml-2',
        'data-toggle': 'modal',
        'data-target': '#modal-nuevoUsuario'
    });
    $('#breadcrumb').append(nuevoBtn);

    obtenerUsuarios();

    $('#guardar-usuario').on('click', guardarUsuario);
    $('#crear-usuario').on('click', crearUsuario);
    $('#btn-cambiar-password').on('click', cambiarPassword);
});