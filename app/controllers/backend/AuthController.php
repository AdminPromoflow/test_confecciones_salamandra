<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase Dashboard Controller
 */
class AuthController extends PulseController
{
    private $usuarioModel;
    private $sucursalModel;

    function __construct()
    {
        parent::__construct();
        $this->usuarioModel = $this->model('UsuarioModel');
        $this->sucursalModel = $this->model('SucursalModel');
    }

    function index()
    {
        $data = [
            'sucursales' => $this->sucursalModel->obtenerSucursales()
        ];

        $this->view('backview/login', $data);
    }

    function process()
    {
        $postData = $this->function->method('POST', TRUE);
        $userData = $this->usuarioModel->obtenerUsuario($postData);

        if (!$userData) {
            $this->session->setFlashMessage('danger', 'Usuario y/o Contraseña invalidos, verifique e intente nuevamente.');
            $this->function->redirectTo('backend/auth');
        }

        if (!$this->function->verifyPass($postData['password'], $userData->password)) {
            $this->session->setFlashMessage('danger', 'Usuario y/o Contraseña invalidos, verifique e intente nuevamente.');
            $this->function->redirectTo('backend/auth');
        }

        if ($userData->estado != 1) {
            $this->session->setFlashMessage('danger', 'Usuario inactivo, contactese con el administrador');
            $this->function->redirectTo('backend/auth');
        }

        $sucursalData = $this->sucursalModel->obtenerSucursalId($postData['idSucursal']);

        if ($userData->permiso != $sucursalData->permiso) {
            $this->session->setFlashMessage('danger', 'Este usuario no tiene el permiso para ingresar a esta sucursal.');
            $this->function->redirectTo('backend/auth');
        }

        $userSession = [
            'usuarioId' => $userData->id_usuario,
            'usuarioNombre' => $userData->nombre . ' ' . $userData->apellidos,
            'usuarioUser' => $userData->usuario,
            'usuarioRol' => $userData->rol,
            'usuarioPermiso' => $userData->permiso,
            'usuarioSucursal' => $sucursalData->id_sucursal,
            'isLoggedIn' => 1
        ];

        $this->session->setUserData('userSession', $userSession);

        switch ($userData->permiso):
            case 1:
                $this->session->setFlashMessage('success', 'Bienvenido a su panel administrativo.');
                $controller = 'dashboard';
                break;
            case 2:
                $this->session->setFlashMessage('success', 'Bienvenido a su panel punto de venta.');
                $controller = 'punto';
                break;
            case 3:
                $this->session->setFlashMessage('success', 'Bienvenido a su panel de producción.');
                $controller = 'producciones';
                break;
        endswitch;

        $this->function->redirectTo('backend/' . $controller);
    }

    public function logout()
    {
        // Unset the user data from the session
        $this->session->unsetUserData('userSession');
        // Destroy the session
        // $this->session->sessionDestroy();
        $this->session->setFlashMessage('success', 'Sesión cerrada correctamente, vuelva pronto.');
        $this->function->redirectTo('backend/auth');
    }
}
