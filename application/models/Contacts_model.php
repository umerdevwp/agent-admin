<?php
class Contacts_model extends CI_Model
{

    private $table = "zoho_contacts";

    public function __construct()
    {
        $this->load->database();
    }

    public function getAll($id)
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
}