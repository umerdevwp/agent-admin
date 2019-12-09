<?php
class RegisterAgent_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    public function find($arData)
    {
        if(count($arData)>0)
        {

            $this->db->limit(1);

            $query = $this->db->get_where('zoho_registered_agents', $arData);

            $result = $query->row();

        }

        return $result;
    }
}