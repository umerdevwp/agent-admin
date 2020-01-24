<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Okta\Generated\Sessions\Session;
use Src\Services\OktaApiService as Okta;

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Admin_model");
        validAdminCheck();
        restrictForAdmin();
    }
    public function index()
    {

        $data['title'] = 'Administrator';
        $data['admins'] = $this->Admin_model->getAdmins();
        $this->load->view("header");
        $this->load->view("admins", $data);
        $this->load->view("footer");
    }

    public function create()
    {
        $result = array();
        $data = array(
            'first_name' => $this->input->post("first_name"),
            'last_name' => $this->input->post("last_name"),
            'email' => $this->input->post("email"),
            'zoho_id' => '999999999'
        );
        $obj = $this->Admin_model->createAdmin($data);
        if (!empty($obj)) {
            $returnData['admins'] = $this->Admin_model->getOneAdmins($obj);
        }
        if (!empty($returnData['admins'])) {
            $result['response'] = array('success');
            $result['markup'] = $this->load->view("listing/admin-listing", $returnData, TRUE);
            echo json_encode($result);
        } else{
            $result['response'] = array('error');
            echo json_encode($result);
        }
    }


    public function info()
    {
        debug($this->session->user);
    }
}
