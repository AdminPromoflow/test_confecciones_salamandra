<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase Institucion Model
 */
class InstitucionModel
{
    private $db;

    public function __construct()
    {
        $this->db = new PulseDatabase;
    }

    public function obtenerInstituciones()
    {
        $sql = "SELECT i.*
            FROM instituciones i";
        $this->db->query($sql);
        $query = $this->db->resultSet();
        return $this->db->rowCount() > 0 ? $query : false;
    }
}
