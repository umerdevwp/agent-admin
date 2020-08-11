<?php
class ContactTypes_model extends CI_Model {
    private $table = "contact_types";
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
        $this->db->order_by("name","asc");

        $query = $this->db->get_where($this->table, $data);
        $result = $query->result_object();

        return $result;
    }
}