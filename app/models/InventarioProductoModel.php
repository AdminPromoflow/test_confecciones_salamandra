<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once SYSTEM_PATH . 'PulseDatabase.php';

/**
 * Clase InventarioProducto Model
 */
class InventarioProductoModel
{
  private $db;
  private $tabla = 'inventario_productos';

  public function __construct()
  {
    $this->db = new PulseDatabase;
  }

  public function obtenerStockByProducto($id_producto)
  {
    $sql = "SELECT ip.stock, s.nombre AS nombre_sucursal, p.codigo, p.nombre AS nombre_producto
      FROM $this->tabla ip
      INNER JOIN sucursales s ON ip.id_sucursal = s.id_sucursal
      INNER JOIN productos p ON ip.id_producto = p.id_producto
      WHERE ip.id_producto = :id_producto";

    $this->db->query($sql);
    $this->db->bind(':id_producto', $id_producto);
    $query = $this->db->resultSet();
    return $this->db->rowCount() > 0 ? $query : false;
  }

  public function obtenerStockBySucursal($id_sucursal)
  {
    $sql = "SELECT ip.stock, s.nombre AS nombre_sucursal, p.codigo, p.nombre AS nombre_producto, p.id_producto, p.precio, p.talla
      FROM $this->tabla ip
      INNER JOIN sucursales s ON ip.id_sucursal = s.id_sucursal
      INNER JOIN productos p ON ip.id_producto = p.id_producto
      WHERE ip.id_sucursal = :id_sucursal AND p.estado = 1";

    $this->db->query($sql);
    $this->db->bind(':id_sucursal', $id_sucursal);
    $query = $this->db->resultSet();
    return $this->db->rowCount() > 0 ? $query : false;
  }


  public function productoEnSucursal($id_producto, $id_sucursal, $stock = null)
  {
    // Realizar la consulta para verificar si el producto está en la sucursal
    $sql = "SELECT stock FROM $this->tabla
              WHERE id_producto = :id_producto AND id_sucursal = :id_sucursal";

    $this->db->query($sql);
    $this->db->bind(':id_producto', $id_producto);
    $this->db->bind(':id_sucursal', $id_sucursal);

    $resultado = $this->db->single();

    if ($resultado) {
      // Si se proporciona la cantidad, verificar si hay suficiente stock
      if ($stock !== null) {
        // Verificar si hay suficiente stock
        return $resultado->stock >= $stock;
      } else {
        // Si no se proporciona la cantidad, simplemente verificar la existencia del producto en la sucursal
        return true;
      }
    } else {
      // Si no se encuentra el producto, devolver false
      return null;
    }
  }

  // *************************************
  // Ejecutor TransferenciasController
  // *************************************

  // Esta función se ejecuta desde el controlador TransferenciasController
  public function registrarInventario($data)
  {
    // Insertamos el producto al inventario
    $campos = [
      'id_producto' => $data['producto'],
      'id_sucursal' => $data['sucursal_destino'],
      'stock' => $data['cantidad'],
      'stock_min' => 5,
      'registro' => date('Y-m-d H:i:s')
    ];

    $columnas = implode(', ', array_keys($campos));
    // Crear una cadena de valores para SQL
    $valores = ':' . implode(', :', array_keys($campos));

    $sql = "INSERT INTO $this->tabla ($columnas) VALUES ($valores)";
    $this->db->query($sql);

    foreach ($campos as $campo => $valor) {
      $this->db->bind(':' . $campo, $valor);
    }

    $this->db->execute();

    if ($this->db->rowCount() > 0) {
      $actualizaciones = "stock =
          CASE
              WHEN id_sucursal = :sucursal_origen THEN stock - :cantidad
              ELSE stock
          END";
      // Actualizamos el stock en la sucursal origen
      $sql = "UPDATE $this->tabla
                SET $actualizaciones
                WHERE id_producto = :id_producto
                AND  id_sucursal = :sucursal_origen";
      $this->db->query($sql);

      $this->db->bind(':id_producto', $data['producto']);
      $this->db->bind(':sucursal_origen', $data['sucursal_origen']);
      $this->db->bind(':cantidad', $data['cantidad']);

      $this->db->execute();

      return $this->db->rowCount() > 0 ? true : false;
    } else {
      return false;
    }
  }

  // Esta función se ejecuta desde el controlador TransferenciasController
  public function actualizarInventario($data)
  {
    $campos = [
      'cantidad' => $data['cantidad'],
    ];

    $sql = "UPDATE $this->tabla
              SET stock =
                  CASE
                      WHEN id_sucursal = :sucursal_destino THEN stock + :cantidad
                      WHEN id_sucursal = :sucursal_origen THEN stock - :cantidad
                      ELSE stock
                  END
              WHERE id_producto = :id_producto
                AND (id_sucursal = :sucursal_origen OR id_sucursal = :sucursal_destino)";

    $this->db->query($sql);

    foreach ($campos as $campo => $valor) {
      $this->db->bind(':' . $campo, $valor);
    }

    $this->db->bind(':id_producto', $data['producto']);
    $this->db->bind(':sucursal_origen', $data['sucursal_origen']);
    $this->db->bind(':sucursal_destino', $data['sucursal_destino']);

    $this->db->execute();

    return $this->db->rowCount() > 0 ? true : false;
  }

