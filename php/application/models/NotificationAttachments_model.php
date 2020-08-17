<?php

class NotificationAttachments_model extends CI_Model {
    private $table = "notification_attachments";
    
    public function getAllWhere(array $aWhere=[])
    {
        
        if(count($aWhere))
        {
            $query = $this->db->get_where($this->table,$aWhere);
        } else {
            $query = $this->db->get($this->table);
        }

        $result = $query->result_object();
        
        if (! is_array($result)) {
            return ['message'=>'No record available','type'=>'error'];
        }

        return ['type'=>'ok','results'=>$result];
    }


    public function addAttachmentNotification($iEntityId,$aColumnData=[])
    {
        if($iEntityId>0)
        {
            $aData = ['entity_id'=>$iEntityId];
            $aData = array_merge($aData,$aColumnData);
            $bResult = $this->db->insert($this->table,$aData);

            if($bResult)
            {
                return ['type'=>'ok','id'=>$this->db->insert_id()];
            } else {
                error_log("addAttachmentNotification insertion failed");
                return ['type'=>'error','message'=>'Unable to insert attachment notification'];
            }
        } else {
            return ['type'=>'error','message'=>'Entity id is not valid'];
        }
    }
}