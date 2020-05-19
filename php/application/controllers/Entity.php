<?php

// use Src\Services\OktaApiService as Okta;
header('Access-Control-Allow-Origin: *');

use zcrmsdk\crm\crud\ZCRMTag;

defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;


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
            $this->session->set_flashdata("error", "Invalid entity id");
            redirectSession();
        }

        //$this->load->model('ZoHo_Account');
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

            $oTempAgetAddress = null;
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
                    $oTempAgetAddress = $data['entity']->agent;
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
                } else if (is_object($oTempAgetAddress)) {
                    $data['registerAgent'] = (array)$oTempAgetAddress;
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
            $this->response([
                'errors' => ['status' => 404, 'detail' => 'Record not found']
            ], 404);
        }
        $this->response([
            'errors' => ['status' => 404, 'detail' => 'This will never appear']
        ], 404);

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

        $this->checkPermission("ADD",$this->sModule);

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

        $this->form_validation->set_rules('inputFirstName', 'First Name', 'required|regex_match[/[a-zA-Z\s]+/]', ["regex_match" => "Only alphabets and spaces allowed."]);
        $this->form_validation->set_rules('inputLastName', 'Last Name', 'required|regex_match[/[a-zA-Z\s]+/]', ["regex_match" => "Only alphabets and spaces allowed."]);


        $this->form_validation->set_rules('inputNotificationContactType', 'Contact Type', 'required|alpha');
        $this->form_validation->set_rules('inputFillingState', 'Filing State', 'required|alpha|exact_length[2]');
        $this->form_validation->set_rules('inputFillingStructure', 'Entity Type', 'required|regex_match[/[A-Z\-]+/]');
        $this->form_validation->set_rules('inputFormationDate', 'Formation Date', 'required|regex_match[/[0-9]{4,}\-[0-9]{2,}\-[0-9]{2,}/]', ["regex_match" => "Allowed %s format: 2019-01-01"]);
        $this->form_validation->set_rules('inputNotificationEmail', 'Notification Email', 'required|valid_email');
        $this->form_validation->set_rules('inputNotificationPhone', 'Phone', 'required|regex_match[/[\+\s\-0-9]+/]');
        $this->form_validation->set_rules('inputNotificationAddress', 'Shipping Street', 'required');
        $this->form_validation->set_rules('inputNotificationCity', 'Shipping City', 'required');
        $this->form_validation->set_rules('inputNotificationState', 'Shipping State', 'required');
        $this->form_validation->set_rules('inputNotificationZip', 'Shipping Code', 'required');
        $this->form_validation->set_rules('inputBusinessPurpose', 'Business purpose', 'required');

        $this->load->model("Smartystreets_model");
        // to avoid smartystreet buffer bug
        // retrying 0, 1, 2 which disrupst the api response
        if ($this->form_validation->run() !== FALSE) {
            $oSmartyStreetResponse = $this->Smartystreets_model->find(
                $this->input->post("inputNotificationAddress"),
                $this->input->post("inputNotificationCity"),
                $this->input->post("inputNotificationState"),
                $this->input->post("inputNotificationZip")
            );
            // to hold smarty address corrections
            $sSmartyAddress = "";
            // check wether invalid address entered the 1st time
            if ($oSmartyStreetResponse['type'] == 'error' && !$this->session->invalid_address_count) {
                $arError[] = "Unable to validate your address, please recheck and confirm ...";
                $this->session->invalid_address_count = 1;

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
            }
            //var_dump($this->session->invalid_address_count);die;
        }
        // allow without file, else check type and size
        if ($_FILES["inputFiling"]["name"] != "") {
            $this->form_validation->set_rules('inputFiling', 'Filing Attachment',
                array(
                    array('validate_extention', function () {
                        return validateFileExt($_FILES['inputFiling']['tmp_name'], ['application/pdf']);
                    }),
                    array('validate_size', function () {
                        return validateFileSize($_FILES['inputFiling']['tmp_name'], 10 * 1000 * 1000);
                    }),
                ),
                array(
                    'validate_extention' => 'Only MIME .pdf files are allowed',
                    'validate_size' => 'Input file: ' . getFileSize(filesize($_FILES['inputFiling']['tmp_name'])) . ' exceeding limit of 10MB.'
                )
            );
        }


        if ($this->form_validation->run() == FALSE) {
            if (count($arError) > 0) {
                $this->session->set_flashdata("error", $arError[0]);
            }
            $aError = $this->form_validation->error_array();

            $this->response([
                'status' => false,
                'field_error' => $aError
            ], 404);


            //$this->form();

            return false;

        } else {
            $_POST['inputFormationDate'] = date("Y-m-d", strtotime($this->input->post("inputFormationDate")));
            $_POST['inputFiscalDate'] = date("Y-m-d", strtotime($this->input->post("inputFiscalDate")));

            $response = $this->zohoCreateEntity($this->input->post['pid'], $bTagSmartyValidated);

            // succcess redirect to dashboard
            if ($response["type"] == 'ok') {
                $this->session->set_flashdata("ok", $response["message"]);

                // allow without file, else check type and size
                $iZohoId = $response['data']['id'];
                //$this->zohoAddAttachment($iZohoId);

                // check address is valid
                $sSmartyAddress = $this->validateSmartyStreet();

                // add a note if smarty validated address successfuly
                if ($sSmartyAddress != '') {
                    $response = $this->ZoHo_Account->newZohoNote("Accounts", $response['data']['id'], "Smartystreet has replaced following", $sSmartyAddress);
                }

                $this->addPermission($response['data']['id']);

                $this->redirectAfterAdd($response['data']['id']);

                // redirect to form, show error
            } else if ($response["error_code"] == 2) {
                $this->session->set_flashdata("error", $response["error"]);
                $this->redirectAfterAdd($response['data']['id']);
            } else {
                $this->session->set_flashdata("error", $response["error"]);


                $this->response([
                    'status' => false,
                    'error' => $response['error']
                ], 400);


            }

        }
    }

    private function redirectAfterAdd($id = 0)
    {
        $this->response([
            'status' => true,
            'id' => $id,
            'message' => 'Added successfully.'
        ], 200);
    }

    private function zohoCreateEntity($iParentZohoId, $bTagSmartyValidated)
    {

        $arError = array();
        $iErrorType = 1;// 1 means user creation failed, 2 means only attachment failed
        $this->load->model('ZoHo_Account');
        $this->load->model("entity_model");
        $this->load->model("RegisterAgents_model");

        $iZohoId = $this->zohoAddEntity();

        if ($iZohoId > 0) {
            // setting 2, so error only reports attachment issue
            $iErrorType = 2;
            $bAttachmentDone = $bContactDone = false;
            $oAttachment = null;

            $bAttachmentDone = $this->zohoAddAttachment($iZohoId);

            $bContactDone = $this->zohoAddContact();

            $this->addEntityToTemp($iZohoId, $bContactDone, $bAttachmentDone);
            // add row to subscription
            $this->addSubscription($iZohoId);

            // add tags
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
            } catch (Exception $e) {
                if (count($arError) > 0) $arError[0] .= ", tags failed (" . $e->getMessage() . ").";
                else $arError[] = "User created successfully, tags failed (" . $e->getMessage() . ").";
            }
        }
        //var_dump($arError);die;
        if (count($arError) > 0) {
            return ['error' => $arError[0], 'error_code' => $iErrorType];
        }

        return ['type' => 'ok', 'message' => "Entity created successfully.", "data" => ['id' => $iZohoId]];
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

    private function addEntityToTemp($iEntityId, $bContactDone = true, $bAttachmentDone = true)
    {
        $this->load->model("Tempmeta_model");
        $today = date("Y-m-d");
        $aDataEntity = [
            "id" => (string)$iEntityId,
            "account_name" => $this->input->post("inputName"),
            "entity_type" => $this->input->post("inputFillingStructure"),
            "filing_state" => $this->input->post("inputFillingState"),
            "formation_date" => $this->input->post("inputFormationDate"),
            "fiscal_date" => $this->input->post("inputFiscalDate"),
            "shipping_street" => $this->input->post("inputNotificationAddress"),
            "shipping_city" => $this->input->post("inputNotificationCity"),
            "shipping_state" => $this->input->post("inputNotificationState"),
            "shipping_code" => $this->input->post("inputNotificationZip"),
            "notification_email" => $this->input->post("inputNotificationEmail"),
            "agent" => [
                "file_as" => "United Agent Services LLC",
                "address" => "1729 W. Tilghman Street",
                "address2" => "Suite 2",
                "city" => "Allentown",
                "state" => "PA",
                "zip_code" => "18104"
            ],
        ];
        $this->Tempmeta_model->appendRow($this->session->user['zohoId'], $this->Tempmeta_model->slugNewEntity, $aDataEntity);
        // attachment is not needed because lorax table storing attachments data
        /*
        if ($bAttachmentDone) {
            $aDataAttachments = [
                "file_name" => $this->input->post("attachment"),
                "size" => filesize(getenv("UPLOAD_PATH") . $this->input->post("attachment")),
                "create_time" => $today,
                "link_url" => getenv("UPLOAD_PATH") . $this->input->post("attachment"),// it helps user to download file from temp path
            ];
            $this->Tempmeta_model->appendRow($iEntityId, $this->Tempmeta_model->slugNewAttachment, $aDataAttachments);
        }*/

        if ($bContactDone) {
            $aDataContacts = [
                "name" => $this->input->post("inputFirstName") . " " . $this->input->post("inputLastName"),
                "email" => $this->input->post("inputNotificationEmail"),
                "phone" => $this->input->post("inputNotificationPhone"),
                "contactType" => $this->input->post("inputNotificationContactType"),

                "mailingStreet" => $this->input->post("inputNotificationAddress"),
                "mailingCity" => $this->input->post("inputNotificationCity"),
                "mailingState" => $this->input->post("inputNotificationState"),
                "mailingZip" => $this->input->post("inputNotificationZip"),
            ];

            $this->Tempmeta_model->appendRow($iEntityId, $this->Tempmeta_model->slugNewContact, $aDataContacts);
        }
    }

    public function getChildAccount($iParentId = 0)
    {
        $this->load->model("entity_model");
        $this->load->model("Tempmeta_model");

        $aColumns = getInputFields();

        $aDataChild = $this->entity_model->getChildAccounts($iParentId, $aColumns);

        $aMyData = $aDataChild['results'];

        $aOutData = ["data" => $aMyData];
        responseJson($aOutData);
    }

    private function addPermission($iEntityId)
    {
        if ($iEntityId > 0) {
            $this->load->model("Permissions_model");
            $this->Permissions_model->add($iEntityId, $this->Permissions_model->aRole['entity']);
        }
    }

    public function zohoAddContact()
    {
        $arError = [];

        $aResponse = $this->ZoHo_Account->newZohoContact(
            $this->input->post("inputName"),
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

        return true;
    }

    private function zohoAddAttachment($iEntityId)
    {
        $bAttachmentDone = false;
        $sError = "File upload failed, please contact administrator...";
        $this->load->model("LoraxAttachments_model");
        $aData = [
            'entity_id'   =>  $iEntityId,
            'file_id'   =>  $this->input->post('inputFileId'),
            'name'  =>  $this->input->post('inputFileName')
        ];

        $id = $this->LoraxAttachments_model->insert($aData);
        
        if($id>0)
        {
            $bAttachmentDone = true;
        }
        /*
        // select entity instance
        $oApi = $this->ZoHo_Account->getInstance("Accounts", $iEntityId);

        // if file is give for attachment
        if ($_FILES['inputFiling']["name"] != "") {
            $sFilename = time() . "-" . $_FILES['inputFiling']['name'];
            $_POST['attachment'] = $sFilename;
            // move uploaded file to local
            if (move_uploaded_file($_FILES['inputFiling']['tmp_name'], getenv("UPLOAD_PATH") . $sFilename)) {
                try {
                    $oAttachment = $oApi->uploadAttachment($_SERVER['DOCUMENT_ROOT'] . "/" . getenv("UPLOAD_PATH") . $sFilename); // $filePath - absolute path of the attachment to be uploaded.
                    //$oAttachmentResponse = $oAttachment->getDetails();
                    $bAttachmentDone = true;
                } catch (Exception $e) {
                    $sError = "code: " . $e->getCode() . ", error: Api: " . $e->getMessage();
                    $bAttachmentDone = false;
                }

                // TODO: remove uploaded file from directory getenv("UPLOAD_PATH")
            } else {
                $sError = "User created successfully, Internal Server Error: file upload failed.";
            }
        }
        */
        if (!$bAttachmentDone) {
            return ['status' => '500', 'detail' => $sError];
        }

        return $bAttachmentDone;
    }

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
            $oApi->setFieldValue("RA", "");
            //$oApi->setFieldValue("Parent_Account", "3743841000000633019");
            //$oApi->setFieldValue("Layout", "4071993000001376034");// for customer layout id = Customer
        } else {
            // push the RA id data to zoho
            $oApi->setFieldValue("RA", $iRAId);
            $oApi->setFieldValue("Parent_Account", $iParentZohoId);
            $oApi->setFieldValue("Layout", "4071993000001376034");// for customer layout id = Customer
        }
        $oApi->setFieldValue("Account_Type", "Distributor");
        $oApi->setFieldValue("status", "InProcess");

        $oLoginUser = $this->entity_model->getOne(getenv("SUPER_ZOHO_ID"));
        $oLoginUser = $oLoginUser['results'];
        if ($oLoginUser->id) {
            // billing info using entity profile
            $oApi->setFieldValue("Billing_City", $oLoginUser->billing_city);
            $oApi->setFieldValue("Billing_Code", $oLoginUser->billing_code);
            $oApi->setFieldValue("Billing_Country", "US");
            $oApi->setFieldValue("Billing_State", $oLoginUser->billing_state);
            $oApi->setFieldValue("Billing_Street", $oLoginUser->billing_street);
            $oApi->setFieldValue("Billing_Street_2", $oLoginUser->billing_street_2);
        } else {
            $sError = "Billing addresses failed";
        }

        $trigger = array();//triggers to include
        $lar_id = "";//lead assignment rule id

        $oResponse = null;
        try {
            // setting trigger and $lar_id causing issue, api url not correct
            $responseIns = $oApi->create();//$trigger , $larid optional

            $oResponse = $responseIns->getDetails();

            return $oResponse["id"];
        } catch (Exception $oError) {
            // message
            $sError = "code: " . $oError->data[0]->code . ", error: Api: " . $oError->data[0]->details->expected_data_type . ", " . $oError->data[0]->details->api_name . ", message: " . $oError->getMessage();
        }

        return $sError;
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
}


