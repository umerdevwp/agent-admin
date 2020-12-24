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
    /** codes that are used in templates */
    private $aShortcode = ['entity'=>
            [
                '[Company Name]'=>'name',
                '[Formation State]'=>'filingState',
                '[Forwarding Address]'=>'shippingStreet,shippingStreet2',
                '[Forwarding City]'=>'shippingCity',
                '[Forwarding State]'=>'shippingState',
                '[Forwarding Zip]'=>'shippingCode',
            ],
        'contact'=>
            [
                '[First Name]'=>'firstName',
                '[Last Name]'=>'lastName',
            ]
        ,
        'agent'=>
            [
                '[RA Amount Due]'=>'ra_due_amount',
                '[RA Date Due]'=>'ra_due_date',
                '[RA Renewal Amount]'=>'ra_renewal_amount'        
            ]        
    ];
    /**
     * Replace shortcodes with actual entity/contact values
     * shortcordes from private class variables $aShortcode
     * values are taken from provided object of respective shortcodes
     */
    public function replaceShortcode($sMessageWithShortcodes,$oObjectHavingVariables,$sReplaceType='entity')
    {
        $aShortcode = $this->aShortcode[$sReplaceType];

        $sMessage = $sMessageWithShortcodes;
        $aObjectHavingVariables = json_decode(json_encode($oObjectHavingVariables),true);

        foreach($aShortcode as $k=>$v)
        {
            // replace single variable with single value
            $sValue = $aObjectHavingVariables[$v];

            // check there r more then 2 replaces required
            if(strpos($v,",")!==false)
            {
                $aCsvFields = explode(",",$v);
                // replace the multiple varialbes
                foreach($aCsvFields as $v2)
                {
                    $sValue .= " ".$aObjectHavingVariables[$v2];
                }
                // remove addition spaces
                $sValue = trim($sValue);
            }
            // finally exchange values
            $sMessage = str_replace($k,$sValue,$sMessage);
        }

        return $sMessage;
    }

    /**
     * Add note to entity profile, similar to message but
     * stored in note table
     * @param int $iEntityId entity id
     * @param string $sSubject title of note
     * @param string $sMessage written content of the message
     */
    public function addNote($iEntityId,$sSubject,$sMessage)
    {
        if(isAdmin())
        {
            $this->load->model("EntityNotes_model");
            $sType = $this->input->post("type");
            if(empty($sType))
                $sType = "notes";

            $iNewNoteId = $this->EntityNotes_model->add($iEntityId,$sType,$sSubject,$sMessage,$_SESSION['eid']);
            
            if($iNewNoteId>0)
            {
                $this->response([
                    'status' => true,
                    'message' => 'Message sent successfully'
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message'=> "Unable to add note to entity"
                ], 500);
            }
        } else {
            $this->response([
                'status' => false,
                'message'=> "No such request found."
            ], 404);
        }
    }

    /**
     * Send mail using sendgrid API,
     * note can also submit using this call, note parameter must exist as 1
     * 
     */
    public function send_post()//$sEmail,$sName,$sState,$sEntityType,$sDate,$sPurpose="recurring")
    {
        $iEid = $this->input->post("eid");
        $iGroupId = (int)$this->input->post("gid");

        $iParentId = $_SESSION['eid'];

        $this->load->model("entity_model");
        // don't validate when user is admin
        if(!isAdmin())
        {
            // check valid parent is shooting mail for entity, if is parent
            $bIsParentValid = $this->entity_model->isParentOf($iEid, $iParentId);
        } else {
            $bIsParentValid = true;
        }        
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

        // if message is a note then record and exit;
        if((int)$this->input->post("note")===1)
        {
            $this->addNote($iEid,$sSubject,$sMessage);
            exit();
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
            

            // replace entity shortcode
            $sMessage = $this->replaceShortcode($sMessage,$oEntity,'entity');
            // replace RA shortcode
            $this->load->model("Contacts_model");
            $oContact = $this->Contacts_model->getPrimaryFromEntityId($iEid);
            // if contact found replace variables
            if(is_object($oContact))
            $sMessage = $this->replaceShortcode($sMessage,$oContact,'contact');
            // replace Contact shortcode
//            $sMessage = $this->replaceShortcode($sMessage,$oContact,'contact');
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
          $iInsertId = $this->SendgridMessage_model->logOutboxMail($iEid,$iMessageId,$sTo,$sFrom,$sSubject,$sMessage,$sEntityEmailHash,$iGroupId);

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
        
        if(empty($sMessage))
            $sMessage = "";

        $sSubject = $this->input->post("subject");


        if(empty($sSubject) && empty($sMessage))
        {
            logToAdmin("Sendgrid receive subject/message missing","Sendgrid Parse API json: " . json_encode($_POST),'SENDGRID');
            die("No subject/message");
        }

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
//            echo strpos($sHeaders,"o1.ptr9325.smallbiz.com");die;

            // if email is for admin then avoid loging as it is logged on sent event
            if(strpos($sHeaders,"o1.ptr9325.smallbiz.com")>0)
            {
              logToAdmin("Sendgrid Parse Post","Skip loging, AgentAdmin mail sent to itself by entity: " . $iEntityId . " to admin: " . getenv("NOTIFICATION_FROM_EMAIL"),"SENDGRID");
              die;
            }
          } else {
            $iEntityId = 0;
            $sEntityEmailHash = "";
          }
          // hold the json for any descrepency in future
          $sRawJson = json_encode($_POST);
          // if subject is matched with any previous mail and contains RE:
          // then bind it to its respective group or sendgrid_message_id

          $sReplySubject = trim(preg_replace("/re:/i","",$sSubject));
          $oRowForGroupId = $this->SendgridMessage_model->whereSubject($sReplySubject,$sFrom);
          $iGroupId = 0;
          if($oRowForGroupId->id>0)
          {
            $iGroupId = $oRowForGroupId->id;
          }
          //error_log("subject: " . $sReplySubject . ", group:" . $iGroupId);
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
                                          $sEntityEmailHash,
                                          $iGroupId,
                                        );
        }

        logToAdmin("Sendgrid Parse API wroking","Recorded response id: " . $iId,'SENDGRID');
      } else {
        logToAdmin("Sendgrid receive failed","Sendgrid Parse API : Path accessed without token",'SENDGRID');
        die("Permission denied");
      }
    }

    /**
     * Upload attachments of entity/customer mails are stored on temp
     * to be checked/validated
     * @param int $iNumAttachments total attachments present in posted file variable
     */
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
    
    /**
     * Used in cron to check statuses in sendgrid of the mails sent
     * for attachments reminder, ruling subscription, interface messages
     */
    public function cronLogMailStatus_get($key="")
    {
        if($key!=getenv("CRON_KEY")) redirectSession();

        $this->load->model("SendgridMessage_model");
        $this->load->model("Messenger_model");

        $sDateTime1 = date("Y-m-d",strtotime("-7 days"));//"2020-03-12T00:00:00Z";
        $sDateTime2 = date("Y-m-d");//"2020-03-12T23:59:59Z";
        //$sDateTime1 = "2020-03-12";
        $aData = $this->SendgridMessage_model->getBetweenDates($sDateTime1,$sDateTime2);
        //$sResult = $this->Messenger_model->fetchStatus(['msg_id LIKE "'.$sMsgId.'%"', 'to_email LIKE "'.$sToEmail.'"']);
        //$sResult = $this->Messenger_model->fetchStatusBetweenDate($sDateTime1,$sDateTime2);
        //$sResult = $this->Messenger_model->fetchStatusMsgId("YhTbHYXjSU2cwE9UN2j-ng");
        
        $iRecordsUpdated = 0;
        if($aData)
        {
            foreach($aData as $v)
            {
                $sMsgId = $v->sg_message_id;//"Ople3WYzQNW9FNyoMB_apA";
                $sToEmail = $v->to;//"najm.a@allshorestaffing.com";

        //        $sResult = $this->sgGetStatus(['(CONTAINS(msg_id,"Ople3WYzQNW9FNyoMB_apA"))', 'to_email LIKE "najm.a@allshorestaffing.com"','last_event_time BETWEEN TIMESTAMP "'.$sDateTime1.'" AND TIMESTAMP "'.$sDateTime2.'"']);
                //$sResult = $this->Messenger_model->fetchStatus(['msg_id LIKE "'.$sMsgId.'%"', 'to_email LIKE "'.$sToEmail.'"']);
                //$sResult = $this->Messenger_model->fetchStatusMsgIdCurl($sMsgId,$sToEmail);
                $oJson = $this->Messenger_model->fetchStatusMsgId($sMsgId);
        
                if(count($oJson->messages)>0)
                {
                    $oMessage = $oJson->messages[0];
                    $this->SendgridMessage_model->updateMailLog($v->id, json_encode($oJson),$oMessage->status,$oMessage->opens_count,$oMessage->clicks_count,$oMessage->last_event_time);
                    $iRecordsUpdated++;
                } else {
                    logToAdmin("Sengrid log not found","For msgid: $sMsgId " . json_encode($oJson),"CRON");
                }
            }
        }
        echo "Total statuses: {$iRecordsUpdated} Updated";
        logToAdmin("CRON Mail Statuses succeed","Total statuses: {$iRecordsUpdated} Updated","CRON");
    }

    // TODO: set cron to update entity_states table for a new zoho_account is synched to zoho_accounts
    // REPLACE INTO entity_states SELECT za.id,s.id,NOW(),NOW() FROM zoho_accounts za, states s WHERE za.filing_state=s.code;
    /**
     * Used in cron, notify about ruling state notification has reached, it runs everyday
     * and check if the mail send day has arrive based on days_before
     * and month_before configuration in the table notification_subscription
     */
    public function cronNotifyForSubscription_get($key="")
    {
        if($key!=getenv("CRON_KEY")) redirectSession();

        $this->load->model("Notifications_model");
        $this->load->model("entity_model");
        $this->load->model("tempmeta_model");
        $this->load->model("Messenger_model");
        $this->load->model("SendgridMessage_model");
        // TODO: apply critearea to get subscription, find which subscription are ready
        // to check for messages, if both intervals are 0 then check before months if it is 0 then check before days
        $aSubscription = $this->Notifications_model->getSubscriptions();

        $iMailsSent = 0;
// TODO: subscription notification loop can be simplified by keeping next notification date in subscription table with flag initial done
        foreach($aSubscription['results'] as $oSubs)
        {
            $aDataEntity = $this->entity_model->getOne($oSubs->entity_id,['id','name','email','type','filingState','entityStructure','formationDate']);
            
            if($aDataEntity['type']=='error')
            {
                $aDataEntity = $this->tempmeta_model->getOneInJson(["json_id"=>$oSubs->entity_id]);
            }

            if($aDataEntity['type']=='ok' && $aDataEntity['results']->id>0)
            {
                $oEntity = $aDataEntity['results'];

                $aRule = $this->Notifications_model->getRules(
                    $oEntity->filingState,
                    $oEntity->entityStructure,
                    $oEntity->formationDate,
                    $oEntity->fiscalDate
                );

                if($aRule['type']=='ok')
                {
                    $oRule = $aRule['results'];
                    $aResult = $this->Notifications_model->getNotifyDate($oSubs,$oRule,date("Y-m-d"));
                    // date reached to send message now/today
                    if(isset($aResult['date']))
                    {
                        //$this->sendMail($oEntity,$oRule);
                        $sTemplateId = "d-02d5c4c6dddf4f709ec6c636a27a18eb";
                        $aTemplateVariables = ['name'=>$oEntity->name,'state'=>$oEntity->filingState,'type'=>$oEntity->entityStructure,'date'=>$aResult['date']];
                        $sMessageId = $this->Messenger_model->sendTemplateEmail(
                            $sTemplateId,
                            $aTemplateVariables,
                            $oEntity->email,
                            $oEntity->name,
                            $oEntity->id,
                            "Template loc Attachment available"
                        );

                        // log the sent mail for trackings
                        $iInsertLogId = $this->SendgridMessage_model->logTemplateMail(
                            $oEntity->id,$sMessageId,$oEntity->email,getenv("NOTIFICATION_FROM_EMAIL"),$sTemplateId,serialize($aTemplateVariables),generateHash($oEntity->email)
                        );
                        $iMailsSent++;
                    }
                } else {
                    logToAdmin("Subscription cron failed","Rule find failed, eid: " . $oSubs->entity_id . ",  state: {$oEntity->filingState}, structure: {$oEntity->entityStructure}, formed:{$oEntity->formationDate}. {$aRule['message']}",'CRON');
                }
            } else {
                logToAdmin("Subscription cron failed","No such entity found: " . $oSubs->entity_id,'CRON');
            }
        }
        echo "Notify Mail cron succeed: {$iMailsSent} sent";
        logToAdmin("Subscription cron working","Notify Mail cron succeed: {$iMailsSent} sent","CRON");
    }

    /**
     * Used in cron to shoot mails to entity that have notification pending
     */
    public function cronNotifyForAttachments_get($key="")
    {
        if($key!=getenv("CRON_KEY")) redirectSession();

        $this->load->model("SendgridMessage_model");
        $this->load->model("Messenger_model");
        $this->load->model("entity_model");
        $this->load->model("tempmeta_model");
        $this->load->model("contacts_model");
        //$aDataNotify = $this->NotificationAttachments_model->getAllWhere(['status'=>'pending']);
        $aDataNotify = $this->SendgridMessage_model->get_many_by(['status'=>'pending','type'=>'attachment']);
        $iMailsSent = 0;
        $iToday = strtotime(date("Y-m-d"));

        foreach($aDataNotify as $oRow)
        {

            if(strtotime($oRow->duedate)<=$iToday)
            {
                $aDataEntity = $this->entity_model->getOne($oRow->entity_id,['id','name','email','type','filingState','entityStructure']);
                // check in tempmeta records
                if($aDataEntity['type']=='error')
                {
                    $aDataEntity = $this->tempmeta_model->getOneInJson(["json_id"=>$oRow->entity_id]);
                }

                if($aDataEntity['type']=='ok' && $aDataEntity['results']->id>0)
                {
                    $oEntity = $aDataEntity['results'];

                    if($oEntity->email!="")
                    {

                        $sDownloadUrl = getenv("SITE_URL") . "download/" . $oRow->lorax_id . "?code=" . $oRow->access_token. "&name=doc-" . date("d-m-y") . ".pdf";

                        //$oDataContact = $this->contacts_model->getEntityProfileContact($oEntity->id);
                        //$oDataSendgrid = $this->sendgrid_model->getOne($oRow->sendgrid_id);
                        $aTemplateVariables = ['entity_name'=>$oEntity->name];
                        switch($oRow->template_id)
                        {
                            case "d-2a672857adad4bd79e7f421636b77f6b":
                                $aTemplateVariables = array_merge($aTemplateVariables,['download_url'=>$sDownloadUrl]);
                                $sTemplateId = "d-2a672857adad4bd79e7f421636b77f6b";
                            break;
                            case "d-d0fa3c4400ff49e5bf48c31eb85fc5fe":
                                $sTemplateId = "d-d0fa3c4400ff49e5bf48c31eb85fc5fe";
                                $aTemplateVariables = array_merge($aTemplateVariables,['login_url'=>getenv("SITE_MAIN_URL")."entity/".$oEntity->id]);

                                if(!empty($oRow->sendgrid_variable))
                                $aTemplateVariables = array_merge(unserialize($oRow->sendgrid_variable),$aTemplateVariables);
                            break;
                        }
                        $sSubject = "";
                        $iMessageId = $this->Messenger_model->sendTemplateEmail(
                            $sTemplateId,
                            $aTemplateVariables,
                            $oEntity->email,
                            $oEntity->name,
                            $oEntity->id,
                            "Template loc Attachment available"
                        );

                        if($iMessageId)
                        {
                            //$this->logMailResponse($iEntityIdForLog,$iMessageId,$sEmailAddress,$sSubjectForLog,"Template ID: " . $sTemplateId . " Data: " . print_r($aTemplateFields,true));
                            //$this->NotificationAttachments_model->updateDataArray($oRow->id,['status'=>'sent']);
                            $this->SendgridMessage_model->update($oRow->id,[
                                'status'=>'sent',
                                "sg_message_id"=>$iMessageId,
                                'send_time'=>date("Y-m-d H:i:s"),
                                'to'=>$oEntity->email,
                                'entity_email_hash'=>generateHash($oEntity->email)
                            ]);
                            $iMailsSent++;
                        }
                    } else {

                        //$this->NotificationAttachments_model->updateDataArray($oRow->id,['status'=>"no-email"]);
                        $this->SendgridMessage_model->update($oRow->id,['status'=>"no-email"]);
                        error_log("Email not found for Attacchment notification, eid: " . $oEntity->id);

                    }
                    
                }
                
            }
        }
        echo "Notify For Attacchment Cron Succeed: {$iMailsSent} sent";
        logToAdmin("Notify Attachment Cron succeed","Total mails sent: {$iMailsSent}","CRON");
    }

    /**
     * List all the messages of entity and system/admin
     */
    public function list_get(int $id=null)
    {
        if($id>0)
        {
            $this->load->model("SendgridMessage_model");

            // for admin bring notes too
            if(isAdmin())
            {
                $aRecords = $this->SendgridMessage_model->getListEntityAdmin($id);
            } else {
                $aRecords = $this->SendgridMessage_model->getListEntity($id);
            }

            $this->response([
                'status'=>true,
                'data' => $aRecords
            ], 200);
        } else {
            $this->response([
                'status'=>false,
                'message' => 'Entity must exist in the request'
            ], 302);
        }
    } 
}
