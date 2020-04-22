<?php
class EntityTypes extends CI_Controller {
    use CommonDbTrait;
    private $sModule = "ENTITY_TYPES";
    public function index($id=0)
    {
        $this->checkPermission("VIEW",$this->sModule);
        
        $this->load->model("EntityTypes_model");

        $aColumns = getInputFields();

        $aData = $this->EntityTypes_model->getRows($id,$aColumns);

        if(count($aData))
        {
            responseJson(['data'=>$aData]);
            exit();
        }

        responseJson(['errors'=>['status'=>'404','detail'=>'No record found']]);
    }
}