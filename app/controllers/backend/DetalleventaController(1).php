<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase DetalleVenta Controller
 */
class DetalleventaController extends PulseController
{
    private $ventaModel,
        $abonoModel,
        $carritoModel,
        $productoModel,
        $detalleventaModel,
        $produccionModel,
        $salidaModel;

    private $idUsuario,
        $idSucursal,
        $rolUsuario;

    public function __construct()
    {
        parent::__construct();

        if (!adminAccess() && !sellerAccess() && !OperatorAccess()) {
            $this->function->redirectTo('backend/auth');
        }

        $this->ventaModel = $this->model('VentaModel');
        $this->detalleventaModel = $this->model('DetalleVentaModel');
        $this->abonoModel = $this->model('AbonoModel');
        $this->carritoModel = $this->model('CarritoModel');
        $this->productoModel = $this->model('ProductoModel');
        $this->produccionModel = $this->model('ProduccionModel');
        $this->salidaModel = $this->model('SalidaModel');

        $this->idSucursal = $this->session->getUserData('userSession', 'usuarioSucursal');
        $this->idUsuario = $this->session->getUserData('userSession', 'usuarioId');
        $this->rolUsuario = $this->session->getUserData('userSession', 'usuarioRol');
    }

    public function index()
    {
        $data = [
            'area' => 'inventario',
            'controller' => 'ventas',
            'description' => 'Resumen general',
            'content' => 'backview/ventas/detalle.php',
            'styles' => [
                'public/assets/plugins/data-tables/css/datatables.min.css',
                'public/assets/plugins/select2/css/select2.css'
            ],
            'scripts' => [
                'public/assets/plugins/data-tables/js/datatables.min.js',
                'public/assets/plugins/select2/js/select2.full.min.js',
                // 'public/assets/js/pages/datatables-custom.js',
                'public/assets/js/pages/select-custom.js',
                'public/customs/detalleventas.js'
            ],
        ];

        $this->view('backview/template/index', $data);
    }

    public function obtenerDetalle()
    {
        $postData = $this->function->method('POST', TRUE);
        $response = $this->detalleventaModel->obtenerDetalleByIdVenta($postData['id_venta']);
        $response['rolUsuario'] = $this->rolUsuario;

        return $this->function->jsonResponse('respuesta', $response);
    }

    public function obtenerDetalles()
    {
        $response = $this->detalleventaModel->obtenerDetalles();
        return $this->function->jsonResponse('respuesta', $response);
    }

    // ************** ACTUALIZAR ESTADO DETALLE VENTA **************

