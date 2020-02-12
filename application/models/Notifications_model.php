<?php
class Notifications_model extends CI_Model
{

    private $table = "notifications";
    private $table_rule = "rules";
    private $table_states = "rule_states";

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
        {
            $this->db->where('state',$sState);
            $this->db->where('entity_type',$sEntityType);
            $this->db->where("origin_type",$sOrigin);
        }
        // remove this line, only for testing
        //$this->db->where("r.custom_condition!=",'');

        $query = $this->db->get();
        $result = $query->result_object();
        
        //echo $this->db->last_query();
        
        return $result;
    }
}