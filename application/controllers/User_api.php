<?php
// use Src\Services\OktaApiService as Okta;
header('Access-Control-Allow-Origin: *');

use chriskacerguis\RestServer\RestController;

class User_api extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_model');
        $this->load->model('Accounts_model');
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
            $entity_list = $this->Accounts_model->getAll();
            $this->response([
                'isAdmin' => true,
                'count' => count($entity_list),
                'result' => $entity_list
            ], 200);
        }

        //check if zoho account exits in the system
        $zoho_existance_system = $this->Accounts_model->loadAccount($zoho_id);
        if ($zoho_existance_system['type'] == 'ok') {
            //fetch children for the parent zoho id
            $children = $this->Accounts_model->loadChildAccounts($zoho_id);

            if ($children['type'] == 'ok' and !empty($children['results'])) {
                $this->response([
                    'count' => count($children['results']),
                    'result' => $children['results']
                ], 200);
            } else {
                $this->response([
                    'status' => true,
                    'child_count' => 0,
                    'message' => 'This account does not have any child account'
                ], 200);

            }
        } else {
            //if account does not exist in the agentAdmin
            $this->response([
                'status' => false,
                'message' => 'Account does not exist'
            ], 404);
        }
    }
}
