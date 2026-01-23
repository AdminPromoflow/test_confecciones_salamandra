<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase Cierre Model
 */
class CierreModel
{
  private $db;

  public function __construct()
  {
    $this->db = new PulseDatabase;
  }

  // public function obtenerVentaTotal($id_usuario)
  // {
  //   $fecha_actual = '2024-01-04';

  //   $sql = "SELECT SUM(total) AS venta_total
  //     FROM ventas
  //     WHERE registro >= '{$fecha_actual} 00:00:00' AND registro <= '{$fecha_actual} 23:59:59' AND id_usuario = {$id_usuario}";

  //     $this->db->query($sql);
  //     $query = $this->db->single();
  //     return $this->db->rowCount() > 0 ? $query : false;
  // }

  // public function obtenerCierre($mediopago, $id_usuario)
  // {
  //   $fecha_actual = date('Y-m-d');
  //   // $fecha_actual = '2024-01-04';

  //   $sql = "SELECT ";

  //   if ($mediopago == 0) {
  //     $sql .= "SUM(total) AS suma_total FROM ventas WHERE registro >= '{$fecha_actual} 00:00:00' AND registro <= '{$fecha_actual} 23:59:59' AND id_usuario = {$id_usuario}";
  //   } else {
  //     $sql .= "SUM(pagacon) AS suma_total FROM ventas WHERE registro >= '{$fecha_actual} 00:00:00' AND registro <= '{$fecha_actual} 23:59:59' AND id_usuario = {$id_usuario} AND mediopago = {$mediopago}";
  //   }

  //   $this->db->query($sql);
  //   $query = $this->db->single();
  //   return $this->db->rowCount() > 0 ? $query : false;
  // }


  // Backup

  // public function obtenerCierre($fechaInicio, $fechaFin)
  // {
  //   $inicio = $fechaInicio . " 00:00:00";
  //   $fin = $fechaFin . " 23:59:59";

  //   $sql = "SELECT
  //       v.id_usuario, u.usuario,
  //       SUM(IF(v.mediopago=1, v.abono, 0)) AS TotalEfectivo,
  //       SUM(IF(v.mediopago=2, v.abono, 0)) AS TotalNequi,
  //       SUM(IF(v.mediopago=3, v.abono, 0)) AS TotalDaviplata,
  //       SUM(v.descuento) AS TotalDescuento,
  //       SUM(v.devolucion) AS TotalDevolucion
  //       FROM ventas v
  //       JOIN usuarios u ON v.id_usuario = u.id_usuario
  //       WHERE v.registro BETWEEN :fechaInicio AND :fechaFin
  //       GROUP BY v.id_usuario";
  //   $this->db->query($sql);
  //   $this->db->bind(":fechaInicio", $inicio);
  //   $this->db->bind(":fechaFin", $fin);

  //   $query = $this->db->resultSet();

  //   return $this->db->rowCount() > 0 ? $query : false;
  // }

  public function obtenerCierreAbonos($fechaInicio, $fechaFin)
  {
    $inicio = $fechaInicio . " 00:00:00.000000";
    $fin = $fechaFin . " 23:59:59.000000";

    $sql = "SELECT a.id_cajero, u.usuario, 
      SUM(IF(a.mediopago=1, a.valor, 0)) AS TotalEfectivo,
      SUM(IF(a.mediopago=2, a.valor, 0)) AS TotalNequi,
      SUM(IF(a.mediopago=3, a.valor, 0)) AS TotalDaviplata
      FROM abonos a
      JOIN usuarios u ON a.id_cajero = u.id_usuario
      WHERE a.registro BETWEEN :fechaInicio AND :fechaFin
      GROUP BY u.id_usuario";
    $this->db->query($sql);
    $this->db->bind(":fechaInicio", $inicio);
    $this->db->bind(":fechaFin", $fin);

    $query = $this->db->resultSet();

    return $this->db->rowCount() > 0 ? $query : false;
  }

//   public function obtenerCierreVentas($fechaInicio, $fechaFin)
//   {
//     $inicio = $fechaInicio . " 00:00:00.000000";
//     $fin = $fechaFin . " 23:59:59.000000";

//     $sql =
//       "SELECT v.id_usuario, u.usuario, 
//       SUM(IF(v.mediopago=1 AND v.abono=0, v.total, 0)) AS TotalEfectivo,
//       SUM(IF(v.mediopago=2 AND v.abono=0, v.total, 0)) AS TotalNequi,
//       SUM(IF(v.mediopago=3 AND v.abono=0, v.total, 0)) AS TotalDaviplata,
//       SUM(v.descuento) AS TotalDescuento,
//       SUM(v.devolucion) AS TotalDevolucion
//       FROM ventas v
//       JOIN usuarios u ON v.id_usuario = u.id_usuario
//       WHERE v.registro BETWEEN :fechaInicio AND :fechaFin
//       GROUP BY v.id_usuario";
//     $this->db->query($sql);
//     $this->db->bind(":fechaInicio", $inicio);
//     $this->db->bind(":fechaFin", $fin);

//     $query = $this->db->resultSet();

//     return $this->db->rowCount() > 0 ? $query : false;
//   }

    public function obtenerCierreCajaAdmin($fechaInicio, $fechaFin)
    {
        $inicio = $fechaInicio . " 00:00:00.000000";
        $fin = $fechaFin . " 23:59:59.000000";
    
        $sql = "
            SELECT 
                ed.id_sucursal,
                ed.id_cajero,
                u.usuario,
                SUM(IF(ed.metodo = 1, ed.valor, 0)) AS TotalEfectivo,
                SUM(IF(ed.metodo = 2, ed.valor, 0)) AS TotalNequi,
                SUM(IF(ed.metodo = 3, ed.valor, 0)) AS TotalDaviplata,
                (
                    SELECT SUM(sd.valor)
                    FROM salida_dinero sd
                    WHERE sd.id_sucursal = ed.id_sucursal
                    AND sd.id_cajero = ed.id_cajero
                    AND sd.fecha BETWEEN :fechaInicio AND :fechaFin
                ) AS TotalSalidaDinero
            FROM entrada_dinero ed
            JOIN usuarios u ON ed.id_cajero = u.id_usuario
            WHERE ed.id_sucursal IN (3, 4)
            AND u.id_rol = 2
            AND ed.fecha BETWEEN :fechaInicio AND :fechaFin
            GROUP BY ed.id_sucursal, ed.id_cajero, u.usuario
        ";
    
        $this->db->query($sql);
        $this->db->bind(":fechaInicio", $inicio);
        $this->db->bind(":fechaFin", $fin);
    
        $query = $this->db->resultSet();
    
        return $this->db->rowCount() > 0 ? $query : false;
    }




}
