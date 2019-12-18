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

    public function addAjax()
    {
        //if(!isSessionValid("Contacts_Add")) redirectSession();

        $aResponse = [];
        $this->load->model("Smartystreets_model");
        
        $this->load->library('form_validation');

        $this->form_validation->set_rules('inputContactFirstName', 'First Name', 'required|regex_match[/[a-zA-Z\s]+/]',["regex_match"=>"Only alphabets and spaces allowed."]);
        $this->form_validation->set_rules('inputContactLastName', 'Last Name', 'required|regex_match[/[a-zA-Z\s]+/]',["regex_match"=>"Only alphabets and spaces allowed."]);
        $this->form_validation->set_rules('inputContactEmail', 'Contact Email', 'required|valid_email');
        $this->form_validation->set_rules('inputContactPhone', 'Contact Phone', 'required|regex_match[/[\+\s\-0-9]+/]');
        
        $this->form_validation->set_rules('inputContactType', 'Contact Type', 'required');
        $this->form_validation->set_rules('inputContactStreet', 'Street', 'required');
        $this->form_validation->set_rules('inputContactCity', 'City', 'required');
        $this->form_validation->set_rules('inputContactState', 'State', 'required');
        $this->form_validation->set_rules('inputContactZipcode', 'Zipcode', 'required|numeric|min_length[5]');

        if($this->form_validation->run() == FALSE)
        {
            echo json_encode(['type'=>'error','results'=>$this->form_validation->error_array()]);

        } else {
            $aResponse = $this->Smartystreets_model->find(
                $this->input->post("inputContactStreet"),
                $this->input->post("inputContactCity"),
                $this->input->post("inputContactState"),
                $this->input->post("inputContactZipcode")
            );
            
            //$this->Smartystreets_model->run();
            //echo "<pre>";
            //print_r($aResponse);
            //echo json_encode($aResponse[0]);die;
            if($aResponse['type']=='ok')
            {
                $_POST['inputContactStreet'] = $aResponse['results'][0]->getDeliveryLine1();
                $_POST['inputContactCity'] = $aResponse['results'][0]->getComponents()->getCityName();
                $_POST['inputContactState'] = $aResponse['results'][0]->getComponents()->getStateAbbreviation();
                $_POST['inputContactZipcode'] = $aResponse['results'][0]->getComponents()->getZIPCode();
                //var_dump($_POST);
                //echo "<pre>";
                //print_r($aResponse['results'][0]->getComponents()->zipcode);
                //getClassMethods($aResponse['results'][0]);
                //die;
                $aResponse = $this->addZoho();

            } else if($this->input->post('acceptInvalidAddress'))
            {
                $aResponse = $this->addZoho();
            }
            
            echo json_encode($aResponse);
            
        }
    }

    private function addZoho()
    {
        $this->load->model("ZoHo_Account");
        $this->load->model("Accounts_model");

        $arError = [];
        $iLoginId = $this->session->user['zohoId'];
        //$iLoginId = 4071993000000411118;
        $oAccountRow = $this->Accounts_model->getOne($iLoginId);
        $sAccountName = $oAccountRow->entity_name;
        //$sAccountName = "Najm Test Comliance";
//var_dump($oAccountRow);
//die;
        
        $oApi = $this->ZoHo_Account->getInstance("Contacts",null);

        $oApi->setFieldValue("Account_Name", $sAccountName);
        $oApi->setFieldValue("First_Name",$this->input->post("inputContactFirstName"));
        $oApi->setFieldValue("Last_Name",$this->input->post("inputContactLastName"));
        
        $oApi->setFieldValue("Email",$this->input->post("inputContactEmail"));
        $oApi->setFieldValue("Phone",$this->input->post("inputContactPhone"));
        $oApi->setFieldValue("Contact_Type",$this->input->post("inputContactType"));

        $oApi->setFieldValue("Mailing_Street",$this->input->post("inputContactStreet"));
        $oApi->setFieldValue("Mailing_City",$this->input->post("inputContactCity"));
        $oApi->setFieldValue("Mailing_State",$this->input->post("inputContactState"));
        $oApi->setFieldValue("Mailing_Zip",$this->input->post("inputContactZipcode"));
        
        try {
            $oApi->create();
        } catch(Exception $e){
            $arError[] = "Add contact failed: " . $e->getMessage();
        }

        if(count($arError))
        {
            return ['type'=>'error','results'=>$arError[0]];
        } else {
            return ["type"=>"ok","results"=>"Contact added successfully"];
        }
        
    }
}