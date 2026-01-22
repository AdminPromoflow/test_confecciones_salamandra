<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clase Home Controller
 */
class HomeController extends PulseController
{

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->view('frontview/index');
    }
}
