<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase Proveedores Controller
 */
class ProveedoresController extends PulseController
{
    private $proveedorModel;

    public function __construct()
    {
        parent::__construct();

        if (!adminAccess()) {
            $this->function->redirectTo('backend/auth');
        }

        $this->proveedorModel = $this->model('ProveedorModel');
    }

    public function index()
    {
        $data = [
            'area' => 'inventario',
            'controller' => 'proveedores',
            'description' => 'Resumen general',
            'content' => 'backview/proveedores/index.php',
            'styles' => [
                'public/assets/plugins/data-tables/css/datatables.min.css',
                'public/assets/plugins/select2/css/select2.css'
            ],
            'scripts' => [
                'public/assets/plugins/data-tables/js/datatables.min.js',
                'public/assets/plugins/select2/js/select2.full.min.js',
                'public/assets/js/pages/datatables-custom.js',
                'public/assets/js/pages/select-custom.js',
                'public/customs/proveedores.js'
            ],
        ];

        $this->view('backview/template/index', $data);
    }

    public function obtenerProveedores()
    {
        $proveedores = $this->proveedorModel->obtenerProveedores();
        return $this->function->jsonResponse('respuesta', $proveedores);
    }

    public function obtenerProveedoresByTipo($tipo)
    {
        $proveedores = $this->proveedorModel->obtenerProveedoresByTipo($tipo);
        return $this->function->jsonResponse('respuesta', $proveedores);
    }
}
