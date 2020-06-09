<?php

use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\crm\crud\ZCRMModule;
use zcrmsdk\crm\crud\ZCRMRecord;

class LoraxAttachments_model extends CI_Model
{

    private $table = "lorax_attachments";

    private $aColumns = [
        "id"    =>  "id",
        "fid"     =>  "file_id",
        "eid"   =>        "entity_id",
        "name"=>        "name",
        "created"=> "added",
        "fileSize"  =>  "file_size",
    ];

    public function __construct()
    {
        $this->load->database();
    }

    public function getAllFromParentId($id,$aColumns=[])
    {
        $data = [
            'za.parent_account' => $id,
        ];

        if(count($aColumns)>0)
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","name","fid","eid","fileSize","created"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }


        foreach($aMyColumns as $k=>$v){
            $this->db->select("la.$v as `$k`");
        }
        $this->db->from($this->table . " la");

        $this->db->join("zoho_accounts za","za.id=la.entity_id");
        $this->db->where($data);
        $query = $this->db->get();
        $result = $query->result_object();
        if (! is_array($result)) {
            return ['message'=>'No attachments available','type'=>'error'];
        }

        return ['type'=>'ok','results'=>$result];
    }

    public function getAllFromEntityId($id,$aColumns=[])
    {
        $data = [
            'entity_id' => $id,
        ];

        if(count($aColumns)>0)
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","name","fid","eid",'fileSize',"created"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }

        foreach($aMyColumns as $k=>$v){
            $this->db->select("$v as `$k`");
        }

        $query = $this->db->get_where($this->table,$data);
        $result = $query->result_object();

        if (! is_array($result)) {
            return ['message'=>'No attachments available','type'=>'error'];
        }

        return ['type'=>'ok','results'=>$result];
    }

    public function getAllFromEntityList($aCommaIds,$aColumns=[])
    {
        if(count($aColumns)>0)
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","name","fid","eid","fileSize","created"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }
        foreach($aMyColumns as $k=>$v)
            $this->db->select("$v as `$k`");

        $this->db->from($this->table);
        $query = $this->db->where_in('entity_id',$aCommaIds);

        $query = $this->db->get();
        $result = false;
        
        if($query)
        {
            $result = $query->result_object();
        }

        
        if (! is_array($result)) {
            return ['msg'=>'No contacts available','msg_type'=>'error'];
        }

        return ['type'=>'ok','results'=>$result];
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

    public function insert($data)
    {
        $bInserted = $this->db->insert($this->table,$data);
        $iId = 0;

        if($bInserted)
        {
            $iId = $this->db->insert_id();
        } else {
            $iId = -1;
        }

        return $iId;
    }
}
