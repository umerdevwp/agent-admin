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
//        $id = $this->get('id');
        $id = $_SESSION["eid"];

        $this->load->model('Entity_model');
        $checkSuperUser = $this->Entity_model->checkRole($id);

        if (!empty($checkSuperUser)) {
            foreach ($checkSuperUser as $value) {
                if (!empty($value->parent_account)) {
                    $this->response(['role' => 'child'], 200);
                } else {
                    $this->response(['role' => 'parent'], 200);

                }
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'User does not exist'
            ], 401);
        }

    }


}
