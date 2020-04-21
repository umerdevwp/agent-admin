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
        $email = $this->get('email');
        $checkSuperUser = $this->Admin_model->checkAdminExist($email);
        if (!empty($checkSuperUser)) {
            $this->Admin_model->updateAdmin($email);
            $this->response(['isAdmin' => true], 200);
        } else {
            $this->response(['isAdmin' => false], 200);
        }
    }


    public function permissions_get()
    {

    }


    public function userdata_get()
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
            $entity_list = $this->Entity_model->getAll();
            $this->response($entity_list, 200);
        }

        //check if zoho account exits in the system
        $zoho_existance_system = $this->Entity_model->loadAccount($zoho_id);

        if ($zoho_existance_system['type'] == 'ok') {
            //fetch children for the parent zoho id
            $children = $this->Entity_model->loadChildAccounts($zoho_id);

            if ($children['type'] == 'ok' and !empty($children['results'])) {
                $this->response($children['results'], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => true,
                    'child_count' => 0,
                    'message' => 'This account does not have any child account'
                ], RESTController::HTTP_OK);

            }
        } else {
            //if account does not exist in the agentAdmin
            $this->response([
                'status' => false,
                'message' => 'Account does not exist'
            ], 404);
        }
    }



    public function index_options() {
        return $this->response(NULL, RESTController::HTTP_OK);
    }
}
