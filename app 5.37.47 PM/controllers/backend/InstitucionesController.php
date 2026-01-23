<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase Instituciones Controller
 */
class InstitucionesController extends PulseController
{
    private $institucionModel;

    public function __construct()
    {
        parent::__construct();

        if (!adminAccess()) {
            $this->function->redirectTo('backend/auth');
        }

        $this->institucionModel = $this->model('InstitucionModel');
    }

    public function index()
    {
        $data = [
            'area' => 'inventario',
            'controller' => 'instituciones',
            'description' => 'Resumen general',
            'content' => 'backview/instituciones/index.php',
            'styles' => [
                'public/assets/plugins/data-tables/css/datatables.min.css',
                'public/assets/plugins/select2/css/select2.css'
            ],
            'scripts' => [
                'public/assets/plugins/data-tables/js/datatables.min.js',
                'public/assets/plugins/select2/js/select2.full.min.js',
                'public/assets/js/pages/datatables-custom.js',
                'public/assets/js/pages/select-custom.js',
                'public/customs/instituciones.js'
            ],
        ];

        $this->view('backview/template/index', $data);
    }

    public function obtenerInstituciones()
    {
        $instituciones = $this->institucionModel->obtenerInstituciones();
        return $this->function->jsonResponse('respuesta', $instituciones);
    }
}
