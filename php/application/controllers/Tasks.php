<?php

use chriskacerguis\RestServer\RestController;

defined('BASEPATH') or exit('No direct script access allowed');

include APPPATH . '/libraries/CommonDbTrait.php';

class Tasks extends RestController
{
    use CommonDbTrait;

    private $sModule = "TASKS";

    public function index_get()
    {
        $this->response([
            'errors' => ['status' => false, 'message'=>'Request not found']
        ], 404);
    }
    /**
     * Get zoho code if user has not granted zoho access
     */
    public function getZohoCode()
    {

        $url = getenv('ZOHO_ACCOUNTS_URL') . "/auth"
        . "?scope=ZohoProjects.tasks.UPDATE"
        . "&client_id=" . getenv('ZOHO_CLIENT_ID')
        . "&response_type=code&access_type=online"
        . "&redirect_uri=" . getenv('ZOHO_REDIRECT_URI') . "&prompt=consent";
        
        header("Location: " . $url);
    }

    /**
     * Mark task complete in ZOHO CRM
     * 
     */
    public function completeTaskInZoho_put($id)
    {
        $this->checkPermission("EDIT", $this->sModule);

        $this->load->model("ZoHo_Account");
        $this->load->model("Tasks_model");
        $this->load->model("entity_model");
        $this->load->model("Tempmeta_model");
        
        $loginId = $_SESSION["eid"];

        $aPutData = parsePutRequest();
        $iStatus = $aPutData['status']??1;
        $iEntityId = $aPutData['eid'];

        // validate that user as parent or entity are authority to update it        
        if(!$this->entity_model->isParentOf($iEntityId,$loginId))
        {
                $this->response([
                    'errors' => ['message'=> "Permission denied"]
                ], 403);
                exit();
        }
        // get task details
        $row = $this->Tasks_model->getOne($id,$iEntityId);

        // if valid authority found
        if($row->id>0)
        {
            try{
                // real id
                $oZohoApi = $this->ZoHo_Account->getInstance("Tasks",$id);

                if($iStatus==1)
                    $oZohoApi->setFieldValue("Status","Completed");
                else
                    $oZohoApi->setFieldValue("Status","Not Started");

                $resp = $oZohoApi->update();
                
                // dump into temp for later logins,
                // fetch temp records into session
                // push new records into temp
                // update session with new records
                $_SESSION[$this->Tempmeta_model->slugTasksComplete][] = $id;
                                
                // update temp table as well
                $this->Tempmeta_model->update($iEntityId,$this->Tempmeta_model->slugTasksComplete,json_encode($_SESSION[$this->Tempmeta_model->slugTasksComplete]));
                if($iEntityId>0)
                {
                    $aData = ['id'=>$id,'status'=>$iStatus];
                    $this->Tempmeta_model->appendRow($iEntityId,$this->Tempmeta_model->slugTasksComplete,$aData);
                } 
                $this->response([
                    'result'=>[
                    'message' => "Task updated successfully",
                    "id"=>$id]
                ], 200);
            } catch(Exception $e)
            {
                $this->response([
                    'errors' => ['message'=> "Internal server error, please try again"]
                ], 500);
                error_log("error","Zoho server errror: " . $e->getMessage());
            }
        } else {
                $this->response([
                    'errors' => ['message'=> "No such tasks exist"]
                ], 500);
        }
    }

    public function list_get(int $eid=0)
    {
        $this->checkPermission("VIEW", $this->sModule);

        $this->load->model('Tasks_model');
        $this->load->model('Entity_model');
        $iLoginId = $_SESSION['eid'];

        // eid must be valid
        if($eid>0)
        {
            // login id must be parent of entity id: eid
            if($this->Entity_model->isParentOf($eid,$iLoginId))
            {
                // TODO: check parent is allowed to access specific entity tasks
                $aDataTasks = $this->Tasks_model->getAll($eid);

                // $aTasksCompleted = $this->Tempmeta_model->getOne($eid, $this->Tempmeta_model->slugTasksComplete);

                // if (is_object($aTasksCompleted['results']))
                //     $data['tasks_completed'] = json_decode($aTasksCompleted['results']->json_data);
                // else
                //     $data['tasks_completed'] = [];
                
                // TODO: loop to mark the task completed recently then list
                // foreach($aDataTasks['res'])

                // update getAll calls on other classes, as now it is return array of type=ok and result=result
                if(count($aDataTasks['results']))
                {
                    $this->response([
                        'status'  => true,
                        'data' => $aDataTasks['results']
                    ], 200);
                } else {
                    $this->response([
                        'status'  => true,
                        'data' => []
                    ], 200);
                }
            } else {
                $this->response([
                    'errors' => ['status' => false, 'message'=>'Entity access denied.']
                ], 404);
            }
        } else {
            $this->response([
                'errors' => ['status' => false, 'message'=>'Invalid entity id given.']
            ], 404);

            // get all the tasks under parent related entities
//             $aDataTasks = $this->Tasks_model->getAllUnderParent($iLoginId);
// //            $aDataTasks = $this->Tasks_model->getAllUnderParent($iLoginId);
//             if($aDataTasks['type']=='ok')
//             {
//                 $this->response([
//                     'status'=>true,
//                     'data'=>$aDataTasks['results']
//                 ],200);
//             } else {
//                 $this->response([
//                     'errors' => ['status' => false, 'message'=>'No tasks found.']
//                 ], 404);
//             }
        }
    }
}
