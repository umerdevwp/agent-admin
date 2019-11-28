<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Entity extends CI_Controller {

	public function index()
	{
        $this->load->view('header');
		$this->load->view('entity');
        $this->load->view('footer');
    }
    
    public function form($id=0)
    {
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
        $this->load->helper("custom");
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('inputName', 'Account Name', 'required|regex_match[/[a-zA-Z\s]+/]',["regex_match"=>"Only alphabets and spaces allowed."]);
        $this->form_validation->set_rules('inputFillingState', 'Filing State', 'required|alpha');
        $this->form_validation->set_rules('inputFillingStructure', 'Entity Type', 'required|regex_match[/[A-Z\-]+/]');
        $this->form_validation->set_rules('inputFormationDate', 'Formation Date');//, 'regex_match[/[0-9]+\-[0-9]+\-[0-9]+/]',["regex_match"=>"Allowed %s format: 01-01-2019"]);
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

        $oApi = $this->ZoHo_Account->getInstance()->getRecordInstance("Accounts",null);
        
        //$_POST["inputName"] = "Jhon9";
        //$_POST["inputFormationDate"] = "20-Apr-2018 16:40:05";
        $oApi->setFieldValue("Account_Name", $this->input->post("inputName")); // This function use to set FieldApiName and value similar to all other FieldApis and Custom field
        $oApi->setFieldValue("Filing_State", $this->input->post("inputFillingState")); // Account Name can be given for a new account, account_id is not mandatory in that case
        $oApi->setFieldValue("Entity_Type", $this->input->post("inputFillingStructure")); // Account Name can be given for a new account, account_id is not mandatory in that case
        
        if(!empty($this->input->post("inputFormationDate"))){
            $oApi->setFieldValue("Formation_Date",$this->input->post("inputFormationDate"));
        }
        // fields should exist in the crm admin
        //$oApi->setFieldValue("firstName",$this->input->post("inputFirstName"));
        //$oApi->setFieldValue("lastName",$this->input->post("inputLastName"));
        
        $oApi->setFieldValue("Notification_Email",$this->input->post("inputNotificationEmail"));
        $oApi->setFieldValue("Phone",$this->input->post("inputNotificationPhone"));
        $oApi->setFieldValue("Shipping_Street",$this->input->post("inputNotificationAddress"));
        $oApi->setFieldValue("Shipping_City",$this->input->post("inputNotificationCity"));
        $oApi->setFieldValue("Shipping_State",$this->input->post("inputNotificationState"));
        $oApi->setFieldValue("Shipping_Code",$this->input->post("inputNotificationZip"));
        $oApi->setFieldValue("Business_purpose",$this->input->post("inputBusinessPurpose"));

        $oApi->setFieldValue("Parent_Account",$iParentZohoId);

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
            //$oAttachmentResponse = array();
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
                } else {
                    $arError[] = "User created successfully, Internal Server Error: file upload failed.";
                }
            }

            /*
            echo "HTTP Status Code:" . $responseIns->getHttpStatusCode(); // To get http response code
            echo "Status:" . $responseIns->getStatus(); // To get response status
            echo "Message:" . $responseIns->getMessage(); // To get response message
            echo "Code:" . $responseIns->getCode(); // To get status code
            echo "Details:" . json_encode($responseIns->getDetails());*/
        }
        /*
        var_dump($oAttachmentResponse);
        echo "-- previous responds ---";
        var_dump($oResponse);
        echo "-- errors -- ";
        var_dump($arError);
        */
        if(count($arError)>0)
        {
            return ['error'=>$arError[0],'error_code'=>$iErrorType];
        }

        return ['ok'=>"Entity created successfully."];
    }
}

