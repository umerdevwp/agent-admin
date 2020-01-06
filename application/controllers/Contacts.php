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
        if(!isSessionValid("Contacts_Add")) redirectSession();

        $aResponse = [];
        $this->load->model("Smartystreets_model");
        
        $this->load->library('form_validation');

        $this->form_validation->set_rules('entityId', 'Entity', 'required|numeric');
        $this->form_validation->set_rules('inputContactFirstName', 'First Name', 'required|regex_match[/[a-zA-Z\s]+/]',["regex_match"=>"Only alphabets and spaces allowed."]);
        $this->form_validation->set_rules('inputContactLastName', 'Last Name', 'required|regex_match[/[a-zA-Z\s]+/]',["regex_match"=>"Only alphabets and spaces allowed."]);

        $this->form_validation->set_rules('inputContactEmail', 'Contact Email', ['required','valid_email','callback_checkEmailExist']);
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

    public function checkEmailExist($strEmail)
    {
        
        $bDontExist = true;
        $this->load->model("Contacts_model");
        $this->load->model("Tempmeta_model");

        $aData = ['email'=>$strEmail,'entity_name'=>$this->input->post('entityId')];

<<<<<<< HEAD
        $bContactRow = $this->Contacts_model->checkRowExistInJson($aData);
=======
        $bContactRow = $this->Contacts_model->checkRowExist($aData);
>>>>>>> development
        // check in zoho contacts list
        if($bContactRow)
        {
            $this->form_validation->set_message('checkEmailExist', 'The {field} already exist');
            $bDontExist = false;
        }
        //var_dump($bDontExist);
        // if not in zoho then check in temp table
        if($bDontExist){
            $aData = ['json_email'=>$strEmail,'userid'=>$this->input->post('entityId')];
            $bTempmetaRow = $this->Tempmeta_model->checkRowExistInJson($aData);
            
            if($bTempmetaRow)
            {
                $this->form_validation->set_message('checkEmailExist', 'The {field} already exist');
                $bDontExist = false;
            }
        }

        return $bDontExist;
    }

    private function addZoho()
    {
        $this->load->model("ZoHo_Account");
        $this->load->model("Accounts_model");

        $arError = [];
        //$iLoginId = $this->session->user['zohoId'];
        $iLoginId = $this->input->post("entityId");
        //$iLoginId = 4071993000000411118;
        // TODO: validate user is the child entity of login parent
        $oAccountRow = $this->Accounts_model->getOne($iLoginId);
        $sAccountName = $oAccountRow->entity_name;
        //$sAccountName = "Najm Test Comliance";
//var_dump($oAccountRow);
//die;
        
$aResponse = $this->ZoHo_Account->newZohoContact(
    $sAccountName,
    [
        "First_Name"    =>$this->input->post("inputContactFirstName"),
        "Last_Name"     =>$this->input->post("inputContactLastName"),

        "Email"         =>$this->input->post("inputContactEmail"),
        "Phone"         =>$this->input->post("inputContactPhone"),
        "Contact_Type"  =>$this->input->post("inputContactType"),

        "Mailing_Street"=>$this->input->post("inputContactStreet"),
        "Mailing_City"  =>$this->input->post("inputContactCity"),
        "Mailing_State" =>$this->input->post("inputContactState"),
        "Mailing_Zip"   =>$this->input->post("inputContactZipcode")
    ]
);

if($aResponse['type']=='error'){
    $arError[] = $aResponse['results'];
} else {
    $this->load->model("Tempmeta_model");

    $data = [
                "first_name"    =>  $this->input->post("inputContactFirstName"),
                "last_name"    =>  $this->input->post("inputContactLastName"),

                "email"    =>  $this->input->post("inputContactEmail"),
                "phone"    =>  $this->input->post("inputContactPhone"),
                "title"    =>  $this->input->post("inputContactType"),

                "mailing_street"    =>  $this->input->post("inputContactStreet"),
                "mailing_city"    =>  $this->input->post("inputContactCity"),
                "mailing_state"    =>  $this->input->post("inputContactState"),
                "mailing_zip"    =>  $this->input->post("inputContactZipcode"),
    ];

<<<<<<< HEAD
    $this->Tempmeta_model->appendRow($iLoginId,"Contacts",$data,"email");
=======
    $this->Tempmeta_model->appendRow($iLoginId,$this->Tempmeta_model->slugNewContact,$data,"email");
>>>>>>> development
}

        if(count($arError))
        {
            return ['type'=>'error','results'=>$arError[0]];
        } else {
            return ["type"=>"ok","results"=>"Contact added successfully"];
        }
        
    }
}