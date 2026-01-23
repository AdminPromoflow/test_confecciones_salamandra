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

    // 🔹 NO TOCAR: usado en el login
    public function obtenerUsuario($data)
    {
        $sql = 'SELECT u.id_usuario, u.nombre, u.apellidos, u.email, u.rol, u.estado, u.permiso, u.usuario, u.password
            FROM usuarios u
            WHERE u.usuario = :usuario';
        $this->db->query($sql);
        $this->db->bind(':usuario', $data['usuario']);
        $query = $this->db->single();
        return $this->db->rowCount() > 0 ? $query : FALSE;
    }

    // 🔹 NO TOCAR: usado en otras partes (cajeros, etc.)
    public function obtenerCajeros()
    {
        $sql = 'SELECT *
            FROM usuarios
            WHERE rol = 2
            AND estado = 1';
        $this->db->query($sql);
        $query = $this->db->resultSet();
        return $this->db->rowCount() > 0 ? $query : 'Error: No se encontraron datos.';
    }

    public function obtenerUsuarioById($idUsuario)
    {
        $sql = 'SELECT *
            FROM usuarios
            WHERE id_usuario = :id_usuario';
        $this->db->query($sql);
        $this->db->bind(':id_usuario', $idUsuario);
        $query = $this->db->single();
        return $this->db->rowCount() > 0 ? $query : 'Error: No se encontraron datos.';
    }

    // 🔸 NUEVOS MÉTODOS (solo para gestión de usuarios en panel admin)

    public function obtenerUsuarios()
    {
        $sql = 'SELECT * FROM usuarios';
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function obtenerUsuarioPorUsuario($usuario)
    {
        $sql = 'SELECT * FROM usuarios WHERE usuario = :usuario';
        $this->db->query($sql);
        $this->db->bind(':usuario', $usuario);
        return $this->db->single();  // Devuelve objeto stdClass o false
    }

    public function crearUsuario($data)
    {
        $campos = ['nombre', 'apellidos', 'email', 'usuario', 'password', 'rol', 'estado', 'registro'];
        $camposStr = implode(', ', $campos);
        $placeholders = ':' . implode(', :', $campos);

        $sql = "INSERT INTO usuarios ($camposStr) VALUES ($placeholders)";
        $this->db->query($sql);

        foreach ($campos as $campo) {
            $this->db->bind(':' . $campo, $data[$campo] ?? null);
        }

        $this->db->execute();
        return $this->db->lastInsertId();
    }

    public function actualizarUsuario($data)
    {
        $sql = 'UPDATE usuarios SET 
                    nombre = :nombre,
                    apellidos = :apellidos,
                    email = :email,
                    usuario = :usuario,
                    rol = :rol
                WHERE id_usuario = :id_usuario';

        $this->db->query($sql);
        $this->db->bind(':nombre', $data['nombre']);
        $this->db->bind(':apellidos', $data['apellidos']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':usuario', $data['usuario']);
        $this->db->bind(':rol', $data['rol']);
        $this->db->bind(':id_usuario', $data['id_usuario']);

        return $this->db->execute();
    }

    public function actualizarEstado($data)
    {
        // Acepta 1/0 o 'activo'/'inactivo' y convierte a 1/0
        $estado = in_array($data['estado'], [1, '1', 'activo'], true) ? 1 : 0;
        $sql = 'UPDATE usuarios SET estado = :estado WHERE id_usuario = :id_usuario';
        $this->db->query($sql);
        $this->db->bind(':estado', $estado, \PDO::PARAM_INT);
        $this->db->bind(':id_usuario', $data['id_usuario'], \PDO::PARAM_INT);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public function actualizarPassword($idUsuario, $nuevaPassword)
    {
        $hash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password = :password WHERE id_usuario = :id";
        $this->db->query($sql);
        $this->db->bind(':password', $hash);
        $this->db->bind(':id', $idUsuario);
        return $this->db->execute(); // ← debe devolver true/false
    }
}
