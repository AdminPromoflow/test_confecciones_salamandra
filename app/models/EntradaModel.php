<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase Entrada Model
 */
class EntradaModel
{
  private $db;
  private $tabla = 'entrada_dinero';

  public function __construct()
  {
    $this->db = new PulseDatabase;
  }

  public function guardarEntrada($datosEntrada)
  {
    $columnas = implode(', ', array_keys($datosEntrada));
    $valores = ':' . implode(', :', array_keys($datosEntrada));

    $sql = "INSERT INTO $this->tabla ($columnas) VALUES ($valores)";

    $this->db->query($sql);

    foreach ($datosEntrada as $campo => $valor) {
      $this->db->bind(':' . $campo, $valor);
    }

    $this->db->execute();
    return $this->db->rowCount() > 0 ? true : false;
  }

  public function obtenerEntrada($idUsuario, $fechaInicio, $fechaFin, $idSucursal)
  {
    $sql = "SELECT e.id_cajero, u.usuario AS nombre_usuario, e.id_sucursal, s.nombre AS nombre_sucursal,   
      SUM(IF(e.metodo=1, e.valor, 0)) AS TotalEfectivo,
      SUM(IF(e.metodo=2, e.valor, 0)) AS TotalNequi,
      SUM(IF(e.metodo=3, e.valor, 0)) AS TotalDaviplata
      FROM entrada_dinero e
      JOIN usuarios u ON e.id_cajero = u.id_usuario
      JOIN sucursales s ON e.id_sucursal = s.id_sucursal
      WHERE e.id_cajero = :idUsuario
        AND e.fecha BETWEEN :fechaInicio AND :fechaFin 
        AND e.id_sucursal = :idSucursal
      GROUP BY e.id_cajero";
    $this->db->query($sql);
    $this->db->bind(':idUsuario', $idUsuario);
    $this->db->bind(":fechaInicio", $fechaInicio);
    $this->db->bind(":fechaFin", $fechaFin);
    $this->db->bind(":idSucursal", $idSucursal);
    $query = $this->db->resultSet();
    return $this->db->rowCount() > 0 ? $query : false;
  }
}
