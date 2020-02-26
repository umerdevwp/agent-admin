<?php
class Accounts_model extends CI_Model
{

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

        $this->db->limit(1);
        $this->db->select("ra.*");
        $this->db->from("zoho_accounts a");
        $this->db->join("zoho_registered_agents ra","a.ra=ra.id");
        $this->db->where(["a.id"=>$id]);
        $query = $this->db->get();

        $result = $query->row();

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
            'parent_entity'    =>  $id
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
            "parent_entity" =>  $id
        ];

        $result = $this->db->get_where($this->table,$data,1,1);
        //echo $this->db->last_query();
        
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
            'a.id'    =>  $id
        ];

        //$this->db->from($this->table." a");
        $this->db->select("a.*,um.account_status");
        $this->db->join("usersmeta um","um.id=a.id",'left');
        //$this->db->where('um.account_status',null,false);
        $this->db->where("(um.account_status IS NULL OR um.account_status='active')");
        //$this->db->or_where(["um.account_status"=>"active"]);

        $query = $this->db->get_where($this->table . " a", $data);
        //echo $this->db->last_query();
        //die;
        $result = $query->row();
        
        if (!$result) {
            return ['msg'=>'No such account found','msg_type'=>'error'];
        }

        return $result;
    }
	
	public function getAll($aColumns=[])
    {
        if(count($aColumns))
        {
            $this->db->select($aColumns);
        }

        $query = $this->db->get('zoho_accounts');
        $result = $query->result_object();
        if (!$result){
            return ['message' => 'No such account found', 'type' => 'error'];
        }

        return ['type'=>'ok','results'=>$result];
    }
}