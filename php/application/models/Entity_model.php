<?php

class Entity_model extends CI_Model
{
    public static $entity_name = "account_name";
    public static $entity_type = "account_type";
    public static $entity_structure = "entity_type";
    public static $parent_entity = "parent_account";
    public static $entity_owner = "owner";
    public static $entity_number = "account_number";

    private $aColumns = ["id"=>"id","createdAt"=>"created_at",
    "name"=>"account_name",
    "phone"=>"phone",
    "type"=>"account_type",
    "entityStructure"=>"entity_type",
    "agentId"=>"ra",
    "filingState"=>"filing_state",
    "parentId"=>"parent_account",
    "email"=>"notification_email",
    "status"=>"status",
    "billingEmail"=>"billing_email",
    "stateId"=>"state_id",
    "formationDate"=>"formation_date",
    "einId"=>"ein",
    "tag"=>"tag",
    "createdTime"=>"created_time",
    "modifiedTime"=>"modified_time",
    "lastActivityTime"=>"last_activity_time",
    "billingStreet"=>"billing_street","shippingStreet"=>"shipping_street",
    "billingStreet2"=>"billing_street_2","shippingStreet2"=>"shipping_street_2",
    "billingCity"=>"billing_city","shippingCity"=>"shipping_city",
    "billingState"=>"billing_state","shippingState"=>"shipping_state",
    "billingCode"=>"billing_code","shippingCode"=>"shipping_code",
    "billingCountry"=>"billing_country","shippingCountry"=>"shipping_country",
    "owner"=>"owner","createdBy"=>"created_by",
    "currency"=>"currency",
    "layout"=>"layout",
    "modifiedBy"=>"modified_by",
    "expirationDate"=>"expiration_date",
    "subscriptionStatus"=>"subscription_status",
    "accountName"=>"account_number",
    "stateStatus"=>"state_status",
    "businessPurpose"=>"business_purpose"];

    private $table = "zoho_accounts";
    private $table_entityMeta = "entitymeta";
    public function __construct()
    {
        $this->load->database();
    }

    public function pullUniqueStatuses()
    {
        $this->db->select('status');
        $this->db->distinct();
        $this->db->where('status IS NOT NULL');
        $query = $this->db->get($this->table);
        $result = $query->result();
        if (!is_array($result)) {
            return ['message' => 'Entities not found.', 'type' => 'error'];
        }
        return ['type' => 'ok', 'results' => $result];
    }

    public function insertStatus($data = NULL)
    {
        if ($this->db->table_exists($this->table_entityMeta)) {

            if (!empty($data)) {
                $this->db->insert($this->table_entityMeta, $data);
                $insert_id = $this->db->insert_id();
                return  $insert_id;
            } else {
                log_message('error', "Couldn't create admin");
            }
        } else {
            log_message('error', 'Administrators table does not exit');
        }
    }

    public function checkIfExists($id = NULL)
    {
        if ($this->db->table_exists($this->table_entityMeta)) {
            $this->db->select('*');
            $this->db->from($this->table_entityMeta);
            $this->db->where('zoho_accounts_id', $id);
            $query = $this->db->get();
            return $query->result();
        } else {
            log_message('error', 'Administrators table does not exit');
            return FALSE;
        }
    }


    public function updateExistingEntityStatus($id = NULL, $data = NULL)
    {
        if ($this->db->table_exists($this->table_entityMeta)) {
            if (!empty($data) and !empty($id)) {
                $this->db->where('zoho_accounts_id', $this->input->post("id"));
                $this->db->update($this->table_entityMeta, $data);
                if ($this->db->affected_rows() > 0) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                log_message('error', 'Data or ID is missing');
                return FALSE;
            }
        } else {
            log_message('error', 'Administrators table does not exit');
            return NULL;
        }
    }


    public function ownerValidity($parent, $entity){
     //make sure the entity belongs to right parent or not.
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('id', $entity);
        $this->db->where('parent_account', $parent);
        $query = $this->db->get();
        return $query->result();
    }

    public function isParent($iEntityId,$iParentId){
        //make sure the entity belongs to right parent or not.
        $this->db->select('id');
        $this->db->from($this->table);
        $this->db->where('id', $iEntityId);
        $this->db->where(Self::$parent_entity, $iParentId);
        $query = $this->db->get();
        $aData = $query->row();

        if(isset($aData->id)) return true;
        else return false;
    }

    public function getOne($id,$aColumns=[])
    {

        $data = [
            'id' => $id
            //'id'    =>  '4071993000000411060',
        ];

        if(count($aColumns)>0)
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","name","type","filingState",
                "formationDate","agentId", "shippingStreet",
                 "shippingCity", "shippingState", "shippingCode", "email",
                 "expirationDate"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }
        foreach($aMyColumns as $k=>$v)
            $this->db->select("$v as `$k`");

        $query = $this->db->get_where('zoho_accounts', $data);

        $result = $query->row();

        if (!$result) {
            return ['message' => 'Account id not found', 'type' => 'error'];
        }

        return ['type' => 'ok', 'results' => $result];
    }

    public function getAgentAddress($id)
    {

        $this->db->limit(1);
        $this->db->select("ra.*");
        $this->db->from("zoho_accounts a");
        $this->db->join("zoho_registered_agents ra", "cast(`a`.`ra` as char) =cast(`ra`.`id` as char)");
        $this->db->where(["a.id" => $id]);
        $query = $this->db->get();

        $result = $query->row();

        if (!$result) {
            return false;
        }

        return $result;
    }

    /**
     * Get array of child entities of sessioned user
     * @param Int $id zoho id of logged in session user
     * @param String $columns (optional) comma seprated columns name
     */
    public function getChildAccounts($id, $aColumns = [])
    {
        $data = [
            Entity_model::$parent_entity    =>  $id
        ];

        if(count($aColumns)>0)
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","name","type","filingState","formationDate","agentId"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }
        foreach($aMyColumns as $k=>$v)
            $this->db->select("$v as `$k`");

        $query = $this->db->get_where($this->table, $data);
        $result = $query->result();

        if (!is_array($result)) {
            return ['message' => 'Entities not found.', 'type' => 'error'];
        }

        return ['type' => 'ok', 'results' => $result];
    }

    public function hasEntities($id)
    {
        $data = [
            Entity_model::$parent_entity =>  $id
        ];
        $result = $this->db->get_where($this->table, $data, 1, 1);
        //echo $this->db->last_query();
        $row = $result->row();
        //var_dump($row);

        if ($row->id > 0) {
            return true;
        }

        return false;
    }

    public function getAll()
    {
        // $query = $this->db->get('zoho_accounts');
        // $result = $query->result_object();
        // if (!$result) {
        //     return ['msg' => 'No such account found', 'msg_type' => 'error'];
        // }
        // return $result;

        $this->db->select('zoho_accounts.*, entitymeta.entity_status');
        $this->db->from('zoho_accounts');
        $this->db->join('entitymeta','entitymeta.zoho_accounts_id=zoho_accounts.id', 'left');
        // $this->db->where(["entitymeta.zoho_accounts_id", "zoho_accounts.id"]);
        $query = $this->db->get();
        $result = $query->result_object();


        if (! is_array($result)) {
            return ['msg'=>'No contacts available','msg_type'=>'error'];
        }

        return $result;


    }

    public function getEmailId($sEmail)
    {
        $this->db->select("id");
        $query = $this->db->get_where($this->table,['notification_email'=>$sEmail]);
        $oData = $query->row_object();

        if($oData)
        {
            return ['type'=>'ok','results'=>$oData];
        }

        return ['type'=>'error','message'=>'Record not found'];
    }
}
