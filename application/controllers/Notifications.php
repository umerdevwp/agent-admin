<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notifications extends CI_Controller
{
    public function form()
    {
        $this->load->library('form_validation');
        $this->load->model("Accounts_model");
        
        if($this->session->user['zohoId']==getenv("SUPER_USER"))
        {
            $aResult = $this->Accounts_model->getAll(["id","entity_name"]);
        } else {
            $aResult = $this->Accounts_model->loadChildAccounts($this->session->user['zohoId']);
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

    public function notify()
    {
        // step 1
        // pick up all the entities that have subscribed for notifications

        // step 2
        // create a join with state rules

        // step 3
        // 
        $this->load->model("Notifications_model");
        
        $aData["subscription"] = $this->Notifications_model->getSubscriptions();
        
        foreach($aData['subscription']['results'] as $oSubs)
        {
            $oRule = $this->getRules(
                $oSubs->filing_state,
                $oSubs->entity_structure,
                $oSubs->formation_date,
                $oSubs->fiscal_date
            );
            $aResult = $this->getNotifyDate($oSubs,$oRule,date("Y-m-d"));
            var_dump($aResult);
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
    //public function planCalendar($iEntityId="3743841000000633009",$sStartDate="2020-01-01",$sEndDate="2020-06-01")
    public function planCalendar($iEntityId="3743841000000633009",$sStartDate="2020-01-01",$sEndDate="2020-06-01"){
        $this->load->model("Notifications_model");
        $this->load->library('form_validation');

        $aData["subscription"] = $this->Notifications_model->getSubscriptions();
        $aCalendar = null;
        $oSubs = null;
        $sCalendarEvents = "";

        $this->form_validation->set_rules("state","State","required");
        $this->form_validation->set_rules("type","Type","required");
        $this->form_validation->set_rules("formation","formation","required");
        $this->form_validation->set_rules("fiscal","fiscal","required");

        $this->form_validation->set_rules("daterange","Start-End Date","required");

        
        if($this->form_validation->run()!==FALSE){

            $oRule = $this->getRules(
                $this->input->post("state"),
                $this->input->post("type"),
                $this->input->post("formation"),
                $this->input->post("fiscal")
            );
            
            if($aData['subscription']['type']=='ok')
                $oSubs = $aData['subscription']['results'][0];
            
            if($oSubs)
            {
                
                $aExplodRange = explode("-",$this->input->post("daterange"));
                $sStartDate = $aExplodRange[0];
                $sEndDate = $aExplodRange[1];

                $oStartDate = new DateTime($sStartDate);
                $oEndDate = new DateTime($sEndDate);

                $oSubsStartDate = new DateTime($oSubs->start_date);
                $oSubsEndDate = new DateTime($oSubs->end_date);
                if($oStartDate<$oSubsStartDate)
                {
                    $oStartDate = $oSubsStartDate;
                }
                if($oEndDate>$oSubsEndDate)
                {
                    $oEndDate = $oSubsEndDate;
                }
                // check the difference of dates b/w start and end date
                $oDateDiff = $oStartDate->diff($oEndDate);
                $sStartDate = $oStartDate->format("Y-m-d");

                $sCalendarEvents .= '{' . '"title":"SUBSCRIPTION START DATE","start":"' . $sStartDate . '"},';

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
        $oDiffDue = $oDueDate->diff($oDateNow);
        $oDiffSubs = $oDateNow->diff(new DateTime($oSubscription->start_date));
        $aResult = null;

        // calculate month intervals
        $iMonthIntervalRemaining = $oDiffSubs->m%$oSubscription->interval_months;
        $iDayToday = $oDateNow->format("d");
        // and today is 1st of month then shoot
        if($iMonthIntervalRemaining==0 && $iDayToday==1)
        {
            $aResult = ['type'=>'interval-months','date'=>$oDateNow->format("Y-m-d")];
            return $aResult;
        }

        // calculate month remaining
        if($oDiffDue->m==$oSubscription->before_months)
        {
            // subtract 1 coz, difference is 0 when day is last of month
            $iLastDayMonth = date("t",strtotime($oDateNow->format("Y-m-d")))-1;
            if($oDiffDue->d == $iLastDayMonth){
                $aResult = ['type'=>'before-months','date'=>$oDateNow->format("Y-m-d")];
                return $aResult;
            }
        }

        // calculate days remaining
        if($oDiffDue->days==$oSubscription->before_days)
        {
            $aResult = ['type'=>'before-days','date'=>$oDateNow->format("Y-m-d")];
            return $aResult;
        }

        // calculate day intervals 
        $iDayIntervalRemaining = $oDiffSubs->days%$oSubscription->interval_days;
        if($iDayIntervalRemaining==0)
        {
            $aResult = ['type'=>'interval-days','date'=>$oDateNow->format("Y-m-d")];
            return $aResult;
        }

        return $aResult;
    }

    public function showRules()
    {
        $this->load->library('form_validation');

        $data['aNotification'] = $this->getRules(
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

    /**
     * Set calendar for upcoming due dates of notification for entity state and type
     * 
     */
    private function getRules($sEntityState,$sEntityType,$sFormationDate,$sFiscalDate="",$sNow="",$bReturnSingle=true)
    {
        $this->load->model("Notifications_model");
        

        //$state = $this->input->post("state");
        //$type = $this->input->post("type");

        $result = $this->Notifications_model->findRule(($sEntityState?:""),($sEntityType?:""));
        //var_dump($result);
        //$result = $this->Notifications_model->findRule();

        $sFiscalDate = convToMySqlDate($sFiscalDate)?:date("2017-12-31");
        // sample dates for testing
        //$sFormationDate = convToMySqlDate($this->input->post("formation"));// ["1/1/2017","1/1/2018","5/7/19","1/7/20","1/26/20"];

            foreach($result as $oRow)
            {
                
                // reset me with actual entity date
                //$sFormationDate=$aSampleDate;
                $bCondition = false;
                $oDateNow = new DateTime(($sNow?:"now"));
                
                if($oRow->base_type=="fiscal")
                {
                    $oDate = $this->getNotificationDate($oRow,$sFiscalDate);
                    $oDateFormation = new DateTime("$sFiscalDate");
                    
                } else {
                    $oDate = $this->getNotificationDate($oRow,$sFormationDate);
                    $oDateFormation = new DateTime("$sFormationDate");
                }
                // set specific month day on date
                $this->setFixedMonthDay($oRow,$oDate);

                // return initial date by stopping loop
                // if initial not expired
                if($oRow->period_type=="initial")
                {
                    if($oDateNow<$oDate)
                    {
                        //echo $sRow;
                        $data = (object)[
                            "formation" =>  $sFormationDate,
                            "fiscal" =>  $sFiscalDate,
                            "state" =>  $oRow->state,
                            "type"   =>  $oRow->entity_type,
                            "duedate"   => $oDate->format("Y-m-d"),
                            "period"    =>  $oRow->period_type,
                            "description"   =>  $oRow->description,
                        ];
                         break;
                    }
                }
                
                //var_dump($oRow);//var_dump($sFormationDate);

                // TODO: performance bug identified as 
                // TODO: if $sDate is in past, generate new sDate: take day and month of formation instead in current year
                while(($oDate<$oDateNow && $oRow->period_type=='recurring') || (!$bCondition && $oRow->custom_condition!=""))
                {
                    //$b1 = ($oDate<$oDateNow && $oRow->period_type=='recurring');
                    //$b2 = (!$bCondition && $oRow->period_type=='recurring');
                    // date is less then next/current date
                    // next date based on generated date
                    // 
                    //echo "<br>";
                    
                    // try to find the date after today
                    if($oDate<$oDateNow)
                    {
                        // fiscal year requires month or day differences, therefore revert them back to fiscal date
                        // of the year + 1, because the date generated exist in past time
                        if($oRow->base_type=='fiscal'){
                            $this->resetFiscalDate($oRow,$oDate,$oDateFormation);
                        }
                        //echo "Date was past: -- " . $oDate->format("Y-m-d");
                        $oDate = $this->getNotificationDate($oRow,$oDate->format("Y-m-d"));
                    } else if($oRow->custom_condition!="")  // solve any custom condition that exist
                    {
                        //echo "solving custom: ";
                        //echo $oDate->format("Y-m-d");
                        $oDate->setDate($oDate->format("Y"),$oDateFormation->format("m"),$oDateFormation->format("d"));
                        $bCondition = $this->customCondition($oDate,$oRow,$oDateFormation);
                    } else {    // set custom condition solved when dont exist
                        $bCondition = true;
                    }

                    $this->setFixedMonthDay($oRow,$oDate);
                }
                // reset date based on subtract condition of days for e.g. -1 mean last day of month, 1 mean 1st day of month
                if($oRow->day_diff<0)
                {
                    $oDate->setDate($oDate->format("Y"),$oDate->format("m"),1);
                    $oDate = new DateTime($oDate->format("Y-m-d"). " {$oRow->day_diff} days");
                }
                if($bReturnSingle)
                {
                    //echo $sRow;
                    $data = (object)[
                        "formation" =>  $sFormationDate,
                        "fiscal" =>  $sFiscalDate,
                        "state" =>  $oRow->state,
                        "type"   =>  $oRow->entity_type,
                        "duedate"   => $oDate->format("Y-m-d"),
                        "period"    =>  $oRow->period_type,
                        "description"   =>  $oRow->description,
                        "nowdate"   =>  $oDateNow->format("Y-m-d")
                    ];
                    break;
                } else {
                    $data[] = (object)[
                        "formation" =>  $sFormationDate,
                        "fiscal" =>  $sFiscalDate,
                        "state" =>  $oRow->state,
                        "type"   =>  $oRow->entity_type,
                        "duedate"   => $oDate->format("Y-m-d"),
                        "period"    =>  $oRow->period_type,
                        "description"   =>  $oRow->description,
                        "nowdate"   =>  $oDateNow->format("Y-m-d")
                    ];
                }
                
            }

        return $data;


    }

    private function resetFiscalDate($oRow,$oDate,$oDateFormation)
    {
        $iFiscalYearDiff = (int)($oRow->year_diff?:1);
        $oDate->setDate($oDate->format("Y")+$iFiscalYearDiff,$oDateFormation->format("m"),$oDateFormation->format("d"));
    }

    private function setFixedMonthDay($oRow,$oDate)
    {        
        // set specific month/days
        if(is_numeric(substr($oRow->day_diff,0,1)) && $oRow->day_diff!=null) $oDate->setDate($oDate->format("Y"),$oDate->format("m"),$oRow->day_diff);
        if(is_numeric(substr($oRow->month_diff,0,1)) && $oRow->month_diff!=null) $oDate->setDate($oDate->format("Y"),$oRow->month_diff,$oDate->format("d"));
    }

    private function getNotificationDate($oRow,$sFormationDate)
    {
        $sDate = "";

        $iYearDiff = $oRow->year_diff;
        $iMonthDiff = $oRow->month_diff;
        $iDayDiff = $oRow->day_diff;
        
        if($iYearDiff==null) $iYearDiff = 0;
        if($iMonthDiff==null) $iMonthDiff = 0;
        if($iDayDiff==null) $iDayDiff = 0;

        switch($oRow->base_type)
        {
            case ("formation"||"fiscal"):
                $sDateInterval = $sFormationDate;
                
                if($oRow->base_type=="fiscal")
                {
                    if((int)$iYearDiff>1) 
                        $sDateInterval .= " {$iYearDiff} year";
                } else if(strpos($iYearDiff,"+")!==false) $sDateInterval .= " {$iYearDiff} year";

                if(strpos($iMonthDiff,"+")!==false) $sDateInterval .= " {$iMonthDiff} months";
                if(strpos($iDayDiff,"+")!==false) $sDateInterval .= " {$iDayDiff} days";
                //$sDateInterval;

                //echo "<br>";
                // select today
                $oDateTime = new DateTime($sDateInterval);
            break;
        }

        return $oDateTime;
    }

    private function customCondition($oDateTime,$oRow,$oDateFormation)
    {
        $month = $oDateTime->format("m");// may used in custom_condition column as $month
        $year = $oDateTime->format("Y");// as $year
        $day = $oDateTime->format("d");// as $dat
        $duedate = $formation = 0;// as $formation

        $formation = $oDateFormation->format("Y-m-d");

        $oJson = json_decode($oRow->custom_condition);

        $bCondition = $bInsideCondition = -1;
        $a = -1;
        switch($oJson->case)
        {
            case "inarray":
                foreach($oJson->against as $v)
                {
                    $index = $v;
                    $str = '$bInsideCondition'." = ($oJson->condition $oJson->operation $v);";
                    eval($str);
                    $bCondition = $bInsideCondition;
                    if($bCondition)
                    {
                        eval($oJson->success.";");
                        
                        $oDateTime->setDate($year,$month,$day);
                        
                        return true;
                    }
                }
            break;
            case "age":
                $formation = $oDateFormation->format("Y-m-d");
                $duedate = $oDateTime->format("Y-m-d");
                $formation2 = strtotime($duedate) . ">" . strtotime($formation. " +5 months");
                $str = '$bInsideCondition'." = ($oJson->condition $oJson->operation $oJson->against);";
                eval($str);
                $bCondition = $bInsideCondition;
                if($bCondition)
                {
                    return true;
                } else {
                    eval($oJson->fail.";");// contains "$year = $year + 1;"
                    $oDateTime->setDate($year,$month,$day);
                }
            break;
            case "math":
                $str = '$bInsideCondition'." = ($oJson->condition $oJson->operation $oJson->against);";
                eval($str);
                $bCondition = $bInsideCondition;
                if($bCondition)
                {
                    return true;
                } else if($oJson->fail!=""){
                    eval($oJson->fail.";");// contains "$year = $year + 1;"
                    $oDateTime->setDate($year,$month,$day);
                }
            break;
        }

        return false;
    }

    private function getOperatorOrNumber($sOperatorNumber,$iNumber)
    {
        if(in_array(substr($sOperatorNumber,0,1),["+"]))
        {
            return $iNumber;
        } else {
            return $sOperatorNumber;
        }
    }
}