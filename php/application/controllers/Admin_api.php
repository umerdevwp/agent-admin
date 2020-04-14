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
        $zoho_id = $this->get('zoho_id');
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
}
