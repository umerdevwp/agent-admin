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
        $this->load->model("Tempmeta_model");
        $this->load->model('LoraxAttachments_model');
        $id = $_SESSION['eid'];

        if($id == getenv("SUPER_USER")){
            $result = $this->entity_model->getAll();
        } else {
            // fetch all childrens ids, to later fetch
            $result = $this->entity_model->getChildAccounts($id,"id");

            $aDataTempEntity = $this->Tempmeta_model->getAll(
                $id,
                $this->Tempmeta_model->slugNewEntity
            );
    
            if ($aDataTempEntity['type'] == 'ok')
            {
                if (count($aDataTempEntity['results']) > 0) {

                    $aNewDataChild = [];
                    
                    if (count($result['results']) > 0) {
                        $aNewDataChild = array_merge($result['results'], json_decode($aDataTempEntity['results'][0]['json_data']));
                    } else {
                        $aNewDataChild = json_decode($aDataTempEntity['results'][0]['json_data']);
                    }
                    
                    $result = $aNewDataChild;
                }
            }
        }

        // create comma seprated ids from result
        $arCommaIds = array();
        foreach($result as $v)
        {
            $arCommaIds[] = (int)$v->id;
        }

        // add parent id as well
        $arCommaIds[] = (int)$id;

        $aDataAttachment = $this->LoraxAttachments_model->getAllFromEntityList($arCommaIds);
                
        if ($aDataAttachment['type'] == 'ok') {
            $data['attachments'] = $aDataAttachment['results'];
            //$aDataAttachment = $this->Tempmeta_model->getOne($id, $this->Tempmeta_model->slugNewAttachment);
        } else {
            $data['attachments'] = [];
        }

        //$data['attachments'] = $this->attachments_model->getAllFromEntityList($arCommaIds);


        $this->response([
            'data' => $data
        ], 200);
    }
}
