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

    public function sendMailSimple(string $sToEmail,string $sName,string $sFromEmail, string $sFromName, string $sSubject,string $sContent)
    {
        $oEmail = new Mail();
        $oEmail->setFrom($sFromEmail, $sFromName);
        $oEmail->addTo($sToEmail, $sName);
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
            
            //$this->logMailResponse($iEntityIdForLog,$iMessageId,$sEmailAddress,$sSubjectForLog,"Template ID: " . $sTemplateId . " Data: " . print_r($aTemplateFields,true));
            if($iMessageId)
            {
                return $iMessageId;
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
    /**
     * Fetch status from sendgrid API, of mails already sent
     */
    public function fetchStatusMsgIdCurl($sMsgId,$sToEmail)
    {

        $oSendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));
        //$sToEmail = "najm.a@allshorestaffing.com";
        $header = array();
        $headr[] = 'Content-length: 0';
        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: Bearer '.getenv("SENDGRID_API_KEY");

//echo urldecode("query=last_event_time%20BETWEEN%20TIMESTAMP%20%22{start_date}%22%20AND%20TIMESTAMP%20%22{end_date}%22AND%20to_email%3D%22<<email>>%22");die;
        //$sQuery = urlencode(implode(" AND ",$aQueryList));
        $sQuery = 'msg_id LIKE "'.$sMsgId.'%" AND to_email LIKE "'.$sToEmail.'"';
        //$sMsgId = ['msg_id LIKE "%"'];
        //$sToEmail = ['to_email LIKE "najm.a@allshorestaffing.com"'];
        //$sFromEmail = ['from_email LIKE "agentadmin@youragentservices.com"'];
        //$sOpenCount = ['opens_count LIKE "0"'];
//        $sArguments = urlencode("to_email=\"{$sToEmail}\"");
//        echo "Authorization: Bearer ".getenv("SENDGRID_API_KEY");die;
        $sQuery."<br>";
        $oCh = curl_init("https://api.sendgrid.com/v3/messages?limit=10&query=".$sQuery);
        curl_setopt($oCh,CURLOPT_HTTPHEADER,$headr);

        curl_setopt($oCh,CURLOPT_RETURNTRANSFER,true);
        $sResult = curl_exec($oCh);

        return $sResult;
    }


    public function fetchStatusBetweenDate(string $sStartDate,string $sEndDate)
    {
        //$sJsonQuery = json_decode('{"aggregated_by": "day", "limit": 1, "start_date": "'.$sStartDate.'", "end_date": "'.$sStartDate.'", "offset": 1}');
        $sJsonQuery = json_decode('{"limit":300,"start_date": "'.$sStartDate.'", "end_date": "'.$sStartDate.'"}');

        $oSendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));

        //$oResponse = $oSendgrid->client->stats()->get(null, $sJsonQuery);
        $oResponse = $oSendgrid->client->messages()->get(null, $sJsonQuery);

        if($oResponse->statusCode()=="200")
        {
            return json_decode($oResponse->body());
        }

        return false;
//        print $response->statusCode() . "\n";
//        print $response->body() . "\n";
//        print_r($response->headers());
//die;
    }

    public function fetchStatusMsgId(string $sMessageId)
    {
        $sJsonQuery = json_decode('{"limit": 1, "start_date": "2020-12-10", "offset": 1}');
        $sJsonQuery = json_decode('{ "limit": 1, "start_date": "2020-12-10", "end_date": "2020-12-10", "offset": 1}');
        $sJsonQuery = json_decode('{ "limit": 1, "query":"msg_id LIKE \'vdV867vIR7iCzRRUXZ519Q%\'", "offset": 1}');
        
        $oSendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));

        $oResponse = $oSendgrid->client->messages()->get(null, $sJsonQuery);

        if($oResponse->statusCode()=="200")
        {
            return json_decode($oResponse->body());
        }

        logToAdmin("Sendgrid API fetch status","for msgid: " . $sMessageId . $oResponse->body(),"CRON");

        return false;
    }
}



