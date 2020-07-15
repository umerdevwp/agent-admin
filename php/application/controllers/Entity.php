<?php

// use Src\Services\OktaApiService as Okta;
header('Access-Control-Allow-Origin: *');

use zcrmsdk\crm\crud\ZCRMTag;

defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use zcrmsdk\crm\exception\ZCRMException;

include APPPATH . '/libraries/CommonDbTrait.php';

class Entity extends RestController
{
    use CommonDbTrait;

    private $sModule = "ENTITY";

    public function __construct()
    {
        parent::__construct();
        $this->load->helper("custom");
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }


    }

    public function entityview_get()
    {
        $this->checkPermission("VIEW", $this->sModule);

        $sid = $_SESSION["eid"];

        if (empty($sid)) {
            responseJson(['type'=>'error','message'=>'Login is not define']);           
        }

        $this->load->model('entity_model');
        $this->load->model('Tasks_model');
        $this->load->model('Contacts_model');
        $this->load->model('LoraxAttachments_model');
        $this->load->model("Tempmeta_model");
        $this->load->model("RegisterAgents_model");

        // use login entity id
        $iParentId = ($this->input->get("eid") ? $sid : null);

        // entity id is session id, when requesting as parent
        $id = $this->input->get("eid") ?: $sid;

        $aColumns = getInputFields();
        $bIsParentValid = $this->entity_model->isParent($id, $iParentId);
        $aDataTempEntity = null;

        if (!$bIsParentValid) {
            $aDataTempEntity = $this->Tempmeta_model->getOneInJson([
                'userid' => $iParentId,
                'json_id' => $id,
                'slug' => $this->Tempmeta_model->slugNewEntity
            ]);
            if ($aDataTempEntity['type'] == 'ok')
                $bIsParentValid = true;
        }

        // if session is parent then get entity ID from url
        if ($bIsParentValid || $iParentId == null) {
            // fetch data from DB
            $aDataEntity = $this->entity_model->getOne($id, $aColumns);

            if ($aDataEntity['type'] == 'error' && $iParentId > 0) {
                if (!is_array($aDataTempEntity)) {
                    $aDataTempEntity = $this->Tempmeta_model->getOneInJson([
                        'userid' => $iParentId,
                        'json_id' => $id,
                        'slug' => $this->Tempmeta_model->slugNewEntity
                    ]);
                }
                if ($aDataTempEntity['type'] == 'ok') {
                    $data['entity'] = $aDataTempEntity['results'];
                }
            } else if ($aDataEntity['type'] == 'ok') {
                $data['entity'] = $aDataEntity['results'];
            }

            // data found in zoho_accounts or tempmeta table, then proceed
            if (is_object($data['entity'])) {
                //$oAgetAddress = $this->entity_model->getAgentAddress($id);
                $oAgetAddress = $this->RegisterAgents_model->getOne($data['entity']->agentId);
                $oAgetAddress = $oAgetAddress['results'];

                if (is_object($oAgetAddress)) {
                    $data['registerAgent'] = (array)$oAgetAddress;
                } else {
                    $data['registerAgent'] = [];
                }

                $data['tasks'] = $this->Tasks_model->getAll($id);

                $aTasksCompleted = $this->Tempmeta_model->getOne($id, $this->Tempmeta_model->slugTasksComplete);

                if (is_object($aTasksCompleted['results']))
                    $data['tasks_completed'] = json_decode($aTasksCompleted['results']->json_data);
                else
                    $data['tasks_completed'] = [];

                $contact_data = $this->Contacts_model->getAllFromEntityId($id);
                $aContactMeta = $this->Tempmeta_model->getOne($id, $this->Tempmeta_model->slugNewContact);

                $data['contacts'] = [];
                if ($contact_data['msg_type'] == 'error') {
                    if ($aContactMeta['type'] == 'ok')
                        $data['contacts'] = json_decode($aContactMeta['results']->json_data);
                } else {
                    $data['contacts'] = $contact_data;
                    if ($aContactMeta['results'] != null) $data['contacts'] = array_merge($data['contacts'], json_decode($aContactMeta['results']->json_data));
                }

                $aDataAttachment = $this->LoraxAttachments_model->getAllFromEntityId($id);

                if ($aDataAttachment['type'] == 'ok') {
                    $data['attachments'] = $aDataAttachment['results'];
                    $aDataAttachment = $this->Tempmeta_model->getOne($id, $this->Tempmeta_model->slugNewAttachment);
                } else {
                    $data['attachments'] = [];
                }

                // invalid id, record do not exist in zoho_accounts and tempmeta
                $this->response([
                    'result' => $data
                ], 200);
            }
        } else {
            if(!$bIsParentValid && $iParentId > 0)
            {
                $this->response([
                    'errors' => ['status' => 401, 'detail' => 'Invalid detail request']
                ], 401);
            } else {
                $this->response([
                    'errors' => ['status' => 404, 'detail' => 'Record not found']
                ], 404);
            }
        }

    }

    private function fetchTempDataOf($iEntityId, $sSlug)
    {
        $aTempRows = $this->Tempmeta_model->getOne($iEntityId, $sSlug);
        $aData = [];
        if ($aTempRows['type'] == 'ok') {
            $aData = json_decode($aTempRows['results']->json_data)[0];
        }

        return $aData;
    }

    public function form($id = 0)
    {
//        if (!isSessionValid("Entity_Add")) redirectSession();

        $this->load->library('form_validation');
        $this->load->model("entity_model");

        if ($id > 0) {
            $data = $this->entity_model->getOne($id);
            $data = $data['results'];
        }

        $this->load->view('header');
        $this->load->view('entity-add', $data);
        $this->load->view('footer');
    }

    public function create_post()
    {

        $this->checkPermission("ADD", $this->sModule);

        $bTagSmartyValidated = true;
        $aError = [];

        $aError = $this->validateForm(); 
        if (is_array($aError)) {
            
            $this->response([
                'status' => false,
                'field_error' => $aError
            ], 404);


            //$this->form();

            return false;

        } else {
            if(!empty($this->input->post('smartyValidated')))
                {
                $sSmartyAddress = <<<HC
                Street: {$this->input->post('smartyNotificationAddress')}
                City: {$this->input->post('smartyNotificationCity')}
                State: {$this->input->post('smartyNotificationState')}
                Zipcode: {$this->input->post('smartyNotificationZip')} 
HC;
            }
            $_POST['inputFormationDate'] = date("Y-m-d", strtotime($this->input->post("inputFormationDate")));
            $_POST['inputFiscalDate'] = date("Y-m-d", strtotime($this->input->post("inputFiscalDate")));

            $aResponseZoho = $this->zohoCreateEntity($_SESSION['eid'], $bTagSmartyValidated);

            // succcess redirect to dashboard
            if ($aResponseZoho["type"] == 'ok') {
                // allow without file, else check type and size
                $iZohoId = $aResponseZoho['id'];
                //$this->zohoAddAttachment($iZohoId);

                // check address is valid from smarty is not needed, validation is at interface
                $sSmartyAddress = '';//$this->validateSmartyStreet();
                
                // add a note if smarty validated address successfuly
                if ($sSmartyAddress != '') {
                    $aResponseNote = $this->ZoHo_Account->newZohoNote("Accounts", $iZohoId, "Smartystreet has replaced following", $sSmartyAddress);
                    if($aResponseNote['type']=='error')
                    {
                        $aResponseZoho['type'] = 'error';
                        if(!empty($aResponseZoho['message']))
                        $aResponseZoho['message'] .= "," . $aResponseNote['message'];
                        else
                        $aResponseZoho['message'] = $aResponseNote['message'];
                        
                    }
                }

                $this->addPermission($iZohoId);

                $this->redirectAfterAdd($aResponseZoho);

                // redirect to form, show error
            } else {
                $this->response([
                    'status' => false,
                    'error' => $aResponseZoho['message']
                ], 400);
            }

        }
    }

    private function validateForm()
    {
        $bTagSmartyValidated = true;
        $arError = [];
        $this->load->helper("custom");
        $this->load->library('form_validation');

        // try to correct user date format, then validate
        if ($this->input->post("inputFormationDate") != "") {
            $strFormationDate = str_replace("  ", " ", $this->input->post("inputFormationDate"));
            $strFormationDate = str_replace(" ", "-", $strFormationDate);
            $strFormationDate = date("Y-m-d", strtotime($strFormationDate));

            if ($strFormationDate == "1970-01-01") {
                //$_POST["inputFormationDate"] = "0000 00 00";
            } else {
                $_POST["inputFormationDate"] = $strFormationDate;
            }
        }

        $this->form_validation->set_rules('inputName', 'Account Name', 'required|regex_match[/[a-zA-Z\s]+/]', ["regex_match" => "Only alphabets and spaces allowed."]);

        $this->form_validation->set_rules('inputEIN', 'EIN', 'numeric|exact_length[9]',["numeric" => "Only numbers are allowed.","exact_length"=>"Must contain 9 digits"]);
        $this->form_validation->set_rules('inputFillingState', 'Filing State', 'required|alpha|exact_length[2]');
        $this->form_validation->set_rules('inputFillingStructure', 'Entity Type', 'required|regex_match[/[A-Z\-]+/]');
        $this->form_validation->set_rules('inputFormationDate', 'Formation Date', 'required|regex_match[/[0-9]{4,}\-[0-9]{2,}\-[0-9]{2,}/]', ["regex_match" => "Allowed %s format: 2019-01-01"]);
        $this->form_validation->set_rules('inputFiscalDate', 'Fiscal Date', 'required|regex_match[/[0-9]{4,}\-[0-9]{2,}\-[0-9]{2,}/]', ["regex_match" => "Allowed %s format: 2019-01-01"]);
        $this->form_validation->set_rules('inputNotificationEmail', 'Notification Email', 'required|valid_email');
        $this->form_validation->set_rules('inputNotificationPhone', 'Phone', 'required|regex_match[/[\+\s\-0-9]+/]');
        $this->form_validation->set_rules('inputNotificationAddress', 'Shipping Street', 'required');
        $this->form_validation->set_rules('inputNotificationCity', 'Shipping City', 'required');
        $this->form_validation->set_rules('inputNotificationState', 'Shipping State', 'required');
        $this->form_validation->set_rules('inputNotificationZip', 'Shipping Code', 'required|exact_length[5]',['exact_lengt'=>"Must contain 5 digits"]);
        $this->form_validation->set_rules('inputBusinessPurpose', 'Business purpose', 'required');

        if ($this->form_validation->run() === FALSE) {
            return $this->form_validation->error_array();
        } else {
            return true;
        }
    }

    /**
     * Handle API post request to edit entity details
     */
    public function edit_post()
    {
        $this->checkPermission("EDIT", $this->sModule);
        
        $this->load->model("Entity_model");

        // is user editing his child or own profile
        $bAllowEdit = $bValidateParent = false;

        // edit request is for child user? check valid parent
        if($_SESSION['eid']!=$this->input->post("eid"))
        $bValidateParent = $this->Entity_model->isParent($this->input->post("eid"),$_SESSION['eid']);
        
        // parent is valid
        if($bValidateParent)
            $bAllowEdit = true;
        // user editing own profile? allow edit
        else if($_SESSION['eid']==$this->input->post("eid"))
            $bAllowEdit = true;

        if($bAllowEdit)
        {
            // return true or error list
            $aError = $this->validateForm();
        
            if (is_array($aError)) {

                $this->response([
                    'status' => false,
                    'field_error' => $aError
                ], 404);

            } else {
                if(!empty($this->input->post('smartyNotificationAddress')))
                {
                $sSmartyAddress = <<<HC
                Street: {$this->input->post('smartyNotificationAddress')}
                City: {$this->input->post('smartyNotificationCity')}
                State: {$this->input->post('smartyNotificationState')}
                Zipcode: {$this->input->post('smartyNotificationZip')} 
HC;
            }
                $_POST['inputFormationDate'] = date("Y-m-d", strtotime($this->input->post("inputFormationDate")));
                $_POST['inputFiscalDate'] = date("Y-m-d", strtotime($this->input->post("inputFiscalDate")));

                $aResponse = $this->zohoEditEntity($this->input->post("eid"));

                // succcess redirect to dashboard
                if ($aResponse["entityId"] > 0) {
                    $aResponse['message'] = 'Edit successfully.';
                    $aResponse['id'] = $aResponse['entityId'];
                    
                    $this->redirectAfterAdd($aResponse);// $response['data']['id']);

                    // redirect to form, show error
                } else {

                    $this->response([
                        'status' => false,
                        'error' => $aResponse['message']
                    ], 400);


                }

            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Invalid edit request'
            ], 401);            
        }
    }

    private function redirectAfterAdd($aResponse)
    {
        if($aResponse['type']=='error')
        {
            $this->response([
                'status' => true,
                'id' => $aResponse['id'],
                'message' => $aResponse['message'],
            ], 200);
        } else {
            $this->response([
                'status' => true,
                'id' => $aResponse['id'],
                'message' => $aResponse['message']
            ], 200);
        }
    }

    private function zohoCreateEntity($iParentZohoId, $bTagSmartyValidated)
    {

        $aError = array();
        $iErrorType = 1;// 1 means user creation failed, 2 means only attachment failed
        $this->load->model('ZoHo_Account');
        $this->load->model("entity_model");
        $this->load->model("RegisterAgents_model");

        $aZohoResponse = $this->zohoAddEntity();
        $iZohoId = $aZohoResponse['entityId'];
        $iAgentId = $aZohoResponse['agentId'];

        if ($iZohoId > 0) {
            $aErrorAttachment = ['type'=>'ok'];
            if (!empty($this->input->post("inputFileId")))
                $aErrorAttachment = $this->zohoAddAttachment($iZohoId);
            // contact with entity is skipped 24/6/2020
            //$iContactId = $this->zohoAddContact($iZohoId);

            $this->addEntityToTemp($iZohoId, $iAgentId);
            // add row to subscription
            $iSubId = $this->addSubscription($iZohoId);
            // if subscription fail report is sent to logs, users are not informed at this point
            $aErrorTag = ['type'=>'ok'];
            // add tags
            if(!isDev())
            {
                $aErrorTag = $this->processTags($iZohoId,$bTagSmartyValidated);
            }
        } else {
            return ['type'=>'error', 'message' => $aZohoResponse['message']];
        }
        $aResponse = ['type' => 'ok', 'message' => "Entity created successfully.", 'id' => $iZohoId];
        
        if ($aErrorTag['type']=='error' && $aErrorAttachment['type']=='error') {
            $sErrorMessage = $aErrorTag['message'] . ", " . $aErrorAttachment['message'];
            $aResponse['message'] = $sErrorMessage;
        } else if($aErrorAttachment['type']=='error')
        {
            $aResponse['message'] = $aErrorAttachment['message'];
        } else if($aErrorTag['type']=='error')
        {
            $aResponse['message'] = $aErrorTag['message'];
        }

        return $aResponse;
    }
    
    private function processTags($iZohoId,$bTagSmartyValidated)
    {
        // no error occur, tags success
        $aResponse = [];

        $oApi = $this->ZoHo_Account->getInstance("Accounts", $iZohoId);
        try {
            $sComplianceOnly = ($this->input->post("inputComplianceOnly") ?? 0);
            $sForeign = ($this->input->post("inputForeign") ?? 0);

            $aTags = ["name" => "OnBoard"];

            if ($sComplianceOnly) {
                $aTags["ComplianceOnly"] = "Compliance Only";
            }

            if ($sForeign) {
                $aTags["Foreign"] = "Foreign";
            }

            if (!$bTagSmartyValidated) {
                $aTags["InvalidatedAddress"] = "Invalidated Address";
            }
            // TODO: zoho enable on production server

            $this->ZoHo_Account->zohoCreateNewTags($iZohoId, $aTags);

            $oResponseTags = $oApi->addTags($aTags);

            $oData = $oResponseTags->getData();
            $aResponse = ['type'=>'ok','message'=>'Tags added successfully'];
        } catch (Exception $e) {
            $aResponse = ['type'=>'error','message'=>"User created successfully, tags failed (" . $e->getMessage() . ")."];
        }

        return $aResponse;
    }

    private function addSubscription($iEntityId)
    {
        $this->load->model("Notifications_model");

        $this->load->library('session');

        $id = null;

        $oRule = $this->Notifications_model->getRules(
            $this->input->post("inputFillingState"),
            $this->input->post("inputFillingStructure"),
            $this->input->post("inputFormationDate"),
            $this->input->post("inputFiscalDate")
        );
        if (!empty($oRule->duedate)) {
            $aData = [
                "created_by" => userLoginId(),
                "entity_id" => $iEntityId,
                "due_date" => $oRule->duedate,
                "description" => "Starting subscription from registeration page",
                "start_date" => date("Y-m-d"),
                "type" => "email",
                "end_date" => date("Y-m-d", strtotime("+1 year")),
                "before_days" => 7,
                "before_months" => 1,
                "interval_days" => 0,
                "interval_months" => 2,
                "limit_notification" => 0,
                "status" => "active",
            ];

            $id = $this->Notifications_model->add($aData);
        } else {
            error_log("Subscription failed unable to find duedate "
                . " state: " . $this->input->post("inputFillingState")
                . " structure: " . $this->input->post("inputFillingStructure")
                . " formed: " . $this->input->post("inputFormationDate")
                . "fiscal: " . $this->input->post("inputFiscalDate")
            );
        }

        if (!is_numeric($id)) {
            error_log("Subscription failed to insert new entity record");
        }

        return $id;

    }

    private function addEntityToTemp($iEntityId, $iAgentId=0)
    {
        $this->load->model("Tempmeta_model");
        $today = date("Y-m-d");
        $aDataEntity = [
            "id" => (string)$iEntityId,
            "name" => $this->input->post("inputName"),
            "entityStructure" => $this->input->post("inputFillingStructure"),
            "filingState" => $this->input->post("inputFillingState"),
            "formationDate" => $this->input->post("inputFormationDate"),
            "fiscalDate" => $this->input->post("inputFiscalDate"),
            "shippingStreet" => $this->input->post("inputNotificationAddress"),
            "shippingCity" => $this->input->post("inputNotificationCity"),
            "shippingState" => $this->input->post("inputNotificationState"),
            "shippingCode" => $this->input->post("inputNotificationZip"),
            "email" => $this->input->post("inputNotificationEmail"),
            "type" => $this->input->post("inputNotificationContactType"),
            "agentId"   =>  $iAgentId,
            "parentId"  =>  $_SESSION['eid']
        ];
        $this->Tempmeta_model->appendRow($_SESSION['eid'], $this->Tempmeta_model->slugNewEntity, $aDataEntity);

        //$this->addContactToTemp($iContactId);
    }

    private function addContactToTemp($iEntityId,$iContactId=0)
    {
        if ($iContactId>0) {
            $this->load->model("Contacts_model");
            // $iContactId = $aResponse['results'];
            $data = [
                        "id" => $iContactId,
                        "owner" => $_SESSION['eid'],
                        "first_name"    =>  $this->input->post("inputFirstName"),
                        "last_name"    =>  $this->input->post("inputLastName"),
                        "full_name" => $this->input->post("inputFirstName") . " " . $this->input->post("inputLastName"),
                        "account_name" => $iEntityId,
                        "email"    =>  $this->input->post("inputNotificationEmail"),
                        "phone"    =>  $this->input->post("inputNotificationPhone"),
                        "title"    =>  $this->input->post("inputNotificationContactType"),
                        "mailing_street"    =>  $this->input->post("inputNotificationAddress"),
                        "mailing_city"    =>  $this->input->post("inputNotificationCity"),
                        "mailing_state"    =>  $this->input->post("inputNotificationState"),
                        "mailing_zip"    =>  $this->input->post("inputNotificationZip"),
                        "created_by" => $_SESSION['eid'],
                        "created_time" => date('Y-m-d H:i:s'),
                        "modified_time" => date('Y-m-d H:i:s'),
                        "last_activity_time" => date('Y-m-d H:i:s'),
                        "number_of_chats"=> '0',
                        "average_time_spent_minutes" => '0.00',
                        "days_visited" => '0',
                        "visitor_score" => '0'
            ];
        
            $response_contact = $this->Contacts_model->addContact($data);

        }
    }

    public function getChildAccount_get()
    {
        $this->checkPermission("ADD", $this->sModule);

        $this->load->model("entity_model");

        $this->load->model("Tempmeta_model");
        $iParentId = $_SESSION['eid'];

        $aColumns = getInputFields();

        $aDataChild = $this->entity_model->getChildAccounts($iParentId, $aColumns);
        $aDataTempEntity = $this->Tempmeta_model->getAll(
            $iParentId,
            $this->Tempmeta_model->slugNewEntity
        );

        if ($aDataTempEntity['type'] == 'ok')
            if (count($aDataTempEntity['results']) > 0) {
                $aNewDataChild = [];
                if (count($aDataChild['results']) > 0) {
                    $aNewDataChild = array_merge($aDataChild['results'], json_decode($aDataTempEntity['results'][0]['json_data']));
                } else {
                    $aNewDataChild = json_decode($aDataTempEntity['results'][0]['json_data']);
                }
                $aDataChild['results'] = $aNewDataChild;
            }

        $aMyData = $aDataChild;

        // remove parentid attr from active entity
        foreach($aMyData['results'] as $obj)
        {
            if($obj->id==$_SESSION['eid'])
            {
                unset($obj->parentId);
                break;
            }
        }

        $this->response([
            'status' => true,
            'data' => $aMyData
        ], 200);
    }

    private function addPermission($iEntityId)
    {
        if ($iEntityId > 0) {
            $this->load->model("Permissions_model");
            $this->Permissions_model->add($iEntityId, $this->Permissions_model->aRole['entity']);
        }
    }

    /**
     * Add zoho contact in the crm, populate all the fields from post request
     * @param Integer $iEntityId numeric id of related entity
     * @return Array Response from zoho api
     */
    public function zohoAddContact($iEntityId)
    {
        $arError = [];

        $aResponse = $this->ZoHo_Account->newZohoContact(
            $iEntityId,
            [
                "First_Name" => $this->input->post("inputFirstName"),
                "Last_Name" => $this->input->post("inputLastName"),

                "Email" => $this->input->post("inputNotificationEmail"),
                "Phone" => $this->input->post("inputNotificationPhone"),
                "Contact_Type" => $this->input->post("inputNotificationContactType"),

                "Mailing_Street" => $this->input->post("inputNotificationAddress"),
                "Mailing_City" => $this->input->post("inputNotificationCity"),
                "Mailing_State" => $this->input->post("inputNotificationState"),
                "Mailing_Zip" => $this->input->post("inputNotificationZip")
            ]
        );

        if ($aResponse['type'] == 'error') {
            error_log("Unknown  server error, contact creation failed.");
            return ['status' => '500', 'detail' => "Unknown server error, contact creation failed."];
        }

        return $aResponse['results'];
    }

    /**
     * Edit zoho contact related to profile in the crm, populate all the fields from post request
     * @param Integer $iContactId numeric id of related to entity
     * @return Array Response from zoho api
     */
    public function zohoEditContact($iContactId)
    {
        $arError = [];
        $this->load->model("ZoHo_Account");
        $aResponse = $this->ZoHo_Account->editZohoContact(
            $iContactId,
            [
                "First_Name" => $this->input->post("inputFirstName"),
                "Last_Name" => $this->input->post("inputLastName"),

                "Email" => $this->input->post("inputNotificationEmail"),
                "Phone" => $this->input->post("inputNotificationPhone"),
                "Contact_Type" => $this->input->post("inputNotificationContactType"),

                "Mailing_Street" => $this->input->post("inputNotificationAddress"),
                "Mailing_City" => $this->input->post("inputNotificationCity"),
                "Mailing_State" => $this->input->post("inputNotificationState"),
                "Mailing_Zip" => $this->input->post("inputNotificationZip")
            ]
        );

        if ($aResponse['type'] == 'error') {
            error_log("Unknown  server error, contact creation failed.");
            return $aResponse;
        }
        // id of the contact updated
        return $aResponse['id'];
    }

    private function zohoAddAttachment($iEntityId)
    {
        $bAttachmentDone = false;
        $sError = "File upload failed, please contact administrator...";
        $this->load->model("LoraxAttachments_model");
        $aData = [
            'entity_id' => $iEntityId,
            'file_id' => $this->input->post('inputFileId'),
            'name' => $this->input->post('inputFileName'),
            'file_size' => $this->input->post('inputFileSize'),
        ];

        $id = $this->LoraxAttachments_model->insert($aData);

        if ($id > 0) {
            $bAttachmentDone = true;
        }
        
        if (!$bAttachmentDone) {
            return ['type' => 'error', 'message' => $sError];
        }

        return ['type'=>'ok','id'=>$id];
    }

    /**
     * Allow editing zoho entity details based on provided id, by populating respective values
     */
    private function zohoEditEntity($iEid)
    {
        $iParentZohoId = $_SESSION['eid'];
        $this->load->model("ZoHo_Account");
        $this->load->model("RegisterAgents_model");

        $oApi = $this->ZoHo_Account->getInstance()->getRecordInstance("Accounts", $iEid);

        $oApi->setFieldValue("Account_Name", $this->input->post("inputName")); // This function use to set FieldApiName and value similar to all other FieldApis and Custom field
        $oApi->setFieldValue("Filing_State", $this->input->post("inputFillingState")); // Account Name can be given for a new account, account_id is not mandatory in that case
        $oApi->setFieldValue("Entity_Type", $this->input->post("inputFillingStructure")); // Account Name can be given for a new account, account_id is not mandatory in that case

        $oApi->setFieldValue("Formation_Date", $this->input->post("inputFormationDate"));

        // firstName, lastName fields going under contacts

        $oApi->setFieldValue("Notification_Email", $this->input->post("inputNotificationEmail"));
        $oApi->setFieldValue("Phone", $this->input->post("inputNotificationPhone"));
        $oApi->setFieldValue("Shipping_Street", $this->input->post("inputNotificationAddress"));
        $oApi->setFieldValue("Shipping_City", $this->input->post("inputNotificationCity"));
        $oApi->setFieldValue("Shipping_State", $this->input->post("inputNotificationState"));
        $oApi->setFieldValue("Shipping_Code", $this->input->post("inputNotificationZip"));
        $oApi->setFieldValue("Business_purpose", $this->input->post("inputBusinessPurpose"));
        $oApi->setFieldValue("EIN", $this->input->post("inputEIN"));

        // fetch RA (registered agent) id from DB
        $strFilingState = $this->input->post("inputFillingState");
        $row = $this->RegisterAgents_model->find(["name" => $strFilingState . " - UAS"]);
        $iRAId = "";
        if ($row->id > 0) {
            $iRAId = $row->id;
        }

        // additional detail as default values for new entity
        // tag call needs account id instance, added below after attachments
        if (isDev()) {
            $oApi->setFieldValue("RA", "3743841000000932064");//sandbox id
            //$oApi->setFieldValue("Parent_Account", "3743841000001424031");//sandbox id
            //$oApi->setFieldValue("Layout", "3743841000000983988");// sandbox id = Customer layout
        } else {
            // push the RA id data to zoho
            $oApi->setFieldValue("RA", $iRAId);
            // parent account not needed for edit
            $oApi->setFieldValue("Layout", "4071993000001376034");// for customer layout id = Customer
        }
        //$oApi->setFieldValue("Account_Type", "Distributor");
        //$oApi->setFieldValue("status", "InProcess");

        // Billing addresses not require in edit

        $trigger = array();//triggers to include
        $lar_id = "";//lead assignment rule id

        $oResponse = $aResponse = null;
        try {
            // setting trigger and $lar_id causing issue, api url not correct
            $responseIns = $oApi->update();//$trigger , $larid optional

            $oResponse = $responseIns->getDetails();

            $aResponse  = ['entityId'=>$oResponse['id'],'agentId'=>$iRAId];

        } catch (ZCRMException $oError) {
            // message
            $sError = "code: " . $oError->getCode() . ", api error: " . $oError->getExceptionCode() . ", name: " . $oError->getExceptionDetails()['api_name'] . ", message: " . $oError->getMessage();
            $aResponse = ['type'=>'error','message'=>$sError];
        }

        return $aResponse;
    }

    /**
     * Add entity into zoho crm, by populating respective values from post request
     */
    private function zohoAddEntity()
    {
        $iParentZohoId = $_SESSION['eid'];

        $oApi = $this->ZoHo_Account->getInstance()->getRecordInstance("Accounts", null);

        $oApi->setFieldValue("Account_Name", $this->input->post("inputName")); // This function use to set FieldApiName and value similar to all other FieldApis and Custom field
        $oApi->setFieldValue("Filing_State", $this->input->post("inputFillingState")); // Account Name can be given for a new account, account_id is not mandatory in that case
        $oApi->setFieldValue("Entity_Type", $this->input->post("inputFillingStructure")); // Account Name can be given for a new account, account_id is not mandatory in that case

        $oApi->setFieldValue("Formation_Date", $this->input->post("inputFormationDate"));

        // firstName, lastName fields going under contacts

        $oApi->setFieldValue("Notification_Email", $this->input->post("inputNotificationEmail"));
        $oApi->setFieldValue("Phone", $this->input->post("inputNotificationPhone"));
        $oApi->setFieldValue("Shipping_Street", $this->input->post("inputNotificationAddress"));
        $oApi->setFieldValue("Shipping_City", $this->input->post("inputNotificationCity"));
        $oApi->setFieldValue("Shipping_State", $this->input->post("inputNotificationState"));
        $oApi->setFieldValue("Shipping_Code", $this->input->post("inputNotificationZip"));
        $oApi->setFieldValue("Business_purpose", $this->input->post("inputBusinessPurpose"));
        $oApi->setFieldValue("EIN", $this->input->post("inputEIN"));

        // fetch RA (registered agent) id from DB
        $strFilingState = $this->input->post("inputFillingState");
        $row = $this->RegisterAgents_model->find(["name" => $strFilingState . " - UAS"]);
        $iRAId = "";
        if ($row->id > 0) {
            $iRAId = $row->id;
        }

        // additional detail as default values for new entity
        // tag call needs account id instance, added below after attachments
        if (isDev()) {
            $oApi->setFieldValue("RA", "3743841000000932064");//sandbox id
            $oApi->setFieldValue("Parent_Account", $_SESSION['eid']);//sandbox id
            $oApi->setFieldValue("Layout", "3743841000000983988");// sandbox id = Customer layout
        } else {
            // push the RA id data to zoho
            $oApi->setFieldValue("RA", $iRAId);
            $oApi->setFieldValue("Parent_Account", $iParentZohoId);
            $oApi->setFieldValue("Layout", "4071993000001376034");// for customer layout id = Customer
        }
        $oApi->setFieldValue("Account_Type", "Distributor");
        $oApi->setFieldValue("status", "InProcess");

        // get billing values of parent entity
        $aBillingColumns = ['id','billingCity','billingCode','billingState','billingStreet','billingStreet2','billingCountry'];
        $oParent = $this->entity_model->getOne($_SESSION['eid'],$aBillingColumns);
        $oParent = $oParent['results'];
        if ($oParent->id) {
            // set child entity billing fields to parent billing details
            $oApi->setFieldValue("Billing_City", $oParent->billingCity);
            $oApi->setFieldValue("Billing_Code", $oParent->billingCode);
            $oApi->setFieldValue("Billing_Country", "US");
            $oApi->setFieldValue("Billing_State", $oParent->billingState);
            $oApi->setFieldValue("Billing_Street", $oParent->billingStreet);
            $oApi->setFieldValue("Billing_Street_2", $oParent->billingStreet2);
        } else {
            error_log("Parent billing address not found for child profile");
        }

        $trigger = array();//triggers to include
        $lar_id = "";//lead assignment rule id

        $oResponse = $aResponse = null;
        try {
            // setting trigger and $lar_id causing issue, api url not correct
            $responseIns = $oApi->create();//$trigger , $larid optional

            $oResponse = $responseIns->getDetails();

            $aResponse  = ['entityId'=>$oResponse['id'],'agentId'=>$iRAId];

        } catch (ZCRMException $oError) {
            // message
            $sError = "code: " . $oError->getCode() . ", api error: " . $oError->getExceptionCode() . ", name: " . $oError->getExceptionDetails()['api_name'] . ", message: " . $oError->getMessage();
            $aResponse = ['type'=>'error','message'=>$sError];
        }

        return $aResponse;
    }

    private function validateSmartyStreet()
    {
        $bTagSmartyValidated = true;
        // to hold smarty address corrections
        $sSmartyAddress = "";

        $this->load->model("Smartystreets_model");

        $oSmartyStreetResponse = $this->Smartystreets_model->find(
            $this->input->post("inputNotificationAddress"),
            $this->input->post("inputNotificationCity"),
            $this->input->post("inputNotificationState"),
            $this->input->post("inputNotificationZip")
        );
        // check wether invalid address entered the 1st time
        if ($oSmartyStreetResponse['type'] == 'error' && !$this->session->invalid_address_count) {
            $arError[] = "Unable to validate your address, please recheck and confirm ...";
            $this->session->invalid_address_count = 1;
            $bTagSmartyValidated = false;
            // qualify address if invalid address entered 2nd time
        } else if ($oSmartyStreetResponse['type'] == 'error' && $this->session->invalid_address_count == 1) {
            $this->session->invalid_address_count = 0;
            $bTagSmartyValidated = false;

            // replace user address with validated smartystreet address
        } else {
            // store previous user input for entity note purpose
            $sSmartyAddress = <<<HC
                Street: {$this->input->post('inputNotificationAddress')}
                City: {$this->input->post('inputNotificationCity')}
                State: {$this->input->post('inputNotificationState')}
                Zipcode: {$this->input->post('inputNotificationZip')}
HC;
            $_POST['inputNotificationAddress'] = $oSmartyStreetResponse['results'][0]->getDeliveryLine1();
            $_POST['inputNotificationCity'] = $oSmartyStreetResponse['results'][0]->getComponents()->getCityName();
            $_POST['inputNotificationState'] = $oSmartyStreetResponse['results'][0]->getComponents()->getStateAbbreviation();
            $_POST['inputNotificationZip'] = $oSmartyStreetResponse['results'][0]->getComponents()->getZIPCode();
            $this->session->invalid_address_count = 0;
            $bTagSmartyValidated = false;
        }

        return $sSmartyAddress;
    }


    public function attachment_get($sLoraxFileId)
    {
        $this->load->model("Attachments_model");
        $this->Attachments_model->download($sLoraxFileId);
        $this->response([
            'status' => true,
            'message' => 'Download link of file'
        ], 200);
    }

    public function role_get()
    {
        $sid = $_SESSION["eid"];
        if (empty($sid)) {
            responseJson(['type'=>'error','message'=>'Login is not define']);
        }

        $this->load->model('entity_model');

        $aData = $this->entity_model->getRoleStatus($sid);
        
        $this->response([
            'status' => true,
            'data' => $aData
        ], 200);
    }
}


