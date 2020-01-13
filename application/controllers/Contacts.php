<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Src\Services\OktaApiService as Okta;

class Contacts extends CI_Controller
{
    private $url = '';
    private $easy_ofac_test = '';
    private $auth_key = '';
    public function __construct()
    {
        parent::__construct();
        
       $this->url = !empty(getenv('EASY_OFAC_URL')) ? getenv('EASY_OFAC_URL') : '';
       $this->easy_ofac_test = !empty(getenv('EASY_OFAC_TEST')) ? getenv('EASY_OFAC_TEST') : '';
       $this->auth_key = !empty(getenv("EASY_OFAC_KEY")) ? getenv('EASY_OFAC_KEY') : '';

    }
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

        $bContactRow = $this->Contacts_model->checkRowExist($aData);
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

    //add contact to OFAC $aResponse["id"];
    $this->addContactOfac($aResponse["id"],$this->input->post("inputContactFirstName"),$this->input->post("inputContactLastName"));


    $this->load->model("Tempmeta_model");

    $data = [
                "id" => $aResponse["id"],
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

    $this->Tempmeta_model->appendRow($iLoginId,$this->Tempmeta_model->slugNewContact,$data,"email");
}

        if(count($arError))
        {
            return ['type'=>'error','results'=>$arError[0]];
        } else {
            return ["type"=>"ok","results"=>"Contact added successfully"];
        }
        
    }

    //Function for adding the contacts to the easyOFAC
    //This function has the dependence of ZohoID, First Name and Last Name and KYC(Know your customer) which is set to 0 at the moment moreover it can be edited directly from easyOFAC
    public function addContactOfac($id, $first_name, $last_name){

        //Key and url from .env
     if(empty($this->url) or empty($this->auth_key) or empty($this->easy_ofac_test)){
        return "OFAC settings are missing.";
     }
     //Test variables
     // $first_name = "Lorem";
     // $last_name = "Lorem";
     // $id = "12345678901234567890";

     $finalurl = $this->url."addCustomer?api_key=".$this->auth_key."&id=".$id."&first_name=".$first_name."&last_name=".$last_name."&test=".$this->easy_ofac_test."&kyc=0";
     $cSession = curl_init();
     if (!$cSession) {
         return "Couldn't initialize a cURL handle";
     }
     // Step 2
     curl_setopt($cSession,CURLOPT_URL,$finalurl);
     curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
     curl_setopt($cSession,CURLOPT_HEADER, false); 
     // Step 3
     $result=curl_exec($cSession);
     // Step 4
     curl_close($cSession);
     // Step 5
     $json = json_decode($result);
     return $json;
 }
}