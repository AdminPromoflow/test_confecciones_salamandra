<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clase Transferencias Controller
 */
class TransferenciasController extends PulseController
{
    private $transferenciaModel, $sucursalModel, $productoModel, $inventarioProductoModel;

    public function __construct()
    {
        parent::__construct();

        if (!adminAccess()) {
            $this->function->redirectTo('backend/auth');
        }

        $this->transferenciaModel = $this->model('TransferenciaModel');
        $this->sucursalModel = $this->model('SucursalModel');
        $this->productoModel = $this->model('ProductoModel');
        $this->inventarioProductoModel = $this->model('InventarioProductoModel');
    }

    public function index()
    {
      $data = [
        'area' => 'inventario',
        'controller' => 'transferencias',
        'description' => 'panel de control',
        'content' => 'backview/transferencias/index.php',
        'styles' => [
          'public/assets/plugins/data-tables/css/datatables.min.css'
        ],
        'scripts' => [
          'public/assets/plugins/data-tables/js/datatables.min.js',
          'public/customs/transferencias/ver_transferencia.js'
        ],
        'transferencias' => $this->transferenciaModel->getTransferencias()
      ];

      $this->view('backview/template/index', $data);
    }

    public function obtenerStock()
    {
      $postData = $this->function->method('POST', TRUE);
      $resultado = $this->inventarioProductoModel->buscarProducto($postData['id_producto'], $postData['id_sucursal']);
      $this->function->jsonResponse('respuesta', $resultado);
    }

    public function registrarTransferencia()
    {
        $transferenciaData = $this->function->method('POST', TRUE);

        // Verifica si se está intentando registrar una transferencia.
        if (isset($transferenciaData['registrar'])) {
            $transferenciaData['id_usuario'] = $this->session->getUserData('userSession', 'usuarioId');
            $productoEnOrigen = $this->inventarioProductoModel->productoEnSucursal(
                $transferenciaData['producto'],
                $transferenciaData['sucursal_origen'],
                $transferenciaData['cantidad']
            );

            if ($productoEnOrigen === null) {
                $this->handleInsufficientQuantity();
            } else {
                $this->processTransfer($transferenciaData);
            }

            $this->function->jsonResponse('respuesta', ['success' => true]);
        } else {
            $data = [
                'area' => 'inventario',
                'controller' => 'transferencias',
                'description' => 'panel de control',
                'content' => 'backview/transferencias/registrar.php',
                'styles' => [
                    'public/assets/plugins/select2/css/select2.min.css'
                ],
                'scripts' => [
                    'public/assets/plugins/select2/js/select2.full.min.js',
                    'public/assets/js/pages/select-custom.js',
                    'public/customs/transferencias/transferir.js'
                ],
                'ultimasTransferencias' => $this->transferenciaModel->getLastTransferencias(),
                'sucursales' => $this->sucursalModel->getSucursales(),
                'productos' => $this->productoModel->obtenerProductos()
            ];

            // showTest($data['productos']);

            $this->view('backview/template/index', $data);
        }
    }

    private function handleInsufficientQuantity()
    {
        $this->session->setFlashMessage('danger', 'No hay suficiente cantidad del producto en la sucursal de origen.');
        $this->function->jsonResponse('respuesta', ['success' => false]);
    }

    private function processTransfer(array $transferenciaData)
    {
        $productoEnDestino = $this->inventarioProductoModel->productoEnSucursal(
            $transferenciaData['producto'], // id_producto
            $transferenciaData['sucursal_destino'] // id_sucursal
        );

        $accion = $productoEnDestino ? 'actualizar' : 'registrar';

        switch ($accion) {
            case 'actualizar':
                $this->inventarioProductoModel->actualizarInventario($transferenciaData);
                $this->session->setFlashMessage('success', 'El producto se actualizó correctamente en el inventario.');
                break;
            case 'registrar':
                $this->inventarioProductoModel->registrarInventario($transferenciaData);
                $this->session->setFlashMessage('success', 'El producto se registró correctamente en el inventario.');
                break;
        }

        $this->transferenciaModel->registrarTransferencia($transferenciaData);
    }

}
