<?php
class Permissions_model extends CI_Model
{

    private $vu_permissions = "vu_permissions vp";
    private $table_entity_role = "entity_roles";
    private $table_role = "roles";
    public $aRole = ['entity'=>'Child Entity','parent'=>'Parent Organization','admin'=>'Administrator'];

    public function __construct()
    {
        $this->load->database();
    }

    public function getPermissionsEntityRow($iEntityId,$sResourceName="")
    {
        $aWhere = [
                        "entity_id"   =>  $iEntityId
                    ];
        
        if($sResourceName!="")
            $aWhere["resource_name"] =  $sResourceName;
        
        $this->db->from($this->vu_permissions);
        $this->db->join($this->table_entity_role . " er","er.role_id=vp.role_id");
        $this->db->where($aWhere);
        $oResult = $this->db->get();
        if($oResult)
        {
            $aData = $oResult->row_object();
        } else {
            logToAdmin('Permission Query Failed',$this->db->last_query(),'DB');
            return ['type'=>'error', 'message'=>'Unable to process query'];
        }
        
        return ['type'=>'ok','results'=>$aData];
    }

    public function add($iEntityId,$sRoleName="entity")
    {
        $oData = $this->getRoleId($sRoleName);
        
        if($oData)
        {
            $aDataEntityRole = [
                "entity_id" =>  $iEntityId,
                "role_id"   =>  $oData->id,
            ];

            $oResult = $this->db->insert($this->table_entity_role,$aDataEntityRole);

            if($oResult) return $this->db->insert_id();
        }

        return false;
        
    }

    public function roleExist($iEntityId)
    {
        $oData = null;
        if($iEntityId>0)
        {
            $this->db->from($this->table_entity_role);
            $this->db->where("entity_id",$iEntityId);
            $oResult = $this->db->get();
            $oData = $oResult->row_object();
        }
        return $oData;
    }

    public function getRoleId($sRoleName="")
    {
        $oData = null;
        if($sRoleName!="")
        {
            $this->db->from($this->table_role);
            $this->db->where("role_name",$this->aRole[$sRoleName]);
            $oResult = $this->db->get();
            $oData = $oResult->row_object();
        }
        return $oData;
    }
}