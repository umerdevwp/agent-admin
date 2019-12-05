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

    public function getOne($id,$entityid="")
    {
        $data = [
            'id'    =>  $id
        ];

        if($entityid>0)
        {
            $data['related_to'] = $entityid;
        }

        $query = $this->db->get_where($this->table, $data);
        $result = $query->row();
        
        if (!$result) {
            return ['msg'=>'No tasks available','msg_type'=>'error'];
        }

        return $result;
    }

    public function getOneParentId($id,$parentid)
    {
        //SELECT * FROM zoho_accounts za LEFT JOIN zoho_tasks zt ON za.id=zt.related_to WHERE za.parent_entity=4071993000000411118 AND zt.id=4071993000001296114
        $this->db->select("zt.id as id");
        $this->db->from("zoho_accounts za");
        $this->db->join("zoho_tasks zt","za.id=zt.related_to","left");
        $this->db->where(["za.parent_entity"=>$parentid,"zt.id"=>$id]);
        $query = $this->db->get();
        $result = $query->row();
        //var_dump($result);die;
        //echo $this->db->last_query();die;
        if (!$result) {
            return ['msg'=>'No tasks available','msg_type'=>'error'];
        }

        return $result;
    }
}