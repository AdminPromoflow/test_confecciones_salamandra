<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase Ventas Controller
 */
class VentasController extends PulseController
{
    private $ventaModel, $abonoModel, $carritoModel, $productoModel, $dealleventaModel, $produccionModel, $clienteModel, $entradaModel;
    private $idUsuario, $idSucursal;

    public function __construct()
    {
        parent::__construct();

        // if (!adminAccess() && !sellerAccess()) {
        //     $this->function->redirectTo('backend/auth');
        // }

        $this->ventaModel = $this->model('VentaModel');
        $this->dealleventaModel = $this->model('DetalleVentaModel');
        $this->abonoModel = $this->model('AbonoModel');
        $this->carritoModel = $this->model('CarritoModel');
        $this->productoModel = $this->model('ProductoModel');
        $this->produccionModel = $this->model('ProduccionModel');
        $this->clienteModel = $this->model('ClienteModel');
        $this->entradaModel = $this->model('EntradaModel');

        $this->idSucursal = $this->session->getUserData('userSession', 'usuarioSucursal');
        $this->idUsuario = $this->session->getUserData('userSession', 'usuarioId');
    }

    public function index()
    {
        $data = [
            'area' => 'inventario',
            'controller' => 'ventas',
            'description' => 'Resumen general',
            'content' => 'backview/ventas/index.php',
            'styles' => [
                'public/assets/plugins/data-tables/css/datatables.min.css',
                'public/assets/plugins/select2/css/select2.css'
            ],
            'scripts' => [
                'public/assets/plugins/data-tables/js/datatables.min.js',
                'public/assets/plugins/select2/js/select2.full.min.js',
                'public/assets/js/pages/datatables-custom.js',
                'public/assets/js/pages/select-custom.js',
                'public/customs/ventas.js'
            ],
            'clientes' => $this->clienteModel->obtenerClientes()
        ];

        $this->view('backview/template/index', $data);
    }

    public function obtenerVentas()
    {
        $ventas = $this->ventaModel->obtenerVentas();
        return $this->function->jsonResponse('respuesta', $ventas);
    }

    public function obtenerVentaById()
    {
        $postData = $this->function->method('POST', TRUE);
        $response = $this->ventaModel->obtenerVentaById($postData['id_venta']);
        return $this->function->jsonResponse('respuesta', $response);
    }

    public function obtenerVentaByCliente()
    {
        $postData = $this->function->method('POST', TRUE);
        $response = $this->ventaModel->obtenerVentaByCliente($postData['id_cliente']);
        return $this->function->jsonResponse('respuesta', $response);
    }

    public function guardarVenta()
    {
        $postData = $this->function->method('POST', TRUE);

        $postData['id_usuario'] = $this->idUsuario;
        $postData['id_sucursal'] = $this->idSucursal;

        $idVenta = $this->ventaModel->guardarVenta($postData);        

        // Registramos la entrada de dinero
        $datoEntrada = [
            'fecha' => date('Y-m-d'),
            'valor' => $postData['tipoventa'] == 2 ? $postData['abono'] : ($postData['pagacon'] - $postData['cambio']),
            'metodo' => $postData['mediopago'],
            'id_venta' => $idVenta,
            'id_cajero' => $postData['id_usuario'],
            'id_sucursal' => $postData['id_sucursal']
        ];
        $this->entradaModel->guardarEntrada($datoEntrada);

        if ($postData['tipoventa'] == 2) {
            $this->abonoModel->guardarAbono($idVenta, $postData);
        }

        $carrito = $this->carritoModel->obtenerCarrito($postData['id_cliente'], $postData['id_sucursal']);

        foreach ($carrito as $detalle) {
            $producto = $this->productoModel->obtenerProductoId($detalle->id_producto);

            $datosDetalleVenta = [
                'id_venta' => $idVenta,
                'id_cliente' => $detalle->id_cliente,
                'id_usuario' => $this->idUsuario,
                'id_producto' => $detalle->id_producto,
                'cantidad' => $detalle->cantidad,
                'costo' => $producto->costo,
                'precio' => $detalle->precio,
                'estado' => $detalle->estado,
                'nota' => $detalle->nota,
                'produccion' => 0,
                'fecha_entrega' => $detalle->estado == 4 ? date('Y-m-d') : $detalle->fecha_entrega,
                'fecha_actualizada' => $detalle->estado == 4 ? date('Y-m-d') : null,
                'registro' => date('Y-m-d H:i:s')
            ];

            $idDetalleVenta = $this->dealleventaModel->guardarDetalle($datosDetalleVenta);

            // if ($detalle->estado == 0 || $detalle->estado == 1) {
            //     $resultado = $this->produccionModel->guardarproduccion($idDetalleVenta);
            // }

            $this->carritoModel->quitarProducto($detalle->id_carrito);
        }

        return $this->function->jsonResponse('respuesta', $idVenta);
    }

    public function imprimirTiquet($id_venta)
    {
        $data = [
            'venta' => $this->ventaModel->obtenerVentaById($id_venta),
            'detalle_venta' => $this->dealleventaModel->obtenerDetalleByIdVenta($id_venta)
        ];

        $this->view('backview/impresiones/tiquet', $data);
    }
}
