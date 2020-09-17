<?php
class Tasks_model extends CI_Model
{

    private $table = "zoho_tasks";
    private $aColumns = [
        "id"=>"id",
        "createdAt"=>"created_at",
        "subject"=>"subject",
        "dueDate"=>"due_date",
        "status"=>"status",
        "priority"=>"priority",
        "createdBy"=>"created_by",
        "tag"=>"tag",
        "sendNotificationEmail"=>"send_notification_email",
        "modifiedBy"=>"modified_by",
        "createdTime"=>"created_time",
        "modifiedTime"=>"modified_time",
        "closedTime"=>"closed_time",
        "currency"=>"currency",
        "exchangeRate"=>"exchange_rate",
        "description"=>"description",
        "owner"=>"owner",
        "whatId"=>"what_id",
        "whoId"=>"who_id",
        "remindAt"=>"remind_at",
        "recurringActivity"=>"recurring_activity"
    ];

    public function __construct()
    {
        $this->load->database();
    }

    public function getAll($id,$aColumns=[])
    {
        // TODO: remove fake id
        $data = [
            'what_id' => $id,
            //'task_owner'    =>  '4071993000000224013', // fake id
        ];

        $aMyColumns = [];
        if(count($aColumns)>0)    
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","subject","status","dueDate"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }

        foreach($aMyColumns as $k=>$v)
        $this->db->select("$v as `$k`");

        $query = $this->db->get_where($this->table, $data);
        $result = $query->result_object();
        //var_dump($result);die;

        if (! is_array($result)) {
            return ['msg'=>'No tasks available','msg_type'=>'error'];
        }

        return $result;
    }

    /**
     * Get task with task id, owned by entity id
     * 
     * @param Integer $id unique id of task
     * @param Integer $entityid Entity id to whom task belongs to
     * 
     * @return Array Record row / Error message no row found
     */
    public function getOne($id,$entityid="")
    {
        $data = [
            'id'    =>  $id
        ];

        if($entityid>0)
        {
            $data['what_id'] = $entityid;
        }

        $query = $this->db->get_where($this->table, $data);
        $result = $query->row();

        if (!$result) {
            return ['message'=>'No tasks available','type'=>'error'];
        }

        return $result;
    }

    /**
     * Get task with task id, and exist under parent entity id
     * 
     * @param Integer $id unique id of task
     * @param Integer $parentid Entity id who is the parent of task owner (entity)
     * 
     * @return Array Record row / Error message no row found
     */
    public function getOneParentId($id,$parentid)
    {
        //SELECT * FROM zoho_accounts za LEFT JOIN zoho_tasks zt ON za.id=zt.related_to WHERE za.parent_entity=4071993000000411118 AND zt.id=4071993000001296114
        $this->db->select("zt.id as id");
        $this->db->from("zoho_accounts za");
        $this->db->join("zoho_tasks zt","za.id=zt.who_id","left");
        $this->db->where(["za." . Entity_model::$parent_entity =>$parentid,"zt.id"=>$id]);
        $query = $this->db->get();
        $result = $query->row();
        //var_dump($result);die;
        //echo $this->db->last_query();die;
        if (!$result) {
            return ['message'=>'No tasks available','type'=>'error'];
        }

        return $result;
    }



    public function getAllNotifications()
    {
        $aWhere = [
            'status'    =>  'pending'
        ];

        $query = $this->db->get_where("notification_subscriptions",$aWhere);
        $result = $query->result_object();

        if (! is_array($result)) {
            return ['message'=>'No tasks available','type'=>'error'];
        }

        return ['type'=>'ok','results'=>$result];
    }
}
