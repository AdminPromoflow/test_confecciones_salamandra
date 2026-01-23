<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase Abono Model
 */
class AbonoModel
{
  private $db;
  private $tabla = 'abonos';

  public function __construct()
  {
    $this->db = new PulseDatabase;
  }

  public function guardarAbono($id_venta, $data)
  {
    $campos = [
      'id_venta' => $id_venta,
      'valor' => $data['abono'],
      'mediopago' => $data['mediopago'],
      'nota' => $data['nota'],
      'id_cliente' => $data['id_cliente'],
      'id_cajero' => $data['id_usuario'],
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
    return $this->db->rowCount() > 0 ? true : false;
  }

  public function obtenerAbonoById($id_venta){
    $sql = "SELECT a.*, v.total, v.saldo
      FROM abonos a
      INNER JOIN ventas v ON a.id_venta = v.id_venta
      WHERE a.id_venta = :id_venta";
    $this->db->query($sql);
    $this->db->bind(':id_venta', $id_venta);
    $query = $this->db->resultSet();
    return $this->db->rowCount() > 0 ? $query : false;
  }
}
