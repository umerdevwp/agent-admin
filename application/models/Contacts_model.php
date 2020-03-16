<?php

class Contacts_model extends CI_Model
{

    private $table = "zoho_contacts";
    private $contact_meta = "contactmeta";

    public function __construct()
    {
        $this->load->database();
    }

    public function getAllFromEntityId($id)
    {
        // TODO: remove fake id


        $this->db->select('zoho_contacts.*, contactmeta.contact_id, contactmeta.ofac_status, contactmeta.last_updated_time');
        $this->db->from('zoho_contacts');
        $this->db->join('contactmeta', 'contactmeta.contact_id=zoho_contacts.id', 'left');
        $this->db->where(["zoho_contacts.entity_name" => $id]);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->result_object();
        }

        if (!is_array($result)) {
            return ['msg' => 'No contacts available', 'msg_type' => 'error'];
        }

        return $result;
    }

    public function getAllFromEntityList($arCommaIds)
    {
        $this->db->from($this->table);
        $this->db->where_in('entity_name', $arCommaIds);
        $query = $this->db->get();
        $result = $query->result_object();
        //echo $this->db->last_query();
        //var_dump($result);die;
        if (!is_array($result)) {
            return ['msg' => 'No contacts available', 'msg_type' => 'error'];
        }

        return $result;
    }

    public function checkRowExist($aData)
    {
        $query = $this->db->select("id")->get_where($this->table, $aData);

        $row = $query->row();

        $bResult = false;

        if ($row) {
            $bResult = true;
        }

        return $bResult;
    }


    /**
     * Add Contacts into the Zoho_contact table and returns the ID.
     *
     * @param $data contains the array of variables which satisfies the current table structure.
     */
    public function addContact($data)
    {
        $this->db->insert($this->table, $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }


    /**
     * Add Contacts extra info into the contactmeta table and returns the ID.
     *
     * @param $data contains the array of variables which satisfies the current table structure.
     */
    public function addContactMeta($data)
    {
        $this->db->insert($this->contact_meta, $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function updateContactMeta($id, $ofac_status = NULL)
    {

        if ($this->db->table_exists($this->contact_meta)) {
            !empty($ofac_status) ? $this->db->set('ofac_status', $ofac_status) : '';
            $this->db->set('last_updated_time', 'NOW()', FALSE);
            $this->db->where('id', $id);
            $this->db->update($this->contact_meta);

            if ($this->db->affected_rows() > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            log_message('error', 'Contactmeta table does not exit');
        }

    }


    Public function getOfacStatus($id)
    {
        $this->db->select('ofac_status');
        $this->db->from($this->contact_meta);
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result();
    }

    public function getAllContactMeta()
    {
        if ($this->db->table_exists($this->contact_meta)) {
            $this->db->select("*");
            $this->db->from($this->contact_meta);
            $query = $this->db->get();
            return $query->result();
        } else {
            log_message('error', 'Contactmeta table does not exit');
        }
    }


    public function ofac_cron_job_get()
    {
        $data = [
            'slug' => 'ofac_status',
        ];
        $query = $this->db->get_where('appmeta', $data);
        $result = $query->result_object();

        if (!is_array($result)) {
            return ['msg' => 'No contacts available', 'msg_type' => 'error'];
        }
        return $result;
    }


    public function ofac_cron_job_insert()
    {
        $data = array(
            'slug' => 'ofac_status',
        );
        $this->db->insert('appmeta', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function ofac_cron_job_update()
    {
        $this->db->set('updated', 'NOW()', FALSE);
        $this->db->where('slug', 'ofac_status');
        $this->db->update('appmeta');
        return ['type' => 'ok'];
    }

}
