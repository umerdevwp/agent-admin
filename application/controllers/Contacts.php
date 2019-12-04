<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Src\Services\OktaApiService as Okta;

class Contacts extends CI_Controller
{
    public function index()
    {
        if(!isSessionValid("Contacts")) redirectSession();

        $this->load->model("contacts_model");
        $this->load->model("accounts_model");
        
        $id = $this->session->user['zohoId'];

        // fetch all childrens ids, to later fetch
        $result = $this->accounts_model->loadChildAccounts($id,"id");

        // create comma seprated ids from result
        $arCommaIds = array();
        foreach($result as $v)
        {
            $arCommaIds[] = $v->id;
        }
        // add parent id as well
        $arCommaIds[] = $id;

        $data['contacts'] = $this->contacts_model->getAllFromEntityList($arCommaIds);
        
        $this->load->view("header");
        $this->load->view("contacts",$data);
        $this->load->view("footer");
    }
}