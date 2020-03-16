<?php

// use Src\Services\OktaApiService as Okta;
header('Access-Control-Allow-Origin: *');

use chriskacerguis\RestServer\RestController;

class Entity_api extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_model');
        $this->load->model('Accounts_model');
        $this->load->model('Tasks_model');
        $this->load->model('Contacts_model');
        $this->load->model('Attachments_model');
        $this->load->model("Tempmeta_model");
        $this->load->model("Entity_model");
    }

    public function entity_get()
    {
        //initialize variables
        $checkSuperUser = '';
        $entityID = $this->get('entity');
        $zoho_id = $this->get('zoho_id');
        $email = $this->get('email');
//      check if the incoming entity id has the child or not.
//      hasEntities
        if (empty($entityID) or empty($zoho_id)) {
            $this->response([
                'status' => false,
                'message' => 'Zoho id or entity id of the user is missing.'
            ], 404);
        }


        //check if admin
        if (!empty($email)) {
            $checkSuperUser = $this->Admin_model->checkAdminExist($email);
        }
        $ownerValidity = $this->Entity_model->ownerValidity($zoho_id, $entityID);
        if (!empty($ownerValidity) or !empty($checkSuperUser)) {
            $entityData = $this->Accounts_model->loadAccount($entityID);
            $oTempAgetAddress = null;
            if ($entityData['type'] == 'error' && $this->session->user['child']) {
                $aDataTempEntity = $this->Tempmeta_model->getOneInJson([
                    'userid' => $zoho_id,
                    'json_id' => $entityID,
                    'slug' => $this->Tempmeta_model->slugNewEntity
                ]);
                if ($aDataTempEntity['type'] == 'ok') {
                    $data['entity'] = $aDataTempEntity['results'];
                    $oTempAgetAddress = $data['entity']->agent;

//                $this->session->set_flashdata("info", "Please note: missing fields will be updated shortly.");
                } else {
//                $this->session->set_flashdata("error", "No such entity exist.");
                }

            } else if ($entityData['type'] == 'ok') {
                $data['entity'] = $entityData['results'];
            } else {
//            $this->session->set_flashdata("error", "No such entity exist.");
            }


//          pull address for RA
            $oAgetAddress = $this->Accounts_model->getAgentAddress($entityID);
            if (is_object($oAgetAddress)) {
                $data['AgentAddress'] = (array)$oAgetAddress;
            } else if (is_object($oTempAgetAddress)) {
                $data['AgentAddress'] = (array)$oTempAgetAddress;
            } else {
                $data['AgentAddress'] = false;
            }

            $contact_data = $this->Contacts_model->getAllFromEntityId($entityID);
            if ($contact_data['msg_type'] == 'error') {
                $data['contacts'] = '';
            } else {
                $data['contacts'] = $contact_data;
            }

            $data['tasks'] = $this->Tasks_model->getAll($entityID);

            $aTasksCompleted = $this->Tempmeta_model->getOne($entityID, $this->Tempmeta_model->slugTasksComplete);
            if (is_object($aTasksCompleted['results']))
                $data['tasks_completed'] = json_decode($aTasksCompleted['results']->json_data);
            else
                $data['tasks_completed'] = [];

            $this->response([
                'count' => count($data),
                'result' => $data
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'The entity information does not belongs to you'
            ], 404);
        }

    }


    function entity_post()
    {
        $this->load->helper("custom");
        $this->load->library('form_validation');

        $this->load->helper("custom");
        $this->load->library('form_validation');
        $this->form_validation->set_rules('inputName', 'Account Name', 'required|regex_match[/[a-zA-Z\s]+/]', ["regex_match" => "Only alphabets and spaces allowed."]);

        $this->form_validation->set_rules('inputFirstName', 'First Name', 'required|regex_match[/[a-zA-Z\s]+/]', ["regex_match" => "Only alphabets and spaces allowed."]);
        $this->form_validation->set_rules('inputLastName', 'Last Name', 'required|regex_match[/[a-zA-Z\s]+/]', ["regex_match" => "Only alphabets and spaces allowed."]);

        $this->form_validation->set_rules('inputNotificationContactType', 'Contact Type', 'required|alpha');
        $this->form_validation->set_rules('inputFillingState', 'Filing State', 'required|alpha');
        $this->form_validation->set_rules('inputFillingStructure', 'Entity Type', 'required|regex_match[/[A-Z\-]+/]');
        $this->form_validation->set_rules('inputFormationDate', 'Formation Date', 'required|regex_match[/[0-9]{4,}\-[0-9]{2,}\-[0-9]{2,}/]', ["regex_match" => "Allowed %s format: 2019-01-01"]);
        $this->form_validation->set_rules('inputNotificationEmail', 'Notification Email', 'required|valid_email');
        $this->form_validation->set_rules('inputNotificationPhone', 'Phone', 'required|regex_match[/[\+\s\-0-9]+/]');
        $this->form_validation->set_rules('inputNotificationAddress', 'Shipping Street', 'required');
        $this->form_validation->set_rules('inputNotificationCity', 'Shipping City', 'required');
        $this->form_validation->set_rules('inputNotificationState', 'Shipping State', 'required');
        $this->form_validation->set_rules('inputNotificationZip', 'Shipping Code', 'required');
        $this->form_validation->set_rules('inputBusinessPurpose', 'Business purpose', 'required');


          $arError = [];
          // try to correct user date format, then validate
          if ($this->input->post("inputFormationDate") != "") {
              $strFormationDate = str_replace("  ", " ", $this->post("inputFormationDate"));
              $strFormationDate = str_replace(" ", "-", $strFormationDate);
              $strFormationDate = date("Y-m-d", strtotime($strFormationDate));

              if ($strFormationDate == "1970-01-01") {

              } else {
                  $_POST["inputFormationDate"] = $strFormationDate;
              }
          }

//
//        $_POST['inputNotificationAddress'] = $oSmartyStreetResponse['results'][0]->getDeliveryLine1();
//        $_POST['inputNotificationCity'] = $oSmartyStreetResponse['results'][0]->getComponents()->getCityName();
//        $_POST['inputNotificationState'] = $oSmartyStreetResponse['results'][0]->getComponents()->getStateAbbreviation();
//        $_POST['inputNotificationZip'] = $oSmartyStreetResponse['results'][0]->getComponents()->getZIPCode();



        //var_dump($this->session->invalid_address_count);die;

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

        $response = array();

        if ($this->form_validation->run() == FALSE || count($arError) > 0) {
            if (count($arError) > 0) {
                $this->session->set_flashdata("error", $arError[0]);
            }

            $this->form();

            return false;

        } else {

            $response = $this->zohoCreateEntity($this->session->user['zohoId'], $bTagSmartyValidated);
            // succcess redirect to dashboard
            if ($response["type"] == 'ok') {
                $this->session->set_flashdata("ok", $response["message"]);
                // add a note if smarty validated address successfuly
                if ($sSmartyAddress != '') {
                    $response = $this->ZoHo_Account->newZohoNote("Accounts", $response['data']['id'], "Smartystreet has replaced following", $sSmartyAddress);
                }
                $this->redirectAfterAdd();
                // redirect to form, show error
            } else if ($response["error_code"] == 2) {
                $this->session->set_flashdata("error", $response["error"]);
                $this->redirectAfterAdd();
            } else {
                $this->session->set_flashdata("error", $response["error"]);
                $this->form();
            }

        }
    }

}
