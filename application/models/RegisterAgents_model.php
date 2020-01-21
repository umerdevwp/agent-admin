<?php
class RegisterAgents_model extends CI_Model
{

    private $table = "zoho_registered_agents";

    public function __construct()
    {
        $this->load->database();
    }

    public function find($arData)
    {
        if(count($arData)>0)
        {

            $this->db->limit(1);

            $query = $this->db->get_where($this->table, $arData);

            $result = $query->row();

        }

        return $result;
    }

    public function getAll()
    {
        $sQuery = "SELECT file_as, address, address2, state, city, zip_code "
                . "FROM {$this->table} "
                . "WHERE right(registered_agent_name, 3) = 'UAS' ORDER BY state";

        $oResult = $this->db->query($sQuery);
        $result = $oResult->result_object();

        if (!$result) {
            return ['type' => 'error', 'message'=>'No records found'];
        }

        return $result;
    }
}