<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase Subcategorias Controller
 */
class SubcategoriasController extends PulseController
{
    private $subcategoriaModel;

    public function __construct()
    {
        parent::__construct();

        if (!adminAccess()) {
            $this->function->redirectTo('backend/auth');
        }

        $this->subcategoriaModel = $this->model('SubcategoriaModel');
    }

    public function index()
    {
        $data = [
            'area' => 'inventario',
            'controller' => 'subcategorias',
            'description' => 'Resumen general',
            'content' => 'backview/subcategorias/index.php',
            'styles' => [
                'public/assets/plugins/data-tables/css/datatables.min.css',
                'public/assets/plugins/select2/css/select2.css'
            ],
            'scripts' => [
                'public/assets/plugins/data-tables/js/datatables.min.js',
                'public/assets/plugins/select2/js/select2.full.min.js',
                'public/assets/js/pages/datatables-custom.js',
                'public/assets/js/pages/select-custom.js',
                'public/customs/subcategorias.js'
            ],
        ];

        $this->view('backview/template/index', $data);
    }

    public function obtenerSubcategorias()
    {
        $subcategorias = $this->subcategoriaModel->obtenerSubcategorias();
        return $this->function->jsonResponse('respuesta', $subcategorias);
    }
}
