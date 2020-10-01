<?php
defined('BASEPATH') or exit('No direct script access allowed');

include APPPATH.'/libraries/CommonDbTrait.php';
use chriskacerguis\RestServer\RestController;

class Documents extends RestController
{
    use CommonDbTrait;

    private $sModule = "ATTACHMENTS";

    public function list_get()
    {
        $this->checkPermission("VIEW",$this->sModule);

        $this->load->model("attachments_model");
        $this->load->model("entity_model");
        $this->load->model("Tempmeta_model");
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
                    $result['results'] = $aNewDataChild;
                }
            }
        }

        // create comma seprated ids from result
        $arCommaIds = array();
        foreach($result['results'] as $v)
        {
            $arCommaIds[] = (int)$v->id;
        }

        // add parent id as well
        $arCommaIds[] = (int)$id;

        $data = $this->getIdIn($arCommaIds);

        $this->response([
            
            'status'=>true,
            'data' => $data
        ], 200);
    }

    private function getIdIn(array $arCommaIds=[])
    {

        $this->load->model('LoraxAttachments_model');
        $aDataAttachment = $this->LoraxAttachments_model->getAllFromEntityList($arCommaIds);
        $data['documents'] = [];

        if ($aDataAttachment['type'] == 'ok') {
            $data['documents'] = $aDataAttachment['results'];
            //$aDataAttachment = $this->Tempmeta_model->getOne($id, $this->Tempmeta_model->slugNewAttachment);
        }

        return $data;
    }

    public function entity_get(int $id=null)
    {
        if($id>0)
        {
            $arCommaIds[] = $id;
            
            $data = $this->getIdIn($arCommaIds);

            $this->response([
                'status'=>true,
                'data' => $data
            ], 200);
        } else {
            $this->response([
                'status'=>false,
                'message' => 'Request must contain numeric ID'
            ], 200);
        }
    } 
    
    public function download_get(int $iAttachmentId=0)
    {
        $this->checkPermission("VIEW",$this->sModule);

        $this->load->model("LoraxAttachments_model");

        $row = $this->LoraxAttachments_model->checkOwnership($_SESSION['eid'],$iAttachmentId);

        if($row->id==$iAttachmentId)
        {
            $this->LoraxAttachments_model->download($row->file_id);
            $this->response([
                'status' => true,
                'message' => 'Downloading ...'
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'File not found'
            ], 200);
        }        
    }

    function attachment_post()
    {
        if(!empty($this->input->post("inputFileId")) && !empty($this->input->post("inputFileName")) && !empty($this->input->post("entityId")))
        {
            $data = array(
                'file_id' => $this->input->post("inputFileId"),
                'entity_id' => $this->input->post("entityId"),
                'name' => $this->input->post("inputFileName"),
                'file_size' =>  $this->input->post("inputFileSize"),
            );
            $sLoraxFileId = $this->input->post("inputFileId");
            $this->load->model("LoraxAttachments_model");
            $iId = $this->LoraxAttachments_model->insert($data);
            if(!empty($iId)) {
                $this->load->model("NotificationAttachments_model");
                $iNotifyId = $this->NotificationAttachments_model->addAttachmentNotification($this->input->post('entityId'),$sLoraxFileId);
                $this->response([
                    'status' => true,
                    'message' => 'File is attached'
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Unable to store file, please try again.'
                ], 400);
            }
        } else {
            if(empty($this->input->post("entityId"))){
                $this->response([
                    'status' => false,
                    'message' => 'Entity information is missing'
                ], 400);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'File field cannot be empty'
                ], 400);
            }
        }
    }
}
