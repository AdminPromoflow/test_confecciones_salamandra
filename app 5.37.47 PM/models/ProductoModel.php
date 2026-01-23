<?php
defined("BASEPATH") or exit("No direct script access allowed");

require_once SYSTEM_PATH . "PulseDatabase.php";

/**
 * Clase Producto Model
 */
class ProductoModel
{
  private $db;
  private $tabla = 'productos';

  public function __construct()
  {
    $this->db = new PulseDatabase();
  }

  // Esta función se ejecuta desde el controlador Productos
  public function obtenerProductos()
  {
    $sql = "SELECT p.id_producto, p.barras, p.codigo, p.nombre AS nombre_producto, p.descripcion, p.talla, p.id_categoria, p.id_subcategoria, p.id_institucion, p.id_proveedor, p.costo, p.precio, p.estado, p.registro, c.nombre AS nombre_categoria, sc.nombre AS nombre_subcategoria, i.nombre AS nombre_institucion, pv.nombre AS nombre_proveedor, COALESCE(SUM(ip.stock), 0) AS cantidad_total
      FROM $this->tabla p
      LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
      LEFT JOIN subcategorias sc ON p.id_subcategoria = sc.id_subcategoria
      LEFT JOIN instituciones i ON p.id_institucion = i.id_institucion
      LEFT JOIN proveedores pv ON p.id_proveedor = pv.id_proveedor
      LEFT JOIN inventario_productos ip ON p.id_producto = ip.id_producto
      GROUP BY p.id_producto";

    $this->db->query($sql);
    $query = $this->db->resultSet();

    if ($this->db->error()) {
      throw new PulseErrorHandler('Error en la consulta: ' . $this->db->error());
    }

    return $this->db->rowCount() > 0 ? $query : false;
  }

  

  public function obtenerProductosAcivos()
  {
    $sql = "SELECT p.id_producto, p.barras, p.codigo, p.nombre AS nombre_producto, p.descripcion, p.talla, p.id_categoria, p.id_subcategoria, p.id_institucion, p.id_proveedor, p.costo, p.precio, p.estado, p.registro, c.nombre AS nombre_categoria, sc.nombre AS nombre_subcategoria, i.nombre AS nombre_institucion, pv.nombre AS nombre_proveedor, COALESCE(SUM(ip.stock), 0) AS cantidad_total
      FROM $this->tabla p
      LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
      LEFT JOIN subcategorias sc ON p.id_subcategoria = sc.id_subcategoria
      LEFT JOIN instituciones i ON p.id_institucion = i.id_institucion
      LEFT JOIN proveedores pv ON p.id_proveedor = pv.id_proveedor
      LEFT JOIN inventario_productos ip ON p.id_producto = ip.id_producto
      WHERE p.estado = :estado
      GROUP BY p.id_producto";

    $this->db->query($sql);
    $this->db->bind(":estado", 1);
    $query = $this->db->resultSet();

    if ($this->db->error()) {
      throw new PulseErrorHandler('Error en la consulta: ' . $this->db->error());
    }

    return $this->db->rowCount() > 0 ? $query : false;
  }

  public function actualizarProducto($productoData)
  {
    $campos = [
      "id_producto",
      "barras",
      "codigo",
      "nombre",
      "descripcion",
      "talla",
      'costo',
      "precio",
      "id_categoria",
      "id_subcategoria",
      "id_institucion",
      "id_proveedor",
      "estado",
    ];

    // Crear una cadena de actualización para SQL
    $updateString = implode(", ", array_map(function ($campo) {
      return $campo . " = :" . $campo;
    }, $campos));

    $sql = "UPDATE $this->tabla SET $updateString WHERE id_producto = :id_producto";

    $this->db->query($sql);

    foreach ($campos as $campo) {
      if (array_key_exists($campo, $productoData)) {
        $this->db->bind(":" . $campo, $productoData[$campo]);
      }
    }

    $this->db->execute();

    return $this->db->rowCount() > 0 ? true : false;
  }

  public function crearProducto($productoData)
  {
    $campos = [
      "barras",
      "codigo",
      "nombre",
      "descripcion",
      "talla",
      'costo',
      "precio",
      "id_categoria",
      "id_subcategoria",
      "id_institucion",
      "id_proveedor",
      "estado",
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
      if (array_key_exists($campo, $productoData)) {
        $this->db->bind(":" . $campo, $productoData[$campo]);
      }
    }

    $this->db->execute();

    return $this->db->rowCount() > 0 ? $this->db->lastInsertId() : false;
  }

  public function actualizarEstado($productoData)
  {
    $sql =
      "UPDATE productos SET estado = :estado WHERE id_producto = :id_producto";
    $this->db->query($sql);
    $this->db->bind(":estado", $productoData["estado"]);
    $this->db->bind(":id_producto", $productoData["id_producto"]);
    $this->db->execute();
    return $this->db->rowCount() > 0 ? true : false;
  }

