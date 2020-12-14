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
        // check valid parent is shooting mail for entity, if is parent
        $bIsParentValid = $this->entity_model->isParentOf($iEid, $iParentId);
        
        // if entity is not authorized to send mail for entity block access
        if(!$bIsParentValid)
        {
            $this->response([
                'status' => false,
                'message'=> "Not authorized to access entity"
            ], 404);
        }

        // if eid parameter passed in request
        if($iEid>0)
        {
          // get entity row
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

        // set entity name and email
        $sEntityEmail = $oEntity->email;
        $sEntityName = $oEntity->name;

        // set email is from admin
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
        // check request parameter error found
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
          // set details for entity mailing admin
          $sFrom = $sEntityEmail;
          $sFromName = $sEntityName;
          $sTo = getenv("NOTIFICATION_FROM_EMAIL");//"kamran@mts.youragentservices.com";//getenv("NOTIFICATION_FROM_EMAIL");
          $sToName = "Agent Admin Support";
        } else {
          // set details for admin mailing entity
          $sFrom = getenv("NOTIFICATION_FROM_EMAIL");
          $sFromName = "Agent Admin Support";
          $sTo = $sEntityEmail;
          $sToName = $sEntityName;
        }

        // generate hash for easy search email
        $sEntityEmailHash = generateHash($sEntityEmail);

        // send mail through messenger centralized model
        $this->load->model("Messenger_model");
        $aSendgridResult = $this->Messenger_model->sendMailSimple($sTo,$sToName,$sFrom,$sFromName,$sSubject,$sMessage);

        // sent success
        if($aSendgridResult['type']=='ok')
        {
          // hold message id of sendgrid
          $iMessageId = $aSendgridResult['id'];

          // record the details for log or trackings
          $this->load->model("SendgridMessage_model");          
          $iInsertId = $this->SendgridMessage_model->logOutboxMail($iEid,$iMessageId,$sTo,$sFrom,$sSubject,$sMessage,$sEntityEmailHash);

        // report to admin on failure             
        if(!$iInsertId)
        {
            logToAdmin("Mail log failed","$iEid,$iMessageId,$sTo,$sFrom,$sSubject,$sMessage,$sEntityEmailHash","DB");
        }
        
        $this->response([
            'status' => true,
            'message' => 'Message sent successfully'
        ], 200);
        } else {
          logToAdmin("Sendgrid failed to sent mail",print_r($aSendgridResult,true),"SENDGRID");
          // sendgrid failed to drop mail
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
    public function receive_post($sToken)
    {
      $this->load->model("SendgridMessage_model");
      // only calls from custom sendgrid server token allowed
      if($sToken==getenv("SENDGRID_POST_TOKEN"))
      {
        $sToName = $this->input->post("to");
        $sFromName = $this->input->post("from");

        $sHeaders = $this->input->post("headers"); // for date received parse
//        error_log($sHeaders);
//        error_log(strpos($sHeaders,"Received: from o1.ptr9325.smallbiz.com"));
//        error_log(substr($sHeaders,strpos($sHeaders,"Received: from o1.ptr9325.smallbiz.com")-50));
//        preg_match_all("/Date: .*-",$sHeaders,$matches);
//error_log(print_r($sHeaders,true));
//error_log("--");
//error_log(strpos($sHeaders,"o1.ptr9325.smallbiz.com"));
//error_log("--");
//die("TESTING HEADERS");
        // parse email from name <email>
        preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $sFromName, $matches);
        $sFrom = $matches[0][0];
        // parse email from name <email>
        preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $sToName, $matches);
        $sTo = $matches[0][0];

        $sEntityEmailHash = "";
        $sMessage = $this->input->post("html");

        // empty message, then check for plain text type
        if(empty($sMessage))
        {
          $sMessage = $this->input->post("text");
        }
        $sSubject = $this->input->post("subject");
        $num_attachments = (int)$this->input->post("attachments");
        // check for attachments and upload to temp
        $aFileName = $this->uploadMailFiles($num_attachments);
        // valid email can be recorded
        if(filter_var($sFrom, FILTER_VALIDATE_EMAIL))
        {
          $this->load->model("entity_model");
          $aEntity = $this->entity_model->getEmailId($sFrom);
          // entity found against email from entity
          if($aEntity['type']=='ok')
          {
            $iEntityId = $aEntity['results']->id;
            $sEntityEmailHash = generateHash($sFrom);
            // if email is for admin then avoid loging as it is logged on sent event
            if(strpos($sHeaders,"o1.ptr9325.smallbiz.com")>0)
            {
              error_log("Skip loging, AgentAdmin mail sent to itself by entity: " . $iEntityId . " to admin: " . getenv("NOTIFICATION_FROM_EMAIL"));
              die;
            }
          } else {
            $iEntityId = 0;
            $sEntityEmailHash = "";
          }
          // hold the json for any descrepency in future
          $sRawJson = json_encode($_POST);

//          error_log("$iEntityId,$sFrom,$sTo,$sSubject,$sMessage,$aFileName,$sEntityEmailHash");
          // log the sendgrid post back parse request
          $iId = $this->SendgridMessage_model->logInboxMail(
                                          $iEntityId,
                                          $sRawJson,
                                          $sFrom,
                                          $sTo,
                                          $sSubject,
                                          $sMessage,
                                          $aFileName,
                                          $sEntityEmailHash
                                        );
        }

        logToAdmin("Sendgrid Parse API wroking","Recorded response id: " . $iId,'SENDGRID');
      } else {
        logToAdmin("Sendgrid receive failed","Sendgrid Parse API : Path accessed without token",'SENDGRID');
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
            //error_log("File in mail not valid: " . print_r($aFile,true));
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
