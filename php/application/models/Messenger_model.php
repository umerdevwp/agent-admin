<?php

use PhpParser\Node\Expr\Cast\Object_;
use SendGrid\Mail\Mail;
class Messenger_model extends CI_Model {

    public function __construct()
    {
        
    }

    public function sendTemplateEmail(string $sTemplateId="d-2a672857adad4bd79e7f421636b77f6b", array $aTemplateFields=[],int $iEntityId, string $sEmailAddress, string $sName,string $sSubject)
    {
        $oEmail = new Mail();
        $oEmail->setFrom(getenv("NOTIFICATION_FROM_EMAIL"), "Your Agent Services Support");
        $oEmail->addTo($sEmailAddress, $sName);
        
        $oEmail->setSubject($sSubject);
        $oEmail->setTemplateId($sTemplateId);
        
        $oTemplateFields = json_encode('{
            "personalizations": [{
                "to": [{
                    "email": "'.$sEmailAddress.'"
                }],
                "substitutions": {
                    "%name%": "recipient",
                    "%CustomerID%": "CUSTOMER ID GOES HERE"
                },
                "subject": "YOUR SUBJECT LINE GOES HERE"
            }]
        }');
         $oB = json_decode($oTemplateFields);
        //$oTemplateFields = (object) $aTemplateFields;
        $oEmail->addSubstitutions($aTemplateFields);
//        $oEmail->addDynamicTemplateDatas(,json_decode($oTemplateFields));

        $oSendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));
        try {
             $response = $oSendgrid->send($oEmail);
             $aHeaders = $response->headers();
             $iMessageId = "";

             foreach($aHeaders as $v) 
                if(strpos($v,"X-Message-Id")!==false) 
                    $iMessageId = explode(": ",$v)[1];
            
              print $response->statusCode() . "\n";
              print_r($aHeaders);
              print $response->body() . "\n";
            die('ss');
            $this->logMailResponse($iEntityId,$iMessageId,$sEmailAddress,$sSubject,"Template ID: " . $sTemplateId);
        } catch (Exception $e) {
            $sMessage = 'Caught exception: '. $e->getMessage() ."\n";
            debug($e);
            die('ss');error_log($sMessage);
        }
    }

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


    public function sendEmail($iEntityId,$sEmail,$sName,$sSubject,$sContent)
    {
        $oEmail = new Mail();
        $oEmail->setFrom(getenv("NOTIFICATION_FROM_EMAIL"), "Your Agent Services Support");
        $oEmail->addTo($sEmail, $sName);
        $oEmail->setSubject($sSubject);
        $oEmail->addContent("text/html", $sContent);

        $oSendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));
        try {
             $response = $oSendgrid->send($oEmail);
             $aHeaders = $response->headers();
             $iMessageId = "";
             
             foreach($aHeaders as $v) 
                if(strpos($v,"X-Message-Id")!==false) 
                    $iMessageId = explode(": ",$v)[1];
            
              print $response->statusCode() . "\n";
              print_r($aHeaders);
              print $response->body() . "\n";
            
            $this->logMailResponse($iEntityId,$iMessageId,$sEmail,$sSubject,$sContent);
        } catch (Exception $e) {
            $sMessage = 'Caught exception: '. $e->getMessage() ."\n";
            //debug($e);
            error_log($sMessage);
        }
    }
}



