<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase Requisiciones Controller
 */
class RequisicionesController extends PulseController
{
    private $requisicionModel, $productoModel;
    private $idUsuario, $idSucursal;

    public function __construct()
    {
        parent::__construct();

        if (!adminAccess() && !sellerAccess()) {
            $this->function->redirectTo('backend/auth');
        }

        $this->requisicionModel = $this->model('RequisicionModel');
        $this->productoModel = $this->model('ProductoModel');

        $this->idSucursal = $this->session->getUserData('userSession', 'usuarioSucursal');
        $this->idUsuario = $this->session->getUserData('userSession', 'usuarioId');
    }

    public function index()
    {
        $data = [
            'area' => 'inventario',
            'controller' => 'requisiciones',
            'description' => 'Requisiciones de nueva prendas',
            'content' => 'backview/requisiciones/index.php',
            'styles' => [
                'public/assets/plugins/data-tables/css/datatables.min.css',
                // 'public/assets/plugins/select2/css/select2.css'
            ],
            'scripts' => [
                'public/assets/plugins/data-tables/js/datatables.min.js',
                // 'public/assets/js/pages/datatables-custom.js',
                // 'public/assets/plugins/select2/js/select2.full.min.js',
                // 'public/assets/js/pages/select-custom.js',
                'public/customs/requisiciones.js'
            ],
        ];

        $this->view('backview/template/index', $data);
    }

    public function obtenerRequisiciones()
    {
        $requisiciones = $this->requisicionModel->obtenerRequisicionesPendientes();
        return $this->function->jsonResponse('respuesta', $requisiciones);
    }

    public function guardarRequisicion()
    {
        $requisicionData = $this->function->getJsonData(true);
        $requisicionData['id_usuario'] = $this->idUsuario;
        $requisicionData['id_sucursal'] = $this->idSucursal;

        

        $response = $this->requisicionModel->crearRequisicion($requisicionData);

        return $this->function->jsonResponse('respuesta', $response);
    }
}
