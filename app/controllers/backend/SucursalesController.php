<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase Sucursales Controller
 */
class SucursalesController extends PulseController
{
  private $sucursalModel;

  public function __construct()
  {
    parent::__construct();
    if (!adminAccess()) {
      $this->function->redirectTo('backend/auth');
    }
    $this->sucursalModel = $this->model('SucursalModel');
  }

  public function index()
  {
    $data = [
      'area' => 'inventario',
      'controller' => 'sucursales',
      'description' => 'Resumen general',
      'content' => 'backview/sucursales/index.php',
      'styles' => [
        'public/assets/plugins/data-tables/css/datatables.min.css',
        'public/assets/plugins/select2/css/select2.css'
      ],
      'scripts' => [
        'public/assets/plugins/data-tables/js/datatables.min.js',
        'public/assets/plugins/select2/js/select2.full.min.js',
        'public/assets/js/pages/datatables-custom.js',
        'public/assets/js/pages/select-custom.js',
        'public/customs/sucursales.js',
      ],
    ];

    $this->view('backview/template/index', $data);
  }

  public function obtenerSucursalesInventario()
  {
    $sucursales = $this->sucursalModel->obtenerSucursalesInventario();
    return $this->function->jsonResponse('respuesta', $sucursales);
  }

  public function guardarSucursal()
  {
    $sucursalData = $this->function->method('POST', TRUE);
    if (isset($sucursalData['id_sucursal'])) {
      $response = $this->sucursalModel->actualizarSucursal($sucursalData);
    } else {
      $response = $this->sucursalModel->crearSucursal($sucursalData);
    }

    return $this->function->jsonResponse('respuesta', $response);
  }
}
