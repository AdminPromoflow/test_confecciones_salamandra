<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase Productos Controller
 */
class ProductosController extends PulseController
{
  private $productoModel, $categoriaModel, $subcategoriaModel, $institucionModel, $proveedorModel, $inventarioProductoModel;

  public function __construct()
  {
    parent::__construct();

    if (!adminAccess()) {
      $this->function->redirectTo('backend/auth');
    }

    $this->productoModel = $this->model('ProductoModel');
    $this->categoriaModel = $this->model('CategoriaModel');
    $this->subcategoriaModel = $this->model('SubcategoriaModel');
    $this->institucionModel = $this->model('InstitucionModel');
    $this->proveedorModel = $this->model('ProveedorModel');
    $this->inventarioProductoModel = $this->model('InventarioProductoModel');
  }

  public function index()
  {
    $data = [
      'area' => 'inventario',
      'controller' => 'productos',
      'description' => 'Resumen general',
      'content' => 'backview/productos/index.php',
      'styles' => [
        'public/assets/plugins/data-tables/css/datatables.min.css',
        'public/assets/plugins/select2/css/select2.css'
      ],
      'scripts' => [
        'public/assets/plugins/data-tables/js/datatables.min.js',
        'public/assets/plugins/select2/js/select2.full.min.js',
        // 'public/assets/js/pages/datatables-custom.js',
        'public/assets/js/pages/select-custom.js',
        'public/customs/productos.js'
      ],
    ];

    $this->view('backview/template/index', $data);
  }

  public function obtenerProductos()
  {
    $productos = $this->productoModel->obtenerProductos();
    return $this->function->jsonResponse('respuesta', $productos);
  }

  public function obtenerProductosActivos()
  {
    $productos = $this->productoModel->obtenerProductosAcivos();
    return $this->function->jsonResponse('respuesta', $productos);
  }

  public function guardarProducto()
  {
    $productoData = $this->function->method('POST', TRUE);
    if (isset($productoData['id_producto'])) {
      $response = $this->productoModel->actualizarProducto($productoData);
    } else {
      $id_producto = $this->productoModel->crearProducto($productoData);
      for ($i=1; $i <= 4 ; $i++) { 
        $data = [
          'producto' => $id_producto,
          'sucursal_destino' => $i,
          'cantidad' => 0,
          'sucursal_origen' => 1
        ];
        $this->inventarioProductoModel->registrarInventario($data);
      }
    }

    return $this->function->jsonResponse('respuesta', true);
  }

  public function actualizarEstado()
  {
    $productoData = $this->function->method('POST', TRUE);
    $response = $this->productoModel->actualizarEstado($productoData);
    $this->function->jsonResponse('respuesta', $response);
  }
}
