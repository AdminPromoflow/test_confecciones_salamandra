<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase PulseFunctions
 */
class PulseFunctions
{
	public function redirectTo($target_page)
	{
		// Asegúrate de que la URL sea segura y válida
		$url_page = URL_PATH . $target_page;
		if (filter_var($url_page, FILTER_VALIDATE_URL) !== false) {
			// Utiliza una redirección 301 (permanente) para redirecciones amigables para SEO
			header('HTTP/1.1 301 Movido Permanentemente');
			header('Location: ' . $url_page);
			exit();
		} else {
			throw new PulseErrorHandler("Error - 404: La vista que esta solicitando no existe");
		}
	}

	public function renderStyles($styles)
	{
		if (isset($styles)) {
			foreach ($styles as $style) {
				echo '<link rel="stylesheet" href="' . URL_PATH . $style . '">';
			}
		}
	}

	public function renderScripts($scripts)
	{
		if (isset($scripts)) {
			foreach ($scripts as $script) {
				echo '<script src="' . URL_PATH . $script . '"></script>';
			}
		}
	}

	function jsonResponse($responseName, $data)
	{
		// Combinar el nombre de la respuesta y los datos en un solo array asociativo
		$response = [
			'success' => $data ? true : false,
			$responseName => $data
		];

		// Configurar el encabezado para indicar que la respuesta es JSON
		header('Content-Type: application/json');

		// Codificar la respuesta JSON y manejar errores
		$jsonEncoded = json_encode($response);
		if ($jsonEncoded === false) {
			http_response_code(500);
			// Manejar el error de codificación JSON
			$error = ['error' => 'Error al codificar la respuesta JSON'];
			echo json_encode($error);
		} else {
			// Imprimir la respuesta JSON y salir
			echo $jsonEncoded;
		}
		die();
	}

	public function encryptPass($data_value)
	{
		$pass = password_hash($data_value, PASSWORD_DEFAULT);
		return $pass;
	}

	public function verifyPass($origin_pass, $encryted_pass)
	{
		$verifed_pass = password_verify($origin_pass, $encryted_pass);
		return $verifed_pass;
	}
	
	public function method($method_data, $sanitize = FALSE)
	{
		$method_data = mb_strtoupper($method_data);
		if ($_SERVER['REQUEST_METHOD'] !== $method_data) {
			return FALSE;
		}

		$result = $_POST;

		if ($sanitize === TRUE) {
			$result = array_map('htmlspecialchars', $result);
		}

		// Si el resultado es un array con un solo elemento y ese elemento es un array,
		// devolvemos directamente ese elemento interno
		if (count($result) === 1 && isset($result[0]) && is_array($result[0])) {
			return $result[0];
		}

		return $result;
	}
	
	public function getJsonData($sanitize = FALSE)
    {
        // Verificar que el Content-Type sea application/json
        $contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';
        if (stripos($contentType, 'application/json') === false) {
            return FALSE;
        }

        try {
            // Obtener el contenido JSON del body
            $jsonInput = file_get_contents('php://input');
            if (empty($jsonInput)) {
                return FALSE;
            }

            // Decodificar el JSON
            $data = json_decode($jsonInput, TRUE);
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                return FALSE;
            }

            // Sanitizar los datos si es requerido
            if ($sanitize === TRUE) {
                $data = $this->sanitizeData($data);
            }

            return $data;
        } catch (Exception $e) {
            return FALSE;
        }
    }
    
    private function sanitizeData($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeData'], $data);
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

// 	public function method($method_data, $sanitize = FALSE)
// 	{
// 		$method_data = mb_strtoupper($method_data);
// 		if ($_SERVER['REQUEST_METHOD'] !== $method_data) {
// 			return FALSE;
// 		}

// 		if ($_SERVER['REQUEST_METHOD'] === $method_data && $sanitize === FALSE) {
// 			return array($_POST);
// 		}
// 		if ($_SERVER['REQUEST_METHOD'] === $method_data && $sanitize === TRUE) {
// 			return array_map('htmlspecialchars', $_POST);
// 		}
// 	}
}
