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
        
        if (!$result) {
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

    public function updateTheme($id,$val)
    {
        $data = [
            "personal_theme"    =>  $val,
        ];

        $where = [
            "id"    =>  $id,
        ];

        if($this->insertNotExist($id))
        {
            $result = $this->db->update($this->table,$data,$where);
            if($result)
            {
                return ["ok"=>"Updated successfully"];
            } else {
                return ["error"=>"failed to update theme"];
            }
        } else {
            return ["error"=>"failed to update theme"];
        }
    }

    public function getMeta($id,$metaname,$bReturnArray)
    {
        $arWhere = [
            "id"    =>  $id,
        ];

        $this->db->select(["id",$metaname]);
        $this->db->from($this->table);
        $this->db->where($arWhere);
        
        if($bReturnArray) $result = $this->db->get()->row_array();
        else $result = $this->db->get()->row();
        
        if($result)
        {
            return $result;
        } else {
            return false;
        }
    }

    private function insertNotExist($id)
    {
        // check user has a meta row
        $row = $this->getOne($id);
        
        if($row->id>0)
        {
            return true;
        } else {
            $data['id'] = $id;
            $result = $this->db->insert($this->table, $data);
            
            if(!$result)
            {
                return false;
            }
            
        }

        return true;
        
    }
}