<?php
defined('BASEPATH') or exit('No direct script access allowed');

use SendGrid\Mail\Mail;
use chriskacerguis\RestServer\RestController;
include APPPATH.'/libraries/CommonDbTrait.php';

class Notifications extends RestController
{
    public function form($id=0)
    {
        $this->load->library('form_validation');
        $this->load->model("entity_model");

        if((int)$id>0)
        {
            $oEntity = $this->entity_model->getOne($id);
            
            if(empty($oEntity->id))
            {
                $data['oEntity'] = $oEntity;
            } else {
                //set_flashdata("error","Entity doesn't exist or expired.");
            }
        } else {
            //set_flashdata("error","Entity doesn't exist");
        }
        
        if($this->session->user['zohoId']==getenv("SUPER_USER"))
        {
            $aResult = $this->entity_model->getAll(["id","entity_name"]);
        } else {
            $aResult = $this->entity_model->loadChildAccounts($this->session->user['zohoId']);
        }

        $data['aEntity'] = [];
        if(count($aResult['results']))
        {
            $aEntity = [];
            $aEntityIndex = [];
            foreach($aResult['results'] as $v){
                $aEntity[] = "{label:'".$v->entity_name."', value: '".$v->entity_name." ($v->id)'}";
            }
            $data['aEntity'] = $aEntity;
        }
        
        $this->load->view("header");
        $this->load->view("plugin");
        $this->load->view("task-add",$data);
        //$this->load->view("footer");
    }

    public function add()
    {
        $this->load->library('form_validation');

        if($this->input->post("inputForEntity")!='')
        {
            // fetch the id under ()
            preg_match('/\([0-9]{19}\)/',$this->input->post("inputForEntity"),$aEntityId);
            // fetch id without ()
            preg_match('/[0-9]{19}/',$aEntityId[0],$aEntityId);
            
            $iEntityId = $aEntityId[0];
            if(empty($iEntityId))
            {
                $_POST['inputForEntity'] = '';
            } else {
                $_POST['iEntityId'] = $iEntityId;
            }
        }
        
        $this->form_validation->set_rules('inputForEntity', 'For Entity', 'required',['required'=>" required, please type and select entity name"]);
        
        $this->form_validation->set_rules('inputDueDate', 'Due Date', 'required|regex_match[/[0-9]{2,}\/[0-9]{2,}\/[0-9]{4,}/]',["regex_match"=>"Allowed %s format: 2019-01-01"]);
        $this->form_validation->set_rules('inputDesc', 'Description', 'required');
        $this->form_validation->set_rules('inputStartDate', 'Start Date', 'required|regex_match[/[0-9]{2,}\/[0-9]{2,}\/[0-9]{4,}/]',["regex_match"=>"Allowed %s format: 2019-01-01"]);
        $this->form_validation->set_rules('inputEndDate', 'End Date', 'required|regex_match[/[0-9]{2,}\/[0-9]{2,}\/[0-9]{4,}/]',["regex_match"=>"Allowed %s format: 2019-01-01"]);

        if($this->input->post("checkBeforeDays"))
        {
            $this->form_validation->set_rules('inputBeforeDays', 'Before Days', 'required|greater_than[0]');
        }
        if($this->input->post("checkBeforeMonths"))
        {
            $this->form_validation->set_rules('inputBeforeMonths', 'Before Months', 'required|greater_than[0]');
        }
        if($this->input->post("checkIntervalDays"))
        {
            $this->form_validation->set_rules('inputIntervalDays', 'Interval of Days', 'required|greater_than[0]');
        }
        if($this->input->post("checkIntervalMonths"))
        {
            $this->form_validation->set_rules('inputIntervalMonths', 'Interval of Months', 'required|greater_than[0]');
        }
        if($this->input->post("checkTotalNotifications"))
        {
            $this->form_validation->set_rules('inputTotalNotifications', 'Notifications Counts', 'required|greater_than[0]');
        }

        // which notifications systems user subscribing for
        if(!$this->input->post("checkTypeEmail") && !$this->input->post("checkTypeSms") && !$this->input->post("checkTypeBrowser"))
        {
            $this->form_validation->set_rules('checkTypeEmail', 'Notifications Type', 'required');
        } else {
            $strNotificationType = "browser,email,sms";
            if(!$this->input->post("checkTypeEmail")) 
                $strNotificationType = str_replace(",email","",$strNotificationType);
            if(!$this->input->post("checkTypeSms")) 
                $strNotificationType = str_replace(",sms","",$strNotificationType);
            if(!$this->input->post("checkTypeBrowser")) 
                $strNotificationType = str_replace("browser,","",$strNotificationType);
            $_POST['notificationType'] = $strNotificationType;
        }
        
        if($this->form_validation->run() == FALSE)
        {
            $this->form();
            // form has errors to correct
            return false;
        }

        $this->addToDB();
        die("TESTING");
        if($this->addToDb())
        {
            $this->session->set_flashdata("ok","Added successfully.");
            redirectSession();
        } else {
            $this->session->set_flashdata("error","Server failed to process your request.");
            $this->form();
        }
        
    }

