<?php
class States extends CI_Controller {

    public function index($id=0)
    {

        $this->load->model("States_model");

        $aColumns = getInputFields();

        $aData = $this->States_model->getRows($id,$aColumns);

        if(count($aData))
        {
            responseJson(['data'=>$aData]);
            exit;
        }

        responseJson(['errors'=>['status'=>'404','detail'=>'No record found']]);
    }
}