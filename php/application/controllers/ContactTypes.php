<?php
class ContactTypes extends CI_Controller {

    use CommonDbTrait;
    private $sModule = "CONTACT_TYPES";

    public function index($id=0)
    {
        $this->checkPermission("VIEW",$this->sModule);

        $this->load->model("ContactTypes_model");

        $aColumns = getInputFields();

        $aData = $this->ContactTypes_model->getRows($id,$aColumns);

        if(count($aData))
        {
            responseJson(['data'=>$aData]);
            exit;
        }

        responseJson(['errors'=>['status'=>'404','detail'=>'No record found']]);
    }
}