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


class Messenger_model extends CI_Model {

    public function __construct()
    {
        
    }

    public function sendMailSimple(string $sEmail,string $sName,string $sSubject,string $sContent)
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
            
            return ['type'=>'ok','id'=>$iMessageId];
        } catch (Exception $e) {
            $sMessage = 'Caught exception: '. $e->getMessage() ."\n";
            logToAdmin("Send mail failed",$sMessage);

            return ['type'=>'error','message'=> "Unable to send message, please try again later"];
        }
    }

    public function sendTemplateEmail(string $sTemplateId="d-2a672857adad4bd79e7f421636b77f6b", array $aTemplateFields=[], string $sEmailAddress, string $sName,int $iEntityIdForLog=0,string $sSubjectForLog="")
    {
        $oEmail = new Mail();
        $oEmail->setFrom(getenv("NOTIFICATION_FROM_EMAIL"), "Your Agent Services Support");
        $oEmail->addTo($sEmailAddress, $sName);
//        $oEmail->addTo("justnajm@gmail.com", "NajmGM");
//        $oEmail->setSubject($sSubject);
        $oEmail->setTemplateId($sTemplateId);
        //$oTemplateFields = (object) $aTemplateFields;
        //$oEmail->addSubstitutions($aTemplateFields);
        /*
        $oB2 = $oEmail->getPersonalizations();
         $oB2[0]->setHasDynamicTemplate(true);
         $oB2[0]->setSubject(new Subject($sSubject));
         $oB2[0]->addTo(new To("najm.a@zep-com.com","Najm Again"));*/
         //$oB2[0]->addDynamicTemplateData('name','Najm'); //working key and value
        //$oEmail->addSubstitutions(json_decode('{"name":"Sample Name"}'));
        $oEmail->addDynamicTemplateDatas($aTemplateFields);
        

        $oSendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $oSendgrid->send($oEmail);
            //  $response = $oSendgrid->send(json_decode('{
            //     "to": '.$sEmailAddress.',
            //     "from": '.getenv("NOTIFICATION_FROM_EMAIL").',
            //     "templateId": '.$sTemplateId.',
            //     "dynamic_template_data": {
            //         "name": "Najm",
            //         "entity_name": "Agent Admin",
            //     }
            //   }'));

             $aHeaders = $response->headers();
             $iMessageId = "";

             foreach($aHeaders as $v) 
                if(strpos($v,"X-Message-Id")!==false) 
                    $iMessageId = explode(": ",$v)[1];
            
            //   print $response->statusCode() . "\n";
            //   print_r($aHeaders);
            //   print $response->body() . "\n";
            
            $this->logMailResponse($iEntityIdForLog,$iMessageId,$sEmailAddress,$sSubjectForLog,"Template ID: " . $sTemplateId . " Data: " . print_r($aTemplateFields,true));
            if($iMessageId)
            {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            $sMessage = 'Caught exception: '. $e->getMessage() ."\n";
            //debug($e);
            error_log($sMessage);
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
            
            $this->logMailResponse($iEntityId,$iMessageId,$sEmail,$sSubject,$sContent);
        } catch (Exception $e) {
            $sMessage = 'Caught exception: '. $e->getMessage() ."\n";
            //debug($e);
            error_log($sMessage);
        }
    }
}



