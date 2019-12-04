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
            
            return ['msg'=>'Account id not found','msg_type'=>'error'];
        }

        return $result;
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
            return ['msg'=>'Entities not found.','msg_type'=>'error'];
        }

        return $result;
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
            'id'    =>  $id
        ];

        $query = $this->db->get_where($this->table, $data);
        $result = $query->row();
        
        if ($result) {
            return ['msg'=>'No such account found','msg_type'=>'error'];
        }

        return $result;
    }
}