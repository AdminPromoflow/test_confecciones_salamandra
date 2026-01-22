<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase Transferencia Model
 */
class TransferenciaModel
{
    private $db;

    public function __construct()
    {
        $this->db = new PulseDatabase;
    }

    // Esta función obtiene todas las transferencias realizadas : Desde el controlador Transferencias
    public function getTransferencias()
    {
      $sql = "SELECT t.*, p.codigo, p.nombre AS nombre_producto, so.nombre as sucursal_origen, sd.nombre as sucursal_destino, u.usuario
              FROM transferencias t
              INNER JOIN productos p ON t.id_producto = p.id_producto
              INNER JOIN sucursales so ON t.sucursal_origen = so.id_sucursal
              INNER JOIN sucursales sd ON t.sucursal_destino = sd.id_sucursal
              INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                ORDER BY t.id_transferencia DESC";
      $this->db->query($sql);
      $query = $this->db->resultSet();
      return $this->db->rowCount() > 0 ? $query : FALSE;
    }

    public function getLastTransferencias()
    {
      $sql = "SELECT t.*, p.codigo, p.nombre AS nombre_producto, so.nombre as sucursal_origen, sd.nombre as sucursal_destino, u.usuario
              FROM transferencias t
              INNER JOIN productos p ON t.id_producto = p.id_producto
              INNER JOIN sucursales so ON t.sucursal_origen = so.id_sucursal
              INNER JOIN sucursales sd ON t.sucursal_destino = sd.id_sucursal
              INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
              ORDER BY id_transferencia DESC
              LIMIT 7";
      $this->db->query($sql);
      $query = $this->db->resultSet();
      return $this->db->rowCount() > 0 ? $query : FALSE;
    }

    // Comenzamos con la transferencia entre sucursales - Vista. transferencias/formRegistrarTransferencia
    public function realizarTransferencia($data)
    {
        // Verificar si el producto está en la sucursal de origen y si hay suficiente cantidad
        if ($this->productoEnSucursal($data['producto'], $data['sucursal_origen'], $data['cantidad'])) {
            // Verificar si el producto está en la sucursal destino
            if ($this->productoEnSucursal($data['producto'], $data['sucursal_destino'])) {
                // El producto ya está en la sucursal destino, realizar actualización
                $this->actualizarStock($data);
            } else {
                // El producto no está en la sucursal destino, realizar inserción
                $this->registrarTransferencia($data);
            }
        } else {
            // No hay suficiente cantidad en la sucursal de origen, manejar según tus requisitos
            // Por ejemplo, podrías lanzar una excepción, registrar un mensaje de error, etc.
            throw new Exception("No hay suficiente cantidad del producto en la sucursal de origen.");
        }
    }

    public function registrarTransferencia($transferencia_data)
    {
      $campos = [
        'id_producto' => $transferencia_data['producto'],
        'cantidad' => $transferencia_data['cantidad'],
        'sucursal_origen' => $transferencia_data['sucursal_origen'],
        'sucursal_destino' => $transferencia_data['sucursal_destino'],
        'anotacion' => $transferencia_data['anotacion'],
        'id_usuario' => $transferencia_data['id_usuario'],
        'registro' => date('Y-m-d H:i:s')
      ];

      // Crear una cadena de columnas para SQL
      $columnas = implode(', ', array_keys($campos));
      // Crear una cadena de valores para SQL
      $valores = ':' . implode(', :', array_keys($campos));

      $sql = "INSERT INTO transferencias ($columnas) VALUES ($valores)";
      $this->db->query($sql);

      foreach ($campos as $campo => $valor) {
        $this->db->bind(':' . $campo, $valor);
      }

      $this->db->execute();
      return $this->db->rowCount() > 0 ? true : false;
    }

    // Obtenemos una sola transferencia con su inventario - Vista. transferencias/ver
    public function getSucursal($id_transferencia)
    {
      $sql = "SELECT s.id_transferencia, s.nombre as nombre_transferencia, s.permiso, ip.stock, p.id_producto, p.codigo, p.nombre
          FROM transferencias s
          LEFT JOIN inventario_productos ip ON s.id_transferencia = ip.id_transferencia
          INNER JOIN productos p ON ip.id_producto = p.id_producto
          WHERE s.id_transferencia = :id_transferencia";
      $this->db->query($sql);
      $this->db->bind(':id_transferencia', $id_transferencia);
      $query = $this->db->resultSet();
      return $this->db->rowCount() > 0 ? $query : FALSE;
    }


    public function getSucursalById($transferencia_data)
    {
        $sql = "SELECT s.*
            FROM transferencias s
            WHERE s.id_transferencia = :id_transferencia";
        $this->db->query($sql);
        $this->db->bind(':id_transferencia', $transferencia_data['id_transferencia']);
        $query = $this->db->single();
        return $this->db->rowCount() > 0 ? $query : FALSE;
    }

    public function getAllTransferencias()
    {
        $sql = "SELECT s.*
                FROM transferencias s";
        $this->db->query($sql);
        $query = $this->db->resultSet();
        return $this->db->rowCount() > 0 ? $query : FALSE;
    }

    // public function getUserByStatus($data)
    // {
    //     $sql = "SELECT *
    //         FROM {$data['tabla']}
    //         WHERE id_usuario = :id_usuario AND estado = :estado";
    //     $this->db->query($sql);
    //     $this->db->bind(':id_usuario', $data['id_usuario']);
    //     $this->db->bind(':estado', $data['estado']);
    //     $query = $this->db->single();
    //     return $this->db->rowCount() > 0 ? $query : FALSE;
    // }
    //
    // public function getSalesByClient()
    // {
    //     $sql = "SELECT c.*, v.*
    //         FROM clientes c
    //         INNER JOIN ventas v
    //         ON c.id_cliente = v.id_cliente
    //         WHERE c.id_cliente = :id_cliente";
    //     $this->db->query($sql);
    //     $this->db->bind(':id_cliente', 1);
    //     $query = $this->db->resultSet();
    //     return $this->db->rowCount() > 0 ? $query : FALSE;
    // }
}
