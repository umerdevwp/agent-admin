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
            'id'    =>  $id
        ];

        $query = $this->db->get_where($this->table,$data);
        $result = $query->result();
        
        if(!is_array($result))
        {
            return ['msg'=>'Entities not found.','msg_type'=>'error'];
        }

        return $result;
    }
}