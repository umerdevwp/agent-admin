<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Src\Services\OktaApiService as Okta;

class Attachments extends CI_Controller
{
    public function index()
    {
        if(!isSessionValid("Attachments")) redirectSession();

        $this->load->model("attachments_model");
        $this->load->model("accounts_model");
        
        $id = $this->session->user['zohoId'];

        if($this->session->user['zohoId'] == getenv("SUPER_USER")){
            $result = $this->accounts_model->getAll();
        } else {
            // fetch all childrens ids, to later fetch
            $result = $this->accounts_model->loadChildAccounts($id,"id");
        }

        // create comma seprated ids from result
        $arCommaIds = array();
        foreach($result as $v)
        {
            $arCommaIds[] = $v->id;
        }
        // add parent id as well
        $arCommaIds[] = $id;

        $data['attachments'] = $this->attachments_model->getAllFromEntityList($arCommaIds);
        
        $this->load->view("header");
        $this->load->view("attachments",$data);
        $this->load->view("footer");
    }
}