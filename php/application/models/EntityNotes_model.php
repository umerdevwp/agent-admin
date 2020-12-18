<?php
include APPPATH . '/libraries/ModelDefault.php';

class EntityNotes_model extends ModelDefault {

    private $aColumns = [
            "id"        => "id",
            "entityId"  => "entity_id",
            "sendTime"  => "added",
            "from"        => "from",
            "subject"   => "subject",
            "message"   => "message",
    ];

    public function __construct()
    {
        parent::__construct();
        $this->_table = "entity_notes";
    }

    /**
     * Record the message send through sendgrid API
     * @param Integer $iEntityId entity id to send from/to
     */
    public function add($iEntityId,$sSubject,$sMessage,$sFrom)
    {
        $aData = [
            "entity_id" => $iEntityId,
            "from"  =>  $sFrom,
            "subject"   =>  $sSubject,
            "message"   =>  $sMessage,
        ];
        
        $iNewId = $this->insert($aData);

        return $iNewId;
        
    }
}