    private function addToDB()
    {
        $this->load->model("Tasks_model");
        
        // fetch the id under ()
        preg_match('/\([0-9]{19}\)/',$this->input->post("inputForEntity"),$aEntityId);
        // fetch id without ()
        preg_match('/[0-9]{19}/',$aEntityId[0],$aEntityId);
        
        $iEntityId = $aEntityId[0];

        $aData = [
            "created_by"    =>  userLoginId(),
            "entity_id"     =>  $this->input->post("iEntityId"),
            "due_date"      =>  convToMySqlDate($this->input->post("inputDueDate")),
            "description"   =>  $this->input->post("inputDesc"),
            "start_date"    =>  convToMySqlDate($this->input->post("inputStartDate")),
            "type"          =>  $this->input->post("notificationType")
        ];

        if($this->input->post("checkEndDate"))
            $aData["end_date"] = convToMySqlDate($this->input->post("inputEndDate"));

        if($this->input->post("checkBeforeDays"))
            $aData["before_days"] = $this->input->post("inputBeforeDays");
        
        if($this->input->post("checkBeforeMonths"))
            $aData["before_months"] = $this->input->post("inputBeforeMonths");
        
        if($this->input->post("checkIntervalDays"))
            $aData["interval_days"] = $this->input->post("inputIntervalDays");

        if($this->input->post("checkIntervalMonths"))
            $aData["interval_months"] = $this->input->post("inputIntervalMonths");

        if($this->input->post("checkTotalNotifications"))
            $aData["limit_notification"] = $this->input->post("inputTotalNotifications");

        $this->Tasks_model->add($aData);
    }

    public function notify1()
    {
        $this->load->model("Tasks_model");
        
        $aResult = $this->Tasks_model->getAllNotifications();

        if(count($aResult['results']))
        {
            foreach($aResult as $v)
            {
                // check today is the day to shoot mail to particular entity
                //if($this->checkTodayShootDay($v->due_date,$v->start_date,$v->status))
                {
                    // shoot email and continue to next user
                }
            }
        }
    }
    // TODO: set cron to update entity_states table for a new zoho_account is synched to zoho_accounts
    // REPLACE INTO entity_states SELECT za.id,s.id,NOW(),NOW() FROM zoho_accounts za, states s WHERE za.filing_state=s.code;

    public function notify($key="")
    {
        if($key!=getenv("CRON_KEY")) redirectSession();

        $this->load->model("Notifications_model");
        $this->load->model("entity_model");
        
        $aData["subscription"] = $this->Notifications_model->getSubscriptions();
        $iMailsSent = 0;
        
        foreach($aData['subscription']['results'] as $oSubs)
        {
            $oRule = $this->Notifications_model->getRules(
                $oSubs->filing_state,
                $oSubs->entity_structure,
                $oSubs->formation_date,
                $oSubs->fiscal_date
            );
            $aResult = $this->getNotifyDate($oSubs,$oRule,date("Y-m-d"));
            if(isset($aResult['date']))
            {

                $aDataEntity = $this->entity_model->getOne($oSubs->entity_id,['id','name','email','type','filingState','entityStructure']);
                if($aDataEntity['type']=='ok' && $aDataEntity['results']->id>0)
                {
                    $oEntity = $aDataEntity['results'];
                    $this->sendMail($oEntity,$oRule);
                    $iMailsSent++;
                }
                
            }
            // var_dump($oRule);
            // var_dump($aResult);
            // var_dump($oSubs);
            // var_dump($oEntity);
             
            //

        }
        error_log("Notify Mail cron succeed: {$iMailsSent} sent");
    }

