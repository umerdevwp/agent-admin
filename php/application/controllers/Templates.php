<?php
use chriskacerguis\RestServer\RestController;
include APPPATH.'/libraries/CommonDbTrait.php';

class Templates extends RestController {

    use CommonDbTrait;
    private $sModule = "TEMPLATES";

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

    }

    /**
     * List all the templates available columns can be limited
     * with aColumn parameter as array but they must exist in class
     * 
     * @param String $sCsvColumns as comma seprated columns as needed
     */
    public function list_get(string $sCsvColumns="id,subject")
    {
//        $this->checkPermission("VIEW", $this->sModule);
        $aColumns = explode(",",$sCsvColumns);

        $this->load->model("MailTemplates_model");

        $aData = $this->MailTemplates_model->get_all($aColumns);

        if (count($aData)) {
            $this->response(['data' => $aData], 200);
        }
        $this->response( [
            'status' => false,
            'message' => 'No record found'
        ], 404 );
    }

    public function index_get(int $iId)
    {
//        $this->checkPermission("VIEW", $this->sModule);

        $this->load->model("MailTemplates_model");
        $iId = (int)$iId;
        if($iId>0)
        {
            $aData = $this->MailTemplates_model->get($iId);
        } else {
            $this->response( [
                'status' => false,
                'message' => 'No id provided'
            ], 404 );            
        }

        if (is_object($aData)) {
            $this->response(['status'=>true,'data' => $aData], 200);
        }
        $this->response( [
            'status' => false,
            'message' => 'No record found'
        ], 404 );
    }
}
