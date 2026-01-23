<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase Cliente Model
 */
class ClienteModel
{
  private $db;
  private $tabla = 'clientes';

  private $campos = [
    'cedula', 'nombre', 'apellidos', 'direccion', 'barrio', 'telefono', 'email', 'registro', 'estado'
  ];

  public function __construct()
  {
    $this->db = new PulseDatabase;
  }

  public function obtenerClientes()
  {
    $sql = "SELECT c.* FROM $this->tabla c";
    $this->db->query($sql);
    return $this->db->resultSet();
  }

  public function obtenerClientePorCedula($cedula)
  {
    $sql = "SELECT * FROM $this->tabla WHERE cedula = :cedula";
    $this->db->query($sql);
    $this->db->bind(':cedula', $cedula);
    return $this->db->single();
  }

  public function buscarClientes($cedula = null, $nombre = null)
  {
    $sql = "SELECT * FROM $this->tabla WHERE 1=1";

    if ($cedula) {
      $sql .= ' AND cedula LIKE :cedula';
    }

    if ($nombre) {
      $sql .= " AND CONCAT(nombre, ' ', apellidos) LIKE :nombre";
    }

    $this->db->query($sql);

    if ($cedula) {
      $this->db->bind(':cedula', '%' . $cedula . '%');
    }

    if ($nombre) {
      $this->db->bind(':nombre', '%' . $nombre . '%');
    }

    $this->db->execute();
    return $this->db->resultSet();
  }

  public function crearCliente($clienteData)
  {
    // Crear una cadena de campos para SQL
    $camposString = implode(', ', $this->campos);

    // Crear una cadena de marcadores de posición para los valores
    $placeholders = implode(', ', array_map(function ($campo) {
      return ':' . $campo;
    }, $this->campos));

    $sql = "INSERT INTO $this->tabla ($camposString) VALUES ($placeholders)";

    $this->db->query($sql);

    foreach ($this->campos as $campo) {
      if (array_key_exists($campo, $clienteData)) {
        $this->db->bind(':' . $campo, $clienteData[$campo]);
      }
    }

    $this->db->execute();

    return $this->db->lastInsertId();
  }

  public function actualizarCliente($clienteData)
  {
    $sql = "UPDATE clientes SET cedula = :cedula, nombre = :nombre, apellidos = :apellidos, direccion = :direccion, barrio = :barrio, telefono = :telefono, email = :email WHERE id_cliente = :id_cliente";
    
    $this->db->query($sql);
    $this->db->bind(':cedula', $clienteData['cedula']);
    $this->db->bind(':nombre', $clienteData['nombre']);
    $this->db->bind(':apellidos', $clienteData['apellidos']);
    $this->db->bind(':direccion', $clienteData['direccion']);
    $this->db->bind(':barrio', $clienteData['barrio']);
    $this->db->bind(':telefono', $clienteData['telefono']);
    $this->db->bind(':email', $clienteData['email']);
    $this->db->bind(':id_cliente', $clienteData['id_cliente']);
    
    return $this->db->execute();
  }

  public function actualizarEstado($clienteData)
  {
    $sql =
      'UPDATE clientes SET estado = :estado WHERE id_cliente = :id_cliente';
    $this->db->query($sql);
    $this->db->bind(':estado', $clienteData['estado']);
    $this->db->bind(':id_cliente', $clienteData['id_cliente']);
    $this->db->execute();
    return $this->db->rowCount() > 0 ? true : false;
  }

  // *************************** */

  public function obtenerVentasCliente($idCliente)
  {
    // Consulta para obtener las ventas del cliente para los años 2023 y 2024
    $sql = 'SELECT YEAR(registro) as anio, 
                   SUM(total) - (SUM(descuento) + SUM(devolucion)) as total 
            FROM ventas 
            WHERE id_cliente = :id_cliente 
            AND (YEAR(registro) >= 2023)
            GROUP BY YEAR(registro)';

    $this->db->query($sql);
    $this->db->bind(':id_cliente', $idCliente);
    return $this->db->resultSet();
  }

  public function obtenerOtrasEstadisticas($idCliente)
  {
    // Consulta para obtener otras estadísticas del cliente
    $sql = 'SELECT MAX(registro) as ultima_compra, 
                 SUM(total) - (SUM(descuento) + SUM(devolucion)) as total_comprado 
          FROM ventas 
          WHERE id_cliente = :id_cliente
          AND YEAR(registro) >= 2023';

    $this->db->query($sql);
    $this->db->bind(':id_cliente', $idCliente);
    return $this->db->single();
  }
}
