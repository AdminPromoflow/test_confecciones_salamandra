<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase Programaciones Controller
 */
class ProgramacionesController extends PulseController
{
    private $programacionModel;
    private $estados = [];

    public function __construct()
    {
        parent::__construct();

        if (!OperatorAccess()) {
            $this->function->redirectTo('backend/auth');
        }

        $this->programacionModel = $this->model('ProgramacionModel');
    }

    public function obtenerProgramacion()
    {
        $estado = 1;
        $programacion = $this->programacionModel->obtenerProgramacion($estado);
        $this->function->jsonResponse('respuesta', $programacion);
    }

    public function actualizarEstado()
    {
        $postData = $this->function->method('POST', true);
        $response = $this->programacionModel->actualizarProduccion($postData['id_programacion'], $postData['nuevoEstado']);

        $this->function->jsonResponse('respuesta', $response);
    }

    public function crearProgramacion()
    {
        $postData = $this->function->method('POST', true);
        $response = $this->programacionModel->guardarProgramacion($postData);

        $this->function->jsonResponse('respuesta', $response);
    }
}
