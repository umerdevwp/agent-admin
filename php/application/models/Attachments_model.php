<?php

use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\crm\crud\ZCRMModule;
use zcrmsdk\crm\crud\ZCRMRecord;

class Attachments_model extends CI_Model
{

    private $table = "zoho_attachments3";

    public function __construct()
    {
        $this->load->database();
    }

    public function getAllApi()
    {
        $configuration = [
			"client_id" => getenv("ZOHO_CLIENT_ID"),
            "client_secret" => getenv("ZOHO_CLIENT_SECRET"),
			"redirect_uri" => getenv("ZOHO_REDIRECT_URI"),
			"currentUserEmail"=> "cboyce@unitedagentservices.com",
			"token_persistence_path" => "zohoauth",
			"accounts_url" => getenv("ZOHO_ACCOUNTS_URL"),
		];

        ZCRMRestClient::initialize($configuration);

        //$rest = ZCRMRestClient::getInstance("Attachments"); // to get the rest client
        
/*        
        //$zcrmModuleIns = ZCRMModule::getInstance("Attachments");
        //$bulkAPIResponse = $zcrmModuleIns->searchRecordsByCriteria("entityId:equals:4071993000001295295", 1, 2);
        //$bulkAPIResponse=$zcrmModuleIns->getRecords();
        $zcrmModuleIns = ZCRMRecord::getInstance("Accounts",$this->input->get("id"));
        //$bulkAPIResponse = $zcrmModuleIns->searchRecordsByCriteria("id:equals:4071993000001276001", 1, 1);
        echo $zcrmModuleIns->AccountData->getFieldValue('Account_Name');die;
        //$subAccounts = ZCRMRecord::getInstance("Accounts", "4071993000001276001");
        $bulkAPIResponse = $zcrmModuleIns->getRelatedListRecords("Attachments");

        $recordsArray = $bulkAPIResponse->getData(); // $recordsArray - array of ZCRMRecord instances

        echo "<pre>";
        print_r($recordsArray);
        */

        $zcrmModuleIns = ZCRMModule::getInstance("Accounts");
        $bulkAPIResponse = $zcrmModuleIns->searchRecordsByCriteria("id:equals:{$this->input->get("id")}", 1, 1);
        $recordsArray = $bulkAPIResponse->getData();
        $this->load->helpers("custom");

        echo $recordsArray[0]->getFieldValue('Account_Name');
        echo "<pre>";print_r($recordsArray[0]->getAttachments()->getData());
        //getClassMethods($recordsArray[0]->getAttachments());
        die;
        if(count($recordsArray) > 0){
            $record = $recordsArray[0];
            $this->AccountData = $record;
        }

        $bulkAPIResponse = $zcrmModuleIns->getRecords();
        $recordsArray = $bulkAPIResponse->getData();
        $this->ChildAccounts = ZoHo_Account::getChildObjects($account_number, $recordsArray);

        $this->Contacts = null;
        try{
            $subAccounts = ZCRMRecord::getInstance("Accounts", $account_number);
            $bulkAPIResponse = $subAccounts->getRelatedListRecords("Contacts");
            if($bulkAPIResponse){
                $this->Contacts = $bulkAPIResponse->getData();
            }
        }
        catch(Exception $e){ }
    }

    public function getAllFromEntityId($id)
    {
        // TODO: remove fake id
        $data = [
            'parent_id' => $id,
            //'parent_id'    =>  '1000000028468', // fake id
        ];

        $query = $this->db->get_where($this->table, $data);
        $result = $query->result_object();
        //var_dump($result);die;
        if (! is_array($result)) {
            return ['msg'=>'No attachments available','msg_type'=>'error'];
        }

        return $result;
    }

    public function getAllFromEntityList($arCommaIds)
    {
        $this->db->from($this->table);
        $this->db->where_in('parent_id',$arCommaIds);
        $query = $this->db->get();
        $result = $query->result_object();
        //echo $this->db->last_query();
        //var_dump($result);die;
        if (! is_array($result)) {
            return ['msg'=>'No contacts available','msg_type'=>'error'];
        }

        return $result;
    }

    public function checkOwnership($owner,$id)
    {
        $data = array(
            "parent_id" =>  $owner,
            "id"    =>  $id
        );
        $query = $this->db->get_where($this->table,$data);
        $row = $query->row();
        if (isset($row))
        {
            return $row;
        }
        return false;
    }

    public function replace($id,$data)
    {
        
            // update
            if($id>0)
            {
                $this->db->replace($this->table, $data);
            }
    }
}