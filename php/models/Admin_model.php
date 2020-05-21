<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin_model extends CI_Model
{

    private $table = "admins";
    function createAdmin($data = Null)
    {
        if ($this->db->table_exists($this->table)) {
            $checkTableCount = $this->checkAdminExist();
            if (empty($checkTableCount)) {
                $data = array(
                    'first_name' => 'Chuck',
                    'last_name' => 'Boyce',
                    'email' => 'chboyce@unitedagentservices.com',
                    'zoho_id' => '999999999'
                );
                log_message('info', "Administrator has has been created");
            }
            if (!empty($data)) {
                $this->db->insert($this->table, $data);
                $insert_id = $this->db->insert_id();
                return  $insert_id;
            } else {
                log_message('error', "Couldn't create admin");
            }
        } else {
            log_message('error', 'Administrators table does not exit');
        }
    }

    function checkAdminExist($email = NULL)
    {
        if ($this->db->table_exists($this->table)) {
            $this->db->select('*');
            $this->db->from($this->table);
            if (!empty($email)) {
                $this->db->where('email', $email);
            }
            $query = $this->db->get();
            return $query->result();
        } else {
            log_message('error', 'Administrators table does not exit');
            return NULL;
        }
    }

    function updateAdmin($email = NULL)
    {
        if ($this->db->table_exists($this->table)) {
            $this->db->set('last_logged_time', 'NOW()', FALSE);
            $this->db->where('email', $email);
            $this->db->update($this->table);
            if ($this->db->affected_rows() > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            log_message('error', 'Administrators table does not exit');
        }
    }


    function getAdmins()
    {
        if ($this->db->table_exists($this->table)) {
            $this->db->select('*');
            $this->db->from($this->table);
            $query = $this->db->get();
            return $query->result();
        } else {
            log_message('error', 'Administrators table does not exit');
            return NULL;
        }
    }


    function getOneAdmins($id = NULL)
    {
        if ($this->db->table_exists($this->table)) {
            $this->db->select('*');
            $this->db->from($this->table);
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->result();
        } else {
            log_message('error', 'Administrators table does not exit');
            return NULL;
        }
    }


    function deleteAdmin($id = NULL)
    {
        if ($this->db->table_exists($this->table)) {
            $this->db->where('id', $id);
            return $this->db->delete($this->table);
        } else {
            log_message('error', 'Administrators table does not exit');
            return NULL;
        }
    }


    public function updateAdminInfo($id = NULL, $data = NULL)
    {
        if ($this->db->table_exists($this->table)) {
            if (!empty($data) and !empty($id)) {
                $this->db->where('id', $this->input->post("id"));
                $this->db->update('admins', $data);
                if ($this->db->affected_rows() > 0) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else{
                log_message('error', 'Data or ID is missing');
                return FALSE;
            }
        } else {
            log_message('error', 'Administrators table does not exit');
            return NULL;
        }
    }
}
