<?php
class Accounts_model extends CI_Model
{

    private $table = "zoho_accounts";

    public function __construct()
    {
        $this->load->database();
    }

    public function loadAccount($id)
    {

        $data = [
            'id' => $id
            //'id'    =>  '4071993000000411060',
        ];

        $query = $this->db->get_where('zoho_accounts', $data);

        $result = $query->row();

        if (!$result) {
            return ['message' => 'Account id not found', 'type' => 'error'];
        }

        return ['type' => 'ok', 'results' => $result];
    }

    public function getAgentAddress($id)
    {

        $this->db->limit(1);
        $this->db->select("ra.*");
        $this->db->from("zoho_accounts a");
        $this->db->join("zoho_registered_agents ra", "a.ra=ra.id");
        $this->db->where(["a.id" => $id]);
        $query = $this->db->get();

        $result = $query->row();

        if (!$result) {
            return false;
        }

        return $result;
    }

    /**
     * Get array of child entities of sessioned user
     * @param Int $id zoho id of logged in session user
     * @param String $columns (optional) comma seprated columns name
     */
    public function loadChildAccounts($id, $columns = "")
    {
        $data = [
            'parent_entity'    =>  $id
        ];

        // select required columns if set
        if (!empty($columns)) {
            $this->db->select($columns);
        }

        $query = $this->db->get_where($this->table, $data);



        $result = $query->result();

        if (!is_array($result)) {
            return ['message' => 'Entities not found.', 'type' => 'error'];
        }

        return ['type' => 'ok', 'results' => $result];
    }

    public function hasEntities($id)
    {
        $data = [
            "parent_entity" =>  $id
        ];

        $result = $this->db->get_where($this->table, $data, 1, 1);
        //echo $this->db->last_query();

        $row = $result->row();
        //var_dump($row);

        if ($row->id > 0) {
            return true;
        }

        return false;
    }

    public function getOne($id)
    {
        $data = [
            'id'    =>  $id
        ];
        $query = $this->db->get_where($this->table, $data);
        $result = $query->row();
        if (!$result) {
            return ['msg' => 'No such account found', 'msg_type' => 'error'];
        }
        return $result;
    }

    public function getAll()
    {
        // $query = $this->db->get('zoho_accounts');
        // $result = $query->result_object();
        // if (!$result) {
        //     return ['msg' => 'No such account found', 'msg_type' => 'error'];
        // }
        // return $result;

        $this->db->select('zoho_accounts.*, entitymeta.entity_status');
        $this->db->from('zoho_accounts');
        $this->db->join('entitymeta','entitymeta.zoho_accounts_id=zoho_accounts.id', 'left');
        // $this->db->where(["entitymeta.zoho_accounts_id", "zoho_accounts.id"]);
        $query = $this->db->get();
        $result = $query->result_object();
        // if ( $query->num_rows() > 0 )
        // {
        //     $result = $query->result_object();
        // }
        // else
        // {
        // $data = [
        //             'entity_name' => $id,
        //             //'contact_owner'    =>  '4071993000000244001', // fake id
        //         ];
        // $query = $this->db->get_where($this->table, $data);
        // $result = $query->result_object();

        // }
           
        if (! is_array($result)) {
            return ['msg'=>'No contacts available','msg_type'=>'error'];
        }

        return $result;


    }
}
