<?php
class Attachments_model extends CI_Model
{

    private $table = "temp_attachments";

    public function __construct()
    {
        $this->load->database();
    }

    public function getAll($id)
    {
        // TODO: remove fake id
        $data = [
            //'contact_owner' => $id,
            'owner'    =>  '1000000028468', // fake id
        ];

        $query = $this->db->get_where($this->table, $data);
        $result = $query->result_object();
        //var_dump($result);die;
        if (! is_array($result)) {
            return ['msg'=>'No attachments available','msg_type'=>'error'];
        }

        return $result;
    }

    public function checkOwnership($owner,$id)
    {
        $data = array(
            "owner" =>  $owner,
            "id"    =>  $id
        );
        $query = $this->db->get_where($this->table,$data);
        $row = $query->row();
        if (isset($row))
        {
            return $row->id;
        }
        return false;
    }
}