<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase Categorias Controller
 */
class CategoriasController extends PulseController
{
    private $categoriaModel;

    public function __construct()
    {
        parent::__construct();

        if (!adminAccess()) {
            $this->function->redirectTo('backend/auth');
        }

        $this->categoriaModel = $this->model('CategoriaModel');
    }

    public function index()
    {
        $data = [
            'area' => 'inventario',
            'controller' => 'categorias',
            'description' => 'Resumen general',
            'content' => 'backview/categorias/index.php',
            'styles' => [
                'public/assets/plugins/data-tables/css/datatables.min.css',
                'public/assets/plugins/select2/css/select2.css'
            ],
            'scripts' => [
                'public/assets/plugins/data-tables/js/datatables.min.js',
                'public/assets/js/pages/datatables-custom.js',
                'public/assets/plugins/select2/js/select2.full.min.js',
                'public/assets/js/pages/select-custom.js',
                'public/customs/categorias.js'
            ],
        ];

        $this->view('backview/template/index', $data);
    }

    public function obtenerCategorias()
    {
        $categorias = $this->categoriaModel->obtenerCategorias();
        return $this->function->jsonResponse('respuesta', $categorias);
    }
}