/*
 {
  "code": 0,
  "message": "The contact has been added.",
  "contact": {
  "contact_id": "478XXXXXXXXXXX001",
  "contact_name": "Shawn",
  "company_name": "",
  "first_name": "",
  "last_name": "",
  "designation": "",
  "department": "",
  "website": "",
  "language_code": "",
  "language_code_formatted": "",
  "contact_salutation": "",
  "email": "",
  "phone": "",
  "mobile": "",
  "portal_status": "disabled",
  "is_client_review_asked": false,
  "has_transaction": false,
  "contact_type": "customer",
  "customer_sub_type": "business",
  "owner_id": "",
  "owner_name": "",
  "source": "api",
  "documents": [
  ],
  "twitter": "",
  "facebook": "",
  "is_crm_customer": false,
  "is_linked_with_zohocrm": false,
  "primary_contact_id": "",
  "zcrm_account_id": "",
  "zcrm_contact_id": "",
  "crm_owner_id": "",
  "payment_terms": 0,
  "payment_terms_label": "Due On Receipt",
  "credit_limit_exceeded_amount": 0.0,
  "currency_id": "478XXXXXXXXXXX099",
  "currency_code": "INR",
  "currency_symbol": "Rs.",
  "price_precision": 2,
  "exchange_rate": "",
  "can_show_customer_ob": true,
  "can_show_vendor_ob": true,
  "opening_balance_amount": 0.0,
  "opening_balance_amount_bcy": "",
  "outstanding_ob_receivable_amount": 0.0,
  "outstanding_ob_payable_amount": 0.0,
  "outstanding_receivable_amount": 0.0,
  "outstanding_receivable_amount_bcy": 0.0,
  "outstanding_payable_amount": 0.0,
  "outstanding_payable_amount_bcy": 0.0,
  "unused_credits_receivable_amount": 0.0,
  "unused_credits_receivable_amount_bcy": 0.0,
  "unused_credits_payable_amount": 0.0,
  "unused_credits_payable_amount_bcy": 0.0,
  "unused_retainer_payments": 0.0,
  "status": "active",
  "payment_reminder_enabled": true,
  "is_sms_enabled": true,
  "is_client_review_settings_enabled": false,
  "custom_fields": [
  ],
  "custom_field_hash": {
  },
  "contact_category": "",
  "sales_channel": "direct_sales",
  "ach_supported": false,
  "billing_address": {
  "address_id": "478XXXXXXXXXXX003",
  "attention": "",
  "address": "",
  "street2": "",
  "city": "",
  "state_code": "",
  "state": "",
  "zip": "",
  "country": "",
  "phone": "",
  "fax": ""
  },
  "shipping_address": {
  "address_id": "478XXXXXXXXXXX005",
  "attention": "",
  "address": "",
  "street2": "",
  "city": "",
  "state_code": "",
  "state": "",
  "zip": "",
  "country": "",
  "phone": "",
  "fax": ""
  },
  "contact_persons": [
  ],
  "addresses": [
  ],
  "pricebook_id": "",
  "pricebook_name": "",
  "default_templates": {
  "invoice_template_id": "",
  "invoice_template_name": "",
  "bill_template_id": "",
  "bill_template_name": "",
  "estimate_template_id": "",
  "estimate_template_name": "",
  "creditnote_template_id": "",
  "creditnote_template_name": "",
  "purchaseorder_template_id": "",
  "purchaseorder_template_name": "",
  "salesorder_template_id": "",
  "salesorder_template_name": "",
  "paymentthankyou_template_id": "",
  "paymentthankyou_template_name": "",
  "invoice_email_template_id": "",
  "invoice_email_template_name": "",
  "estimate_email_template_id": "",
  "estimate_email_template_name": "",
  "creditnote_email_template_id": "",
  "creditnote_email_template_name": "",
  "purchaseorder_email_template_id": "",
  "purchaseorder_email_template_name": "",
  "salesorder_email_template_id": "",
  "salesorder_email_template_name": "",
  "paymentthankyou_email_template_id": "",
  "paymentthankyou_email_template_name": "",
  "payment_remittance_email_template_id": "",
  "payment_remittance_email_template_name": ""
  },
  "associated_with_square": false,
  "cards": [
  ],
  "checks": [
  ],
  "bank_accounts": [
  ],
  "vpa_list": [
  ],
  "notes": "",
  "created_time": "2019-08-29T15:51:04+0530",
  "last_modified_time": "2019-08-29T15:51:04+0530",
  "tags": [
  ],
  "zohopeople_client_id": ""
  },
  "instrumentation": {
  "query_execution_time": 59,
  "request_handling_time": 1049,
  "response_write_time": 82,
  "page_context_write_time": 0
  }
  }

    /* -----------------------------------------------
[ { "recordId": 173907000000181100,
"Date of joining": "",
"Employee Role": "Team member",
"Work phone": "",
"AbouMe": "",
"EmployeeID": "123",
"Extension": "",
"Nick Name": "",
"ID Proof": "",
"Department": "",
"Work location": "",
"createdTime": 1351588396243,
"modifiedTime": 1351588396243,
"Job Description": "",
"Employee Type": "",
"Mobile Phone": "",
"Photo": "",
"Title": "",
"Marital Status": "",
"Tags": "",
"Source of Hire": "",
"Offer Letter": "",
"Birth Date": "", "Address": "", "Reporting To": "", "Ask me about/Expertise": "", "Employee Status": "Active",
"Other Email": "",
"Email ID": "",
"Last Name": "",
"First Name": "dummy123" },
{ "recordId": 173907000000111000,
"Date of joining": "19-Nov-2016",
"Employee Role": "Team member",
"Work phone": "",
"AbouMe": "",
"EmployeeID": "HRM3",
"Extension": "",
"Nick Name": "",
"ID Proof": "" } ]
*/