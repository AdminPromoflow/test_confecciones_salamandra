<?php

// --- CONFIGURACIÓN CENTRALIZADA ---
const CONFIG = [
    'TOKEN_BOT' => "salamandrabot",
    'ACCESS_TOKEN' => "EAAX0dYIE0GEBOZBqcxZCSd8dtd7pyjioa1N0wufsGmOAcQFzjFezZBS8ZAyn2RlN4ZCuMs3qhZCdevhYJqySDOZA9fA8dPhwetqFOHL8HSDlgzv7AWNZBlOjqYa8K7C5loSowmOPQbcMMWSMaNAZBcnnhTbkPKwogkStpG1fL1Yvyeil8ZAZBR6OkUSavy3V73jQJ7B6wTHzdsALOskowqE007nrk3H",
    'PHONE_NUMBER_ID' => "524466370757094",
    'DB' => [
        'HOST' => "localhost",
        'NAME' => "u467113866_salamandra2",
        'USER' => "u467113866_4dm1n",
        'PASS' => "Q2w3e4r5t6y@*",
    ],
    'FILES' => [
        'ESTADOS_USUARIOS' => 'estados_usuarios.txt',
        'MENSAJES_PROCESADOS' => 'mensajes_procesados.txt',
        'LOGS' => [
            'MENSAJES' => 'mensajes.txt',
            'ERRORES' => 'errores.txt',
        ],
    ],
    'OPCIONES' => [
        '1' => 'Proporciona los detalles de la prenda para cotizar',
        '2' => 'Ingresa el número de seguimiento',
        '3' => 'Horarios de atención',
        '4' => 'Ubicación',
        '5' => 'Terminar conversación. ¡Gracias!'
    ],
    'INSTITUCIONES' => [
        ["id_institucion" => 1, "nombre" => "Andes", "valor" => 45000],
        ["id_institucion" => 2, "nombre" => "Camilo Torres", "valor" => 25000],
        ["id_institucion" => 3, "nombre" => "Children W", "valor" => 15000],
        ["id_institucion" => 4, "nombre" => "Divino Amor", "valor" => 35000],
        ["id_institucion" => 5, "nombre" => "Particular", "valor" => 55000],
        ["id_institucion" => 6, "nombre" => "TAV", "valor" => 65000],
        ["id_institucion" => 7, "nombre" => "Hum. Comp.", "valor" => 75000],
        ["id_institucion" => 8, "nombre" => "Hum. Temp.", "valor" => 85000],
        ["id_institucion" => 9, "nombre" => "Varios", "valor" => 95000],
        ["id_institucion" => 10, "nombre" => "Lozano", "valor" => 105000],
        ["id_institucion" => 11, "nombre" => "Manuel Aya", "valor" => 115000],
        ["id_institucion" => 12, "nombre" => "Paideia", "valor" => 125000],
        ["id_institucion" => 13, "nombre" => "Presentacion", "valor" => 135000],
        ["id_institucion" => 14, "nombre" => "Ricaurte", "valor" => 145000],
        ["id_institucion" => 15, "nombre" => "Santander", "valor" => 155000],
        ["id_institucion" => 16, "nombre" => "Santa Ines", "valor" => 165000],
        ["id_institucion" => 17, "nombre" => "Tecnico", "valor" => 175000],
        ["id_institucion" => 18, "nombre" => "Otros", "valor" => 185000],
        ["id_institucion" => 19, "nombre" => "Campestre", "valor" => 195000]
    ],
];

// --- INICIALIZACIÓN DE SESIÓN ---
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
        'read_and_close'  => false
    ]);
}

// --- CLASE MANEJADORA DE RESPUESTAS ---
class ResponseHandler {
    public function status($code) {
        http_response_code($code);
        return $this;
    }
    public function send($msg) {
        echo $msg;
        exit;
    }
}

// --- FUNCIONES DE MANEJO DE ESTADOS DE USUARIO ---
function leerEstadoUsuario($sender) {
    $file = CONFIG['FILES']['ESTADOS_USUARIOS'];
    if (!file_exists($file)) return '';

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($user, $state) = explode(':', $line, 2);
        if ($user === $sender) return $state;
    }
    return '';
}

