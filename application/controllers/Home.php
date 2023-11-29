<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('admin/Login_model');
    }

    public function index() {
        redirect(base_url() . 'admin');
    }

}
