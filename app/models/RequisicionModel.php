<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase Requisicion Model
 */
class RequisicionModel
{
    private $db;
    private $tabla = 'requisiciones';

    public function __construct()
    {
        $this->db = new PulseDatabase;
    }

    public function crearRequisicion($requisicionData)
    {
        $campos = [
            'fecha',
            'id_usuario',
            'id_sucursal',
            'id_producto',
            'cantidad'
        ];

        // Crear una cadena de campos para SQL
        $camposString = implode(', ', $campos);

        // Crear una cadena de marcadores de posición para los valores
        $placeholders = implode(', ', array_map(function ($campo) {
            return ':' . $campo;
        }, $campos));

        $sql = "INSERT INTO {$this->tabla} ({$camposString}) VALUES ({$placeholders})";

        $this->db->query($sql);

        foreach ($campos as $campo) {
            if (array_key_exists($campo, $requisicionData)) {
                $this->db->bind(':' . $campo, $requisicionData[$campo]);
            }
        }

        $this->db->execute();

        return $this->db->rowCount() > 0 ? true : false;
    }

    public function obtenerRequisiciones()
    {
        $sql = "SELECT r.*, p.codigo
            FROM $this->tabla r
            LEFT JOIN productos p ON r.id_producto = p.id_producto
            WHERE r.estado = :estado
            ORDER BY r.id_requisicion ASC";

        $this->db->query($sql);
        $this->db->bind(":estado", 1);

        $query = $this->db->resultSet();

        return $this->db->rowCount() > 0 ? $query : false;
    }

    public function obtenerRequisicionesPendientes()
    {
        $sql = "SELECT r.*, p.codigo, p.nombre
            FROM $this->tabla r
            LEFT JOIN productos p ON r.id_producto = p.id_producto
            WHERE r.estado = :estado
            ORDER BY r.id_requisicion DESC";

        $this->db->query($sql);
        $this->db->bind(":estado", 0);

        $query = $this->db->resultSet();
        
        return $this->db->rowCount() > 0 ? $query : false;
    }

    public function actualizarEstado($requisicion)
    {
        $sql = "UPDATE $this->tabla
            SET estado = :estado
            WHERE id_requisicion = :id_requisicion";
        $this->db->query($sql);
        $this->db->bind(':estado', $requisicion['estado']);
        $this->db->bind(':id_requisicion', $requisicion['id_requisicion']);
        $this->db->execute();
        return $this->db->rowCount() > 0 ? true : false;
    }
}