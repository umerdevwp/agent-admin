<?php

use chriskacerguis\RestServer\RestController;
include APPPATH . '/libraries/CommonDbTrait.php';

class Tasks extends RestController
{

    use CommonDbTrait;
    private $sModule = "TASKS";
    public function getZohoCode()
    {
        $url = getenv('ZOHO_ACCOUNTS_URL') . "/auth"
            . "?scope=ZohoProjects.tasks.UPDATE"
            . "&client_id=" . getenv('ZOHO_CLIENT_ID')
            . "&response_type=code&access_type=online"
            . "&redirect_uri=" . getenv('ZOHO_REDIRECT_URI') . "&prompt=consent";
        header("Location: " . $url);
    }


    public function completeTaskInZoho_put($id)
    {

        $this->checkPermission("EDIT", $this->sModule);

        $this->load->model("ZoHo_Account");
        $this->load->model("Tasks_model");
        $this->load->model("entity_model");
        $this->load->model("Tempmeta_model");

        $loginId = $_SESSION["eid"];

        $iStatus = $this->input->get('status')??1;

        if(!empty($this->input->get('eid')))
        {
            $iEntityId = $this->input->get('eid');
        }

        // validate that user as parent or entity are authority to update it
        if($iEntityId>0){
            $row = $this->Tasks_model->getOneParentId($id,$loginId);
        } else {
            $row = $this->Tasks_model->getOne($id,$loginId);
        }

        // if valid authority found
        if($row->id>0)
        {
            try{
                // real id
                $oZohoApi = $this->ZoHo_Account->getInstance("Tasks",$id);

                //$oZohoApi->setFieldValue("percent_complete",100);
                $oZohoApi->setFieldValue("Status","Completed");

                if($iStatus==1)
                    $oZohoApi->setFieldValue("Status","Completed");
                else
                    $oZohoApi->setFieldValue("Status","Not Started");

                $resp = $oZohoApi->update();
                $this->session->set_flashdata("ok","Task updated successfully");

                // dump into temp for later logins,
                // fetch temp records into session
                // push new records into temp
                // update session with new records

                // update temp table as well
                //$this->Tempmeta_model->update($this->session->user["zohoId"],$sTempSlug,json_encode($this->session->temp[$sTempSlug]));
                if($iEntityId>0)
                {
                    $aData = ['id'=>$id,'status'=>$iStatus];
                    $this->Tempmeta_model->appendRow($iEntityId,$this->Tempmeta_model->slugTasksComplete,$aData);

                }

                $this->response([
                    'message' => 'Task updated successfully'
                ], 200);
            } catch(Exception $e)
            {
                log_message("error","Zoho server errror: " . $e->getMessage());
                $this->response([
                    'message' => 'Unable to update task, server error'
                ], 200);
            }
        } else {
            $this->session->set_flashdata("error","Permission denied, no such tasks found");
            $this->response([
                'message' => 'Such task do not exist'
            ], 200);
        }
    }


}
