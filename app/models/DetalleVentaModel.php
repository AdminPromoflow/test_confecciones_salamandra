<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase DetalleVenta Model
 */
class DetalleVentaModel
{
  private $db;
  private $tabla = 'detalle_venta';

  public function __construct()
  {
    $this->db = new PulseDatabase;
  }

  public function obtenerDetalles()
  {
    $sql = "SELECT dv.*, p.codigo 
        FROM $this->tabla dv
        JOIN productos p ON dv.id_producto = p.id_producto";
    $this->db->query($sql);
    $query = $this->db->resultSet();
    return $this->db->rowCount() > 0 ? $query : false;
  }

  // *************************************
  // Ejecutor VentasController
  // *************************************

  // Esa función se ejecuta desde el controlador VentasController - método guardarVenta()
  public function guardarDetalle($datosDetalle)
  {
    $columnas = implode(', ', array_keys($datosDetalle));
    $valores = ':' . implode(', :', array_keys($datosDetalle));

    $sql = "INSERT INTO $this->tabla ($columnas) VALUES ($valores)";

    $this->db->query($sql);

    foreach ($datosDetalle as $campo => $valor) {
      $this->db->bind(':' . $campo, $valor);
    }

    $this->db->execute();
    return $this->db->lastInsertId();
  }

  // *************************************
  // Ejecutor ProduccionesController
  // *************************************

  // Esa función se ejecuta desde el controlador ProduccionesController - método actualizarEstado()
  public function obtenerDetalle($id_detalle)
  {
    $sql = "SELECT dv.*, v.id_sucursal
        FROM detalle_venta dv
        LEFT JOIN ventas v ON dv.id_venta = v.id_venta
        WHERE id_detalle = :id_detalle";
    $this->db->query($sql);
    $this->db->bind(':id_detalle', $id_detalle);
    return $this->db->single();
  }

  // *************************************
  // Ejecutor VentasController
  // *************************************

  // Esa función se ejecuta desde el controlador VentasController - método imprimirTiquet()
  // Esa función se ejecuta desde el controlador DetalleVentaController - método obtenerDetalle()
  public function obtenerDetalleByIdVenta($id_venta)
  {
    $sql = "SELECT dv.*, p.codigo, p.nombre AS nombre_producto, p.talla
      FROM $this->tabla dv
      INNER JOIN productos p ON dv.id_producto = p.id_producto
      WHERE dv.id_venta = :id_venta";
    $this->db->query($sql);
    $this->db->bind(":id_venta", $id_venta);
    $query = $this->db->resultSet();
    return $this->db->rowCount() > 0 ? $query : false;
  }

  public function obtenerDetallePendiente($estados)
  {
    // Convierte el array de estados en una cadena separada por comas
    $estadosStr = implode(',', $estados);

    $sql = "SELECT dv.id_detalle, dv.id_venta, dv.estado, dv.fecha_entrega, dv.nota, p.id_producto, p.codigo AS codigo_producto, p.nombre AS nombre_producto, s.id_sucursal, s.nombre AS nombre_sucursal
      FROM detalle_venta dv
      INNER JOIN productos p ON dv.id_producto = p.id_producto
      INNER JOIN ventas v ON dv.id_venta = v.id_venta
      INNER JOIN sucursales s ON v.id_sucursal = s.id_sucursal
      WHERE dv.estado IN (0, 1)";
    $this->db->query($sql);
    return $this->db->resultSet();
  }

  // *************************************
  // Ejecutor DetalleVentaController
  // *************************************

  // Esa función se ejecuta desde el controlador ProduccionesController - método actualizarEstado()
  // Esa función se ejecuta desde el controlador DetalleVentaController - método actualizarEstadoDetalle()
  public function actualizarEstado($data)
  {
    $sql = "UPDATE detalle_venta SET estado = :estado, produccion = :produccion, fecha_actualizada = :fecha_actualizada WHERE id_detalle = :id_detalle";

    $this->db->query($sql);
    $this->db->bind(':estado', $data['nuevo_estado']);
    $this->db->bind(':produccion', isset($data['produccion']) ?: 0);
    $this->db->bind(':fecha_actualizada', date('Y-m-d H:i:s'));
    $this->db->bind(':id_detalle', $data['id_detalle']);

    // Muestra la consulta SQL con los valores asignados (opcional)
    // showTest($this->db->getDebugInfo());

    $this->db->execute();

    return $this->db->rowCount() > 0 ? true : false;
  }
  // public function actualizarEstado($id_detalle, $estado, $fecha = null, $produccion = 0)
  // {
  //   $sql = "UPDATE detalle_venta SET estado = :estado, produccion = :produccion";
  //   if ($fecha) {
  //     $sql .= ", fecha_actualizada = :fecha_actualizada";
  //   }
  //   $sql .= " WHERE id_detalle = :id_detalle";

  //   $this->db->query($sql);
  //   $this->db->bind(':estado', $estado);
  //   $this->db->bind(':produccion', $produccion);
  //   if ($fecha) {
  //     $this->db->bind(':fecha_actualizada', $fecha);
  //   }
  //   $this->db->bind(':id_detalle', $id_detalle);

  //   // Muestra la consulta SQL con los valores asignados (opcional)
  //   // showTest($this->db->getDebugInfo());

  //   $this->db->execute();

  //   return $this->db->rowCount() > 0 ? true : false;
  // }

  public function exportarPendientes()
  {
    // Consulta a la base de datos
    $sql = "SELECT dv.id_venta, p.codigo, p.nombre AS nombre_producto, dv.nota, dv.fecha_entrega
      FROM detalle_venta dv 
      JOIN productos p ON dv.id_producto = p.id_producto 
      WHERE dv.estado <= 1";
    $this->db->query($sql);

    return $this->db->resultSet();
  }

  public function exportarListos()
  {
    // Consulta a la base de datos
    $sql = "SELECT dv.id_venta, p.codigo, p.nombre AS nombre_producto, dv.fecha_entrega, dv.fecha_actualizada
      FROM detalle_venta dv 
      JOIN productos p ON dv.id_producto = p.id_producto 
      WHERE dv.produccion = 1";
    $this->db->query($sql);

    return $this->db->resultSet();
  }
}
