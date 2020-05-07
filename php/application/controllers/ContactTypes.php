<?php
use chriskacerguis\RestServer\RestController;
include APPPATH.'/libraries/CommonDbTrait.php';

class ContactTypes extends RestController {

    use CommonDbTrait;
    private $sModule = "CONTACT_TYPES";
    function __construct()
    {
        // Construct the parent class
        parent::__construct();

    }
    
    public function contacttypelist_get()
    {

        $this->load->model("ContactTypes_model");

        $aColumns = getInputFields();

        $aData = $this->ContactTypes_model->getRows($id, $aColumns);

        if (count($aData)) {
            $this->response(['data' => $aData], 200);
        }

        $this->response( [
            'status' => false,
            'message' => 'No record found'
        ], 404 );
    }
}
