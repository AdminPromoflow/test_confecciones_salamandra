<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase Categoria Model
 */
class CategoriaModel
{
    private $db;
    private $tabla = 'categorias';

    public function __construct()
    {
        $this->db = new PulseDatabase;
    }

    public function obtenerCategorias()
    {
        $sql = "SELECT c.*
            FROM $this->tabla c";
        $this->db->query($sql);
        $query = $this->db->resultSet();
        return $this->db->rowCount() > 0 ? $query : false;
    }
}
