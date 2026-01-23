<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clase HomeController
 */
class HomeController extends PulseController
{
    private $user_model;

    public function __construct()
    {
        parent::__construct();
        $this->user_model = $this->model('UserModel');
    }

    public function index()
    {
        $model_data = [
            'tabla' => 'usuarios',
            'id_usuario' => 1,
            'estado' => 'Activo',
            'id_cliente' => 1
        ];

        $data['users'] = $this->user_model->getUsers($model_data['tabla']);
        $data['user_id'] = $this->user_model->getUserById($model_data);
        $data['user_status'] = $this->user_model->getUserByStatus($model_data);
        $data['sales_client'] = $this->user_model->getSalesByClient($model_data);

        $this->view('backview/index', $data);
    }
}
