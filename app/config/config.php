<?php
defined('BASEPATH') or exit('No direct script access allowed');

date_default_timezone_set("America/Bogota");

// Site Name, Project Name Local, Project Name Remote y App Version
define('SITE_NAME', 'Confecciones Salamandra');
define('PROJECT_LOCAL', 'localhost/salamandra');
define('PROJECT_REMOTE', 'www.aleinarossui.com');
define('APP_VERSION', '2.0.0');

if ($_SERVER['SERVER_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_ADDR'] === '::1' || $_SERVER['SERVER_ADDR'] === 'localhost') {
    define('ENVIRONMENT', 'development'); // Configuración del entorno
    define('ENABLE_SESSION', TRUE); // Por defecto, las sesiones están desactivadas
    define('URL_PATH', 'http://' . PROJECT_LOCAL . '/'); // Acceso relativo
} else {
    define('ENVIRONMENT', 'production'); // Configuración del entorno
    define('ENABLE_SESSION', TRUE); // Por defecto, las sesiones están activadas
    define('URL_PATH', 'https://' . PROJECT_REMOTE . '/'); // Acceso relativo
}


// Opción 1: Detectar por HTTP_HOST
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    // desarrollo
} else {
    // producción
}

// Controlador por defecto y Metodo por defecto
define('DEFAULT_CONTROLLER', 'home');
define('DEFAULT_METHOD', 'index');

if (ENVIRONMENT == 'development') {
    // Configuracion de la base de datos en desarrollo
    define('DB_HOST', 'localhost');
    define('DB_USER', 'u348170507_oscar');
    define('DB_PASS', 'Con_Salamadra_2026!');
    define('DB_NAME', 'u348170507_con_salamandra');
    define('DB_CHAR', 'utf8mb4');
} else {
    // Configuracion de la base de datos en produccion
    define('DB_HOST', 'localhost');
    define('DB_USER', 'u348170507_oscar');
    define('DB_PASS', 'Con_Salamadra_2026!');
    define('DB_NAME', 'u348170507_con_salamandra');
    define('DB_CHAR', 'utf8mb4');
}

// Configuración de PHPMailer
function getMailerConfig()
{
    return [
        'SMTPAuth' => true,
        'SMTPSecure' => 'ssl', // Puedes cambiar a 'ssl' si es necesario
        'Host' => 'smtp.hostinger.com',
        'Port' => 465,
        'Username' => 'prueba@confeccionessalamandra.com',
        'Password' => 'Q2w3e4r5t6y@*',
        'FromAddress' => 'prueba@confeccionessalamandra.com',
        'FromName' => 'Correo de Prueba',
        // ... Otras configuraciones de correo ...
    ];
}