  // *************************************
  // Ejecutor PuntoController
  // *************************************

  // Esta función se ejecuta desde el controlador PuntoController
  // Esta función tambien se ejecuta desde el controlador ProduccionesController método actualizarEstado()
  public function buscarProducto($id_producto, $id_sucursal)
  {
    $sql = "SELECT ip.stock
        FROM $this->tabla ip
        WHERE id_producto = :id_producto AND id_sucursal = :id_sucursal";
    $this->db->query($sql);
    $this->db->bind(':id_producto', $id_producto);
    $this->db->bind(':id_sucursal', $id_sucursal);
    $query = $this->db->single();
    return $this->db->rowCount() > 0 ? $query : false;
  }

  // Esta funcion obtiene todos los productos segun el id de la sucursal: Desde el controlador PuntoController
  public function obtenerInventarioSucursal($id_sucursal, $id_producto = null)
  {
    $sql = "SELECT ip.id_producto, ip.id_sucursal, ip.stock, p.barras, p.codigo, p.nombre, p.precio, p.talla
          FROM $this->tabla ip
          RIGHT JOIN productos p ON ip.id_producto = p.id_producto";

    if ($id_producto === null) {
      $sql .= " WHERE ip.id_sucursal = :id_sucursal";
      $this->db->query($sql);
      $this->db->bind(':id_sucursal', $id_sucursal);
      $query = $this->db->resultSet();
    } else {
      $sql .= " WHERE ip.id_sucursal = :id_sucursal AND ip.id_producto = :id_producto";
      $this->db->query($sql);
      $this->db->bind(':id_sucursal', $id_sucursal);
      $this->db->bind(':id_producto', $id_producto);
      $query = $this->db->single();
    }

    return $this->db->rowCount() > 0 ? $query : "Error: No se encontraron datos.";
  }

  // *************************************
  // Ejecutor CarritoController
  // *************************************

  // Esta función se ejecuta desde el controlador CarritoController
  public function restamosInventario($id_producto, $id_sucursal)
  {
    $sql = "UPDATE $this->tabla
      SET stock = stock - 1
      WHERE id_producto = :id_producto AND id_sucursal = :id_sucursal";
    $this->db->query($sql);

    $this->db->bind(':id_producto', $id_producto);
    $this->db->bind(':id_sucursal', $id_sucursal);

    $this->db->execute();

    return $this->db->rowCount() > 0 ? true : false;
  }

  // Esta función se ejecuta desde el controlador CarritoController
  // Esta función se ejecuta desde el controlador ProduccionesController - método actualizarEstado()
  public function sumamosInventario($id_producto, $id_sucursal, $cantidad = 1)
  {
    $sql = "UPDATE $this->tabla
      SET stock = stock + :cantidad
      WHERE id_producto = :id_producto AND id_sucursal = :id_sucursal";
    $this->db->query($sql);
    $this->db->bind(':id_producto', $id_producto);
    $this->db->bind(':id_sucursal', $id_sucursal);
    $this->db->bind(':cantidad', $cantidad);
    $this->db->execute();

    return $this->db->rowCount() > 0 ? true : false;
  }

  // *************************************
  // Ejecutor ProduccionesController
  // *************************************

  // Esa función se ejecuta desde el controlador ProduccionesController
  public function actualizarInventarioXproduccion($accion, $data, $id_sucursal)
  {
    if ($accion == 'registrar') {
      $sql = "INSERT INTO $this->tabla (id_producto, id_sucursal, stock, stock_min, registro) VALUES (:id_producto, :id_sucursal, :stock, :stock_min, :registro)";
      $this->db->query($sql);
      $this->db->bind(':id_producto', $data->id_producto);
      $this->db->bind(':id_sucursal', $id_sucursal);
      $this->db->bind(':stock', $data->cantidad);
      $this->db->bind(':stock_min', 5);
      $this->db->bind(':registro', date('Y-m-d H:i:s'));
      $this->db->execute();
    } else {
      $sql = "UPDATE $this->tabla
        SET stock = stock + 1
        WHERE id_producto = :id_producto AND id_sucursal = :id_sucursal";
      $this->db->query($sql);
      $this->db->bind(':id_producto', $data->id_producto);
      $this->db->bind(':id_sucursal', $id_sucursal);
      $this->db->execute();
    }
  }

  // ------------------------------

  public function aumentarStock($id_producto, $id_sucursal, $cantidad)
  {
    $sql = "UPDATE inventario_productos ip
      -- SET ip.stock = :cantidad
      SET ip.stock = ip.stock + :cantidad
      WHERE ip.id_producto = :id_producto AND ip.id_sucursal = :id_sucursal";

    $this->db->query($sql);
    $this->db->bind(':id_producto', $id_producto);
    $this->db->bind(':id_sucursal', $id_sucursal);
    $this->db->bind(':cantidad', $cantidad);
    $this->db->execute();
  }
}