function guardarEstadoUsuario($sender, $estado) {
    $file = CONFIG['FILES']['ESTADOS_USUARIOS'];
    $lines = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $newLines = [];
    $found = false;

    foreach ($lines as $line) {
        list($user, ) = explode(':', $line, 2);
        if ($user === $sender) {
            if ($estado !== '') $newLines[] = "$sender:$estado";
            $found = true;
        } else {
            $newLines[] = $line;
        }
    }

    if (!$found && $estado !== '') $newLines[] = "$sender:$estado";
    file_put_contents($file, implode("\n", $newLines));
}

// --- FUNCIONES DE VERIFICACIÓN DE TOKEN ---
function verificarToken($req) {
    try {
        if (($req['hub_verify_token'] ?? '') === CONFIG['TOKEN_BOT']) {
            (new ResponseHandler())->send($req['hub_challenge'] ?? '');
        } else {
            (new ResponseHandler())->status(403)->send("Forbidden");
        }
    } catch (Exception $e) {
        guardarError("Error en verificarToken: " . $e->getMessage());
        (new ResponseHandler())->status(400)->send("Bad Request");
    }
}

// --- FUNCIONES DE PROCESAMIENTO DE MENSAJES ---
function procesarMensaje($data) {
    try {
        guardarMensaje("Mensaje recibido: " . json_encode($data));
        $message = $data['entry'][0]['changes'][0]['value']['messages'][0] ?? [];

        if (empty($message) || $message['type'] !== 'text') return;

        // Control de mensajes duplicados
        $messageId = $message['id'];
        if (in_array($messageId, file(CONFIG['FILES']['MENSAJES_PROCESADOS'], FILE_IGNORE_NEW_LINES))) return;
        file_put_contents(CONFIG['FILES']['MENSAJES_PROCESADOS'], $messageId."\n", FILE_APPEND);

        $texto = strtolower($message['text']['body']);
        $sender = $message['from'];
        $opciones = CONFIG['OPCIONES'];

        $estadoUsuario = leerEstadoUsuario($sender);

        // Manejo de flujo de opción 2
        if ($estadoUsuario === 'solicitando_cedula' || $estadoUsuario === 'solicitando_factura') {
            manejarOpcion2($sender, $texto, $estadoUsuario);
            return;
        }

        // Manejo de flujo de opción 1
        if ($estadoUsuario === 'solicitando_institucion' || $estadoUsuario === 'mostrando_institucion') {
            manejarOpcion1($sender, $texto, $estadoUsuario);
            return;
        }

        // Mensaje de bienvenida original restaurado
        if ($estadoUsuario === 'finalizado' || !array_key_exists($texto, $opciones)) {
            $mensajeBienvenida = "¡Hola! Bienvenido a Confecciones Salamandra, soy Salamandrin el chatbot! 🦎\n\nPara ayudarte necesito que elijas una de estas opciones:\n\n1 - Cotizar\n2 - Consultar estado de factura\n3 - Horarios de atención\n4 - Ubicación\n5 - Terminar conversación\n\nPor favor, ingresa el número de la opción deseada.";
            responderMensaje($sender, $mensajeBienvenida);
            guardarEstadoUsuario($sender, 'activo');
            return;
        }

        if (array_key_exists($texto, $opciones)) {
            switch ($texto) {
                case '1':
                    $mensajeInstituciones = "Por favor elige una institución:\n";
                    foreach (CONFIG['INSTITUCIONES'] as $institucion) {
                        $mensajeInstituciones .= "{$institucion['id_institucion']} - {$institucion['nombre']}\n";
                    }
                    responderMensaje($sender, $mensajeInstituciones);
                    guardarEstadoUsuario($sender, 'solicitando_institucion');
                    break;
                case '2':
                    responderMensaje($sender, "Por favor ingresa tu número de cédula registrada:");
                    guardarEstadoUsuario($sender, 'solicitando_cedula');
                    break;
                case '3':
                    responderMensaje($sender, "Nuestro horario de atención es de lunes a viernes de 8:00 AM a 5:00 PM.");
                    guardarEstadoUsuario($sender, 'finalizado');
                    unlink("opcion2_data_$sender.txt"); // Eliminar archivo temporal
                    eliminarPersistencia($sender);
                    break;
                case '4':
                    responderMensaje($sender, "Nuestra dirección es Calle 123, Barrio Ejemplo, Ciudad.");
                    guardarEstadoUsuario($sender, 'finalizado');
                    unlink("opcion2_data_$sender.txt"); // Eliminar archivo temporal
                    eliminarPersistencia($sender);
                    break;
                case '5':
                    responderMensaje($sender, "¡Gracias por utilizar el chatbot de Confecciones Salamandra! 🦎 Esperamos verte pronto. ¡Hasta luego! 👋");
                    guardarEstadoUsuario($sender, 'finalizado');
                    unlink("opcion2_data_$sender.txt"); // Eliminar archivo temporal
                    break;
            }
        }

    } catch (Exception $e) {
        guardarError("Error en procesamiento: " . $e->getMessage());
        (new ResponseHandler())->status(500)->send("Error interno");
        unlink("opcion2_data_$sender.txt"); // Eliminar archivo temporal
    }
}

