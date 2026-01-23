<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase Carrito Model
 */
class CarritoModel
{
    private $db;
    private $tabla = 'carrito';

    public function __construct()
    {
        $this->db = new PulseDatabase;
    }

    public function obtenerAll()
    {
        $sql = "SELECT c.*, c.create_at AS fecha, p.codigo AS producto_codigo, p.nombre AS producto_nombre, u.usuario AS usuario_nombre, s.nombre AS sucursal_nombre, cl.nombre AS cliente_nombre, cl.apellidos AS cliente_apellido
        FROM $this->tabla c
        LEFT JOIN productos p ON c.id_producto = p.id_producto
        LEFT JOIN usuarios u ON c.id_vendedor = u.id_usuario
        LEFT JOIN sucursales s ON c.id_sucursal = s.id_sucursal
        LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
        WHERE c.id_producto != 0";
        $this->db->query($sql);
        $query = $this->db->resultSet();

        return $this->db->rowCount() > 0 ? $query : "Error: No se encontraron datos.";
    }

    public function obtenerCarrito($id_cliente, $id_sucursal = null)
    {
        $sql = "SELECT c.*, p.codigo AS producto_codigo, p.nombre AS producto_nombre, p.estado AS producto_estado, p.id_producto AS producto_id
            FROM $this->tabla c
            LEFT JOIN productos p ON c.id_producto = p.id_producto
            WHERE id_cliente = :id_cliente";

        if ($id_sucursal != null) {
            $sql .= " AND id_sucursal = :id_sucursal";
        }

        $this->db->query($sql);
        $this->db->bind(':id_cliente', $id_cliente);

        if ($id_sucursal != null) {            
            $this->db->bind(':id_sucursal', $id_sucursal);
        }

        $query = $this->db->resultSet();

        return $this->db->rowCount() > 0 ? $query : "Error: No se encontraron datos.";
    }

    public function guardarCarrito($data)
    {
        // Obtenemos el ultimo id insertado en la tabla
        $sql = "SELECT MAX(id_carrito) as ultimoId FROM carrito";
        $this->db->query($sql);
        $query = $this->db->single();

        $fechaEntrega = null;
        if (isset($data['fecha_entrega']) && $data['fecha_entrega'] !== '') {
            // Verificar si es una fecha válida
            $fechaEntrega = strtotime($data['fecha_entrega']) ? $data['fecha_entrega'] : null;
        }

        $campos = [
            'id_carrito' => $query->ultimoId + 1,
            'id_sucursal' => $data['id_sucursal'],
            'id_producto' => $data['id_producto'],
            'cantidad' => $data['cantidad'],
            'precio' => $data['precio'],
            'estado' => $data['estado'],
            'nota' => $data['nota'],
            'fecha_entrega' => $fechaEntrega,
            'id_cliente' => $data['id_cliente'],
            'id_vendedor' => $data['id_usuario'],
            'create_at' => date('Y-m-d H:i:s')
        ];

        $columnas = implode(', ', array_keys($campos));
        $valores = ':' . implode(', :', array_keys($campos));

        $sql = "INSERT INTO carrito ($columnas) VALUES ($valores)";

        $this->db->query($sql);


        foreach ($campos as $campo => $valor) {
            $this->db->bind(':' . $campo, $valor);
        }

        $this->db->execute();

        return $this->db->rowCount() > 0 ? true : false;
    }

    public function quitarProducto($id_carrito)
    {
        $sql = "DELETE FROM $this->tabla
          WHERE id_carrito = :id_carrito";

        $this->db->query($sql);
        $this->db->bind(':id_carrito', $id_carrito);
        $this->db->execute();

        return $this->db->rowCount() > 0 ? true : false;
    }

    public function actualizarOpciones($data)
    {
        $columnas = ['cantidad', 'estado', 'nota', 'fecha_entrega'];
        $setClause = '';

        foreach ($columnas as $columna) {
            // Verifica si la columna está presente en los datos y no es null
            if (array_key_exists($columna, $data) && $data[$columna] !== null) {
                $setClause .= "`$columna` = :$columna, ";
            }
        }

        // Quita la coma extra al final
        $setClause = rtrim($setClause, ', ');

        $sql = "UPDATE $this->tabla
                SET $setClause
                WHERE id_carrito = :id_carrito";

        $this->db->query($sql);

        foreach ($columnas as $columna) {
            // Verifica si la columna está presente en los datos y no es null
            if (array_key_exists($columna, $data) && $data[$columna] !== null) {
                $this->db->bind(":$columna", $data[$columna]);
            }
        }

        $this->db->bind(':id_carrito', $data['idCarrito']);
        $this->db->execute();

        return $this->db->rowCount() > 0 ? true : false;
    }
}
