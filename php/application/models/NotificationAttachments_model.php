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


    public function addAttachmentNotification(int $iEntityId,string $sLoraxFileId, int $iSendgridId=1, array $aSendgridVariable=['document_count'=>'',])
    {
        if($iEntityId>0 && $_SESSION['eid']!=$iEntityId)
        {
            $aData = [
                'entity_id'=>$iEntityId,
                "duedate"=>date("Y-m-d"),
                "created_by"=>$_SESSION['eid'],
                "lorax_id"=>$sLoraxFileId,
                "token"=>generateToken(),
                "sendgrid_id"=>$iSendgridId,
                "sendgrid_variable"=>serialize($aSendgridVariable),
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
    public function updateDataArray(int $iId, array $aData)
    {
            if($iId>0 && count($aData)>0)
            {
                $this->db->set($aData);
                $this->db->where('id', $iId);
                $this->db->update($this->table);

                if ($this->db->affected_rows() > 0) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }

    }

        /**
     * Get notification with where array
     * 
     * @param Array $aWhere columns criterea
     * 
     * @return Array Record row / Error message no row found
     */
    public function getOne(array $aWhere=[])
    {
        if(count($aWhere)>0)
        {
            $query = $this->db->get_where($this->table, $aWhere);
            $result = $query->row();

            if (!$result) {
                return ['message'=>'No such record found','type'=>'error'];
            }
            return $result;
        } else {
            return ['message'=>'Incomplete arguments','type'=>'error'];
        }
    }
}