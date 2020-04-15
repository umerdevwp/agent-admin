<?php

class Entity_model extends CI_Model
{
    public static $entity_name = "account_name";
    public static $entity_type = "account_type";
    public static $entity_structure = "entity_type";
    public static $parent_entity = "parent_account";
    public static $entity_owner = "owner";
    public static $entity_number = "account_number";

    private $table = "zoho_accounts";
    private $table_entityMeta = "entitymeta";
    public function __construct()
    {
        $this->load->database();
    }

    public function pullUniqueStatuses()
    {
        $this->db->select('status');
        $this->db->distinct();
        $this->db->where('status IS NOT NULL');
        $query = $this->db->get($this->table);
        $result = $query->result();
        if (!is_array($result)) {
            return ['message' => 'Entities not found.', 'type' => 'error'];
        }
        return ['type' => 'ok', 'results' => $result];
    }

    public function insertStatus($data = NULL)
    {
        if ($this->db->table_exists($this->table_entityMeta)) {

            if (!empty($data)) {
                $this->db->insert($this->table_entityMeta, $data);
                $insert_id = $this->db->insert_id();
                return  $insert_id;
            } else {
                log_message('error', "Couldn't create admin");
            }
        } else {
            log_message('error', 'Administrators table does not exit');
        }
    }

    public function checkIfExists($id = NULL)
    {
        if ($this->db->table_exists($this->table_entityMeta)) {
            $this->db->select('*');
            $this->db->from($this->table_entityMeta);
            $this->db->where('zoho_accounts_id', $id);
            $query = $this->db->get();
            return $query->result();
        } else {
            log_message('error', 'Administrators table does not exit');
            return FALSE;
        }
    }


    public function updateExistingEntityStatus($id = NULL, $data = NULL)
    {
        if ($this->db->table_exists($this->table_entityMeta)) {
            if (!empty($data) and !empty($id)) {
                $this->db->where('zoho_accounts_id', $this->input->post("id"));
                $this->db->update($this->table_entityMeta, $data);
                if ($this->db->affected_rows() > 0) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                log_message('error', 'Data or ID is missing');
                return FALSE;
            }
        } else {
            log_message('error', 'Administrators table does not exit');
            return NULL;
        }
    }


    public function ownerValidity($parent, $entity){
     //make sure the entity belongs to right parent or not.
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('id', $entity);
        $this->db->where('parent_account', $parent);
        $query = $this->db->get();
        return $query->result();
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
            Entity_model::$parent_entity    =>  $id
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
            Entity_model::$parent_entity =>  $id
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
            'a.id'    =>  $id
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


        if (! is_array($result)) {
            return ['msg'=>'No contacts available','msg_type'=>'error'];
        }

        return $result;


    }
}
