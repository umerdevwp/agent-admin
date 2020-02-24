<?php
class Notifications_model extends CI_Model
{

    private $table = "notification_subscriptions";
    private $table_rule = "rules";
    private $table_states = "rule_states";
    private $table_entity_states = "entity_states";
    private $table_zoho_accounts = "zoho_accounts";
    private $table_entitymeta = "usersmeta";

    public function __construct()
    {
        $this->load->database();
    }

    public function add($data)
    {
        
        $this->db->insert($this->table,$data);

        return $this->db->insert_id();
        
    }

    public function getAll()
    {
        $aWhere = [
            'status'    =>  'pending'
        ];

        $query = $this->db->get_where($this->table,$aWhere);
        $result = $query->result_object();
        
        if (! is_array($result)) {
            return ['message'=>'No tasks available','type'=>'error'];
        }

        return ['type'=>'ok','results'=>$result];
    }

    public function findRule($sState="",$sEntityType="",$sOrigin="domestic")
    {

        $this->db->select("rs.id,rs.rule_id,rs.state,rs.entity_type,r.origin_type,r.base_type,r.period_type,r.month_diff,r.day_diff,year_diff,custom_condition,description");
        $this->db->from($this->table_rule . " r");
        $this->db->join($this->table_states. " rs","rs.rule_id = r.id");
        $result = null;
        
        if($sState!="")
            $this->db->where('state',$sState);
            
        if($sEntityType!="")
            $this->db->where('entity_type',$sEntityType);
        
        if($sOrigin!="")
            $this->db->where("origin_type",$sOrigin);

        // remove this line, only for testing
        //$this->db->where("r.custom_condition!=",'');

        $query = $this->db->get();
        $result = $query->result_object();
        
        //echo $this->db->last_query();
        
        return $result;
    }

    public function getSubscriptions($iEntityId=0)
    {
        
        $sQueryEntity =<<<HC
SELECT za.filing_state,za.entity_structure,za.formation_date,ns.* 
FROM {$this->table} ns
INNER JOIN {$this->table_entity_states} es ON ns.entity_id=es.entity_id
INNER JOIN {$this->table_zoho_accounts} za ON ns.entity_id=za.id
LEFT JOIN {$this->table_entitymeta} em ON ns.entity_id=em.id
WHERE (ISNULL(em.account_status) OR em.account_status='active')
AND ns.status='active'
HC;

        if($iEntityId>0) $sQueryEntity .= " AND ns.entity_id={$iEntityId}";

        $oQuery = $this->db->query($sQueryEntity);
        //echo $this->db->last_query();die;
        $result = $oQuery->result_object();

        if(!$result)
        {
            return ['type'=>'error','message'=>"No data found"];
        }

        return ['type'=>'ok','results'=>$result];
    }

    /**
     * Set calendar for upcoming due dates of notification for entity state and type
     * 
     */
    public function getRules($sEntityState,$sEntityType,$sFormationDate,$sFiscalDate="",$sNow="",$bReturnSingle=true)
    {
        $this->load->model("Notifications_model");
        

        //$state = $this->input->post("state");
        //$type = $this->input->post("type");

        $result = $this->findRule(($sEntityState?:""),($sEntityType?:""));
        //var_dump($result);
        //$result = $this->Notifications_model->findRule();

        $sFiscalDate = convToMySqlDate($sFiscalDate)?:date("2020-12-31");
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
                        // select this date for due as initial not filed yet
                         break;
                    }
                    // go to next rule if available as initial
                    continue;
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
}