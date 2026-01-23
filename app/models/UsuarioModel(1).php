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

    public function obtenerUsuario($data)
    {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellidos, u.email, u.rol, u.estado, u.permiso, u.usuario, u.password
            FROM usuarios u
            WHERE u.usuario = :usuario";
        $this->db->query($sql);
        $this->db->bind(':usuario', $data['usuario']);
        $query = $this->db->single();
        return $this->db->rowCount() > 0 ? $query : FALSE;
    }
}
