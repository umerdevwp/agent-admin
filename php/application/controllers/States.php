<?php
include APPPATH.'/libraries/CommonDbTrait.php';
use chriskacerguis\RestServer\RestController;

class States extends RestController {

    use CommonDbTrait;
    private $sModule = "STATES";
    function __construct()
    {
        // Construct the parent class
        parent::__construct();

    }
    public function list_get($id=0)
    {
        $this->checkPermission("VIEW",$this->sModule);
        $this->load->model("States_model");
        $aColumns = getInputFields();
        $aData = $this->States_model->getRows($id,$aColumns);
        if(count($aData))
        {
            $this->response(['data' => $aData], 200);
        }
        $this->response( [
            'status' => false,
            'message' => 'No record found'
        ], 404 );
    }
}
