<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase Produccion Model
 */
class ProduccionModel
{
  private $db;
  private $tabla = 'producciones';

  public function __construct()
  {
    $this->db = new PulseDatabase;
  }

  public function obtenerProduccionTotal($estados)
  {
    // Convierte el array de estados en una cadena separada por comas
    $estadosStr = implode(',', $estados);

    $sql = "SELECT SUM(cantidad) AS cantidad_total
      FROM detalle_venta
      WHERE estado IN (0, 1)";

    $this->db->query($sql);
    $query = $this->db->single();
    return $query;
  }

  public function obtenerProduccion($estados)
  {
    // Convierte el array de estados en una cadena separada por comas
    $estadosStr = implode(',', $estados);

    $sql = "SELECT dv.id_venta, dv.estado, dv.fecha_entrega, dv.nota, p.codigo AS codigo_producto, p.nombre AS nombre_producto, s.nombre AS nombre_sucursal
      FROM detalle_venta dv
      INNER JOIN productos p ON dv.id_producto = p.id_producto
      INNER JOIN ventas v ON dv.id_venta = v.id_venta
      INNER JOIN sucursales s ON v.id_sucursal = s.id_sucursal
      WHERE dv.estado IN (0, 1)";
    $this->db->query($sql);
    return $this->db->resultSet();
  }

  public function actualizarProduccion($id_produccion)
  {
    $sql = "UPDATE $this->tabla
        SET fecha_produccion = :fecha_produccion
        WHERE id_produccion = :id_produccion";
    $this->db->query($sql);
    $this->db->bind(':fecha_produccion', date('Y-m-d H:m:i'));
    $this->db->bind(':id_produccion', $id_produccion);
    $this->db->execute();
    return $this->db->rowCount() > 0 ? true : false;
  }
}
