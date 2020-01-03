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
        $this->load->model("Tempmeta_model");

        // use login entity id
        $iEntityId = $this->session->user['zohoId'];

		// fetch data from zoho api
        //$this->ZoHo_Account->LoadAccount($id);
        //$this->ZoHo_Account->dumpAll();
		//$data['account'] = $this->ZoHo_Account;
		
		// fetch data from DB
        $aDataEntity = $this->Accounts_model->loadAccount($id);
        $oTempAgetAddress = null;
        if($aDataEntity['type']=='error' && $this->session->user['child'])
        {
            $aDataTempEntity = $this->Tempmeta_model->getOneInJson([
                'userid'=>$iEntityId,
                'json_id'=>$id,
                'slug'=>$this->Tempmeta_model->slugNewEntity
            ]);
            if($aDataTempEntity['type']=='ok')
            {
                $data['entity'] = $aDataTempEntity['results'];
                $oTempAgetAddress = $data['entity']->agent;
                
                $this->session->set_flashdata("info","Please note: missing fields will be updated shortly.");
            } else {
                $this->session->set_flashdata("error","No such entity exist.");
            }
            
        } else if($aDataEntity['type']=='ok'){
            $data['entity'] = $aDataEntity['results'];
        } else {
            $this->session->set_flashdata("error","No such entity exist.");
        }

        $oAgetAddress = $this->Accounts_model->getAgentAddress($id);
        
        if(is_object($oAgetAddress)){
            $data['AgentAddress'] = (array)$oAgetAddress;
        } else if(is_object($oTempAgetAddress)){
            $data['AgentAddress'] = (array)$oTempAgetAddress;
        } else {
            $data['AgentAddress'] = false;
        }
        
        $data['tasks'] = $this->Tasks_model->getAll($id);

        $aTasksCompleted = $this->Tempmeta_model->getOne($id,$this->Tempmeta_model->slugTasksComplete);
        
        if(is_object($aTasksCompleted['results']))
            $data['tasks_completed'] = json_decode($aTasksCompleted['results']->json_data);
        else 
            $data['tasks_completed'] = [];
                
        $data['contacts'] = $this->Contacts_model->getAllFromEntityId($id);
        
        if($data['contacts'])
        {
            $aDataContact = $this->Tempmeta_model->getOne($id,$this->Tempmeta_model->slugNewContact);
            if($aDataContact['type']=='ok') $data['contacts'] = array_merge($data['contacts'],json_decode($aDataContact['results']->json_data));

        } else {
            $aDataContact = $this->Tempmeta_model->getOne($id,$this->Tempmeta_model->slugNewContact);
            if($aDataContact['type']=='ok')
            {
                $data['contacts'] = json_decode($aDataContact['results']->json_data);
            } else {
                $data['contacts'] = [];
            }
        }
        
        $data['attachments'] = $this->Attachments_model->getAllFromEntityId($id);
        if($data['attachments'])
        {
            $aDataAttachment = $this->Tempmeta_model->getOne($id,$this->Tempmeta_model->slugNewAttachment);
            if($aDataAttachment) $data['attachments'] = array_merge($data['attachments'],json_decode($aDataAttachment['results']->json_data));
        } else {
            $aDataAttachment = $this->Tempmeta_model->getOne($id,$this->Tempmeta_model->slugNewAttachment);
            if($aDataAttachment['type']=='ok')
            {
                $data['attachments'] = json_decode($aDataAttachment['results']->json_data);
            } else {
                $data['attachments'] = [];
            }
        }
        
        // if session is parent then get entity ID from url
        if($this->session->user['child'])
            $iEntityId = $id;

        $data['iEntityId'] = $iEntityId;

		$this->load->view('header');
		$this->load->view('entity', $data);
		$this->load->view('footer');
    }
    
    private function fetchTempDataOf($iEntityId,$sSlug)
    {
        $aTempRows = $this->Tempmeta_model->getOne($iEntityId,$sSlug);
        $aData = [];
        if($aTempRows['type']=='ok')
        {
            $aData = json_decode($aTempRows['results']->json_data)[0];
        }

        return $aData;
    }

    public function form($id=0)
    {
        if(!isSessionValid("Entity_Add")) redirectSession();

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
        
        $bTagSmartyValidated = true;
        $arError = [];
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

        $this->load->model("Smartystreets_model");
        
        $oSmartyStreetResponse = $this->Smartystreets_model->find(
            $this->input->post("inputNotificationAddress"),
            $this->input->post("inputNotificationCity"),
            $this->input->post("inputNotificationState"),
            $this->input->post("inputBusinessPurpose")
        );

        // check wether invalid address entered the 1st time
        if($oSmartyStreetResponse['type']=='error' && !$this->session->invalid_address_count)
        {
            $arError[] = "Unable to validate your address, please recheck and confirm ...";
            $this->session->invalid_address_count = 1;

        // qualify address if invalid address entered 2nd time
        } else if($oSmartyStreetResponse['type']=='error' && $this->session->invalid_address_count==1){
            $this->session->invalid_address_count = 0;
            $bTagSmartyValidated = false;

        // replace user address with validated smartystreet address
        } else {
            $_POST['inputNotificationAddress'] = $oSmartyStreetResponse['results'][0]->getDeliveryLine1();
            $_POST['inputNotificationCity'] = $oSmartyStreetResponse['results'][0]->getComponents()->getCityName();
            $_POST['inputNotificationState'] = $oSmartyStreetResponse['results'][0]->getComponents()->getStateAbbreviation();
            $_POST['inputNotificationZip'] = $oSmartyStreetResponse['results'][0]->getComponents()->getZIPCode();
            $this->session->invalid_address_count = 0;
        }
        //var_dump($this->session->invalid_address_count);die;

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

        if($this->form_validation->run() == FALSE || count($arError)>0)
        {
            if(count($arError)>0)
            {
                $this->session->set_flashdata("error",$arError[0]);
            }

            $this->form();
            
            return false;

        } else {

            $response = $this->zohoCreateEntity($this->session->user['zohoId'],$bTagSmartyValidated);
            // succcess redirect to dashboard
            if(isset($response["ok"]))
            {
                $this->session->set_flashdata("ok",$response["ok"]);
                $this->redirectAfterAdd();
            // redirect to form, show error
            } else if($response["error_code"]==2){
                $this->session->set_flashdata("error",$response["error"]);
                $this->redirectAfterAdd();
            } else {
                $this->session->set_flashdata("error",$response["error"]);
                $this->form();
            }
            
        }
    }

    private function redirectAfterAdd()
    {
        if(empty($this->input->post('btnSaveNew')))
            redirect("portal");
        else
            redirect("entity/form");
    }

    private function zohoCreateEntity($iParentZohoId,$bTagSmartyValidated)
    {
        $arError = array();
        $iErrorType = 1;// 1 means user creation failed, 2 means only attachment failed
        $this->load->model('ZoHo_Account');
        $this->load->model("Accounts_model");
        $this->load->model("RegisterAgent_model");
        
        $oApi = $this->ZoHo_Account->getInstance()->getRecordInstance("Accounts",null);
        
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
            $bAttachmentDone = $bContactDone = false;
            $oAttachment = null;
            
            // if file is give for attachment
            if($_FILES['inputFiling']["name"]!="")
            {
                $sFilename = time() . "-" . $_FILES['inputFiling']['name'];
                $_POST['attachment'] = $sFilename;
                // move uploaded file to local
                if(move_uploaded_file($_FILES['inputFiling']['tmp_name'],getenv("UPLOAD_PATH").$sFilename))
                {
                    try {
                        $oAttachment = $oApi->uploadAttachment($_SERVER['DOCUMENT_ROOT'] . "/" . getenv("UPLOAD_PATH").$sFilename); // $filePath - absolute path of the attachment to be uploaded.
                        //$oAttachmentResponse = $oAttachment->getDetails();
                        $bAttachmentDone = true;
                    } catch(Exception $e) {
                        $arError[] = "code: " . $e->getCode() . ", error: Api: " . $e->getMessage();
                        $bAttachmentDone = false;
                    }

                    // TODO: remove uploaded file from directory getenv("UPLOAD_PATH")
                } else {
                    $arError[] = "User created successfully, Internal Server Error: file upload failed.";
                }
            }

            //$oApi->setModuleApiName("Contacts");
            $oApi = $this->ZoHo_Account->getInstance()->getRecordInstance("Contacts",null);
            
            
            $aResponse = $this->ZoHo_Account->newZohoContact(
                $this->input->post("inputName"),
                [
                    "First_Name"    =>$this->input->post("inputFirstName"),
                    "Last_Name"     =>$this->input->post("inputLastName"),

                    "Email"         =>$this->input->post("inputNotificationEmail"),
                    "Phone"         =>$this->input->post("inputNotificationPhone"),
                    "Contact_Type"  =>"",

                    "Mailing_Street"=>$this->input->post("inputNotificationAddress"),
                    "Mailing_City"  =>$this->input->post("inputNotificationCity"),
                    "Mailing_State" =>$this->input->post("inputNotificationState"),
                    "Mailing_Zip"   =>$this->input->post("inputNotificationZip")
                ]
            );

            if($aResponse['type']=='error'){
                if(count($arError)>0) $arError[0] .= ", contacts failed.";
                else $arError[] = "User created successfully, contacts failed.";
            } else $bContactDone = true;

            $this->addEntityToTemp($oResponse["id"],$bContactDone,$bAttachmentDone);

            // add tags
            $oApi = $this->ZoHo_Account->getInstance("Accounts",$oResponse["id"]);
            
            try {
                $sComplianceOnly = ($this->input->post("inputComplianceOnly")??0);
    
                $aTags = ["name"=>"OnBoard"];
                
                if($sComplianceOnly)
                {
                    $aTags["ComplianceOnly"] = "Compliance Only";
                }

                if(!$bTagSmartyValidated)
                {
                    $aTags["InvalidatedAddress"] = "Invalidated Address";
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

    private function addEntityToTemp($iEntityId,$bContactDone=true,$bAttachmentDone=true)
    {
        $this->load->model("Tempmeta_model");
        $today = date("Y-m-d");
        $aDataEntity = [
            "id"                =>  (string)$iEntityId,
            "entity_name"       =>  $this->input->post("inputName"),
            "entity_structure"  =>  $this->input->post("inputFillingStructure"),
            "filing_state"      =>  $this->input->post("inputFillingState"),
            "formation_date"    => $this->input->post("inputFormationDate"),
            "shipping_street"           =>  $this->input->post("inputNotificationAddress"),
            "shipping_city"              =>  $this->input->post("inputNotificationCity"),
            "shipping_state"             =>  $this->input->post("inputNotificationState"),
            "shipping_code"          =>  $this->input->post("inputNotificationZip"),
            "notification_email"    =>  $this->input->post("inputNotificationEmail"),
            "agent"=>[
                        "file_as"=>"United Agent Services LLC",
                        "address"=>"1729 W. Tilghman Street",
                        "address2"=>"Suite 2",
                        "city"=>"Allentown",
                        "state"=>"PA",
                        "zip_code"=>"18104"
                    ],
        ];
        $this->Tempmeta_model->appendRow($this->session->user['zohoId'],$this->Tempmeta_model->slugNewEntity,$aDataEntity);
        
        if($bAttachmentDone){
            $aDataAttachments = [
                "file_name"=>$this->input->post("attachment"),
                "size"=>filesize(getenv("UPLOAD_PATH").$this->input->post("attachment")),
                "create_time"=>$today,
                "link_url"=>getenv("UPLOAD_PATH").$this->input->post("attachment"),// it helps user to download file from temp path
            ];
            $this->Tempmeta_model->appendRow($iEntityId,$this->Tempmeta_model->slugNewAttachment,$aDataAttachments);
        }

        if($bContactDone)
        {
            $aDataContacts = [
                "first_name"    =>  $this->input->post("inputFirstName"),
                "last_name"    =>  $this->input->post("inputLastName"),

                "email"    =>  $this->input->post("inputNotificationEmail"),
                "phone"    =>  $this->input->post("inputNotificationPhone"),
                "title"    =>  "",

                "mailing_street"    =>  $this->input->post("inputNotificationAddress"),
                "mailing_city"    =>  $this->input->post("inputNotificationCity"),
                "mailing_state"    =>  $this->input->post("inputNotificationState"),
                "mailing_zip"    =>  $this->input->post("inputNotificationZip"),
            ];

            $this->Tempmeta_model->appendRow($iEntityId,$this->Tempmeta_model->slugNewContact,$aDataContacts);
        }
    }

}