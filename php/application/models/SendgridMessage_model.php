<?php
include APPPATH . '/libraries/ModelDefault.php';

class SendgridMessage_model extends ModelDefault {
    private $sTable = "sendgrid_messages";
    private $aColumns = [
            "id"        => "id",
            "entityId"  => "entity_id",
            "sendTime"  => "send_time",
            "to"        => "to",
            "subject"   => "subject",
            "message"   => "message",
            "sgMessageId"=> "sg_message_id",
            "added"     => "added",
            "updated"   => "updated",
            "sgStatus"  => "sg_status",
            "status"    => "status",
            "entityEmailHash"=>"entity_email_hash",
            "rawJson"   => "raw_json",
            "from"      => "from",
            "attachments"=> "attachments",
            "type"      => "type",
            "loraxId"   => "lorax_id",
            "templateVariable"=> "template_variable",
            "accessToken"=> "access_token",
            "createdBy" => "created_by",
            "duedate"   => "duedate",
            "templateId"=> "template_id",
            "gid"       =>  "group_id"
    ];

    public function __construct()
    {
        $this->_table = $this->sTable;
        parent::__construct();
    }

    /**
     * 
     */
    public function logMailResponse($iEntityId,$sSgMessageId,$sTo,$sSubject="",$sMessage="")
    {
        $this->load->model("Notifications_model");
        $aData = [
            "entity_id" => $iEntityId,
            "send_time" =>  date("Y-m-d H:i:s"),
            "to"    =>  $sTo,
            "subject"   =>  $sSubject,
            "message"   =>  $sMessage,
            "sg_message_id" =>  $sSgMessageId,
            "email_hash"=>md5($sTo),
        ];

        $iInsertId = $this->Notifications_model->addMailLog($aData);
        if($iInsertId)
        {
            return $iInsertId;
        } else {
            error_log("Unable to insert mail log",0);
            return false;
        }
    }

    /**
     * Record the message send through sendgrid API
     * @param Integer $iEntityId entity id to send from/to
     */
    public function logOutboxMail($iEntityId,$sSgMessageId,$sTo,$sFrom,$sSubject="",$sMessage="",$sEntityEmailHash="",$iGroupId)
    {
        $aData = [
            "entity_id" => $iEntityId,
            "send_time" =>  date("Y-m-d H:i:s"),
            "to"    =>  $sTo,
            "from"  =>  $sFrom,
            "subject"   =>  $sSubject,
            "message"   =>  $sMessage,
            "sg_message_id" =>  $sSgMessageId,
            "entity_email_hash"=>$sEntityEmailHash,
            'type'=>'outbox',
            'group_id'=>$iGroupId
        ];
        
        $iNewId = $this->insert($aData);

        return $iNewId;
        
    }

    /**
     * Record the message send through sendgrid API
     * @param Integer $iEntityId entity id to send from/to
     */
    public function logTemplateMail($iEntityId,$sSgMessageId,$sTo,$sFrom,$sTemplateId="",$sTemplateVariables="",$sEntityEmailHash="")
    {
        $aData = [
            "entity_id" => $iEntityId,
            "send_time" =>  date("Y-m-d H:i:s"),
            "to"    =>  $sTo,
            "from"  =>  $sFrom,
            "template_id"   =>  $sTemplateId,
            "template_variable"   =>  $sTemplateVariables,
            "sg_message_id" =>  $sSgMessageId,
            "entity_email_hash"=>$sEntityEmailHash,
            'type'=>'outbox',
        ];
        
        $iNewId = $this->insert($aData);

        return $iNewId;
        
    }

    /**
     * Record the message received through sendgrid parse API
     * @param Array $data array of insertable values
     */
    public function logInboxMail($iEntityId=0,string $sRawJson="",string $sFrom="",string $sTo="",string $sSubject="",string $sMessage="",array $aFileName=[],$sEntityEmailHash="",$iGroupId=0)
    {

        $aData = [
          'entity_id'=>$iEntityId,
          'raw_json' => $sRawJson,
          'to'  =>  $sTo,
          'from'=>$sFrom,
          'subject'=>$sSubject,
          'message'=>$sMessage,
          "entity_email_hash"=>$sEntityEmailHash,
          'attachments'=>json_encode($aFileName),
          'type'=>'inbox',
          "send_time" =>  date("Y-m-d H:i:s"),
          "group_id"=>$iGroupId,
        ]; 

        $iNewId = $this->insert($aData);

        return $iNewId;
    }

