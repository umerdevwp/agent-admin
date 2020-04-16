<?php

class Auth_model extends CI_Model
{

    private $table = "user_token";

    public function __construct()
    {
        $this->load->database();
    }

    public function add($data)
    {

        $this->db->insert($this->table, $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function tokenExists($token, $email = NULL)
    {
        $this->db->from($this->table);
        $this->db->where('token', $token);
		$this->db->where('expired_on >', date('Y-m-d H:i:s'));
		
        if (!empty($email) and $email != NULL) {
            $this->db->where('email', $email);
        }
        $query = $this->db->get();
        $sQuery = $this->db->last_query();
        //$result = $query->row(); remove it
		if($query->num_rows() > 0){
			return $query->row();
		} 
		else {
			return null;
		}
		
		//return $result remove it
        
    }


    public function delete($sub)
    {
        if ($this->db->table_exists($this->table)) {
            $this->db->where('sub', $sub);
            return $this->db->delete($this->table);
        } else {
            log_message('error', 'Administrators table does not exit');
            return NULL;
        }
    }
}
