<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase Subcategoria Model
 */
class SubcategoriaModel
{
    private $db;

    public function __construct()
    {
        $this->db = new PulseDatabase;
    }

    public function obtenerSubcategorias()
    {
        $sql = "SELECT sc.*
            FROM subcategorias sc";
        $this->db->query($sql);
        $query = $this->db->resultSet();
        return $this->db->rowCount() > 0 ? $query : false;
    }
}
