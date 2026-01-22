<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UsuariosController extends PulseController
{
    private $usuarioModel;

    public function __construct()
    {
        parent::__construct();
        if (!adminAccess()) {
            $this->function->redirectTo('backend/auth');
        }
        $this->usuarioModel = $this->model('UsuarioModel');
    }

    public function index()
    {
        $data = [
            'area' => 'configuracion',
            'controller' => 'usuarios',
            'description' => 'Gestión de usuarios',
            'content' => 'backview/usuarios/index.php',
            'styles' => [
                'public/assets/plugins/data-tables/css/datatables.min.css',
                'public/assets/plugins/select2/css/select2.css'
            ],
            'scripts' => [
                'public/assets/plugins/data-tables/js/datatables.min.js',
                'public/assets/plugins/select2/js/select2.full.min.js',
                'public/assets/js/pages/datatables-custom.js',
                'public/assets/js/pages/select-custom.js',
                'public/customs/usuarios.js',
            ],
        ];

        $this->view('backview/template/index', $data);
    }

    public function obtenerUsuarios()
    {
        $usuarios = $this->usuarioModel->obtenerUsuarios();
        return $this->function->jsonResponse('respuesta', $usuarios);
    }

  public function obtenerCajeros()
  {
    $cajeros = $this->usuarioModel->obtenerCajeros();
    return $this->function->jsonResponse('respuesta', $cajeros);
  }

    public function guardarUsuario()
  {
      $postData = $this->function->method('POST', true);

      if (empty($postData['nombre']) || empty($postData['usuario']) || empty($postData['email'])) {
          return $this->function->jsonResponse('error', 'Faltan campos obligatorios.');
      }

      if (!empty($postData['id_usuario'])) {
          // Edición
          $usuarioExistente = $this->usuarioModel->obtenerUsuarioPorUsuario($postData['usuario']);
          if ($usuarioExistente && $usuarioExistente->id_usuario != $postData['id_usuario']) {
              return $this->function->jsonResponse('error', 'El nombre de usuario ya está en uso.');
          }

          $response = $this->usuarioModel->actualizarUsuario($postData);
          return $this->function->jsonResponse('respuesta', $response ? 'actualizado' : 'error');
      }

      // Creación
      $usuarioExistente = $this->usuarioModel->obtenerUsuarioPorUsuario($postData['usuario']);
      if ($usuarioExistente) {
          return $this->function->jsonResponse('error', 'El nombre de usuario ya está en uso.');
      }

      if (empty($postData['password'])) {
          return $this->function->jsonResponse('error', 'La contraseña es obligatoria.');
      }

      $postData['password'] = password_hash($postData['password'], PASSWORD_DEFAULT);
      $postData['registro'] = date('Y-m-d H:i:s');
      $postData['estado'] = 'activo';

      $id = $this->usuarioModel->crearUsuario($postData);
      return $this->function->jsonResponse('respuesta', $id ? 'creado' : 'error');
  }

    public function actualizarEstado()
    {
        $data = $this->function->method('POST', true);
        
        // Recibimos el estado ACTUAL (1 o 0 o 'activo'/'inactivo')
        $estadoActual = $data['estado'] ?? '1';
        
        // Convertir a booleano
        $esActivo = in_array($estadoActual, ['1', 1, 'activo'], true);
        
        // El nuevo estado es lo opuesto
        $nuevoEstado = $esActivo ? 0 : 1;

        $response = $this->usuarioModel->actualizarEstado([
            'id_usuario' => $data['id_usuario'],
            'estado' => $nuevoEstado
        ]);

        if ($response) {
            // Devolvemos el nuevo estado como 1 o 0
            $this->function->jsonResponse('respuesta', ['estado' => $nuevoEstado]);
        } else {
            $this->function->jsonResponse('error', 'No se pudo actualizar el estado.');
        }
    }

    public function cambiarPassword()
    {
        $postData = $this->function->method('POST', true);

        if (empty($postData['id_usuario']) || empty($postData['password'])) {
            return $this->function->jsonResponse('error', 'Datos incompletos.');
        }

        // Validar que el usuario exista
        $usuario = $this->usuarioModel->obtenerUsuarioById($postData['id_usuario']);
        if (!$usuario || is_string($usuario)) {
            return $this->function->jsonResponse('error', 'Usuario no encontrado.');
        }

        // ✅ Llamar al método correcto
        $resultado = $this->usuarioModel->actualizarPassword(
            $postData['id_usuario'],
            $postData['password']
        );

        if ($resultado) {
            return $this->function->jsonResponse('respuesta', 'Contraseña actualizada.');
        } else {
            return $this->function->jsonResponse('error', 'No se pudo actualizar la contraseña.');
        }
    }
}