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
    public function logOutboxMail($iEntityId,$sSgMessageId,$sTo,$sSubject="",$sMessage="")
    {
        $aData = [
            "entity_id" => $iEntityId,
            "send_time" =>  date("Y-m-d H:i:s"),
            "to"    =>  $sTo,
            "subject"   =>  $sSubject,
            "message"   =>  $sMessage,
            "sg_message_id" =>  $sSgMessageId,
            "email_hash"=>md5($sTo),
        ];

        $iNewId = $this->insert($aData);

        return $iNewId;
        
    }

    /**
     * Record the message received through sendgrid parse API
     * @param Array $data array of insertable values
     */
    public function logInboxMail(string $sRawJson="",string $sFrom="",string $sTo="",string $sSubject="",string $sMessage="",array $aFileName=[])
    {
        $aData = [
          'raw_json' => $sRawJson,
          'to'  =>  $sTo,
          'from'=>$sFrom,
          'subject'=>$sSubject,
          'message'=>$sMessage,
          "email_hash"=>md5($sFrom),
          'attachments'=>json_encode($aFileName)
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

    public function getLogDates($sDate1,$sDate2)
    {
        $q = "SELECT * FROM {$this->table_sendgridMessage} WHERE send_time BETWEEN '{$sDate1}' AND '{$sDate2} 23:59:59'";
        $oResult = $this->db->query($q);
//        echo $this->db->last_query();die;
        $aData = $oResult->result_object();
        return $aData;
    }
}



