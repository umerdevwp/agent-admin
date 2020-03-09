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
        $this->load->model("entity_model");
        
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
            $sSmartyAddress = "";
            if($aResponse['type']=='ok')
            {
                // store previous user input for contact note purpose
                $sSmartyAddress =<<<HC
Street: {$this->input->post('inputContactStreet')}
City: {$this->input->post('inputContactCity')}
State: {$this->input->post('inputContactState')}
Zipcode: {$this->input->post('inputContactZipcode')}
HC;
                // update the user input with smarty response data
                $_POST['inputContactStreet'] = $aResponse['results'][0]->getDeliveryLine1();
                $_POST['inputContactCity'] = $aResponse['results'][0]->getComponents()->getCityName();
                $_POST['inputContactState'] = $aResponse['results'][0]->getComponents()->getStateAbbreviation();
                $_POST['inputContactZipcode'] = $aResponse['results'][0]->getComponents()->getZIPCode();
                
                $aResponse = $this->addZoho($sSmartyAddress);

            } else if($this->input->post('acceptInvalidAddress'))
            {
                $aResponse = $this->addZoho($sSmartyAddress);
            }
            
            echo json_encode($aResponse);
            
        }
    }

    public function checkEmailExist($strEmail)
    {
        
        $bDontExist = true;
        $this->load->model("Contacts_model");
        $this->load->model("Tempmeta_model");

        $aData = ['email'=>$strEmail,'account_name'=>$this->input->post('entityId')];

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

    private function addZoho($sSmartyAddress='')
    {
        $this->load->model("ZoHo_Account");
        $this->load->model("entity_model");

        $arError = [];
        //$iLoginId = $this->session->user['zohoId'];
        $iLoginId = $this->input->post("entityId");
        //$iLoginId = 4071993000000411118;
        // TODO: validate user is the child entity of login parent
        $oAccountRow = $this->entity_model->getOne($iLoginId);
        $sAccountName = $oAccountRow->account_name;
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
    $arError[] = $aResponse['message'];
} else {
    $iContactId = $aResponse['results'];
    //add contact to OFAC $aResponse["id"];
   

    $this->load->model("Contacts_model");
    // $iContactId = $aResponse['results'];
    $data = [
                "id" => $iContactId,
                "owner" => $this->session->user['zohoId'],
                "first_name"    =>  $this->input->post("inputContactFirstName"),
                "last_name"    =>  $this->input->post("inputContactLastName"),
                "full_name" => $this->input->post("inputContactFirstName").' '.$this->input->post("inputContactLastName"),
                "account_name" => $this->input->post("entityId"),
                "email"    =>  $this->input->post("inputContactEmail"),
                "phone"    =>  $this->input->post("inputContactPhone"),
                "title"    =>  $this->input->post("inputContactType"),
                "mailing_street"    =>  $this->input->post("inputContactStreet"),
                "mailing_city"    =>  $this->input->post("inputContactCity"),
                "mailing_state"    =>  $this->input->post("inputContactState"),
                "mailing_zip"    =>  $this->input->post("inputContactZipcode"),
                "created_by" => $this->session->user['zohoId'],
                "created_time" => date('Y-m-d H:i:s'),
                "modified_time" => date('Y-m-d H:i:s'),
                "last_activity_time" => date('Y-m-d H:i:s'),
                "number_of_chats"=> '0',
                "average_time_spent_minutes" => '0.00',
                "days_visited" => '0',
                "visitor_score" => '0'


    ];

    $response_contact = $this->Contacts_model->addContact($data);

    //array for inserting in contactmeta
    $data = array(
        'contact_id' => $iContactId,
        'ofac_status' => 'safe',
    );

    //after saving the contact in database it returns id.
    $response_contact_meta_id = $this->Contacts_model->addContactMeta($data);


    //Using the id initiating call to easyOFAC.
    //Ternary operator checks if the Contact meta ID is not empty else will dump an error log.
    !empty($response_contact_meta_id) ? 
        $this->addContactOfac(
          $response_contact_meta_id,
          $this->input->post("inputContactFirstName"),
          $this->input->post("inputContactLastName")) : 
        log_message('error', 'Contact Meta ID is missing.');
 

    //Sending note to Zoho, stating the initial status of the contact
    !empty($iContactId) and isset($iContactId) ? 
        $this->ZoHo_Account->newZohoNote("Contacts",$iContactId,"Contact OFAC Status",'safe') :
        log_message('error', 'OFAC Status can not be added to the Zoho CRM due to missing Contact ID (ZohoID)');



    // add a note if smarty validated address successfuly
    if($sSmartyAddress!='') $response = $this->ZoHo_Account->newZohoNote("Contacts",$iContactId,"Smartystreet has replaced following",$sSmartyAddress);
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
        log_message('error', 'OFAC settings are missing.');
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

  public function UpdateContactMeta(){
    $this->load->model("Contacts_model");
    $get_cron_info = $this->Contacts_model->ofac_cron_job_get();
    if(!empty($get_cron_info)){
            $now = time(); // or your date as well
            $your_date = strtotime($get_cron_info[0]->updated);
            $datediff = $now - $your_date;
            echo round($datediff / (60 * 60 * 24)). ' Days';
            if(round($datediff / (60 * 60 * 24)) >= 90){
                $this->Contacts_model->ofac_cron_job_update();
                $get_all_contact = $this->Contacts_model->getAllContactMeta();
                if(is_array($get_all_contact)){
                    foreach($get_all_contact as $contactmeta){
                        $ofac_response = $this->getContactOfac($contactmeta->id);
                        if(!empty($ofac_response->customer_status)){
                            $current_status_db = $this->Contacts_model->getOfacStatus($contactmeta->id);
                            if($current_status_db[0]->ofac_status != $ofac_response->customer_status){
                                $response = $this->Contacts_model->updateContactMeta($contactmeta->id, $ofac_response->customer_status);
                                $response2 = $this->ZoHo_Account->newZohoNote("Contacts",$contactmeta->contact_id,"Contact OFAC Status",$ofac_response->customer_status);
                            }
                        }
                    }
                   
                }  
            }
    } else {
        $get_cron_info = $this->Contacts_model->ofac_cron_job_insert();
    }
  }

public function getContactOfac($id){
    //Key and url from .env
    if(empty($this->url) or empty($this->auth_key) or empty($this->easy_ofac_test)){
    log_message('error', 'OFAC settings are missing.');
    return $data['error'] = "OFAC settings are missing.";
    }
    //Test variables


    $finalurl = $this->url."inspectCustomer?api_key=".$this->auth_key."&id=".$id;
    $cSession = curl_init();
    if (!$cSession) {
    log_message('error', "Couldn't initialize a cURL handle");
        return $data['error'] = "Couldn't initialize a cURL handle";
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