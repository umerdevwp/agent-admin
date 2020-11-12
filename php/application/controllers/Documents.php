<?php
defined('BASEPATH') or exit('No direct script access allowed');

include APPPATH.'/libraries/CommonDbTrait.php';
use chriskacerguis\RestServer\RestController;

class Documents extends RestController
{
    use CommonDbTrait;

    private $sModule = "ATTACHMENTS";

    public function list_get($id=null)
    {
        $this->checkPermission("VIEW",$this->sModule);
        if($id>0)
        {
            $this->entity_get($id);
            die;
        }
        $this->load->model("attachments_model");
        $this->load->model("entity_model");
        $this->load->model("Tempmeta_model");
        $id = $_SESSION['eid'];
        $aDataTempEntity = [];

        if(isAdmin()){
            // get zoho accounts data
            $result = $this->entity_model->getAll();
            // get temp entity data
            $aDataTempEntity = $this->Tempmeta_model->getAllForAdmin($this->Tempmeta_model->slugNewEntity);
        } else {
            // fetch all childrens ids, to later fetch
            $result = $this->entity_model->getChildAccounts($id,"id");
            // get temp entity data for parent login
            $aDataTempEntity = $this->Tempmeta_model->getAll(
                $id,
                $this->Tempmeta_model->slugNewEntity
            );

            if(count($aDataTempEntity['results'])>0)
            $aDataTempEntity['results'] = json_decode($aDataTempEntity['results'][0]['json_data']);
        }
//        print_r($aDataTempEntity);die;
//print_r($result);die;
        // create comma seprated ids from result
        $arCommaIds = array();
        foreach($result['results'] as $v)
        {
            $arCommaIds[] = (int)$v->id;
        }
//print_r($result);
        // add parent id as well
        $arCommaIds[] = (int)$id;

        $data = $this->getIdIn($arCommaIds,true);
// print_r($data);

        if(count($aDataTempEntity)>0)
        {
            $arCommaIds = array();
            foreach($aDataTempEntity['results'] as $v)
            {
                $arCommaIds[] = (int)$v->id;
            }        
            // print_r($aDataTempEntity);die;
            $aDataTempAttachment = $this->getIdIn($arCommaIds,false);

            foreach($aDataTempAttachment['documents'] as $v)
            {
                foreach($aDataTempEntity['results'] as $v2)
                {
                    if($v2->id==$v->entityId)
                    {
                    $v->entityName = $v2->name;
                    }
                }
            }

            $data['documents'] = array_merge($data['documents'],$aDataTempAttachment['documents']);
        }
//        $data['attachments'] = $data['documents'];
        $this->response([
            
            'status'=>true,
            'data' => $data
        ], 200);
    }

    private function getIdIn(array $arCommaIds=[],$bWithEntity=true)
    {

        $this->load->model('LoraxAttachments_model');

        if($bWithEntity)
        {
        $aDataAttachment = $this->LoraxAttachments_model->getAllWithEntity($arCommaIds);
        } else {
        $aDataAttachment = $this->LoraxAttachments_model->getAllFromEntityList($arCommaIds);
        }

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
                'data' => $data['documents']
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
        if($this->input->post("inputFileId")=='undefined')
            $_POST["inputFileId"] = "";
        if($this->input->post("inputFileName")=='undefined')
            $_POST["inputFileName"] = "";
        if($this->input->post("entityId")=='undefined')
            $_POST["entityId"] = "";

        $this->load->model("entity_model");
         $eid = $this->input->post("entityId");
         $iParentId = $_SESSION['eid'];
        $bIsParentValid = $this->entity_model->isParentOf($eid, $iParentId);
        if(!$bIsParentValid)
        {
            $this->load->model("Tempmeta_model");
            $aDataWhereTemp = ['json_id'=>$eid,'userid'=>$iParentId];

            $bRowExist = $this->Tempmeta_model->checkRowExistInJson($aDataWhereTemp);
            
            if($bRowExist)
            {
                $bIsParentValid = true;
            }
        }


        if(isAdmin()){
            $bIsParentValid = true;
        }
        
        if(!$bIsParentValid)
        {
            $this->response([
                'status' => false,
                'message' => 'Not authorize to access entity'
            ], 403);
        }

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
                // test bulk upload by admins
                if(isAdmin())
                {
                    // if bulk upload then check is admin and total count = current queue (so it become notification last upload)
                    if($this->input->post("bulkUpload")==$this->input->post("totalBulkUpload") && $this->input->post("totalBulkUpload")>0){
                        $aSendgridVariable = [
                            'document_count'   =>  $this->input->post("totalBulkUpload")
                        ];
                        // sendgrid id = 2, to send bulk attachment template
                        $iNotifyId = $this->NotificationAttachments_model->addAttachmentNotification($this->input->post('entityId'),$sLoraxFileId, 2, $aSendgridVariable);
                    }
                }
                // only insert single attachment from detail page add attachment, where no bulkUpload variable exist
                if(empty($this->input->post("bulkUpload"))){
                    $iNotifyId = $this->NotificationAttachments_model->addAttachmentNotification($this->input->post('entityId'),$sLoraxFileId, 1);
                }

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
