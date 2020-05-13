<?php
defined('BASEPATH') or exit('No direct script access allowed');

include APPPATH.'/libraries/CommonDbTrait.php';
use chriskacerguis\RestServer\RestController;

class Attachments extends RestController
{
    use CommonDbTrait;

    private $sModule = "ATTACHMENTS";

    public function list_get()
    {
        $this->checkPermission("VIEW",$this->sModule);

        $this->load->model("attachments_model");
        $this->load->model("entity_model");

        $id = $_SESSION['eid'];

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

        $this->response([
            'data' => $data
        ], 200);
    }
}
