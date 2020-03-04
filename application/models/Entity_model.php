<?php
class Entity_model extends CI_Model
{
    public static $entity_name = "account_name";
    public static $entity_type = "account_type";
    public static $entity_structure = "entity_type";
    public static $parent_entity = "parent_account";
    public static $entity_owner = "owner";
    public static $entity_number = "account_number";

    private $table = "zoho_accounts";

    public function __construct()
    {
        $this->load->database();
    }

    public function loadAccount($id)
    {

        $data = [
            'id' => $id
            //'id'    =>  '4071993000000411060',
        ];

        $query = $this->db->get_where('zoho_accounts', $data);

        $result = $query->row();

        if (! $result) {
            
            return ['message'=>'Account id not found','type'=>'error'];
        }

        return ['type'=>'ok','results'=>$result];
    }

    public function getAgentAddress($id)
    {
        $query = $this->db->get_where("zoho_registered_agents",['id'=>$id]);

        $this->db->limit(1);
        $this->db->select("ra.*");
        $this->db->from("zoho_accounts a");
        $this->db->join("zoho_registered_agents ra","a.ra=ra.id");
        $this->db->where(["a.id"=>$id]);
        $query = $this->db->get();
        
        $result = $query->row();
        //echo $this->db->last_query();
//var_dump($result);die;
        if(!$result)
        {
            return false;
        }
        
        return $result;
    }

    /**
     * Get array of child entities of sessioned user
     * @param Int $id zoho id of logged in session user
     * @param String $columns (optional) comma seprated columns name
     */
    public function loadChildAccounts($id,$columns="")
    {
        $data = [
            Entity_model::$parent_entity    =>  $id
        ];

        // select required columns if set
        if(!empty($columns))
        {
            $this->db->select($columns);
        }

        $query = $this->db->get_where($this->table,$data);
        
        $result = $query->result();
        
        if(!is_array($result))
        {
            return ['message'=>'Entities not found.','type'=>'error'];
        }

        return ['type'=>'ok','results'=>$result];
    }

    public function hasEntities($id)
    {
        $data = [
            Entity_model::$parent_entity =>  $id
        ];

        $result = $this->db->get_where($this->table,$data,1,1);
        
        $row = $result->row();
        //var_dump($row);

        if($row->id>0)
        {
            return true;
        }

        return false;
    }

    public function getOne($id)
    {
        $data = [
            'id'    =>  $id
        ];

        $query = $this->db->get_where($this->table, $data);
        $result = $query->row();
        
        if (!$result) {
            return ['msg'=>'No such account found','msg_type'=>'error'];
        }

        return $result;
    }
	
	  public function getAll()
    {
        $query = $this->db->get('zoho_accounts');
        $result = $query->result_object();
        if (!$result) {
            return ['msg' => 'No such account found', 'msg_type' => 'error'];
        }

        return $result;
    }
}