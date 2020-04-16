<?php
class EntityTypes_model extends CI_Model {
    private $table = "entity_types";
    private $aColumns = [
        "id"    =>  "id",
        "code"  =>  "code",
        "name"  =>  "name",
    ];

    public function getRows($id=0,$aColumns=[])
    {

        if($id>0)
        {
            $data = [
                'id' => $id,
            ];
        }

        $aMyColumns = [];
        if(count($aColumns)>0)    
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","code","name"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }

        foreach($aMyColumns as $k=>$v)
        $this->db->select("$v as `$k`");

        $query = $this->db->get_where($this->table, $data);
        $result = $query->result_object();
        //var_dump($result);die;
        if (! is_array($result)) {
            return ['message'=>'No tasks available','type'=>'error'];
        }

        return $result;
    }
}