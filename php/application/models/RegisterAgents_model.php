<?php
class RegisterAgents_model extends CI_Model
{

    private $table = "zoho_registered_agents";
    private $aColumns = ["id"=>"id",
    "createdAt"=>"created_at",
    "name"=>"name",
    "owner"=>"owner",
    "email"=>"email",
    "fileAs"=>"file_as",
    "modifiedBy"=>"modified_by",
    "secondaryEmail"=>"secondary_email",
    "createdTime"=>"created_time",
    "modifiedTime"=>"modified_time",
    "lastActivityTime"=>"last_activity_time",
    "tag"=>"tag",
    "isRecordDuplicate"=>"is_record_duplicate",
    "createdBy"=>"created_by",
    "currency"=>"currency",
    "address"=>"address",
    "phone"=>"local_phone",
    "address2"=>"address2",
    "state"=>"state",
    "city"=>"city",
    "zipcode"=>"zip_code",
    "raNotes"=>"ra_notes"];
    public function __construct()
    {
        $this->load->database();
    }

    public function find($arData)
    {
        if(count($arData)>0)
        {

            $this->db->limit(1);

            $query = $this->db->get_where($this->table, $arData);

            $result = $query->row();

        }

        return $result;
    }

    public function getAll($aColumns=[])
    {

        if(count($aColumns)>0)
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","name","email","phone","fileAs","address","address2","state","city","zipcode"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }
        
        foreach($aMyColumns as $k=>$v)
            $this->db->select("$v as `$k`");

            $this->db->from($this->table);

/*        $sQuery = "SELECT file_as, address, address2, state, city, zip_code "
                . "FROM {$this->table} "
                . "WHERE right(name, 3) = 'UAS' ORDER BY state";*/
        $this->db->order_by("state");
        $this->db->where("right(name, 3)='UAS'");
        $oResult = $this->db->get();
        $result = $oResult->result_object();

        if (!$result) {
            return ['type' => 'error', 'message'=>'No records found'];
        }

        return $result;
    }

    public function getOne($iRaId,$aColumns=[])
    {

        $aDataWhere = [
            'id' => $iRaId
        ];
        if(count($aColumns)>0)
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","name","email","phone","address","address2","zipcode","state","fileAs","city"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }

        foreach($aMyColumns as $k=>$v)
        $this->db->select("$v as `$k`");

        $query = $this->db->get_where($this->table, $aDataWhere);

        $result = $query->row();

        if (!$result) {
            return ['message' => 'Account id not found', 'type' => 'error'];
        }

        return ['type' => 'ok', 'results' => $result];
    }
}