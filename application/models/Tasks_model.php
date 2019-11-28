<?php
class Tasks_model extends CI_Model
{

    private $table = "zoho_tasks";

    public function __construct()
    {
        $this->load->database();
    }

    public function getAll($id)
    {
        // TODO: remove fake id
        $data = [
            'related_to' => $id,
            //'task_owner'    =>  '4071993000000224013', // fake id
        ];

        $query = $this->db->get_where($this->table, $data);
        $result = $query->result_object();
        //var_dump($result);die;
        if (! is_array($result)) {
            return ['msg'=>'No tasks available','msg_type'=>'error'];
        }

        return $result;
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
}