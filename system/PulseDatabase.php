<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase PulseDatabase
 */
class PulseDatabase
{
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $charset = DB_CHAR;

    private $dbh, $stmt, $error;

    public function __construct()
    {
        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8';
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        // Create PDO instance
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            // echo 'connecton ok';
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            throw new PulseErrorHandler($this->error);
        }
    }

    // prepare statement with query
    public function query($sql)
    {
        $this->stmt = $this->dbh->prepare($sql);
    }

    // Bind Values
    public function bind($param, $value, $type = null)
    {
        if ($this->stmt) {
            if (is_null($type)) {
                switch (true) {
                    case is_int($value):
                        $type = PDO::PARAM_INT;
                        break;
                    case is_bool($value):
                        $type = PDO::PARAM_BOOL;
                        break;
                    case is_null($value):
                        $type = PDO::PARAM_NULL;
                        break;
                    default:
                        $type = PDO::PARAM_STR;
                }
            }

            $this->stmt->bindValue($param, $value, $type);
        } else {
            // Manejar el caso donde $this->stmt es null (lanzar una excepción, registrar un error, etc.)
            // Por ejemplo, puedes agregar un mensaje de error y luego lanzar una excepción.
            throw new Exception("El objeto de instrucción (stmt) es nulo.");
        }
    }

    // Execute the prepared statement
    public function execute()
    {
        return $this->stmt->execute();
    }

    // Get result set as array of objects
    public function resultSet()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get single record as object
    public function single()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // Get row count
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    // Get the last inserted ID
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    // Get the last error message
    public function error()
    {
        return $this->error;
    }
}
