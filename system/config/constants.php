<?php
define('BASEPATH', TRUE);

// Acceso rutas absolutas
define('DS', DIRECTORY_SEPARATOR);
define('PROJECT_PATH', dirname(dirname(dirname(__FILE__))) . DS);

define('APP_PATH', PROJECT_PATH . 'app' . DS);
define('CONFIG_PATH', APP_PATH . 'config' . DS);
define('CONTROLLERS_PATH', APP_PATH . 'controllers' . DS);
define('FUNCTIONS_PATH', APP_PATH . 'functions' . DS);
define('MODELS_PATH', APP_PATH . 'models' . DS);
define('VIEWS_PATH', APP_PATH . 'views' . DS);

define('PUBLIC_PATH', PROJECT_PATH . 'public' . DS);

define('SYSTEM_PATH', PROJECT_PATH . 'system' . DS);
Logger::log(PROJECT_PATH);
