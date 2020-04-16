<?php
class EntityType extends CI_Controller {
    public function index($id=0)
    {
        $this->load->model("EntityTypes_model");

        $aColumns = getInputFields();

        $aData = $this->EntityTypes_model->getRows($id,$aColumns);

        responseJson(['data'=>$aData]);
    }
}