    /**
     * Send mail using sendgrid API
     * 
     */
    private function sendMail($oEntity,$oRule)//$sEmail,$sName,$sState,$sEntityType,$sDate,$sPurpose="recurring")
    {
        $sEmail = $oEntity->email;
        $sName = $oEntity->name;
        $sState = $oEntity->filingState;
        $sEntityType = $oEntity->entityStructure;
        $sDate = $oRule->duedate;
        $sPurpose = $oRule->period;

        switch($sPurpose)
        {
            case "initial":
                $sSubject = "Reminder: Upcoming Initial filing Due";
                $sContent =<<<HC
<br />
Dear <strong>{$sName}</strong>, <br /><br />
It is a reminder for upcoming initial filing for your state: <strong>{$sState}</strong> and structure: <strong>{$sEntityType}</strong> on date: <strong>{$sDate}</strong> <br />
Regards, <br /><br />
Your Agent Services Support <br />
HC;
            break;

            default:
            $sSubject = "Reminder: Upcoming Recurring filing Due";
            $sContent =<<<HC
<br />
Dear <strong>{$sName}</strong>, <br /><br />
It is a reminder for upcoming recurring filing for your state: <strong>{$sState}</strong> and structure: <strong>{$sEntityType}</strong> on date: <strong>{$sDate}</strong> <br />
Regards, <br /><br />
Your Agent Services Support <br />
HC;
        }

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
            
            //  print $response->statusCode() . "\n";
            //  print_r($aHeaders);
            //  print $response->body() . "\n";
            
            $this->logMailResponse($oEntity->id,$iMessageId,$sEmail,$sSubject,$sContent);
        } catch (Exception $e) {
            $sMessage = 'Caught exception: '. $e->getMessage() ."\n";
            //debug($e);
            error_log($sMessage);
        }
    }

    /**
     * Plan calendar of notifications based on supplied start and end dates
     * for particular entity
     * 
     * @param $iEntityId Numeric id of existing entity
     * @param $sStartDate String of start date
     * @param $sEndDate String of end date
     * 
     * @return Array of dates of notification from start till end date
     */
    public function planCalendar(){
        $this->load->model("Notifications_model");
        $this->load->library('form_validation');

        $aData["subscription"] = $this->Notifications_model->getSubscriptions();
        $aCalendar = null;
        $oSubs = null;
        $sCalendarEvents = "";

        $this->form_validation->set_rules("state","State","required");
        $this->form_validation->set_rules("type","Type","required");
        $this->form_validation->set_rules("formation","Formation date","required");
        $this->form_validation->set_rules("fiscal","Fiscal date","required");

        $this->form_validation->set_rules("beforedays","Before days","required");
        $this->form_validation->set_rules("beforemonths","Before months","required");
        $this->form_validation->set_rules("intervaldays","Interval days","required");
        $this->form_validation->set_rules("intervalmonths","Interval months","required");

        $this->form_validation->set_rules("daterange","Start-End Date","required");

        
        if($this->form_validation->run()!==FALSE){

            $oRule = $this->Notifications_model->getRules(
                $this->input->post("state"),
                $this->input->post("type"),
                $this->input->post("formation"),
                $this->input->post("fiscal")
            );

 
            $aExplodRange = explode("-",$this->input->post("daterange"));
            $sStartDate = $aExplodRange[0];
            $sEndDate = $aExplodRange[1];

            $oStartDate = new DateTime($sStartDate);
            $oEndDate = new DateTime($sEndDate);
            
            if($aData['subscription']['type']=='ok')
                $oSubs = $aData['subscription']['results'][0];

                $oSubs = (object)[
                    "start_date"=>$sStartDate,
                    "end_date"=>$sEndDate,
                    "interval_months"=>$this->input->post('intervalmonths'),
                    "interval_days"=>$this->input->post('intervaldays'),
                    "before_months"=>$this->input->post('beforemonths'),
                    "before_days"=>$this->input->post('beforedays'),
                ];
            
            if($oSubs)
            {
                $oSubsStartDate = new DateTime($oSubs->start_date);
                $oSubsEndDate = new DateTime($oSubs->end_date);
                $oNowDate = new DateTime(date("Y-m-d"));
                $oDueDate = new DateTime($oRule->duedate);
                if($oStartDate<$oSubsStartDate)
                {
                    $oStartDate = $oSubsStartDate;
                }
                if($oEndDate>$oSubsEndDate)
                {
                    $oEndDate = $oSubsEndDate;
                }
                if($oDueDate<$oEndDate)
                {
                    $oEndDate = $oDueDate;
                }
                
                // check the difference of dates b/w start and end date
                $oDateDiff = $oNowDate->diff($oEndDate);
                $sStartDate = $oStartDate->format("Y-m-d");
                // subscription start is past then start notifying from now
                if($oStartDate<$oNowDate)
                    $sStartDate = $oNowDate->format("Y-m-d");


                $sCalendarEvents .= '{' . '"title":"SUBSCRIPTION START DATE","start":"' . $oStartDate->format("Y-m-d") . '"},';

                for($i=0;$i<=$oDateDiff->days;$i++)
                {
                    $sDateNow = date("Y-m-d",strtotime($sStartDate . " +{$i} days"));
                    $aDate = $this->getNotifyDate($oSubs,$oRule,$sDateNow);
                    if(is_array($aDate)){
                        $aCalendar[$sDateNow] = $aDate;

                        $sCalendarEvents .= '{' . '"title":"' . $aDate['type'] . '","start":"' . $aDate['date'] . '"';
                        if($aDate['type']=='interval-days') $sCalendarEvents .= ',"className":"fc-event-success"';
                        else if($aDate['type']=='interval-months') $sCalendarEvents .= ',"className":"fc-event-primary"';
                        else if($aDate['type']=='before-days') $sCalendarEvents .= ',"className":"fc-event-danger"';
                        else if($aDate['type']=='before-months') $sCalendarEvents .= ',"className":"fc-event-warning"';
                        $sCalendarEvents .= '},';

                    }
                    
                    if($sDateNow==$oEndDate->format("Y-m-d")){
                        $sCalendarEvents .= '{' . '"title":"SUBSCRIPTION END DATE","start":"' . $sDateNow . '"},';
                        break;
                    }


                }
            }
            $sCalendarEvents .= '{' . '"title":"FINAL DATE","start":"' . $oRule->duedate . '","className":"fc-event-danger"},';
            $sCalendarEvents = "[".substr($sCalendarEvents,0,-1)."]";
        }
        //[{"title":"Sony Meeting","start":"2019-08-06","className":"fc-event-success"},{"title":"Conference","start":"2019-08-14","end":"2019-08-16","className":"fc-event-warning"},{"title":"System Testing","start":"2019-08-26","end":"2019-08-28","className":"fc-event-primary"},{"title":"Sony Meeting","start":"2019-09-05","className":"fc-event-success"},{"title":"Conference","start":"2019-09-11","end":"2019-09-12","className":"fc-event-warning"},{"title":"System Testing","start":"2019-09-26","end":"2019-09-28","className":"fc-event-primary"},{"title":"Sony Meeting","start":"2019-10-06","className":"fc-event-success"},{"title":"Conference","start":"2019-10-14","end":"2019-10-16","className":"fc-event-warning"},{"title":"System Testing","start":"2019-10-26","end":"2019-10-28","className":"fc-event-primary"},{"title":"Sony Meeting","start":"2019-11-09","className":"fc-event-success"},{"title":"Conference","start":"2019-11-17","end":"2019-11-18","className":"fc-event-warning"},{"title":"System Testing","start":"2019-11-26","end":"2019-11-28","className":"fc-event-primary"},{"title":"Sony Meeting","start":"2019-12-06","className":"fc-event-success"},{"title":"Conference","start":"2019-12-14","end":"2019-12-16","className":"fc-event-warning"},{"title":"System Testing","start":"2019-12-26","end":"2019-12-27","className":"fc-event-primary"},{"title":"Sony Meeting","start":"2020-01-09","className":"fc-event-success"},{"title":"Conference","start":"2020-01-17","end":"2020-01-18","className":"fc-event-warning"},{"title":"System Testing","start":"2020-01-26","end":"2020-01-28","className":"fc-event-primary"}]
        $this->load->view("header");
        $this->load->view("notification-calendar",['aCalendar'=>$aCalendar,'sCalendarEvents'=>$sCalendarEvents,'aNotification'=>[$oRule]]);
        $this->load->view("footer");
    }


    private function getNotifyDate($oSubscription,$oRule,$sDateNow)
    {
        
        // setup now, due and subscripiton date objects
        $oDueDate = new DateTime($oRule->duedate);
        $oDateNow = new DateTime($sDateNow);
        $oDiffDue = $oDueDate->diff($oDateNow,true);
        $oDiffSubs = $oDateNow->diff(new DateTime($oSubscription->start_date));
        $aResult = null;

        if($oSubscription->interval_months>0 && $oDiffSubs->m>0)
        {
            // calculate month intervals
            $iMonthIntervalRemaining = $oDiffSubs->m%$oSubscription->interval_months;
            $iDayToday = $oDateNow->format("d");
            // and today is 1st of month then shoot
            if($iMonthIntervalRemaining==0 && $iDayToday==1)
            {
                $aResult = ['type'=>'interval-months','date'=>$oDateNow->format("Y-m-d")];
                return $aResult;
            }
        }

        // calculate month remaining, difference is 0 when day is last of month
        if($oDiffDue->m==$oSubscription->before_months && $oDiffDue->d==0)
        {
            $aResult = ['type'=>'before-months','date'=>$oDateNow->format("Y-m-d")];
            return $aResult;
        }

        // calculate days remaining
        if($oDiffDue->days==$oSubscription->before_days)
        {
            $aResult = ['type'=>'before-days','date'=>$oDateNow->format("Y-m-d")];
            return $aResult;
        }

        if($oSubscription->interval_days>0 && $oDiffSubs->days>0)
        {
            // calculate day intervals 
            $iDayIntervalRemaining = $oDiffSubs->days%$oSubscription->interval_days;
            if($iDayIntervalRemaining==0)
            {
                $aResult = ['type'=>'interval-days','date'=>$oDateNow->format("Y-m-d")];
                return $aResult;
            }
        }

        return $aResult;
    }

    public function showRules()
    {
        $this->load->library('form_validation');
        $this->load->model("Notifications_model");

        $data['aNotification'] = $this->Notifications_model->getRules(
            $this->input->post("state"),
            $this->input->post("type"),
            convToMySqlDate($this->input->post("formation")),
            convToMySqlDate($this->input->post("fiscal")),
            convToMySqlDate($this->input->post("now")),
            false
        );

        $this->load->view('header');
        $this->load->view("test-notification",$data);
        $this->load->view('footer');
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
            // 
        } else {
            error_log("Unable to insert mail log",0);
        }
    }

    public function logMailStatus($key="")
    {
        if($key!=getenv("CRON_KEY")) redirectSession();

        $this->load->model("Notifications_model");

        $sDateTime1 = date("Y-m-d",strtotime("-2 days"));//"2020-03-12T00:00:00Z";
        $sDateTime2 = date("Y-m-d");//"2020-03-12T23:59:59Z";
        //$sDateTime1 = "2020-03-12";
        $aData = $this->Notifications_model->getLogDates($sDateTime1,$sDateTime2);
        
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
                    $this->Notifications_model->updateMailLog($v->id, $oJson->messages[0]->status,$sResult);
                    $iRecordsUpdated++;
                }
            }
        }

        error_log("Log Mail status cron succeed: {$iRecordsUpdated} Updated");        
    }

    public function sgGetStatus($aQueryList)
    {
        $oSendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));
        //$sToEmail = "najm.a@allshorestaffing.com";
        $header = array();
        $headr[] = 'Content-length: 0';
        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: Bearer '.getenv("SENDGRID_API_KEY");

