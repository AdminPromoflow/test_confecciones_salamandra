<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase Salida Model
 */
class SalidaModel
{
  private $db;
  private $tabla = 'salida_dinero';

  public function __construct()
  {
    $this->db = new PulseDatabase;
  }

  public function guardarSalida($datosSalida)
  {
    $columnas = implode(', ', array_keys($datosSalida));
    $valores = ':' . implode(', :', array_keys($datosSalida));

    $sql = "INSERT INTO $this->tabla ($columnas) VALUES ($valores)";

    $this->db->query($sql);

    foreach ($datosSalida as $campo => $valor) {
      $this->db->bind(':' . $campo, $valor);
    }

    $this->db->execute();
    return $this->db->rowCount() > 0 ? true : false;
  }

  public function obtenerSalida($idUsuario, $fechaInicio, $fechaFin, $idSucursal)
  {
    $sql = "SELECT s.id_cajero, u.usuario AS nombre_usuario, s.id_sucursal, suc.nombre AS nombre_sucursal,   
      SUM(s.valor) AS TotalDevolucion
      FROM {$this->tabla} s
      JOIN usuarios u ON s.id_cajero = u.id_usuario
      JOIN sucursales suc ON s.id_sucursal = suc.id_sucursal
      WHERE s.id_cajero = :idUsuario
        AND s.fecha BETWEEN :fechaInicio AND :fechaFin 
        AND s.id_sucursal = :idSucursal
      GROUP BY s.id_cajero, u.usuario, s.id_sucursal, suc.nombre";
    
    $this->db->query($sql);
    $this->db->bind(':idUsuario', $idUsuario);
    $this->db->bind(":fechaInicio", $fechaInicio);
    $this->db->bind(":fechaFin", $fechaFin);
    $this->db->bind(":idSucursal", $idSucursal);
    $query = $this->db->resultSet();
    return $this->db->rowCount() > 0 ? $query : false;
  }
}
