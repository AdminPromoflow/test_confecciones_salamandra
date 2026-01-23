<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase Proveedor Model
 */
class ProveedorModel
{
    private $db;

    public function __construct()
    {
        $this->db = new PulseDatabase;
    }

    public function obtenerProveedoresByTipo($tipo)
    {
        $sql = "SELECT pv.*
            FROM proveedores pv
            WHERE tipo = :tipo";
        $this->db->query($sql);
        $this->db->bind(':tipo', $tipo);
        $query = $this->db->resultSet();
        return $this->db->rowCount() > 0 ? $query : false;
    }
}