// --- FUNCIONES DE MANEJO DE OPCIÓN 2 (CONSULTA DE FACTURA) ---
function manejarOpcion2($sender, $texto, $estadoActual) {
    try {
        guardarMensaje("Iniciando manejo de opción 2. Estado actual: $estadoActual");

        // Validación numérica estricta
        if (!preg_match('/^\d+$/', $texto)) {
            guardarError("Validación fallida: El texto no es numérico");
            responderMensaje($sender, "⚠️ Formato incorrecto. Solo se permiten números. Por favor ingresa nuevamente:");
            return;
        }

        if ($estadoActual === 'solicitando_cedula') {
            guardarMensaje("Guardando cédula en archivo temporal: $texto");
            file_put_contents("opcion2_data_$sender.txt", "cedula:$texto");
            responderMensaje($sender, "Ahora ingresa el número de factura:");
            guardarEstadoUsuario($sender, 'solicitando_factura');
        }
        elseif ($estadoActual === 'solicitando_factura') {
            guardarMensaje("Procesando factura...");
            $cedula = file_exists("opcion2_data_$sender.txt") ? explode(':', file_get_contents("opcion2_data_$sender.txt"))[1] : null;
            $factura = $texto;

            if (!$cedula) {
                guardarError("Error: No se encontró cédula en archivo temporal");
                throw new Exception("Datos de sesión perdidos");
            }

            guardarMensaje("Consultando factura para cédula: $cedula, factura: $factura");
            $resultado = consultarFacturaBD($cedula, $factura);

            if ($resultado === false) {
                guardarError("Error en la consulta a la base de datos");
                responderMensaje($sender, "🔴 Error temporal al consultar los datos. Por favor intenta más tarde.");
                guardarEstadoUsuario($sender, 'finalizado');
                unlink("opcion2_data_$sender.txt"); // Eliminar archivo temporal
                return;
            }
            elseif (empty($resultado)) {
                guardarError("No se encontraron registros");
                $mensaje = "⚠️ No encontramos registros con:\n";
                $mensaje .= "Cédula: $cedula\nFactura: $factura\n\n";
                $mensaje .= "Verifica los datos o contacta a soporte 📞312 485-8575";
                responderMensaje($sender, $mensaje);
                guardarEstadoUsuario($sender, 'finalizado');
                unlink("opcion2_data_$sender.txt"); // Eliminar archivo temporal
                return;
            }
            else {
                guardarMensaje("Resultados encontrados: " . print_r($resultado, true));
                $respuesta = "📄 **Detalles de tu factura**\n\n";
                $respuesta .= "🔢 Número: $factura\n";
                $respuesta .= "👤 Cédula: $cedula\n\n";
                $respuesta .= "📦 **Productos**\n\n";

                foreach ($resultado as $item) {
                    $date = DateTime::createFromFormat('Y-m-d H:i:s', $item['fecha_entrega']);
                    $fecha_formateada = $date->format('d-m-Y');
                    $respuesta .= "➤ {$item['codigo']} - {$item['nombre']} ({$item['talla']})\n";
                    $respuesta .= "   Estado: {$item['estado']}\n";
                    $respuesta .= "   Entrega programada: {$fecha_formateada}\n\n";
                }

                responderMensaje($sender, $respuesta);
                guardarEstadoUsuario($sender, 'finalizado');
                unlink("opcion2_data_$sender.txt"); // Eliminar archivo temporal
                return;
            }

            // Reiniciar estado y datos
            guardarEstadoUsuario($sender, 'activo');
            unlink("opcion2_data_$sender.txt"); // Eliminar archivo temporal
            guardarMensaje("Proceso de factura completado exitosamente");
        }
    } catch (Exception $e) {
        guardarError("Excepción en manejarOpcion2: " . $e->getMessage());
        guardarError("Trace: " . $e->getTraceAsString());
        responderMensaje($sender, "⚠️ Ocurrió un error inesperado. Estamos trabajando para solucionarlo.");
        guardarEstadoUsuario($sender, 'finalizado');
        unlink("opcion2_data_$sender.txt"); // Eliminar archivo temporal
        return;
    }
}

