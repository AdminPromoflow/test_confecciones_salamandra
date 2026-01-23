<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clase Dashboard Controller
 */
class DashboardController extends PulseController
{
    public function __construct()
    {
        parent::__construct();

        if (!adminAccess()) {
          $this->function->redirectTo('backend/auth');
        }
    }

    public function index()
    {
        $data = [
            'area' => '',
            'controller' => 'dashboard',
            'description' => 'resumenes generales',
            'content' => 'backview/dashboard.php',
        ];

        $this->view('backview/template/index', $data);
    }
}
