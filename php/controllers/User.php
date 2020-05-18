<?php

use Src\Services\OktaApiService as Okta;

class User extends CI_Controller
{
    protected $okta;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url_helper');
        $this->load->model('users_model');
        $this->okta = new Okta;
    }

    public function login()
    {
        if (! isset($this->session->username)) {
            $state = bin2hex(random_bytes(5));
            $authorizeUrl = $this->okta->buildAuthorizeUrl($state);
            $this->session->state = $state;
            redirect($authorizeUrl, 'refresh');
        }

        redirect('/');
    }

    public function callback()
    {
        if (isset($_GET['code'])) {
            $result = $this->okta->authorizeUser($this->session->state);
			
            if (isset($result['error'])) {
                echo $result['errorMessage'];
                die();
            }
        }

        $userId = $this->users_model->find_or_create($result['username']);

        $this->session->userId = $userId;
        $this->session->username = $result['username'];
        redirect('/portal');
    }

    public function logout()
    {
        $this->session->userId = null;
        $this->session->username = null;
        redirect('/');
    }

    public function saveThemeAjax()
    {
        $string = $this->input->post("number");

        if(!isSessionValid("Theme_Update")) redirectSession();

        //echo $this->session->user["zohoId"];die;
        $this->load->model("Usersmeta_model");

        if(!empty($string))
        {
            $response = $this->Usersmeta_model->updateTheme($this->session->user["zohoId"],$string);
        } else {
            $response["error"] = "Field cannot be empty";
        }

        echo json_encode($response);
    }

    public function getThemeAjax($metaname="")
    {
        if(!isSessionValid("Theme_View")) redirectSession();

        $arAllowedMeta = array(
            "name"  =>  "personal_theme",
        );

        // if meta name in db
        if(array_key_exists($metaname,$arAllowedMeta))
        {
            $metaname = $arAllowedMeta[$metaname];
            //echo $this->session->user["zohoId"];die;
            $this->load->model("Usersmeta_model");
            $row = $this->Usersmeta_model->getMeta($this->session->user["zohoId"],$metaname,1);

        // requested meta do not exist
        } else {
            $row["id"] = 0;
        }

        if($row["id"]>0)
        {
            echo json_encode(["ok"=>$row[$metaname]]);
        } else {
            echo json_encode(['error'=>'Unable to find requested data.']);
        }
    }
}