<?php
class Permissions_model extends CI_Model
{

    private $vu_permissions = "vu_permissions vp";
    private $table_entity_role = "entity_roles er";
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
        $this->db->join($this->table_entity_role,"er.role_id=vp.role_id");
        $this->db->where($aWhere);
        $oResult = $this->db->get();
        
        $aData = $oResult->row_object();

        return $aData;
    }

    public function add($iEntityId,$sRoleName="")
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

    public function getRoleId($sRoleName="")
    {
        $oData = null;
        if($sRoleName!="")
        {
            $this->db->from($this->table_role);
            $this->db->where("role_name",$sRoleName);
            $oResult = $this->db->get();
            $oData = $oResult->row_object();
        }
        return $oData;
    }
}