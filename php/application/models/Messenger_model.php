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
            
            $this->logMailResponse($iEntityId,$iMessageId,$sEmail,$sSubject,$sContent);
        } catch (Exception $e) {
            $sMessage = 'Caught exception: '. $e->getMessage() ."\n";
            //debug($e);
            error_log($sMessage);
        }
    }

    /**
     * Test all parameters using objects
     */
    public function testKitchenSinkExampleWithObjectsAndLegacyTemplate()
    {
        $email = new Mail();

        // For a detailed description of each of these settings,
        // please see the
        // [documentation](https://sendgrid.com/docs/API_Reference/api_v3.html).
        $email->setSubject(
            new Subject("Sending with Twilio SendGrid is Fun 2")
        );

        $email->addTo(new To("najm.a@allshorestaffing.com", "Najm User"));
//        $email->addTo(new To("test+1@example.com", "Example User1"));
        // $toEmails = [
        //     new To("test+2@example.com", "Example User2"),
        //     new To("test+3@example.com", "Example User3")
        // ];
        // $email->addTos($toEmails);

        // $email->addCc(new Cc("test+4@example.com", "Example User4"));
        // $ccEmails = [
        //     new Cc("test+5@example.com", "Example User5"),
        //     new Cc("test+6@example.com", "Example User6")
        // ];
        // $email->addCcs($ccEmails);

        // $email->addBcc(
        //     new Bcc("test+7@example.com", "Example User7")
        // );
        // $bccEmails = [
        //     new Bcc("test+8@example.com", "Example User8"),
        //     new Bcc("test+9@example.com", "Example User9")
        // ];
        // $email->addBccs($bccEmails);

        $email->addSubstitution(
            new Substitution("%name%", "Example Name 1")
        );
        // $email->addSubstitution(
        //     new Substitution("%city1%", "Denver")
        // );
        // $substitutions = [
        //     new Substitution("%name2%", "Example Name 2"),
        //     new Substitution("%city2%", "Orange")
        // ];
        // $email->addSubstitutions($substitutions);

        // The values below this comment are global to entire message

        $email->setFrom(new From("najm@gmail.com", "Najm Twilio SendGrid"));

        $email->setGlobalSubject(
            new Subject("Sending with Twilio SendGrid is Fun and Global 2")
        );

        $plainTextContent = new PlainTextContent(
            "and easy to do anywhere, even with PHP"
        );
        $htmlContent = new HtmlContent(
            "<strong>and easy to do anywhere, even with PHP</strong>"
        );
        $email->addContent($plainTextContent);
        $email->addContent($htmlContent);
        $contents = [
            new Content("text/calendar", "Party Time!!"),
            new Content("text/calendar2", "Party Time 2!!")
        ];
        $email->addContents($contents);

        $email->setTemplateId(
            new TemplateId("d-2a672857adad4bd79e7f421636b77f6b")
        );

        // $json = json_encode($email->jsonSerialize());
        // $isEqual = BaseTestClass::compareJSONObjects($json, $this->REQUEST_OBJECT_LEGACY);
        // $this->assertTrue($isEqual);

        $oSendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));
        try {
             $response = $oSendgrid->send($email);
             $aHeaders = $response->headers();
             $iMessageId = "";
             
             foreach($aHeaders as $v) 
                if(strpos($v,"X-Message-Id")!==false) 
                    $iMessageId = explode(": ",$v)[1];
            
              print $response->statusCode() . "\n";
              print_r($aHeaders);
              print $response->body() . "\n";
            
            $this->logMailResponse("000",$iMessageId,"testings","sss","sContent");
        } catch (Exception $e) {
            $sMessage = 'Caught exception: '. $e->getMessage() ."\n";
            //debug($e);
            error_log($sMessage);
        }
    }

    public function sendCurlTemplate()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.sendgrid.com/v3/mail/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\"personalizations\":[{\"to\":[{\"email\":\"najm.a@allshorestaffing.com\",\"name\":\"Najm Doe\"}],\"dynamic_template_data\":{\"verb\":\"\",\"adjective\":\"\",\"noun\":\"\",\"currentDayofWeek\":\"\"},\"subject\":\"Hello, World! Try it\"}],\"from\":{\"email\":\"noreplynajm@gmail.com\",\"name\":\"John Doe no reply\"},\"reply_to\":{\"email\":\"noreplynajm@gmail.com\",\"name\":\"John Doe no reply\"},\"template_id\":\"d-546828bba7ce46dfbf131e400b69d7cb\"}",
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer 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",
            "content-type: application/json"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
        echo "cURL Error #:" . $err;
        } else {
        echo $response;
        }
    }
}



