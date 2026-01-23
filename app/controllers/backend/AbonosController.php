<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase Abonos Controller
 */
class AbonosController extends PulseController
{
    private $abonoModel, $ventaModel, $entradaModel;
    private $idUsuario;

    public function __construct()
    {
        parent::__construct();

        if (!adminAccess() && !sellerAccess()) {
            $this->function->redirectTo('backend/auth');
        }

        $this->abonoModel = $this->model('AbonoModel');
        $this->ventaModel = $this->model('VentaModel');
        $this->entradaModel = $this->model('EntradaModel');

        $this->idSucursal = $this->session->getUserData('userSession', 'usuarioSucursal');
        $this->idUsuario = $this->session->getUserData('userSession', 'usuarioId');
    }

    public function index()
    {
        $data = [
            'area' => 'inventario',
            'controller' => 'abonos',
            'description' => 'Resumen general',
            'content' => 'backview/abonos/index.php',
            'styles' => [
                'public/assets/plugins/data-tables/css/datatables.min.css',
                'public/assets/plugins/select2/css/select2.css'
            ],
            'scripts' => [
                'public/assets/plugins/data-tables/js/datatables.min.js',
                'public/assets/plugins/select2/js/select2.full.min.js',
                'public/assets/js/pages/datatables-custom.js',
                'public/assets/js/pages/select-custom.js',
                'public/customs/abonos.js'
            ],
        ];

        $this->view('backview/template/index', $data);
    }

    public function obtenerAbonos()
    {
        $abonos = $this->abonoModel->obtenerAbonos();
        return $this->function->jsonResponse('respuesta', $abonos);
    }

    public function guardarAbono()
    {
        $postData = $this->function->method('POST', TRUE);
        
        $postData['id_usuario'] = $this->idUsuario;
        $postData['id_sucursal'] = $this->idSucursal;

        // Registramos la entrada de dinero
        $datoEntrada = [
            'fecha' => date('Y-m-d'),
            'valor' => $postData['abono'],
            'metodo' => $postData['mediopago'],
            'id_venta' => $postData['id_venta'],
            'id_cajero' => $postData['id_usuario'],            
            'id_sucursal' => $postData['id_sucursal']
        ];
        $this->entradaModel->guardarEntrada($datoEntrada);

        $this->ventaModel->actualizarAbono($postData);

        if ($postData['nuevoSaldo'] == 0) {
            $this->ventaModel->actualizarEstado($postData);
        }

        $response = $this->abonoModel->guardarAbono($postData['id_venta'], $postData);
        return $this->function->jsonResponse('respuesta', $response);
    }

    public function imprimirTiquet($id_venta)
    {
        $data = [
            'abonos' => $this->abonoModel->obtenerAbonoById($id_venta)
        ];
        
        $this->view('backview/impresiones/tiquetabono', $data);
    }
}
