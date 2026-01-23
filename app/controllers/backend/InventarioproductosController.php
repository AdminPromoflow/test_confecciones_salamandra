<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase InventarioProductos Controller
 */
class InventarioproductosController extends PulseController
{
    private $inventarioModel, $productoModel, $sucursalModel;
    private $idSucursal;

    public function __construct()
    {
        parent::__construct();

        if (!adminAccess() && !sellerAccess()) {
            $this->function->redirectTo('backend/auth');
        }

        $this->inventarioModel = $this->model('InventarioProductoModel');
        $this->productoModel = $this->model('ProductoModel');
        $this->sucursalModel = $this->model('SucursalModel');


        $this->idSucursal = $this->session->getUserData('userSession', 'usuarioSucursal');
    }

    public function index()
    {
        $data = [
            'area' => 'inventario',
            'controller' => 'inventarioproducto',
            'description' => 'Resumen general',
            'content' => 'backview/categorias/index.php',
            'styles' => [
                'public/assets/plugins/data-tables/css/datatables.min.css',
                'public/assets/plugins/select2/css/select2.css'
            ],
            'scripts' => [
                'public/assets/plugins/data-tables/js/datatables.min.js',
                'public/assets/plugins/select2/js/select2.full.min.js',
                'public/assets/js/pages/datatables-custom.js',
                'public/assets/js/pages/select-custom.js',
                'public/customs/inventarioProductos.js'
            ],
        ];

        $this->view('backview/template/index', $data);
    }

    public function obtenerStockByProducto($idProducto)
    {
        $response = $this->inventarioModel->obtenerStockByProducto($idProducto);
        return $this->function->jsonResponse('respuesta', $response);
    }

    public function obtenerStockBySucursal($id_sucursal)
    {
        $response = $this->inventarioModel->obtenerStockBySucursal($id_sucursal);
        return $this->function->jsonResponse('respuesta', $response);
    }

    public function actualizarStock()
    {
        $postData = $this->function->method('POST', TRUE);
        $response = $this->inventarioModel->sumamosInventario($postData['id_producto'], $postData['id_sucursal']);
        return $this->function->jsonResponse('respuesta', $response);
    }

    // *********** Provisional **************

    public function botonPanico()
    {
        $data = [
            'area' => 'inventario',
            'controller' => 'inventarioproductos',
            'description' => 'Botón de pánico JEJE',
            'content' => 'backview/inventarios/panico.php',
            'styles' => [
                'public/assets/plugins/select2/css/select2.css'
            ],
            'scripts' => [
                'public/assets/plugins/select2/js/select2.full.min.js',
                'public/assets/js/pages/select-custom.js',
                // 'public/customs/inventarioProductos.js'
            ],
            'productos' => $this->productoModel->obtenerProductos(),
            'sucursales' => $this->sucursalModel->obtenerSucursales()
        ];

        $this->view('backview/template/index', $data);
    }

    public function modificarStock() 
    {
        $postData = $this->function->method('POST', TRUE);
        $this->inventarioModel->aumentarStock($postData['producto'], $postData['sucursal'], $postData['cantidad']);
        $this->session->setFlashMessage('success', 'Inventario modificado.');
        $this->function->redirectTo('backend/inventarioproductos/botonPanico');
    }
}
