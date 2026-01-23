<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase Usuario Model
 */
class UsuarioModel
{
    private $db;

    public function __construct()
    {
        $this->db = new PulseDatabase;
    }

    public function getUsers($table)
    {
        $sql = "SELECT *
            FROM $table";
        $this->db->query($sql);
        $query = $this->db->resultSet();
        return $this->db->rowCount() > 0 ? $query : "Error: No se encontraron datos.";
    }

    public function getUserById($data)
    {
        $sql = "SELECT *
            FROM {$data['tabla']}
            WHERE id_usuario = :id_usuario";
        $this->db->query($sql);
        $this->db->bind(':id_usuario', $data['id_usuario']);
        $query = $this->db->single();
        return $this->db->rowCount() > 0 ? $query : "Error: No se encontraron datos.";
    }

    public function getUserByStatus($data)
    {
        $sql = "SELECT *
            FROM {$data['tabla']}
            WHERE id_usuario = :id_usuario AND estado = :estado";
        $this->db->query($sql);
        $this->db->bind(':id_usuario', $data['id_usuario']);
        $this->db->bind(':estado', $data['estado']);
        $query = $this->db->single();
        return $this->db->rowCount() > 0 ? $query : "Error: No se encontraron datos.";
    }

    public function getSalesByClient()
    {
        $sql = "SELECT c.*, v.*
            FROM clientes c
            INNER JOIN ventas v
            ON c.id_cliente = v.id_cliente
            WHERE c.id_cliente = :id_cliente";
        $this->db->query($sql);
        $this->db->bind(':id_cliente', 1);
        $query = $this->db->resultSet();
        return $this->db->rowCount() > 0 ? $query : "Error: No se encontraron datos.";
    }
}
