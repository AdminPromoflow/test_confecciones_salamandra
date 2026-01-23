<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// require_once 'Validations.php';
require_once 'PulseCsrf.php';
// require_once 'File.php';
require_once __DIR__ . '/PulseFunctions.php';
require_once 'PulseSessions.php';

/**
 * Clase PulseController
 */
class PulseController
{
    protected $validation, $csrf, $function, $model, $session;

    public function __construct()
    {
        // $this->validations = new Validations();
        $this->csrf = new PulseCsrf();
        // $this->files = new File();
        $this->function = new PulseFunctions();
        $this->session = new PulseSessions();
    }

    // Load model
    public function model($model_name)
    {
        // check for view file
        if (file_exists(MODELS_PATH . $model_name . '.php')) {
            require_once MODELS_PATH . $model_name . '.php';

            if (!class_exists($model_name)) {
                throw new PulseErrorHandler("Error: La Clase <b>(--> {$model_name})</b> no está definida en el archivo.");
            }

            // Instatiate model
            return new $model_name();
        } else {
            // Model does not exist
            throw new PulseErrorHandler("Error: El Modelo <b>(--> {$model_name})</b> no existe");
        }
    }

    // Load view
    public function view($view_name, $data_store = [])
    {
        // check for view file
        if (file_exists(VIEWS_PATH . $view_name . '.php')) {
            // Extraer los datos para que estén disponibles en la vista
            extract($data_store);
            require_once VIEWS_PATH . $view_name . '.php';
        } else {
            // View does not exist
            throw new PulseErrorHandler("Error: La Vista <b>(--> {$view_name})</b> no existe");
        }
    }
}
