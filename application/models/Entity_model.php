
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
            Entity_model::$parent_entity    =>  $id
        ];

        // select required columns if set
        if (!empty($columns)) {
            $this->db->select($columns);
        }

<<<<<<<< HEAD:application/models/Entity_model.php
        $query = $this->db->get_where($this->table,$data);
        
========
        $query = $this->db->get_where($this->table, $data);



>>>>>>>> master-notification:application/models/Accounts_model.php
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

<<<<<<<< HEAD:application/models/Entity_model.php
        $result = $this->db->get_where($this->table,$data,1,1);
        
========
        $result = $this->db->get_where($this->table, $data, 1, 1);
        //echo $this->db->last_query();

>>>>>>>> master-notification:application/models/Accounts_model.php
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