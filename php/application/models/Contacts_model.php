<?php

class Contacts_model extends CI_Model
{

    private $table = "zoho_contacts";
    private $contact_meta = "contactmeta";
    private $aColumns = [
        "id"=>"id",
        "createdAt"=>"created_at",
        "owner"=>"owner",
        "firstName"=>"first_name",
        "lastName"=>"last_name",
        "salutation"=>"salutation",
        "leadSource"=>"lead_source",
        "accountName"=>"account_name",
        "contactType"=>"title",
        "email"=>"email",
        "name"=>"full_name",
        "vendorName"=>"vendor_name",
        "phone"=>"phone",
        "mobile"=>"mobile",
        "title"=>"title",
        "department"=>"department",
        "fax"=>"fax",
        "assistant"=>"assistant",
        "skypeId"=>"skype_id",
        "createdBy"=>"created_by",
        "emailOptOut"=>"email_opt_out",
        "layout"=>"layout",
        "tag"=>"tag",
        "modifiedBy"=>"modified_by",
        "secondaryEmail"=>"secondary_email",
        "currency"=>"currency",
        "createdTime"=>"created_time",
        "modifiedTime"=>"modified_time",
        "lastActivityTime"=>"last_activity_time",
        "isRecordDuplicate"=>"is_record_duplicate",
        "mailingStreet"=>"mailing_street",
        "mailingCountry"=>"mailing_country",
        "mailingCity"=>"mailing_city",
        "mailingState"=>"mailing_state",
        "mailingZip"=>"mailing_zip",
        "description"=>"description",
        "recordImage"=>"record_image",
        "lastVisitedTime"=>"last_visited_time",
        "firstVisitedTime"=>"first_visited_time",
        "referrer"=>"referrer",
        "firstVisitedUrl"=>"first_visited_url",
        "numberOfChats"=>"number_of_chats",
        "averageTimeSpentMinutes"=>"average_time_spent_minutes",
        "daysVisited"=>"days_visited",
        "visitorScore"=>"visitor_score",
        "contactId"=>"contact_id",
        "ofacStatus"=>"ofac_status",
        "lastUpdatedTime"=>"last_updated_time"
    ];

    public function __construct()
    {
        $this->load->database();
    }

    public function getAllFromEntityId($id,$aColumns=[])
    {
        // TODO: remove fake id

        $aMyColumns = [];
        if(count($aColumns)>0)
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","name","email","phone", "contactType",
                "mailingStreet", "mailingCountry", "mailingCity",
        "mailingState", "mailingZip"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }
        foreach($aMyColumns as $k=>$v)
        $this->db->select("zc.$v as `$k`");

        //$this->db->select('zoho_contacts.*,contactmeta.*');
        $this->db->from('zoho_contacts zc');
        $this->db->join('contactmeta','zc.id=contactmeta.contact_id', 'left');
        $this->db->where(["zc.account_name"=>$id]);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_object();
        }

        if (!is_array($result)) {
            return ['msg' => 'No contacts available', 'msg_type' => 'error'];
        }

        return $result;
    }

    public function getAllFromEntityList($arCommaIds,$aColumns=[])
    {
        $aMyColumns = [];
        if(count($aColumns)>0)
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","name","email","phone", "contactType",
                "mailingStreet", "mailingCountry", "mailingCity",
        "mailingState", "mailingZip"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }
        foreach($aMyColumns as $k=>$v)
            $this->db->select("$v as `$k`");
            
            $query = $this->db->get_where($this->table,"account_name IN (".implode(",",$arCommaIds).")");
            $result = $query->result();
//        echo $this->db->last_query();
 //       var_dump($result);die;
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
    /**
     * Get entity main contact by ascending contacts by id, to get 1st contact
     * related to entity..
     * 
     * @param Integer $iEntityId entity id of the contact owner
     * @param Array $aColumns array of columns api should response with
     */
    public function getEntityProfileContact($iEntityId,$aColumns=[])
    {
        $aMyColumns = [];
        if(count($aColumns)>0)
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","name","email","phone", "contactType",
                "mailingStreet", "mailingCountry", "mailingCity",
                "mailingState", "mailingZip"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }
        foreach($aMyColumns as $k=>$v)
        $this->db->select("zc.$v as `$k`");

        $this->db->from('zoho_contacts zc');
        $this->db->join('contactmeta','zc.id=contactmeta.contact_id', 'left');
        $this->db->where(["zc.account_name"=>$iEntityId]);
        $this->db->order_by("zc.id", "ASC");
        $this->db->limit(1);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->row_object();
        }

        if (!is_object($result)) {
            return ['type' => 'error','message'=>'No contacts available'];
        }

        return ['type'=>'ok','data'=>$result];
    }
}