//echo urldecode("query=last_event_time%20BETWEEN%20TIMESTAMP%20%22{start_date}%22%20AND%20TIMESTAMP%20%22{end_date}%22AND%20to_email%3D%22<<email>>%22");die;
        $sQuery = urlencode(implode(" AND ",$aQueryList));

//        $sArguments = urlencode("to_email=\"{$sToEmail}\"");
//        echo "Authorization: Bearer ".getenv("SENDGRID_API_KEY");die;
        $sQuery."<br>";
        $oCh = curl_init("https://api.sendgrid.com/v3/messages?limit=10&query=".$sQuery);
        curl_setopt($oCh,CURLOPT_HTTPHEADER,$headr);

        curl_setopt($oCh,CURLOPT_RETURNTRANSFER,true);
        $sResult = curl_exec($oCh);

        return $sResult;
    }

    public function showEmailLogs_get()
    {
        //if(!isSessionValid("EmailLogs")) redirectSession();
        $this->load->model("Notifications_model");
        $sDate1 = $this->input->get("startDate");
        $sDate2 = $this->input->get("endDate");

        if(!isset($_GET['startDate']) || !isset($_GET['endDate']))
        {
            $sDate1 = date("Y-m-d",strtotime("-1 week"));
            $sDate2 = date("Y-m-d");
        } else {
            $sDate1 = date("Y-m-d",strtotime($sDate1));
            $sDate2 = date("Y-m-d",strtotime($sDate2));
        }

        $aData =$this->Notifications_model->getLogEntityDates($sDate1,$sDate2);

        $this->response([
            'startDate' =>  $sDate1,
            'endDate'   =>  $sDate2,
            'data' => $aData
        ], 200);
    }

    public function najm_get()
    {

        $this->response([
            'data' => []
        ], 200);
    }
}