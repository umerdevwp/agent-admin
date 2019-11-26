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

    public function loadChildAccounts($id)
    {
        $data = [
            'parent_entity'    =>  $id
        ];

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
}