// --- FUNCIONES DE MANEJO DE OPCIÓN 1 (COTIZAR) ---
function manejarOpcion1($sender, $texto, $estadoActual) {
    try {
        guardarMensaje("Iniciando manejo de opción 1. Estado actual: $estadoActual");

        if ($estadoActual === 'solicitando_institucion') {
            $institucionId = intval($texto);
            $institucion = array_filter(CONFIG['INSTITUCIONES'], function($inst) use ($institucionId) {
                return $inst['id_institucion'] === $institucionId;
            });

            if (empty($institucion)) {
                responderMensaje($sender, "⚠️ Opción no válida. Por favor elige una opción correcta:");
                $mensajeInstituciones = "Por favor elige una institución:\n";
                foreach (CONFIG['INSTITUCIONES'] as $institucion) {
                    $mensajeInstituciones .= "{$institucion['id_institucion']} - {$institucion['nombre']}\n";
                }
                responderMensaje($sender, $mensajeInstituciones);
                return;
            }

            $institucion = reset($institucion);
            $mensaje = "Has elegido:\n";
            $mensaje .= "Nombre: {$institucion['nombre']}\n";
            $mensaje .= "Valor: {$institucion['valor']}\n\n";
            $mensaje .= "¿Deseas cotizar una nueva prenda o terminar la conversación?\n";
            $mensaje .= "1 - Cotizar nueva prenda\n";
            $mensaje .= "2 - Terminar conversación";

            responderMensaje($sender, $mensaje);
            guardarEstadoUsuario($sender, 'mostrando_institucion');
        } elseif ($estadoActual === 'mostrando_institucion') {
            if ($texto === '1') {
                $mensajeInstituciones = "Por favor elige una institución:\n";
                foreach (CONFIG['INSTITUCIONES'] as $institucion) {
                    $mensajeInstituciones .= "{$institucion['id_institucion']} - {$institucion['nombre']}\n";
                }
                responderMensaje($sender, $mensajeInstituciones);
                guardarEstadoUsuario($sender, 'solicitando_institucion');
            } elseif ($texto === '2') {
                responderMensaje($sender, "¡Gracias por utilizar el chatbot de Confecciones Salamandra! 🦎 Esperamos verte pronto. ¡Hasta luego! 👋");
                guardarEstadoUsuario($sender, 'finalizado');
                unlink("opcion2_data_$sender.txt"); // Eliminar archivo temporal
            } else {
                responderMensaje($sender, "⚠️ Opción no válida. Por favor elige una opción correcta:");
                $mensaje = "¿Deseas cotizar una nueva prenda o terminar la conversación?\n";
                $mensaje .= "1 - Cotizar nueva prenda\n";
                $mensaje .= "2 - Terminar conversación";
                responderMensaje($sender, $mensaje);
            }
        }
    } catch (Exception $e) {
        guardarError("Excepción en manejarOpcion1: " . $e->getMessage());
        guardarError("Trace: " . $e->getTraceAsString());
        responderMensaje($sender, "⚠️ Ocurrió un error inesperado. Estamos trabajando para solucionarlo.");
        guardarEstadoUsuario($sender, 'finalizado');
        unlink("opcion2_data_$sender.txt"); // Eliminar archivo temporal
        return;
    }
}

