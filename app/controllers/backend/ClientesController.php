<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase Clientes Controller
 */
class ClientesController extends PulseController
{
    private $clienteModel;

    public function __construct()
    {
        parent::__construct();

        if (!adminAccess() && !sellerAccess()) {
            $this->function->redirectTo('backend/auth');
        }

        $this->clienteModel = $this->model('ClienteModel');
    }

    public function index()
    {
        $data = [
            'area' => 'inventario',
            'controller' => 'clientes',
            'description' => 'Resumen general',
            'content' => 'backview/clientes/index.php',
            'styles' => [
                'public/assets/plugins/data-tables/css/datatables.min.css',
                'public/assets/plugins/select2/css/select2.css'
            ],
            'scripts' => [
                'public/assets/plugins/data-tables/js/datatables.min.js',
                'public/assets/plugins/select2/js/select2.full.min.js',
                // 'public/assets/js/pages/datatables-custom.js',
                'public/assets/js/pages/select-custom.js',
                'public/customs/clientes.js',
            ]
        ];

        $this->view('backview/template/index', $data);
    }

    public function buscarCliente()
    {
        $postData = $this->function->method('POST', true);

        // Obtener los parámetros de búsqueda
        $cedula = $postData['cedula'] ?? null;
        $nombre = $postData['nombre'] ?? null;

        // Buscar clientes en el modelo
        $clientes = $this->clienteModel->buscarClientes($cedula, $nombre);

        if ($clientes) {
            return $this->function->jsonResponse('respuesta', $clientes);
        } else {
            return $this->function->jsonResponse('error', 'No se encontraron clientes con los datos proporcionados.');
        }
    }

    public function obtenerClientes()
    {
        $clientes = $this->clienteModel->obtenerClientes();
        return $this->function->jsonResponse('respuesta', $clientes);
    }

    public function crearNuevoCliente()
    {
        $postData = $this->function->method('POST', true);
        $postData = [
            'cedula' => $postData['cedula'],
            'nombre' => ucwords(strtolower($postData['nombre'])),
            'apellidos' => ucwords(strtolower($postData['apellidos'])),
            'direccion' => ucfirst(strtolower($postData['direccion'])),
            'barrio' => ucfirst(strtolower($postData['barrio'])),
            'telefono' => $postData['telefono'],
            'email' => strtolower($postData['email']),
            'registro' => date('Y-m-d H:m:i'),
            'estado' => 'activo',
        ];
        // Necesito que antes de crear busque si existe el cliente
        $cliente = $this->clienteModel->obtenerClientePorCedula($postData['cedula']);
        if ($cliente) {
            $errorData = ['error_code' => 404, 'respuesta' => 'Cliente ya registrado'];
            return $this->function->jsonResponse('error', []);
        }
        // Crear cliente
        $idCliente = $this->clienteModel->crearCliente($postData);
        return $this->function->jsonResponse('respuesta', $idCliente);
    }

    public function guardarCliente()
    {
        $postData = $this->function->method('POST', true);
        $response = $this->clienteModel->actualizarCliente($postData);
        return $this->function->jsonResponse('respuesta', $response);
    }

    public function obtenerEstadisticasCliente()
    {
        $postData = $this->function->method('POST', true);
        $idCliente = $postData['id_cliente'];
        // Obtener estadísticas del cliente desde el modelo
        $ventas = $this->clienteModel->obtenerVentasCliente($idCliente);
        $otrasEstadisticas = $this->clienteModel->obtenerOtrasEstadisticas($idCliente);

        // Devolver los datos en formato JSON
        return $this->function->jsonResponse('respuesta', [
            'ventas' => $ventas,
            'otrasEstadisticas' => $otrasEstadisticas
        ]);
    }

    public function actualizarEstado()
    {
        $clienteData = $this->function->method('POST', TRUE);
        $response = $this->clienteModel->actualizarEstado($clienteData);
        $this->function->jsonResponse('respuesta', $response);
    }
}