    public function actualizarEstadoDetalle()
    {
        // Inicializar la variable $response con un valor predeterminado
        $response = false;

        // Obtener datos de la solicitud
        $postData = $this->function->method('POST', false);

        // Obtener información de la venta y el detalle
        $venta = $this->ventaModel->obtenerVentaById($postData['id_venta']);
        $detalleVenta = $this->detalleventaModel->obtenerDetalle($postData['id_detalleventa']);

        // Preparar objeto con datos relevantes
        $objeto = [
            'id_venta' => $venta->id_venta,
            'id_sucursal' => $venta->id_sucursal,
            'id_usuario' => $venta->id_usuario,  // Cajero
            'total' => $venta->total,
            'descuento' => $venta->descuento,
            'devolucion' => $venta->devolucion,
            'abono' => $venta->abono,
            'saldo' => $venta->saldo,
            'entregado' => $venta->entregado,
            'estado_venta' => $venta->estado,
            'id_detalle' => $postData['id_detalleventa'],
            'id_producto' => $detalleVenta->id_producto,
            'precio' => $postData['precio'],
            'estado_detalle' => $detalleVenta->estado,
            'nuevo_estado' => $postData['nuevoEstado'],
            'nota' => ''
        ];

        // Validar y procesar según el rol
        if ($postData['usuarioRol'] == 1) {
            // Transiciones permitidas para el administrador
            $transicionesAdministrador = [
                0 => [1, 5],  // Desde estado 0 puede ir a 1, 2 o 5
                1 => [0, 5],  // Desde estado 1 puede ir a 0, 2 o 5
                2 => [3, 6],  // Desde estado 2 puede ir a 3 o 6
                3 => [2, 4, 6],  // Desde estado 3 puede ir a 2, 4 o 6
                4 => [6]  // Desde estado 4 puede ir a 6
            ];

            // Verificar si la transición es permitida
            if (isset($transicionesAdministrador[$objeto['estado_detalle']]) &&
                    in_array($objeto['nuevo_estado'], $transicionesAdministrador[$objeto['estado_detalle']])) {
                // Si es cancelar (5) o retornar (6), procesar con lógica especial
                if (in_array($objeto['nuevo_estado'], [5, 6])) {
                    $response = $this->procesarCancelacionORetorno($objeto);
                } else {
                    // Otras transiciones permitidas para el administrador
                    $response = $this->detalleventaModel->actualizarEstado($objeto);
                }
            } else {
                $response = false;  // Transición no permitida
            }
        } elseif ($postData['usuarioRol'] == 2) {
            // Transiciones permitidas para el cajero
            $transicionesCajero = [
                0 => [1],  // Desde estado 0 puede ir a 1
                1 => [0],  // Desde estado 1 puede ir a 0
                2 => [3],  // Desde estado 2 puede ir a 3
                3 => [4]  // Desde estado 3 puede ir a 4
            ];

            // Verificar si la transición es permitida
            if (isset($transicionesCajero[$objeto['estado_detalle']]) &&
                    in_array($objeto['nuevo_estado'], $transicionesCajero[$objeto['estado_detalle']])) {
                $response = $this->detalleventaModel->actualizarEstado($objeto);
            } else {
                $response = false;  // Transición no permitida
            }
        }

        return $this->function->jsonResponse('respuesta', $response);
    }

    private function procesarCancelacionORetorno(&$objeto)
    {
        $valorProducto = $objeto['precio'];
        $objeto['nota'] = 'Venta ajustada por cancelación/devolución de producto.';

        if ($objeto['nuevo_estado'] == 5) {  // Cancelar
            if (in_array($objeto['estado_detalle'], [0, 1])) {
                $objeto['descuento'] += $valorProducto;
                $objeto['saldo'] -= $valorProducto;

                if ($objeto['saldo'] < 0) {
                    $objeto['valor'] = ($objeto['saldo'] * -1);
                    $this->registrarSalida($objeto);
                }

                if ($objeto['saldo'] <= 0 && $objeto['estado_venta'] == 2) {
                    $objeto['estado_venta'] = 1;  // Cambiar a pagada
                }
            } else {
                return false;  // No se puede cancelar en este estado
            }
        } elseif ($objeto['nuevo_estado'] == 6) {  // Retornar
            if (in_array($objeto['estado_detalle'], [2, 3, 4])) {
                $objeto['descuento'] += $valorProducto;
                $objeto['saldo'] -= $valorProducto;

                if ($objeto['saldo'] < 0) {
                    $objeto['valor'] = ($objeto['saldo'] * -1);
                }
                $this->registrarSalida($objeto);
                // } elseif ($objeto['estado_detalle'] == 4) {
                //     $objeto['devolucion'] += $valorProducto;
                //     $objeto['saldo'] -= $valorProducto;

                //     if ($objeto['saldo'] < 0) {
                //         $objeto['valor'] = ($objeto['saldo'] * -1);
                //         $this->registrarSalida($objeto);
                //     }
            } else {
                return false;  // No se puede retornar en este estado
            }
        }

        // Actualizar venta y detalle
        $this->ventaModel->actualizarVenta($objeto);
        $this->detalleventaModel->actualizarEstado($objeto);
        return true;  // Operación exitosa
    }

    private function registrarSalida($objeto)
    {
        $salida = [
            'valor' => $objeto['valor'],
            'id_venta' => $objeto['id_venta'],
            'id_cajero' => $objeto['id_usuario'],
            'id_sucursal' => $objeto['id_sucursal'],
            'nota' => $objeto['nota']
        ];
        $this->salidaModel->guardarSalida($salida);
    }
}
