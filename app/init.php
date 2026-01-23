<?php




    require_once __DIR__ . '/../logs/Logger.php';
    Logger::init(__DIR__ . '/../logs/app.log');

    // Verificar el estado actual de la sesión
    $current_session_status = session_status();

    // Load Config
    require_once '../system/config/constants.php';

    // Configuración de errores según el entorno
    if (!defined('ENVIRONMENT')) {
        error_reporting(E_ALL); // Mostrar todos los errores
        ini_set('display_errors', '1'); // Mostrar errores en pantalla
    }

    require_once CONFIG_PATH . 'config.php';

    // Configuración de errores según el entorno
    // if (ENVIRONMENT === 'production') {
    //     error_reporting(0); // Desactivar todos los errores
    //     ini_set('display_errors', '0'); // No mostrar errores en pantalla
    // } else {
    //     error_reporting(E_ALL); // Mostrar todos los errores
    //     ini_set('display_errors', '1'); // Mostrar errores en pantalla
    // }

    // Activar o desactivar las sesiones según la configuración y el estado actual
    if (ENABLE_SESSION && $current_session_status !== PHP_SESSION_ACTIVE) {
        session_start();
    }


    require_once FUNCTIONS_PATH . 'functions.php';

    // Autoload de las clases y funciones de Pulse
    spl_autoload_register(function($class_name) {
        require_once SYSTEM_PATH . $class_name . '.php';
    });
