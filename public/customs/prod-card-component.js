$(document).ready(function () {

    // Variables

    const urlController = window.location.pathname;
    const urlPath = urlController.slice(0, urlController.lastIndexOf("/"));

    // Funciones

    function obtenerProduccionPendiente() {
        $.ajax({
            url: urlPath + '/producciones/obtenerTotalProduccionPendiente',
            type: 'GET'
        })
            .done(function (response) {
                // console.log(response.respuesta);
                let produccion = response.respuesta;
                let titulo = 'Producción Pendiente';
                agregarTarjeta(titulo, produccion);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            });
    }

    function agregarTarjeta(titulo, produccion) {
        var nuevaCard = $('<div>', {
            class: 'col-md-6 col-xl-4'
        }).append(
            $('<div>', {
                class: 'card card-social'
            }).append(
                $('<div>', {
                    class: 'card-block border-bottom'
                }).append(
                    $('<div>', {
                        class: 'row align-items-center justify-content-center'
                    }).append(
                        $('<div>', {
                            class: 'col-auto'
                        }).append(
                            $('<i>', {
                                class: 'feather icon-pause-circle',
                                width: 24
                            })
                        ),
                        $('<div>', {
                            class: 'col text-right'
                        }).append(
                            $('<h5>', {
                                class: 'text-c-red mb-0',
                                text: titulo
                            }),
                            $('<h3>', {
                                text: produccion.cantidad_total
                            })
                        )
                    )
                )
            )
        );

        // Agregar la nuevaCard al contenedor
        $('#contenedor').append(nuevaCard);
    }

    // Acciones

    obtenerProduccionPendiente()
})
