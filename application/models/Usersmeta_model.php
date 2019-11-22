<?php
class Usersmeta_model extends CI_Model
{

    private $table = "usersmeta";
    private $arColumns = array(
            "zoho_access_token",
            "zoho_refresh_token"
        );
    public function __construct()
    {
        $this->load->database();
    }

    public function getOne($id)
    {
        $data = [
            'id'    =>  $id
        ];

        $query = $this->db->get_where($this->table, $data);
        $result = $query->row();
        
        if ($result) {
            return ['msg'=>'No tasks available','msg_type'=>'error'];
        }

        return $result;
    }

    public function insert($iUserId,$data)
    {
            /*
            // fails if column doesn't exist
            foreach($data as $k=>$v)
            {
                if(in_array($k,$arColumns)) continue;
                return false;
            }*/

            // check user has a meta row
            $row = $this->getOne($id);
            // update
            if($row->id>0)
            {
                $this->db->update($this->table, $data);
            // insert
            } else {
                $this->db->insert($this->table, $data);
            }
    }
}