// --- FUNCIONES DE CONSULTA A LA BASE DE DATOS ---
function consultarFacturaBD($cedula, $factura) {
    try {
        guardarMensaje("Conectando a la base de datos...");
        // Set DSN
        $dsn = 'mysql:host=' . CONFIG['DB']['HOST'] . ';dbname=' . CONFIG['DB']['NAME'] . ';charset=utf8';
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        $pdo = new PDO($dsn, CONFIG['DB']['USER'], CONFIG['DB']['PASS'], $options);
        guardarMensaje("Conexión exitosa");

        // Paso 1: Obtener ID del cliente
        $queryCliente = "SELECT id_cliente FROM clientes WHERE cedula = :cedula";
        guardarMensaje("Ejecutando consulta cliente: $queryCliente");
        $stmt = $pdo->prepare($queryCliente);
        $stmt->bindValue(':cedula', $cedula, PDO::PARAM_STR);
        $stmt->execute();
        $cliente = $stmt->fetch();

        if (!$cliente) {
            guardarError("Cliente no encontrado con cédula: $cedula");
            return [];
        }
        guardarMensaje("Cliente encontrado: " . print_r($cliente, true));

        // Paso 2: Consultar detalle de venta
        $queryDetalle = "
            SELECT
                p.codigo,
                p.nombre,
                p.talla,
                dv.estado,
                dv.fecha_entrega
            FROM detalle_venta dv
            JOIN productos p ON dv.id_producto = p.id_producto
            WHERE dv.id_venta = :factura
            AND dv.estado IN (0, 1, 2, 3)
            AND dv.id_cliente = :id_cliente
        ";

        guardarMensaje("Ejecutando consulta detalles: $queryDetalle");
        $stmt = $pdo->prepare($queryDetalle);
        $stmt->bindValue(':factura', $factura, PDO::PARAM_INT);
        $stmt->bindValue(':id_cliente', $cliente['id_cliente'], PDO::PARAM_INT);
        $stmt->execute();
        $resultados = $stmt->fetchAll();

        if (empty($resultados)) {
            guardarError("No se encontraron detalles para factura: $factura y cliente ID: {$cliente['id_cliente']}");
            return [];
        }
        guardarMensaje("Detalles encontrados: " . print_r($resultados, true));

        // Mapear estados
        $estados = [
            0 => '🔴 Pendiente de producción',
            1 => '🔴 Pendiente de producción',
            2 => '🔵 En camino a punto de venta',
            3 => '🟡 Listo para entrega'
        ];

        return array_map(function($item) use ($estados) {
            $item['estado'] = $estados[$item['estado']] ?? '⚪ Estado desconocido';
            return $item;
        }, $resultados);

    } catch (PDOException $e) {
        guardarError("Error PDO: " . $e->getMessage());
        guardarError("Trace: " . $e->getTraceAsString());
        return false;
    } catch (Exception $e) {
        guardarError("Error general: " . $e->getMessage());
        guardarError("Trace: " . $e->getTraceAsString());
        return false;
    }
}

// --- FUNCIONES DE ENVÍO DE RESPUESTAS A WHATSAPP ---
function responderMensaje($destinatario, $mensaje) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "https://graph.facebook.com/v13.0/".CONFIG['PHONE_NUMBER_ID']."/messages",
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer ".CONFIG['ACCESS_TOKEN'],
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode([
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $destinatario,
            "type" => "text",
            "text" => ["body" => $mensaje]
        ])
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        guardarError("Error cURL: " . curl_error($ch));
    } else {
        guardarMensaje("Respuesta API WhatsApp ($httpCode): " . $response);
    }

    curl_close($ch);
}

// --- MANEJO PRINCIPAL DE SOLICITUDES ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    procesarMensaje(json_decode(file_get_contents('php://input'), true));
} elseif (isset($_GET['hub_mode']) && $_GET['hub_mode'] === 'subscribe') {
    verificarToken($_GET);
} else {
    (new ResponseHandler())->status(405)->send("Método no permitido");
}

// --- FUNCIONES PARA GUARDAR MENSAJES Y ERRORES ---
function guardarMensaje($mensaje) {
    file_put_contents(CONFIG['FILES']['LOGS']['MENSAJES'], date('Y-m-d H:i:s') . " - " . $mensaje . "\n", FILE_APPEND);
}

function guardarError($mensaje) {
    file_put_contents(CONFIG['FILES']['LOGS']['ERRORES'], date('Y-m-d H:i:s') . " - " . $mensaje . "\n", FILE_APPEND);
}

// --- FUNCIÓN PARA ELIMINAR PERSISTENCIA ---
function eliminarPersistencia($sender) {
    $file = CONFIG['FILES']['ESTADOS_USUARIOS'];
    $lines = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $newLines = [];

    foreach ($lines as $line) {
        list($user, ) = explode(':', $line, 2);
        if ($user !== $sender) {
            $newLines[] = $line;
        }
    }

    file_put_contents($file, implode("\n", $newLines));
    if (file_exists("opcion2_data_$sender.txt")) {
        unlink("opcion2_data_$sender.txt");
    }
}
?>
