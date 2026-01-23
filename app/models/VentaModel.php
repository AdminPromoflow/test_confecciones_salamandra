<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase Venta Model
 */
class VentaModel
{
  private $db;
  private $tabla = 'ventas';
  private $campos = ['id_venta', 'id_sucursal', 'tipoventa', 'total', 'descuento', 'pagacon', 'cambio', 'abono', 'saldo', 'mediopago', 'estado', 'nota', 'id_cliente', 'id_usuario', 'registro', 'mod'];

  public function __construct()
  {
    $this->db = new PulseDatabase;
  }

  public function obtenerVentaById($id_venta)
  {
    $sql = "SELECT v.*, s.nombre AS nombre_sucursal, c.cedula AS cliente_cedula, c.nombre AS cliente_nombre, c.apellidos AS cliente_apellidos, c.email AS cliente_email, c.direccion AS cliente_direccion, c.barrio AS cliente_barrio, c.telefono AS cliente_telefono, c.email AS cliente_email, u.usuario
      FROM $this->tabla v
      INNER JOIN sucursales s ON v.id_sucursal = s.id_sucursal
      INNER JOIN clientes c ON v.id_cliente = c.id_cliente
      INNER JOIN usuarios u ON v.id_usuario = u.id_usuario  
      WHERE v.id_venta = :id_venta";
    $this->db->query($sql);
    $this->db->bind(":id_venta", $id_venta);
    $query = $this->db->single();
    return $this->db->rowCount() > 0 ? $query : false;
  }

  public function obtenerVentaByCliente($id_cliente)
  {
    $sql = "SELECT v.*, s.nombre AS nombre_sucursal, c.cedula AS cliente_cedula, c.nombre AS cliente_nombre, c.apellidos AS cliente_apellidos, c.email AS cliente_email, c.direccion AS cliente_direccion, c.barrio AS cliente_barrio, c.telefono AS cliente_telefono, c.email AS cliente_email, u.usuario
      FROM $this->tabla v
      INNER JOIN sucursales s ON v.id_sucursal = s.id_sucursal
      INNER JOIN clientes c ON v.id_cliente = c.id_cliente
      INNER JOIN usuarios u ON v.id_usuario = u.id_usuario  
      WHERE v.id_cliente = :id_cliente
      ORDER BY v.registro DESC"; // Modificación aquí
    $this->db->query($sql);
    $this->db->bind(":id_cliente", $id_cliente);
    $query = $this->db->resultSet();
    return $this->db->rowCount() > 0 ? $query : false;
  }

  public function guardarVenta($data)
  {
    $campos = [
      'id_sucursal' => $data['id_sucursal'],
      'tipoventa' => $data['tipoventa'],
      'total' => $data['total'],
      'descuento' => $data['descuento'],
      'devolucion' => $data['devolucion'],
      'pagacon' => $data['pagacon'],
      'cambio' => $data['cambio'],
      'abono' => $data['abono'],
      'saldo' => $data['saldo'],
      'mediopago' => $data['mediopago'],
      'estado' => $data['estado'],
      'nota' => $data['nota'],
      'id_cliente' => $data['id_cliente'],
      'id_usuario' => $data['id_usuario'],
      'registro' => date('Y-m-d H:i:s')
    ];

    $columnas = implode(', ', array_keys($campos));
    $valores = ':' . implode(', :', array_keys($campos));

    $sql = "INSERT INTO $this->tabla ($columnas) VALUES ($valores)";

    $this->db->query($sql);

    foreach ($campos as $campo => $valor) {
      $this->db->bind(':' . $campo, $valor);
    }

    $this->db->execute();
    return $this->db->lastInsertId();
  }

