<?php
// use Src\Services\OktaApiService as Okta;
//header('Access-Control-Allow-Origin: *');
//header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
//header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");


use chriskacerguis\RestServer\RestController;

class User_api extends RestController
{
    public function __construct($config = 'rest')
    {
        parent::__construct($config);
        $this->load->model('Admin_model');
        $this->load->model('Entity_model');
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
    }

    public function role_get()
    {
//
        $this->response([ 'status' => true, 'eid' => $_SESSION['eid']], 200);

    }


}
