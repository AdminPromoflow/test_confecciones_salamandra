<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase Producciones Controller
 */
class ProduccionesController extends PulseController
{
  private $produccionModel, $detalleventaModel, $inventarioProductoModel, $programacionModel, $requisicionModel;
  private $estados = [];

  public function __construct()
  {
    parent::__construct();

    if (!OperatorAccess()) {
      $this->function->redirectTo('backend/auth');
    }

    $this->produccionModel = $this->model('ProduccionModel');
    $this->detalleventaModel = $this->model('DetalleVentaModel');
    $this->inventarioProductoModel = $this->model('InventarioProductoModel');
    $this->programacionModel = $this->model('ProgramacionModel');
    $this->requisicionModel = $this->model('RequisicionModel');
  }

  public function index()
  {
    $data = [
      'area' => '',
      'controller' => 'producciones',
      'description' => 'resumen general',
      'content' => 'backview/producciones/index.php',
      'styles' => [
        'public/assets/plugins/data-tables/css/datatables.min.css'
      ],
      'scripts' => [
        'public/assets/plugins/data-tables/js/datatables.min.js',
        // 'public/assets/js/pages/datatables-custom.js',
        'public/customs/prod-card-component.js',
        'public/customs/programacion.js'
      ]
    ];

    $this->view('backview/template/index', $data);
  }

  public function pendientes()
  {
    $data = [
      'area' => '',
      'controller' => 'producciones',
      'description' => 'Producción pendiente',
      'metodo' => 'producciones',
      'content' => 'backview/producciones/pendientes.php',
      'styles' => [
        'public/assets/plugins/data-tables/css/datatables.min.css'
      ],
      'scripts' => [
        'public/assets/plugins/data-tables/js/datatables.min.js',
        // 'public/assets/js/pages/datatables-custom.js',
        'public/customs/producciones.js'
      ]
    ];

    $this->view('backview/template/index', $data);
  }

  public function requisiciones()
  {
    $data = [
      'area' => '',
      'controller' => 'producciones',
      'description' => 'Requisición pendiente',
      'metodo' => "requisiciones",
      'content' => 'backview/producciones/requisiciones.php',
      'styles' => [
        'public/assets/plugins/data-tables/css/datatables.min.css'
      ],
      'scripts' => [
        'public/assets/plugins/data-tables/js/datatables.min.js',
        // 'public/assets/js/pages/datatables-custom.js',
        'public/customs/producciones.js'
      ]
    ];

    $this->view('backview/template/index', $data);
  }

  public function obtenerTotalProduccionPendiente()
  {
    $estados = array(0, 1);
    $produccion = $this->produccionModel->obtenerProduccionTotal($estados);
    $this->function->jsonResponse('respuesta', $produccion);
  }

  public function obtenerProgramacion()
  {
    $estado = 1;
    $programacion = $this->programacionModel->obtenerProgramacion($estado);
    $this->function->jsonResponse('respuesta', $programacion);
  }

  public function obtenerProduccionPendiente()
  {
    $estados = array(0, 1);
    $produccion = $this->detalleventaModel->obtenerDetallePendiente($estados);
    $this->function->jsonResponse('respuesta', $produccion);
  }

  public function listas()
  {
    $estado = array(2);
    $data = [
      'area' => '',
      'controller' => 'producciones',
      'method' => 'listas',
      'description' => 'resumen general',
      'content' => 'backview/producciones/index.php',
      'styles' => [
        'public/assets/plugins/data-tables/css/datatables.min.css'
      ],
      'scripts' => [
        'public/assets/plugins/data-tables/js/datatables.min.js',
        'public/assets/js/pages/data-basic-custom.js',
        'public/customs/producciones/pendientes.js'
      ],
      'producciones' => $this->produccionModel->obtenerProducciones($estado),
    ];

    $this->view('backview/template/index', $data);
  }

  // Actualiza el estado de la produccioon
  public function actualizarEstado()
  {
      $postData = $this->function->method('POST', true);
      $data = [
        "id_detalle" => $postData['id_detalleventa'],
        "nuevo_estado" => $postData['nuevoEstado'],
        "produccion" => 1
      ];
      
      $response = $this->detalleventaModel->actualizarEstado($data);

      // Sumamos el producto producido al inventario de la sucursal
    //   $this->inventarioProductoModel->sumamosInventario($postData['id_producto'], $postData['id_sucursal']);

      $response = true;

    $this->function->jsonResponse('respuesta', $response);
  }

  public function exportarPendientes()
  {
      $respuesta = $this->detalleventaModel->exportarPendientes();  

      // Encabezados para forzar la descarga como archivo CSV
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="Produccion_Pendiente.csv"');

      // Salida del archivo
      $output = fopen('php://output', 'w');

      // Encabezados
      fputcsv($output, array('id_venta', 'codigo', 'nombre_producto', 'nota', 'fecha_entrega'));

      // Escribir datos en el archivo CSV
      foreach ($respuesta as $objeto) {
          // Convertir el objeto stdClass a array asociativo
          $rowArray = (array)$objeto;

          // Escribir el array en el archivo CSV
          fputcsv($output, $rowArray);
      }

      fclose($output);

      // $this->function->jsonResponse('respuesta', $output);
  }

  public function exportarListos()
  {
      $respuesta = $this->detalleventaModel->exportarListos();  

      // Encabezados para forzar la descarga como archivo CSV
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="Produccion_Lista.csv"');

      // Salida del archivo
      $output = fopen('php://output', 'w');

      // Encabezados
      fputcsv($output, array('id_venta', 'codigo', 'nombre_producto', 'fecha_entrega', 'fecha_actualizada'));

      // Escribir datos en el archivo CSV
      foreach ($respuesta as $objeto) {
          // Convertir el objeto stdClass a array asociativo
          $rowArray = (array)$objeto;

          // Escribir el array en el archivo CSV
          fputcsv($output, $rowArray);
      }

      fclose($output);

      // $this->function->jsonResponse('respuesta', $output);
  }

  public function obtenerRequisicionesPendientes()
  {
    $requisiciones = $this->requisicionModel->obtenerRequisicionesPendientes();
    return $this->function->jsonResponse('respuesta', $requisiciones);
  }

  // Actualiza el estado de la requisicion
  public function actualizarEstadoRequisicion()
  {
      $postData = $this->function->method('POST', true);
      $data = [
        "id_requisicion" => $postData['id_requisicion'],
        "estado" => $postData['nuevoEstado']
      ];
      
      $response = $this->requisicionModel->actualizarEstado($data);

      // Sumamos el producto producido al inventario de la sucursal
      $this->inventarioProductoModel->sumamosInventario($postData['id_producto'], $postData['id_sucursal'], $postData['cantidad']);

      $response = true;

    $this->function->jsonResponse('respuesta', $response);
  }

}
