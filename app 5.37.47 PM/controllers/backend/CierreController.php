<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase Cierre Controller
 */
class CierreController extends PulseController
{
    private $cierreModel, $entradaModel, $salidaModel, $usuarioModel, $sucursalModel;
    private $idUsuario;

    public function __construct()
    {
        parent::__construct();

        if (!adminAccess() && !sellerAccess()) {
            $this->function->redirectTo('backend/auth');
        }

        $this->cierreModel = $this->model('CierreModel');
        $this->entradaModel = $this->model('EntradaModel');
        $this->salidaModel = $this->model('SalidaModel');
        $this->usuarioModel = $this->model('UsuarioModel');
        $this->sucursalModel = $this->model('SucursalModel');

        $this->idUsuario = $this->session->getUserData('userSession', 'usuarioId');
    }

    public function index()
    {
        $data = [
            'area' => 'inventario',
            'controller' => 'cierre',
            'description' => 'Cierre de caja',
            'content' => 'backview/cierre/index.php',
            'styles' => [
                // 'public/assets/plugins/data-tables/css/datatables.min.css',
                // 'public/assets/plugins/select2/css/select2.css'
            ],
            'scripts' => [
                // 'public/assets/plugins/data-tables/js/datatables.min.js',
                // 'public/assets/plugins/select2/js/select2.full.min.js',
                // 'public/assets/js/pages/datatables-custom.js',
                // 'public/assets/js/pages/select-custom.js',
                'public/customs/cierre.js'
            ],
        ];

        $this->view('backview/template/index', $data);
    }

    public function obtenerCierre()
    {
        try {
            $dataPost = $this->function->method('POST', TRUE);
            
            $idSucursal = $this->session->getUserData('userSession', 'usuarioSucursal');

            $usuario = $this->usuarioModel->obtenerUsuarioById($this->idUsuario);

            $cierre = [];

            if ($usuario->rol == 1) {
                $cajeros = $this->usuarioModel->obtenerCajeros();
                for ($idSucursal = 3; $idSucursal <= 4; $idSucursal++) {
                    $sucursal = $this->sucursalModel->obtenerSucursalId($idSucursal);
                    foreach ($cajeros as $key => $cajero) {
                        $entrada = $this->entradaModel->obtenerEntrada($cajero->id_usuario, $dataPost['fechaInicio'], $dataPost['fechaFin'], $idSucursal);
                        $salida = $this->salidaModel->obtenerSalida($cajero->id_usuario, $dataPost['fechaInicio'], $dataPost['fechaFin'], $idSucursal);
                        
                        if ($entrada || $salida) {
                            $cierre[$sucursal->nombre][$cajero->usuario] = [
                                'entrada' => $entrada,
                                'salida' => $salida
                            ];
                        }
                    }
                }
            } else {
                $entrada = $this->entradaModel->obtenerEntrada($this->idUsuario, $dataPost['fechaInicio'], $dataPost['fechaFin'], $idSucursal);
                $salida = $this->salidaModel->obtenerSalida($this->idUsuario, $dataPost['fechaInicio'], $dataPost['fechaFin'], $idSucursal);
                
                if ($entrada || $salida) {
                    $sucursal = $this->sucursalModel->obtenerSucursalId($idSucursal);
                    $cierre[$sucursal->nombre][$usuario->usuario] = [
                        'entrada' => $entrada,
                        'salida' => empty($salida) ? 0 : $salida
                    ];
                }
            }

            return $this->function->jsonResponse('respuesta', [
                'data' => $cierre,
                'success' => !empty($cierre),
                'message' => empty($cierre) ? 'No se encontraron resultados' : 'Datos obtenidos correctamente'
            ]);
        } catch (Exception $e) {
            return $this->function->jsonResponse('respuesta', [
                'success' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ]);
        }
    }
}
