<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase Programacion Model
 */
class ProgramacionModel
{
    private $db;
    private $tabla = 'programaciones';

    public function __construct()
    {
        $this->db = new PulseDatabase;
    }

    public function obtenerProgramacion($estado)
    {
        $sql = "SELECT p.*
            FROM programaciones p
            WHERE estado = :estado";
        $this->db->query($sql);
        $this->db->bind(':estado', $estado);
        $query = $this->db->resultSet();
        return $this->db->rowCount() > 0 ? $query : false;
    }

    public function guardarProgramacion($data)
    {
        $campos = [
            "produccion",
            "fecha",
            "estado",
        ];

        // Crear una cadena de campos para SQL
        $camposString = implode(", ", $campos);

        // Crear una cadena de marcadores de posición para los valores
        $placeholders = implode(", ", array_map(function ($campo) {
            return ":" . $campo;
        }, $campos));

        $sql = "INSERT INTO $this->tabla ($camposString) VALUES ($placeholders)";

        $this->db->query($sql);

        foreach ($campos as $campo) {
            if (array_key_exists($campo, $data)) {
                $this->db->bind(":" . $campo, $data[$campo]);
            }
        }

        $this->db->execute();

        return $this->db->rowCount() > 0 ? true : false;
    }

    public function actualizarProduccion($id_programacion, $nuevoEstado)
    {
        $sql = "UPDATE $this->tabla
        SET estado = :nuevoEstado
        WHERE id_programacion = :id_programacion";
        $this->db->query($sql);
        $this->db->bind(':nuevoEstado', $nuevoEstado);
        $this->db->bind(':id_programacion', $id_programacion);
        $this->db->execute();
        return $this->db->rowCount() > 0 ? true : false;
    }

    public function obtenerProgramacionId($id)
    {
        $sql = "SELECT p.fecha
            FROM programaciones p
            WHERE id_programacion = :id_programacion";
        $this->db->query($sql);
        $this->db->bind(':id_programacion', $id);
        $query = $this->db->single();
        return $this->db->rowCount() > 0 ? $query : "";
    }
}
