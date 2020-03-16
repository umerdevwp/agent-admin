<?php

class Entity_model extends CI_Model
{

    private $table_Zoho_Zccounts = "zoho_accounts";
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
        $query = $this->db->get($this->table_Zoho_Zccounts);
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
        $this->db->from($this->table_Zoho_Zccounts);
        $this->db->where('id', $entity);
        $this->db->where('parent_entity', $parent);
        $query = $this->db->get();
        return $query->result();
    }


}
