<?php

include APPPATH.'/libraries/CommonDbTrait.php';

use chriskacerguis\RestServer\RestController;

class EntityTypes extends RestController {
    use CommonDbTrait;
    private $sModule = "ENTITY_TYPES";
    function __construct()
    {
        // Construct the parent class
        parent::__construct();

    }

    public function entitytypelist_get($id = 0)
    {

        $this->load->model("EntityTypes_model");

        $aColumns = getInputFields();

        $aData = $this->EntityTypes_model->getRows($id, $aColumns);

        if (count($aData)) {
            $this->response(['data' => $aData], 200 );
        }
        $this->response( [
            'status' => false,
            'message' => 'No record found'
        ], 404 );
    }
}
