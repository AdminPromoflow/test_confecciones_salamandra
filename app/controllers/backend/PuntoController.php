<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APP_PATH . 'config/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Clase Punto Controller
 */
class PuntoController extends PulseController
{
  private $carritoModel, $clienteModel, $programacionModel, $productoModel, $inventarioProductoModel, $ventaModel, $abonoModel, $dealleventaModel, $produccionModel;
  private $idUsuario, $rolUsuario, $idSucursal;

  public function __construct()
  {
    parent::__construct();

    if (!sellerAccess()) {
      $this->function->redirectTo('backend/auth');
    }

    $this->carritoModel = $this->model('CarritoModel');
    $this->clienteModel = $this->model('ClienteModel');
    $this->programacionModel = $this->model('ProgramacionModel');
    $this->productoModel = $this->model('ProductoModel');
    $this->inventarioProductoModel = $this->model('InventarioProductoModel');
    $this->ventaModel = $this->model('VentaModel');
    $this->abonoModel = $this->model('AbonoModel');
    $this->dealleventaModel = $this->model('DetalleVentaModel');
    $this->produccionModel = $this->model('ProduccionModel');

    $this->rolUsuario = $this->session->getUserData('userSession', 'usuarioRol');
    $this->idUsuario = $this->session->getUserData('userSession', 'usuarioId');
    $this->idSucursal = $this->session->getUserData('userSession', 'usuarioSucursal');
  }

  // Esta función es el inicio del punto de venta
  public function index()
  {
    $estado = 1;
    $data = [
      'area' => '',
      'controller' => 'punto',
      'content' => 'backview/puntoventa/index.php',
      'styles' => [
        'public/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datepicker3.min.css',
        'public/assets/plugins/material-datetimepicker/css/bootstrap-material-datetimepicker.css',
        'public/assets/plugins/select2/css/select2.css'
      ],
      'scripts' => [
        'public/assets/plugins/moment/moment-with-locales.min.js',
        'public/assets/plugins/material-datetimepicker/js/bootstrap-material-datetimepicker.js',
        'public/assets/plugins/select2/js/select2.full.min.js',
        'public/assets/js/pages/select-custom.js',
        'public/assets/js/pages/form-picker-custom.js',
        'public/customs/punto.js?v=' . time(),
      ],
      'clientes' => $this->clienteModel->obtenerClientes(),
      'idSucursal' => $this->idSucursal,
      'programaciones' => $this->programacionModel->obtenerProgramacion($estado)
    ];

    $this->view('backview/template/index', $data);
  }

  // Esta funcion obtiene todos los productos de la sucursal para el select: Desde el controlador PuntoController
  public function obtenerInventario()
  {
    $dataPost = $this->function->method('POST', TRUE);
    $idProducto = $dataPost['id_producto'];
    $producto = $this->inventarioProductoModel->obtenerInventarioSucursal($this->idSucursal, $idProducto);
    $this->function->jsonResponse('respuesta', $producto);
  }

  public function guardarOpciones()
  {
    $dataPost = $this->function->method('POST', TRUE);
    $nuevasOpciones = $this->carritoModel->actualizarOpciones($dataPost);
    $this->function->jsonResponse('nuevasOpciones', $nuevasOpciones);
  }

  public function enviarEmail()
  {
    // Configuración y uso de PHPMailer
    $mailConfig = getMailerConfig();
    $mail = new PHPMailer(true);

    $destinatario = 'malvarezcalderon82@gmail.com';

    try {
      $mail->isSMTP();
      $mail->SMTPAuth = $mailConfig['SMTPAuth'];
      $mail->SMTPSecure = $mailConfig['SMTPSecure'];
      $mail->Host = $mailConfig['Host'];
      $mail->Port = $mailConfig['Port'];
      $mail->Username = $mailConfig['Username'];
      $mail->Password = $mailConfig['Password'];
      $mail->setFrom($mailConfig['FromAddress'], $mailConfig['FromName']);

      // Agrega el destinatario
      $mail->addAddress($destinatario);
      $mail->Subject = 'Correo de prueba';
      $mail->Body = 'Contenido del correo en texto plano';

      $mail->Priority = 1;

      // ... Otras configuraciones de correo ...

      // Envía el correo
      $mail->send();
      echo 'Correo enviado correctamente';
    } catch (Exception $e) {
      echo 'Error al enviar el correo: ', $mail->ErrorInfo;
    }
  }
}
