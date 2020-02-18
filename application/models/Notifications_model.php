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


}