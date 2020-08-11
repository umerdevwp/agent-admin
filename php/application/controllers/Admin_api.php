<?php
// use Src\Services\OktaApiService as Okta;
//header('Access-Control-Allow-Origin: *');
//header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
//header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");


use chriskacerguis\RestServer\RestController;

class Admin_api extends RestController
{
    public function __construct($config = 'rest')
    {
        parent::__construct($config);
        $this->load->model('Admin_model');

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
    }



    public function checkadmin_get()
    {

        $email = $this->get('email');
        $zoho_id = $this->get('eid');
        //check if the parameters are not empty
        if (empty($email) or empty($zoho_id)) {
            $this->response([
                'status' => false,
                'message' => 'zoho_id or email of the user is missing.'
            ], 404);
        }
        //check of the user is valid admin
        $email = $this->get('email');
        $checkSuperUser = $this->Admin_model->checkAdminExist($email);
        if (!empty($checkSuperUser)) {
            //fetching the list of all entities
            $this->response(true, 200);
        } else {
            $this->response(false, 200);

        }
    }


    public function adminlist_get()
    {

        $email = $this->get('email');
        $zoho_id = $this->get('eid');
        //check if the parameters are not empty
        if (empty($email) or empty($zoho_id)) {
            $this->response([
                'status' => false,
                'message' => 'zoho_id or email of the user is missing.'
            ], 404);
        }
        //check of the user is valid admin
        $email = $this->get('email');
        $checkSuperUser = $this->Admin_model->checkAdminExist($email);
        if (!empty($checkSuperUser)) {
            $response = $this->Admin_model->getAdmins();
            $this->response([
                'result' => $response
            ], 200);
        } else {
            $this->response(false, 200);
        }
    }

    public function create_post()
    {
        $result = array();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('first_name', 'First Name', 'required|regex_match[/[a-zA-Z\s]+/]', ["regex_match" => "Only alphabets and spaces allowed."]);
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|regex_match[/[a-zA-Z\s]+/]', ["regex_match" => "Only alphabets and spaces allowed."]);
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[admins.email]');
        if ($this->form_validation->run() == FALSE) {
            $this->response([
                'response' => ['message' => 'error', 'results' => $this->form_validation->error_array()]
            ], 200);

        }

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
            $this->response([
                'result' => $result
            ], 200);
        } else {
            $result['response'] = array('error');
            $this->response([
                'result' => $result
            ], 200);
        }
    }



}
