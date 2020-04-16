<?php
defined('BASEPATH') or exit('No direct script access allowed');

include APPPATH.'/libraries/CommonDbTrait.php';

class Attachments extends CI_Controller
{
    use CommonDbTrait;
    
    private $sModule = "ATTACHMENTS";

    public function index()
    {
        $this->checkPermission("VIEW",$this->sModule);

        $this->load->model("attachments_model");
        $this->load->model("entity_model");
        
        $id = $this->input->get('eid');

        if($id == getenv("SUPER_USER")){
            $result = $this->entity_model->getAll();
        } else {
            // fetch all childrens ids, to later fetch
            $result = $this->entity_model->getChildAccounts($id,"id");
        }

        // create comma seprated ids from result
        $arCommaIds = array();
        foreach($result as $v)
        {
            $arCommaIds[] = $v->id;
        }
        // add parent id as well
        $arCommaIds[] = $id;

        $data['attachments'] = $this->attachments_model->getAllFromEntityList($arCommaIds);
        
        responseJson(['data'=>$data]);
    }
}