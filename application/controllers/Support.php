<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Support extends CI_Controller
{
    function index()
    {
        if (!file_exists(APPPATH . 'views/support.php')) {
            // Whoops, we don't have a page for that!
            show_404();
        }
        $this->load->view('support');
    }
}