  public function actualizarEstado($data) {
    // Obtener el id_venta y el abono a sumar del array
      $id_venta = $data['id_venta'];
      $estado = $data['nuevoEstado'];

      // Construir la consulta SQL para actualizar el campo abono y saldo
      $sql = "UPDATE ventas SET estado = :estado WHERE id_venta = :id_venta";

      // Ejecutar la consulta usando el método query de tu objeto de base de datos
      // Asumiendo que tu objeto de base de datos tiene un método query y bind
      $this->db->query($sql);

      // Enlazar los parámetros
      $this->db->bind(":id_venta", $id_venta);
      $this->db->bind(":estado", $estado);

      // Ejecutar la consulta con los parámetros enlazados
      $this->db->execute();

      // Verificar si la consulta fue exitosa
      if ($this->db->rowCount() > 0) {
          return true;
      } else {
          return false;
      }
  }

  public function actualizarAbono($data)
  {
      // Obtener el id_venta y el abono a sumar del array
      $id_venta = $data['id_venta'];
      $abono = $data['nuevoAbono'];
      $saldo = $data['nuevoSaldo'];

      // Construir la consulta SQL para actualizar el campo abono y saldo
      $sql = "UPDATE ventas SET abono = :abono, saldo = :saldo WHERE id_venta = :id_venta";

      // Ejecutar la consulta usando el método query de tu objeto de base de datos
      // Asumiendo que tu objeto de base de datos tiene un método query y bind
      $this->db->query($sql);

      // Enlazar los parámetros
      $this->db->bind(":id_venta", $id_venta);
      $this->db->bind(":abono", $abono);
      $this->db->bind(":saldo", $saldo);

      // Ejecutar la consulta con los parámetros enlazados
      $this->db->execute();

      // Verificar si la consulta fue exitosa
      if ($this->db->rowCount() > 0) {
          return true;
      } else {
          return false;
      }
  }

  // Esta función se ejecuta desde DetalleVentas, cuando se actualiza el estado del producto
  public function actualizarVenta($data)
  {
    $sql = "UPDATE ventas v
      SET v.descuento = :descuento, v.devolucion = :devolucion, v.saldo = :saldo, v.estado = :estado, v.entregado = :entregado, v.nota = :nota, v.mod = :fechaActualizacion
      WHERE v.id_venta = :id_venta";
      
    $this->db->query($sql);
    $this->db->bind(":id_venta", $data['id_venta']);
    $this->db->bind(":descuento", $data['descuento']);
    $this->db->bind(":devolucion", $data['devolucion']);
    $this->db->bind(":saldo", $data['saldo']);
    $this->db->bind(":estado", isset($data['estado_venta']) ?$data['estado_venta']: $data['estado']);
    $this->db->bind(":entregado", $data['entregado']);
    $this->db->bind(":nota", $data['nota']);

    $this->db->bind(":fechaActualizacion", date('Y-m-d H:i:s'));

    $this->db->execute();
  }
  // public function actualizarVenta($estadoVenta, $anteriorEstado, $id_venta, $valor, $mod)
  // {
  //     if ($anteriorEstado <= 4 && $estadoVenta == 2) {
  //         $sql = "UPDATE ventas v
  //             JOIN detalle_venta dv ON v.id_venta = dv.id_venta
  //             SET v.descuento = v.descuento + :valor, v.saldo = v.saldo - :valor, v.nota = 'Venta modificada por anulación de producto.', v.mod = :mod
  //             WHERE v.id_venta = :id_venta";
  //     } else if ($anteriorEstado <= 4 && $estadoVenta == 1) {
  //         $sql = "UPDATE ventas v
  //             JOIN detalle_venta dv ON v.id_venta = dv.id_venta
  //             SET v.devolucion = v.devolucion + :valor, v.nota = 'Venta modificada por devolución de producto.', v.mod = :mod
  //             WHERE v.id_venta = :id_venta";
  //     }
      
  //     $this->db->query($sql);
  //     $this->db->bind(":valor", $valor);
  //     $this->db->bind(":id_venta", $id_venta);
  //     $this->db->bind(":mod", $mod);
  //     $this->db->execute();

  //     // Verificar si la consulta se ejecutó sin errores
  //     // return !$this->db->error();
  // }

}
