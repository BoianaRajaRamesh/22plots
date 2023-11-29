<?php

header("access-control-allow-origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
        $this->load->helper('form');
        $this->load->library('session');
        $this->load->library('form_validation');

        $this->load->helper("file");
        define('FILE_PATH', base_url() . "/uploads/");
        define('DEFAULT_IMAGE', base_url() . "/uploads/default_image.png");
    }

    function common_pagination($url, $total_count, $per_page)
    {
        $config['base_url'] = $url;
        $config['total_rows'] = $total_count;
        $config['per_page'] = $per_page;
        $config['page_query_string'] = true;
        $config['num_links'] = 10;
        $config['full_tag_open'] = "<ul class='pagination'>";
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';


        $config['prev_link'] = '<span aria-hidden="true">&laquo;</span>';
        $config['prev_tag_open'] = '<li class="button grey">';
        $config['prev_tag_close'] = '</li>';


        $config['next_link'] = '<span aria-hidden="true">&raquo;</span>';
        $config['next_tag_open'] = '<li class="button grey">';
        $config['next_tag_close'] = '</li>';

        //        $start = ($this->input->get_post('per_page')) ? $this->input->get_post('per_page') : 0;
        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();
    }
}
