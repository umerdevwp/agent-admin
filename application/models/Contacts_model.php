<?php
class Contacts_model extends CI_Model
{

    private $table = "zoho_contacts";

    public function __construct()
    {
        $this->load->database();
    }

    public function getAllFromEntityId($id)
    {
        // TODO: remove fake id
        $data = [
            'entity_name' => $id,
            //'contact_owner'    =>  '4071993000000244001', // fake id
        ];

        $query = $this->db->get_where($this->table, $data);
        $result = $query->result_object();
        //var_dump($result);die;
        if (! is_array($result)) {
            return ['msg'=>'No contacts available','msg_type'=>'error'];
        }

        return $result;
    }

    public function getAllFromEntityList($arCommaIds)
    {
        $this->db->from($this->table);
        $this->db->where_in('entity_name',$arCommaIds);
        $query = $this->db->get();
        $result = $query->result_object();
        //echo $this->db->last_query();
        //var_dump($result);die;
        if (! is_array($result)) {
            return ['msg'=>'No contacts available','msg_type'=>'error'];
        }

        return $result;
    }
}