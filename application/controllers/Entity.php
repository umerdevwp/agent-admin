<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Entity extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->helper("custom");

    }

	public function index($id="")
	{
        if(!isSessionValid("Entity")) redirectSession();
        if(empty($id)){
            $this->session->set_flashdata("error","Invalid entity id");
            redirectSession();
        }
        //$this->load->model('ZoHo_Account');
		$this->load->model('Accounts_model');
		$this->load->model('Tasks_model');
		$this->load->model('Contacts_model');
		$this->load->model('Attachments_model');

		// fetch data from zoho api
        //$this->ZoHo_Account->LoadAccount($id);
        //$this->ZoHo_Account->dumpAll();
		//$data['account'] = $this->ZoHo_Account;
		
		// fetch data from DB
        $data['entity'] = $this->Accounts_model->loadAccount($id);
        
        $oAgetAddress = $this->Accounts_model->getAgentAddress($id);
        
        if(is_object($oAgetAddress)){
            $data['AgentAddress']['file_as'] = $oAgetAddress->file_as;
            $data['AgentAddress']['address'] = $oAgetAddress->address;
            $data['AgentAddress']['address2'] = $oAgetAddress->address2;
            $data['AgentAddress']['city'] = $oAgetAddress->city;
            $data['AgentAddress']['state'] = $oAgetAddress->state;
            $data['AgentAddress']['zip_code'] = $oAgetAddress->zip_code;
        } else {
            $data['AgentAddress'] = false;
        }
        
		$data['tasks'] = $this->Tasks_model->getAll($id);
		$data['contacts'] = $this->Contacts_model->getAllFromEntityId($id);
		//$data['attachments'] = $this->ZoHo_Account->arAttachments;
		$data['attachments'] = $this->Attachments_model->getAllFromEntityId($id);
		
        // use login entity id
        $iEntityId = $this->session->user['zohoId'];

        // if session is parent then get entity ID from url
        if($this->session->user['child'])
            $iEntityId = $id;

        $data['iEntityId'] = $iEntityId;

		$this->load->view('header');
		$this->load->view('entity', $data);
		$this->load->view('footer');
    }
    
    public function form($id=0)
    {
        if(!isSessionValid("Entity_Add")) redirectSession();
        // test tags with compliance only
        
        $this->load->library('form_validation');
        $this->load->model("Accounts_model");

        if($id>0)
        {
            $data = $this->Account_model->getOne($id);
        }

        $this->load->view('header');
		$this->load->view('entity-add', $data);
		$this->load->view('footer');
    }

    public function add()
    {
        if(!isSessionValid("Entity_Add")) redirectSession();

        $this->load->helper("custom");
        $this->load->library('form_validation');

        // try to correct user date format, then validate
        if($this->input->post("inputFormationDate")!="")
        {
            $strFormationDate = str_replace("  "," ",$this->input->post("inputFormationDate"));
            $strFormationDate = str_replace(" ","-",$strFormationDate);
            $strFormationDate = date("Y-m-d",strtotime($strFormationDate));
            
            if($strFormationDate=="1970-01-01")
            {
                //$_POST["inputFormationDate"] = "0000 00 00";
            } else {
                $_POST["inputFormationDate"] = $strFormationDate;
            }
        }

        $this->form_validation->set_rules('inputName', 'Account Name', 'required|regex_match[/[a-zA-Z\s]+/]',["regex_match"=>"Only alphabets and spaces allowed."]);
        $this->form_validation->set_rules('inputFillingState', 'Filing State', 'required|alpha');
        $this->form_validation->set_rules('inputFillingStructure', 'Entity Type', 'required|regex_match[/[A-Z\-]+/]');
        $this->form_validation->set_rules('inputFormationDate', 'Formation Date', 'required|regex_match[/[0-9]{4,}\-[0-9]{2,}\-[0-9]{2,}/]',["regex_match"=>"Allowed %s format: 2019-01-01"]);
        $this->form_validation->set_rules('inputNotificationEmail', 'Notification Email', 'required|valid_email');
        $this->form_validation->set_rules('inputNotificationPhone', 'Phone', 'required|regex_match[/[\+\s\-0-9]+/]');
        $this->form_validation->set_rules('inputNotificationAddress', 'Shipping Street', 'required');
        $this->form_validation->set_rules('inputNotificationCity', 'Shipping City', 'required');
        $this->form_validation->set_rules('inputNotificationState', 'Shipping State', 'required');
        $this->form_validation->set_rules('inputNotificationZip', 'Shipping Code', 'required');
        $this->form_validation->set_rules('inputBusinessPurpose', 'Business purpose', 'required');
        // allow without file, else check type and size
        if($_FILES["inputFiling"]["name"]!=""){
            $this->form_validation->set_rules('inputFiling', 'Filing Attachment', 
                array(
                    array('validate_extention',function(){return validateFileExt($_FILES['inputFiling']['tmp_name'],['application/pdf']);}),
                    array('validate_size',function(){return validateFileSize($_FILES['inputFiling']['tmp_name'],10*1000*1000);}),
                ),
                array(
                    'validate_extention'=>'Only MIME .pdf files are allowed',
                    'validate_size'=>'Input file: ' . getFileSize(filesize($_FILES['inputFiling']['tmp_name'])) . ' exceeding limit of 10MB.'
                )
            );
        }

        $response = array();

        if($this->form_validation->run() == FALSE)
        {
            $this->form();

            return false;

        } else {

            $response = $this->zohoCreateEntity($this->session->user['zohoId']);
            // succcess redirect to dashboard
            if(isset($response["ok"]))
            {
                $this->session->set_flashdata("ok",$response["ok"]);
                redirect("portal");
            // redirect to form, show error
            } else if($response["error_code"]==2){
                $this->session->set_flashdata("error",$response["error"]);
                redirect("portal");
            } else {
                $this->session->set_flashdata("error",$response["error"]);
                $this->form();
            }
            
        }
    }

    private function zohoCreateEntity($iParentZohoId)
    {
        $arError = array();
        $iErrorType = 1;// 1 means user creation failed, 2 means only attachment failed
        $this->load->model('ZoHo_Account');
        $this->load->model("Accounts_model");
        $this->load->model("RegisterAgent_model");
        
        $oApi = $this->ZoHo_Account->getInstance()->getRecordInstance("Accounts",null);
        /* //testing contacts for new entity
        
        $oApi = $this->ZoHo_Account->getInstance("Accounts");
        $oApiData = $oApi->searchRecordsByCriteria("id:equals:4071993000001672002", 1, 1);
        
        $oApiSingleRecord = $oApiData->getData()[0];
        //$oContacts = $oApi->getRelatedListRecords("Contacts");
        
        //$oSingleContact = $oContacts->getData()[0];
        //echo $oSingleContact->getModuleApiName();
        
        $oNewContact = $this->ZoHo_Account->getInstance("Contacts");
        echo "<pre>";getClassMethods($oNewContact);print_r($oNewContact);die;
            //$oContacts->setData($oNewContact);
            //$oNewContact->setOwner($oApi->getData()[0]);
            $oApi->setFieldValue("Account_Name", $oApiData->getData()[0]);
            $oNewContact->setFieldValue("First_Name","Najm");
            $oNewContact->setFieldValue("Last_Name","A2");
        try{
            $obj = $oNewContact->create();
            echo "Done";
        } catch(Exception $e){
            echo "<Pre>";
            print_r($e);
        }
        die;
        
        
        echo "<pre>";
        getClassMethods($oApiSingleRecord);
        print_r($oApiSingleRecord);
        
        die;*/
        $oApi->setFieldValue("Account_Name", $this->input->post("inputName")); // This function use to set FieldApiName and value similar to all other FieldApis and Custom field
        $oApi->setFieldValue("Filing_State", $this->input->post("inputFillingState")); // Account Name can be given for a new account, account_id is not mandatory in that case
        $oApi->setFieldValue("Entity_Type", $this->input->post("inputFillingStructure")); // Account Name can be given for a new account, account_id is not mandatory in that case
        
        $oApi->setFieldValue("Formation_Date",$this->input->post("inputFormationDate"));

        // firstName, lastName fields going under contacts
        
        $oApi->setFieldValue("Notification_Email",$this->input->post("inputNotificationEmail"));
        $oApi->setFieldValue("Phone",$this->input->post("inputNotificationPhone"));
        $oApi->setFieldValue("Shipping_Street",$this->input->post("inputNotificationAddress"));
        $oApi->setFieldValue("Shipping_City",$this->input->post("inputNotificationCity"));
        $oApi->setFieldValue("Shipping_State",$this->input->post("inputNotificationState"));
        $oApi->setFieldValue("Shipping_Code",$this->input->post("inputNotificationZip"));
        $oApi->setFieldValue("Business_purpose",$this->input->post("inputBusinessPurpose"));

        $oApi->setFieldValue("Parent_Account",$iParentZohoId);

        // additional detail as default values for new entity
        // tag call needs account id instance, added below after attachments
        $oApi->setFieldValue("Account_Type","Distributor");
        $oApi->setFieldValue("Layout","4071993000001376034");// for customer layout id = Customer
        $oApi->setFieldValue("status","InProcess");

        $oLoginUser = $this->Accounts_model->getOne($this->session->user["zohoId"]);
        
        if($oLoginUser->id)
        {
            // billing info using entity profile
            $oApi->setFieldValue("Billing_City",$oLoginUser->billing_city);
            $oApi->setFieldValue("Billing_Code",$oLoginUser->billing_code);
            $oApi->setFieldValue("Billing_Country","US");
            $oApi->setFieldValue("Billing_State",$oLoginUser->billing_state);
            $oApi->setFieldValue("Billing_Street",$oLoginUser->billing_street);
            $oApi->setFieldValue("Billing_Street_2",$oLoginUser->billing_street_2);
        } else {
            $arError[] = "Billing addresses failed";
        }

        // fetch RA (registered agent) id from DB
        $strFilingState = $this->input->post("inputFillingState");
        $row = $this->RegisterAgent_model->find(["registered_agent_name"=>$strFilingState." - UAS"]);
        $iRAId = "";
        if($row->id>0)
        {
            $iRAId = $row->id;
        }

        // push the id to zoho
        $oApi->setFieldValue("RA",$iRAId);

        $trigger=array();//triggers to include
        $lar_id="";//lead assignment rule id

        $oResponse = null;
        try {
            // setting trigger and $lar_id causing issue, api url not correct
            $responseIns = $oApi->create();//$trigger , $larid optional

            $oResponse = $responseIns->getDetails();
        } catch(Exception $e) {
            // message
            $arError[] = "code: " . $e->getCode() . ", error: Api: " . $e->getMessage();
        }
        
        if($oResponse["id"]>0)
        {

            // setting 2, so error only reports attachment issue
            $iErrorType = 2;
            
            $oAttachment = null;
            
            // if file is give for attachment
            if($_FILES['inputFiling']["name"]!="")
            {
                // move uploaded file to local
                if(move_uploaded_file($_FILES['inputFiling']['tmp_name'],getenv("UPLOAD_PATH").$_FILES['inputFiling']['name']))
                {
                    try {
                        $oAttachment = $oApi->uploadAttachment($_SERVER['DOCUMENT_ROOT'] . "/" . getenv("UPLOAD_PATH").$_FILES['inputFiling']['name']); // $filePath - absolute path of the attachment to be uploaded.
                        //$oAttachmentResponse = $oAttachment->getDetails();
                    } catch(Exception $e) {
                        $arError[] = "code: " . $e->getCode() . ", error: Api: " . $e->getMessage();
                    }
                    // TODO: remove uploaded file from directory getenv("UPLOAD_PATH")
                } else {
                    $arError[] = "User created successfully, Internal Server Error: file upload failed.";
                }
            }

            //$oApi->setModuleApiName("Contacts");
            $oApi = $this->ZoHo_Account->getInstance()->getRecordInstance("Contacts",null);
            
            $oApi->setFieldValue("Account_Name", $this->input->post("inputName"));
            
            $oApi->setFieldValue("First_Name",$this->input->post("inputFirstName"));
            $oApi->setFieldValue("Last_Name",$this->input->post("inputLastName"));
            try {
                $oApi->create();
            } catch(Exception $e){
                if(count($arError)>0) $arError[0] .= ", contacts failed.";
                else $arError[] = "User created successfully, contacts failed.";
            }

            // add tags
            
            $oApi = $this->ZoHo_Account->getInstance("Accounts",$oResponse["id"]);
            
            try {
                $sComplianceOnly = ($this->input->post("inputComplianceOnly")??0);
    
                $aTags = ["name"=>"OnBoard"];
                
                if($sComplianceOnly)
                {
                    $aTags["ComplianceOnly"] = "Compliance Only";
                }
    
                $oResponseTags = $oApi->addTags($aTags);
                
                $oData = $oResponseTags->getData();
            } catch(Exception $e){
                if(count($arError)>0) $arError[0] .= ", tags failed.";
                else $arError[] = "User created successfully, tags failed.";
            }
        }
        //var_dump($arError);die;
        if(count($arError)>0)
        {
            return ['error'=>$arError[0],'error_code'=>$iErrorType];
        }

        return ['ok'=>"Entity created successfully."];
    }

}