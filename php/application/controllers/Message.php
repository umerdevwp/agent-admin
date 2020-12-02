<?php

// use Src\Services\OktaApiService as Okta;
header('Access-Control-Allow-Origin: *');

use zcrmsdk\crm\crud\ZCRMTag;

defined('BASEPATH') OR exit('No direct script access allowed');

use SendGrid\Mail\Mail;
use chriskacerguis\RestServer\RestController;


include APPPATH . '/libraries/CommonDbTrait.php';

class Message extends RestController
{
    /**
     * Send mail using sendgrid API
     * 
     */
    public function send_post()//$sEmail,$sName,$sState,$sEntityType,$sDate,$sPurpose="recurring")
    {
        $iEid = $this->input->post("eid");
        $iParentId = $_SESSION['eid'];

        $this->load->model("entity_model");
        $bIsParentValid = $this->entity_model->isParentOf($iEid, $iParentId);
        
        // if entity not found or eid is blank
        if(!$bIsParentValid)
        {
            $this->response([
                'status' => false,
                'message'=> "Not authorized to access entity"
            ], 404);
        }

        if($iEid>0)
        {
            $aEntity = $this->entity_model->getOne($iEid);
            $oEntity = $aEntity['results'];
        }

        // if entity not found or eid is blank
        if(($aEntity['type']=='error' || !$iEid))
        {
            $this->response([
                'status' => false,
                'message'=> "Unable to find entity details"
            ], 404);
        }

        $sEmail = $oEntity->email;
        $sName = $oEntity->name;
        $sFrom = getenv("NOTIFICATION_FROM_EMAIL");

        $sSubject = $this->input->post("subject");
        $sMessage = $this->input->post("message");
        // check subject empty
        if(empty($sSubject))
        {
            $aError[] = "Subject cannot be empty, please add subject";
        }
        // check content empty
        if(empty($sMessage))
        {
            $aError[] = "Message cannot be blank, please add message";
        }
        // check error found
        if(count($aError))
        {
            $this->response([
                'status' => false,
                'error'=> $aError
            ], 404);
        }

        // if entity send to admin
        if(!isAdmin())
        {
          $sEmail = getenv("NOTIFICATION_FROM_EMAIL");
          $sName = "Your Agent Services Support";
        }

        $this->load->model("Messenger_model");

        $aSendgridResult = $this->Messenger_model->sendMailSimple($sEmail,$sName,$sSubject,$sMessage);

        if($aSendgridResult['type']=='ok')
        {
          $iMessageId = $aSendgridResult['id'];

          $this->load->model("SendgridMessage_model");
          
          $iInsertId = $this->SendgridMessage_model->logOutboxMail($iEid,$iMessageId,$sEmail,$sSubject,$sMessage);
             
        if(!$iInsertId)
        {
            logToAdmin("Unable to insert mail log",0);
        }

        $this->response([
            'status' => true,
            'message' => 'Message sent successfully'
        ], 200);
      } else {

        $this->response([
          'status' => false,
          'message'=> "Server unable to send message, please try again later"
        ], 302);
      }
    }

    /**
     * Receive Sendgrid Parse API post back request/data
     * @param String $sToken to verify that request is made from sendgrid panel
     */
    public function receive($sToken)
    {

      $this->load->model("SgParseLog_model");

      if($sToken==getenv("SENDGRID_POST_TOKEN"))
      {

        $sTo = $_POST["to"];
        $sFrom =  $_POST["from"];
        $email_hash = md5($from);
        $sMessage = $_POST["text"];
        $sSubject = $_POST["subject"];
        $num_attachments = (int)$_POST["attachments"];

        $aFileName = $this->uploadMailFiles($num_attachments);
        if(filter_var(substr($sFrom,strpos($sFrom,"<")+1,-1), FILTER_VALIDATE_EMAIL))
        {
          $sRawJson = json_encode($_POST);

          $iId = $this->SendgridMessage_model->insert(
                                          $sRawJson,
                                          $sFrom,
                                          $sTo,
                                          $sSubject,
                                          $sMessage,
                                          $aFileName
                                        );
        }
        logToAdmin("Sendgrid Parse API","Recorded response id: " . $iId);
      } else {
        logToAdmin("Sendgrid receive failed","SendgridParser: Path accessed without token");
        die("Permission denied");
      }
    }

    private function uploadMailFiles(int $iNumAttachments=0)
    {
      $aFileName = [];

      if($iNumAttachments){
        foreach($_FILES as $aFile) {

          $sName = uniqid()."-".$aFile['name'];
          
          if(!empty($aFile['tmp_name']) && strpos($aFile['type'],"pdf")!==false && $aFile['size']<100000)
          {
            $result = move_uploaded_file(
              $aFile['tmp_name'],
              $_SERVER['DOCUMENT_ROOT']."/"."temp786/".$sName
            );
            $aFileName[] = $sName;
          } else {
            error_log("File in mail not valid: " . print_r($aFile,true));
          }
        }
      }

      return $aFileName;
    }

    public function logMailStatus($key="")
    {
        if($key!=getenv("CRON_KEY")) redirectSession();

        $this->load->model("SendgridMessage_model");

        $sDateTime1 = date("Y-m-d",strtotime("-2 days"));//"2020-03-12T00:00:00Z";
        $sDateTime2 = date("Y-m-d");//"2020-03-12T23:59:59Z";
        //$sDateTime1 = "2020-03-12";
        $aData = $this->SendgridMessage_model->getLogDates($sDateTime1,$sDateTime2);
        
        $iRecordsUpdated = 0;
        if($aData)
        {
            foreach($aData as $v)
            {
                $sMsgId = $v->sg_message_id;//"Ople3WYzQNW9FNyoMB_apA";
                $sToEmail = $v->to;//"najm.a@allshorestaffing.com";

        //        $sResult = $this->sgGetStatus(['(CONTAINS(msg_id,"Ople3WYzQNW9FNyoMB_apA"))', 'to_email LIKE "najm.a@allshorestaffing.com"','last_event_time BETWEEN TIMESTAMP "'.$sDateTime1.'" AND TIMESTAMP "'.$sDateTime2.'"']);
                $sResult = $this->sgGetStatus(['msg_id LIKE "'.$sMsgId.'%"', 'to_email LIKE "'.$sToEmail.'"']);
                $oJson = json_decode($sResult);
                if(count($oJson->messages)>0)
                {
                    $this->SendgridMessage_model->updateMailLog($v->id, $oJson->messages[0]->status,$sResult);
                    $iRecordsUpdated++;
                }
            }
        }

        logToAdmin("Cron: message statuses","Log Mail status cron succeed: {$iRecordsUpdated} Updated","CRON");
    }
}
