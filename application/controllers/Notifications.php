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

    public function notify()
    {
        $this->load->model("Tasks_model");
        
        $aResult = $this->Tasks_model->getAllNotifications();

        if(count($aResult['results']))
        {
            foreach($aResult as $v)
            {
                // check today is the day to shoot mail to particular entity
                if($this->checkTodayShootDay($v->due_date,$v->start_date,$v->status))
                {
                    // shoot email and continue to next user
                }
            }
        }
    }

    private function checkTodayShootDay($sDueDate,$sStartDate,$sStatus)
    {
        
    }

    public function setRules()
    {
        $this->load->model("Notifications_model");

        //$result = $this->Notifications_model->findRule("HI","LLC");
        $result = $this->Notifications_model->findRule();

        // sample dates for testing
        $aSampleDate = ["1/1/2017","1/1/2018","5/7/19","1/7/20","1/26/20"];
        foreach($result as $oRow)
        {
            var_dump($oRow);
            // reset me with actual entity date
            $sFormationDate=$aSampleDate[2];
            
            $oDate = $this->getNotificationDate($oRow,$sFormationDate);
            
            $oDateFormation = new DateTime("$sFormationDate");
            $oDateNow = new DateTime("now");
            $bCondition = false;
            // TODO: performance bug identified as 
            // TODO: if $sDate is in past, generate new sDate: take day and month of formation instead in current year
            while( ($oDate<$oDateNow && $oRow->period_type=='recurring') || !$bCondition)
            {
                // date is less then next/current date
                // next date based on generated date
                // 
                echo "<br>";
                
                /*$iTempDayDiff = 0;
                // if date changed due to day diff
                // return back it close to formation_date
                if($oRow->day_diff<0){
                    $iTempDayDiff = $oRow->day_diff*-1;
                    $oTempDate = new DateTime($oDate->format("Y-m-d")." +{$iTempDayDiff} days");
                }
                // if date changed due to month diff
                // return back it close to formation_date
                if($oRow->month_diff<0){
                    $iTempMonthDiff = $oRow->month_diff*-1;
                    $oTempDate = new DateTime($oDate->format("Y-m-d")." +{$iTempMonthDiff} months");
                }

                $oDate->setDate($oTempDate->format("Y"),$oTempDate->format("m"),$oTempDate->format("d"));
                */
                // fiscal year requires month or day differences, therefore revert them back to fiscal date
                // of the year + 1, because the date generated exist in past time
                if($oRow->base_type=='fiscal'){
                    $oDate->setDate($oDate->format("Y")+1,$oDateFormation->format("m"),$oDateFormation->format("d"));
                }
                // try to find the date after today
                if($oDate<$oDateNow)
                {
                    echo "Date was past: -- ";
                    echo $oDate = $this->getNotificationDate($oRow,$oDate->format("Y-m-d"));
                } else if($oRow->custom_condition!="")  // solve any custom condition that exist
                {
                    echo "solving custom: ";
                    echo $oDate->format("Y-m-d");
                    $oDate->setDate($oDate->format("Y"),$oDateFormation->format("m"),$oDateFormation->format("d"));
                    $bCondition = $this->customCondition($oDate,$oRow,$oDateFormation);
                } else {    // set custom condition solved when dont exist
                    $bCondition = true;
                }

                // set specific month/days
                if(is_numeric(substr($oRow->day_diff,0,1)) && $oRow->day_diff!=null) $oDate->setDate($oDate->format("Y"),$oDate->format("m"),$oRow->day_diff);
                if(is_numeric(substr($oRow->month_diff,0,1)) && $oRow->month_diff!=null) $oDate->setDate($oDate->format("Y"),$oRow->month_diff,$oDate->format("d"));
            }
            // reset date based on subtract condition of days for e.g. -1 mean last day of month, 1 mean 1st day of month
            if($oRow->day_diff<0)
            {
                $oDate->setDate($oDate->format("Y"),$oDate->format("m"),1);
                $oDate = new DateTime($oDate->format("Y-m-d"). " {$oRow->day_diff} days");
            }

            $sDate = $oDate->format("Y-m-d");

            $sRow =<<<HT
            <br><br>
            Formation: {$sFormationDate} <br>
            Condition: {$oRow->state},    {$oRow->entity_type},  {$oRow->description},  {$sDate} <br>
            Result: {$sDate}<br>
HT;
            echo $sRow;
        }
        
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
            case "date":
                // select today
                $oDateTime = new DateTime("now");
                $oDateNow = new DateTime("now");
                $iTempYearDiff = $oDateTime->format('Y');
                // make date
                $oDateTime->setDate($iTempYearDiff,$iMonthDiff,$iDayDiff);
                // is date in past, move to next year
                if($oDateTime<$oDateNow){
                    // set for annually critearea
                    $iTempYearDiff += 1;
                }
                // make date
                $oDateTime->setDate($iTempYearDiff,$iMonthDiff,$iDayDiff);
            break;
            case ("formation"||"fiscal"):
                $sDateInterval = $sFormationDate;
                if(strpos($iYearDiff,"+")!==false) $sDateInterval .= " {$iYearDiff} year";
                if(strpos($iMonthDiff,"+")!==false) $sDateInterval .= " {$iMonthDiff} months";
                if(strpos($iDayDiff,"+")!==false) $sDateInterval .= " {$iDayDiff} days";
                echo $sDateInterval;

                echo "<br>";
                // select today
                $oDateTime = new DateTime($sDateInterval);



/*
                echo "<br>";
                // return date
                echo $sDate = $oDateTime->format("Y-m-d");
                if($iYearDiff && $iMonthDiff && $iDayDiff)
                {
                    echo " --> to specific --> ";
                    // make date
                    $oDateTime->setDate(
                        $this->getOperatorOrNumber($iYearDiff,$oDateTime->format("Y")),
                        $this->getOperatorOrNumber($iMonthDiff,$oDateTime->format("m")),
                        $this->getOperatorOrNumber($iDayDiff,$oDateTime->format("d"))
                    );
                    // return date
                    echo $sDate = $oDateTime->format("Y-m-d");
                }*/
                

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