    /**
     * Record the message received through sendgrid parse API
     * @param Array $data array of insertable values
     */
    public function logAttachmentMail(
    int $iEntityId=0,
    int $iCreatedBy=0,
    string $sLoraxFileId="",
    string $sDueDate="",
    string $sToken="",
    string $sTemplateId="d-2a672857adad4bd79e7f421636b77f6b",
    string $aTemplateVariables="")
    {
        $aData = [
            'entity_id'=>$iEntityId,
            "created_by"=>$iCreatedBy,
            "lorax_id"=>$sLoraxFileId,
            "duedate"=>$sDueDate,
            "access_token"=>$sToken,
            "template_id"=>$sTemplateId,
            "template_variable"=>serialize($aTemplateVariables),
            "type"=>"attachment",
            "status"=>"pending",
        ];

        $iNewId = $this->insert($aData);

        return $iNewId;
    }

    public function updateMailLog(int $iId,string $sJson,string $sStatus="",int $iOpenCount=0,int $iClickCount=0,string $sEventDateTime="")
    {
        $aData = [
            "sg_status" =>  $sJson
        ];

        if($sStatus) $aData['status'] = $sStatus;
        if($iOpenCount) $aData['open_count'] = $iOpenCount;
        if($iClickCount) $aData['click_count'] = $iClickCount;
        if($sEventDateTime) $aData['last_event_datetime'] = date("Y-m-d H:i:s",strtotime($sEventDateTime));

        $this->update($iId,$aData);
    }

    public function getBetweenDates($sDate1,$sDate2)
    {
        $aData = $this->get_many_by("send_time BETWEEN '{$sDate1}' AND '{$sDate2} 23:59:59' AND sg_message_id!=''");
        return $aData;
    }
    /**
     * Get messages from logs plus notes
     */
    public function getListEntityAdmin(int $iEntityId,array $aColumns=[])
    {
        if(count($aColumns)>0)
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","to","from","sendTime","subject",
                "message","status"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }

//        foreach($aMyColumns as $k=>$v)
//            $this->db->select("$v as `$k`");

//        $this->order_by("send_time");
//        $aRecords = $this->get_many_by(['entity_id'=>$iEntityId]);

        $sQueryCombineNotes = "
SELECT id,`to`,`from`,entity_id,send_time AS sendTime,subject,message,status,group_id AS gid
FROM {$this->sTable} WHERE entity_id={$iEntityId}
UNION
SELECT id,'','',entity_id,added AS sendTime, subject,message,type AS status,0
FROM entity_notes WHERE entity_id={$iEntityId}
ORDER BY sendTime ASC
";
        $oQuery = $this->db->query($sQueryCombineNotes);

        $aRecords = null;
        if($oQuery)
        {
            $aRecords = $oQuery->result();
        }

        if(count($aRecords)==0)
        {
            $aRecords = null;
        }

        return $aRecords;
    }
    /**
     * Get only messages from logs
     */
    public function getListEntity(int $iEntityId,array $aColumns=[])
    {
        if(count($aColumns)>0)
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","to","from","sendTime","subject",
                "message","gid"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }

        foreach($aMyColumns as $k=>$v)
            $this->db->select("$v as `$k`");

        $this->order_by("send_time");
        $aRecords = $this->get_many_by(['entity_id'=>$iEntityId]);

        if(count($aRecords)==0)
        {
            $aRecords = null;
        }

        return $aRecords;
    }

    /**
     * Get mail where subject matches
     */
    public function whereSubject(string $sSubject,string $sFrom)
    {
        return $this->get_by(['subject'=>$sSubject,'to'=>$sFrom,'group_id'=>0]);
    }
}