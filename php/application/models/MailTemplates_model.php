<?php
include APPPATH . '/libraries/ModelDefault.php';

class MailTemplates_model extends ModelDefault {
    private $sTable = "mail_templates";
    private $aColumns = [
            "id"        => "id",
            "description"  => "description",
            "added"  => "added",
            "subject"        => "subject",
            "message"   => "message",
    ];

    public function __construct()
    {
        parent::__construct();
        $this->_table = $this->sTable;
    }

    /**
     * List all the templates available columns can be limited
     * with aColumn parameter as array but they must exist in class
     * 
     * @param Array $aColumns as array of columns required
     */
    public function get_all(array $aColumns=[])
    {        
        if(count($aColumns)>0)
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","subject"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }

        foreach($aMyColumns as $k=>$v)
            $this->db->select("$v as `$k`");


        $aData = parent::get_all();

        if (count($aData)) {
            return ['type'=>'ok','results'=>$aData];
        }

        return ['type'=>'error','message'=>'No records found'];
    }
}