  // *************************************
  // Ejecutor VentasController
  // *************************************
  // Esta funcion se ejecuta desde el controlador VentasController - método guardaVenta()
  public function obtenerProductoId($id_producto)
  {
    $sql = "SELECT p.*
      FROM productos p
      WHERE p.id_producto = :id_producto";

    $this->db->query($sql);
    $this->db->bind(":id_producto", $id_producto);
    $query = $this->db->single();

    return $this->db->rowCount() > 0 ? $query : false;
  }




  // Esta función obtiene las sucursales que posean inventario del producto: Desde el controlador Productos
  // public function getInventarioProducto($idProducto, $existencias)
  // {
  //   $sql = "SELECT
  //       p.id_producto,
  //       p.codigo,
  //       p.nombre,
  //       ip.stock,
  //       s.id_sucursal,
  //       s.nombre AS nombre_sucursal
  //     FROM productos p
  //     LEFT JOIN inventario_productos ip ON p.id_producto = ip.id_producto
  //     LEFT JOIN sucursales s ON ip.id_sucursal = s.id_sucursal
  //     WHERE p.id_producto = :id_producto";

  //   if ($existencias) {
  //     $sql .= " AND ip.stock > 0";
  //   }
  //   $this->db->query($sql);
  //   $this->db->bind(':id_producto', $idProducto);
  //   $query = $this->db->resultSet();
  //   return $this->db->rowCount() > 0 ? $query : false;
  // }

  // Esta funcion obtiene el detalle completo del producto: Desde el controlador Productos
  // public function getDetalleProducto($idProducto)
  // {
  //   $sql = "SELECT
  //       p.*
  //     FROM
  //       productos p
  //     WHERE
  //       p.id_producto = :id_producto";
  //   $this->db->query($sql);
  //   $this->db->bind(":id_producto", $idProducto);
  //   $query = $this->db->single();
  //   return $this->db->rowCount() > 0
  //     ? $query
  //     : "Error: No se encontraron datos.";
  // }

  // Esta funcion actualiza la edicion del producto: Desde el controlador Productos
  // public function updateProducto($idProducto, $productoData)
  // {
  //   // showTest($productoData);
  //   $campos = [
  //     "barras" => $productoData["barras"],
  //     "nombre" => $productoData["nombre"],
  //     "descripcion" => $productoData["descripcion"],
  //     "talla" => $productoData["talla"],
  //     // 'costo' => $productoData['costo'],
  //     "precio" => $productoData["precio"],
  //     "id_categoria" => $productoData["categoria"],
  //     "id_subcategoria" => $productoData["subcategoria"],
  //     "id_institucion" => $productoData["institucion"],
  //     "id_proveedor" => $productoData["proveedor"],
  //     "estado" => $productoData["estado"],
  //   ];

  //   // Crear una cadena de actualización para SQL
  //   $updateString = implode(
  //     ", ",
  //     array_map(function ($campo) {
  //       return $campo . " = :" . $campo;
  //     }, array_keys($campos))
  //   );

  //   $sql = "UPDATE productos SET $updateString WHERE id_producto = :idProducto";
  //   $this->db->query($sql);
  //   foreach ($campos as $campo => $valor) {
  //     $this->db->bind(":" . $campo, $valor);
  //   }
  //   $this->db->bind(":idProducto", $idProducto);
  //   $this->db->execute();
  //   return $this->db->rowCount() > 0 ? true : false;
  // }

  // 

  // public function buscarCodigoProducto($data)
  // {
  //   $sql = " SELECT p.id_producto, p.codigo, p.nombre, p.precio
  //         FROM productos p
  //         WHERE codigo = :codigo AND estado = :estado";
  //   $this->db->query($sql);
  //   $this->db->bind(":codigo", $data["codigo_producto"]);
  //   $this->db->bind(":estado", 1);
  //   $query = $this->db->single();
  //   return $this->db->rowCount() > 0
  //     ? $query
  //     : "Error: No se encontraron datos.";
  // }

  // public function getUserByStatus($data)
  // {
  //     $sql = "SELECT *
  //         FROM {$data['tabla']}
  //         WHERE id_usuario = :id_usuario AND estado = :estado";
  //     $this->db->query($sql);
  //     $this->db->bind(':id_usuario', $data['id_usuario']);
  //     $this->db->bind(':estado', $data['estado']);
  //     $query = $this->db->single();
  //     return $this->db->rowCount() > 0 ? $query : "Error: No se encontraron datos.";
  // }
  //
  // public function getSalesByClient()
  // {
  //     $sql = "SELECT c.*, v.*
  //         FROM clientes c
  //         INNER JOIN ventas v
  //         ON c.id_cliente = v.id_cliente
  //         WHERE c.id_cliente = :id_cliente";
  //     $this->db->query($sql);
  //     $this->db->bind(':id_cliente', 1);
  //     $query = $this->db->resultSet();
  //     return $this->db->rowCount() > 0 ? $query : "Error: No se encontraron datos.";
  // }
}
