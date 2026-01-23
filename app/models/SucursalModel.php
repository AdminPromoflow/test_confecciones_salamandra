<?php
defined("BASEPATH") or exit("No direct script access allowed");

require_once SYSTEM_PATH . "PulseDatabase.php";

/**
 * Clase Sucursal Model
 */
class SucursalModel
{
    private $db;
    private $tabla = 'sucursales';

    public function __construct()
    {
        $this->db = new PulseDatabase();
    }

    // Esta función se ejecuta desde el controlador SucursalesController
    public function obtenerSucursales()
    {
        $sql = "SELECT s.*
            FROM $this->tabla s";
        $this->db->query($sql);
        $query = $this->db->resultSet();
        return $this->db->rowCount() > 0 ? $query : false;
    }

    public function obtenerSucursalesInventario()
    {
        $sql = "SELECT s.id_sucursal, s.nombre, s.permiso, s.registro, SUM(ip.stock) as total_stock
            FROM $this->tabla s
            LEFT JOIN inventario_productos ip ON s.id_sucursal = ip.id_sucursal
            GROUP BY s.id_sucursal";
        $this->db->query($sql);
        $query = $this->db->resultSet();
        return $this->db->rowCount() > 0 ? $query : false;
    }

    public function actualizarSucursal($sucursalData)
    {
        $campos = [
            "id_sucursal",
            "nombre",
            "permiso"
        ];

        // Crear una cadena de actualización para SQL
        $updateString = implode(", ", array_map(function ($campo) {
            return $campo . " = :" . $campo;
        }, $campos));

        $sql = "UPDATE $this->tabla SET $updateString WHERE id_sucursal = :id_sucursal";

        $this->db->query($sql);

        foreach ($campos as $campo) {
            if (array_key_exists($campo, $sucursalData)) {
                $this->db->bind(":" . $campo, $sucursalData[$campo]);
            }
        }

        $this->db->execute();

        return $this->db->rowCount() > 0 ? true : false;
    }

    public function crearSucursal($sucursalData)
    {
        $campos = [
            "nombre",
            "permiso",
            "registro"
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
            if (array_key_exists($campo, $sucursalData)) {
                $this->db->bind(":" . $campo, $sucursalData[$campo]);
            }
        }

        $this->db->execute();

        return $this->db->rowCount() > 0 ? true : false;
    }

    public function obtenerSucursalId($idSucursal)
    {
        $sql = "SELECT s.*
          FROM $this->tabla s
          WHERE s.id_sucursal = :id_sucursal";
        $this->db->query($sql);
        $this->db->bind(":id_sucursal", $idSucursal);
        $query = $this->db->single();
        return $this->db->rowCount() > 0 ? $query : false;
    }



    // Esta función obtiene los detalles de cada sucursal registrada con toda : Desde SucursalesController
    public function getSucursales()
    {
        $sql = "SELECT s.id_sucursal, s.nombre as nombre_sucursal, SUM(ip.stock) as total_stock
        FROM sucursales s
        LEFT JOIN inventario_productos ip ON s.id_sucursal = ip.id_sucursal
        GROUP BY s.id_sucursal";
        $this->db->query($sql);
        $query = $this->db->resultSet();
        return $this->db->rowCount() > 0 ? $query : false;
    }

    // Obtenemos una sola sucursal con su inventario - Vista. sucursales/ver
    public function getInventarioSucursal($idSucursal)
    {
        $sql = "SELECT s.id_sucursal, s.nombre as nombre_sucursal, s.permiso, ip.stock, p.id_producto, p.codigo, p.nombre
        FROM sucursales s
        LEFT JOIN inventario_productos ip ON s.id_sucursal = ip.id_sucursal
        INNER JOIN productos p ON ip.id_producto = p.id_producto
        WHERE s.id_sucursal = :id_sucursal";
        $this->db->query($sql);
        $this->db->bind(":id_sucursal", $idSucursal);
        $query = $this->db->resultSet();
        return $this->db->rowCount() > 0 ? $query : false;
    }
}
