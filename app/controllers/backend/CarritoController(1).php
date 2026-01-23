<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase Carrito Controller
 */
class CarritoController extends PulseController
{
    private $carritoModel, $inventarioProductoModel;
    private $idUsuario, $idSucursal;

    public function __construct()
    {
        parent::__construct();

        if (!sellerAccess()) {
            // $this->function->redirectTo('backend/auth');
        }

        $this->carritoModel = $this->model('CarritoModel');
        $this->inventarioProductoModel = $this->model('InventarioProductoModel');

        $this->idSucursal = $this->session->getUserData('userSession', 'usuarioSucursal');
        $this->idUsuario = $this->session->getUserData('userSession', 'usuarioId');
    }

    public function index()
    {
        $data = [
            'area' => 'inventario',
            'controller' => 'carrito',
            'description' => 'Resumen general',
            'content' => 'backview/carrito/index.php',
            'styles' => [
                'public/assets/plugins/data-tables/css/datatables.min.css',
            ],
            'scripts' => [
                'public/assets/plugins/data-tables/js/datatables.min.js',
                'public/assets/plugins/select2/js/select2.full.min.js',
                'public/assets/js/pages/datatables-custom.js',
                'public/customs/carrito.js'
            ]
        ];

        $this->view('backview/template/index', $data);
    }

    public function obtenerAll()
    {
        $carrito = $this->carritoModel->obtenerAll();
        $this->function->jsonResponse('respuesta', $carrito);
    }

    public function obtenerCarrito()
    {
        $postData = $this->function->method('POST', TRUE);
        $carrito = $this->carritoModel->obtenerCarrito($postData['id_cliente'], $this->idSucursal);
        $this->function->jsonResponse('respuesta', $carrito);
    }

    public function agregarProductoCarrito()
    {
        $postData = $this->function->method('POST', TRUE);
        if ($postData['id_sucursal'] == '') {
            $postData['id_sucursal'] = $this->idSucursal;
        }
        $postData['id_usuario'] = $this->idUsuario;
        $postData['fecha_entrega'] = $postData['fecha_entrega'] != '' ? $postData['fecha_entrega'] : null;
        $this->carritoModel->guardarCarrito($postData);

        // Restamos en el inventario 1
        $response = $this->inventarioProductoModel->restamosInventario($postData['id_producto'], $postData['id_sucursal']);
        $this->function->jsonResponse('respuesta', $response);
    }

    public function quitarProductoCarrito()
    {
        $postData = $this->function->method('POST', TRUE);

        $this->inventarioProductoModel->sumamosInventario($postData['id_producto'], $this->idSucursal);
        $respuesta = $this->carritoModel->quitarProducto($postData['id_carrito']);

        $this->function->jsonResponse('respuesta', $respuesta);
    }

    public function vaciarCarrito()
    {
        $postData = $this->function->method('POST', TRUE);

        $carrito = $this->carritoModel->obtenerCarrito($postData['id_cliente']);

        foreach ($carrito as $detalle) {
            $respuesta = $this->carritoModel->quitarProducto($detalle->id_carrito);
            $this->inventarioProductoModel->sumamosInventario($detalle->id_producto, $this->idSucursal);
        }

        $this->function->jsonResponse('respuesta', $respuesta);
    }
}
