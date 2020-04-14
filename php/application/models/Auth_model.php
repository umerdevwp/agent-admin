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
        if (!empty($email) and $email != NULL) {
            $this->db->where('email', $email);
        }
        $query = $this->db->get();
        $result = $query->row();
        return $result;
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
