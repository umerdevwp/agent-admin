<?php

use PhpParser\Node\Expr\Cast\Object_;
use SendGrid\Mail\Mail;
use SendGrid\Mail\Subject;
use SendGrid\Mail\To;
use SendGrid\Mail\From;

use SendGrid\Mail\Cc;
use SendGrid\Mail\Bcc;
use SendGrid\Mail\Content;
use SendGrid\Mail\PlainTextContent;
use SendGrid\Mail\Substitution;
use SendGrid\Mail\TemplateId;
use SendGrid\Mail\HtmlContent;
include APPPATH . '/libraries/ModelDefault.php';

class SendgridMessage_model extends ModelDefault {

    public function __construct()
    {
        parent::__construct();
        $this->_table = "sendgrid_message";
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
    public function logOutboxMail($iEntityId,$sSgMessageId,$sTo,$sFrom,$sSubject="",$sMessage="",$sEntityEmailHash="")
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
    public function logInboxMail($iEntityId=0,string $sRawJson="",string $sFrom="",string $sTo="",string $sSubject="",string $sMessage="",array $aFileName=[],$sEntityEmailHash="")
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

    public function updateMailLog($iId, $sStatus, $sJson)
    {
        $aData = [
            "status"    =>  $sStatus,
            "sg_status" =>  $sJson
        ];
        $this->update($iId,$aData);
    }

    public function getBetweenDates($sDate1,$sDate2)
    {
        $aData = $this->get_many_by("send_time BETWEEN '{$sDate1}' AND '{$sDate2} 23:59:59' AND sg_message_id!=''");
        return $aData;
    }
}



