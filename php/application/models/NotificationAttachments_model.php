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


    public function addAttachmentNotification(int $iEntityId,int $iAttachmentId)
    {
        if($iEntityId>0 && $_SESSION['eid']!=$iEntityId)
        {
            $aData = [
                'entity_id'=>$iEntityId,
                "duedate"=>date("Y-m-d"),
                "created_by"=>$_SESSION['eid'],
                "attachment_id"=>$iAttachmentId
            ];

            $bResult = $this->db->insert($this->table,$aData);

            if($bResult)
            {
                return ['type'=>'ok','id'=>$this->db->insert_id()];
            } else {
                error_log("addAttachmentNotification insertion failed");
                return ['type'=>'error','message'=>'Unable to insert attachment notification'];
            }
        } else {
            if($_SESSION['eid']!=$iEntityId) return ['type'=>'error','message'=>'Entity id is not valid'];
        }
    }
    public function updateStatus(int $iId, string $sStatus)
    {


            !empty($sStatus) ? $this->db->set('status', $sStatus):'';

            $this->db->where('id', $iId);
            $this->db->update($this->table);

            if ($this->db->affected_rows() > 0) {
                return TRUE;
            } else {
                return FALSE;
            }


    }
}