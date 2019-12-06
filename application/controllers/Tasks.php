<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Src\Services\OktaApiService as Okta;

class Tasks extends CI_Controller
{
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
     * Get access token from zoho using grant code
     */
    public function getAccessToken()
    {
        // user allowed our app to access zoho
        $code = $_GET['code'];
        if(!empty($code))
        {
            // get access token
            $url = getenv('ZOHO_ACCOUNTS_URL') . "/oauth/v2/token"
            . "?code= " . $code . "&redirect_uri=" . getenv('ZOHO_REDIRECT_URI') . "&client_id=" . getenv('ZOHO_CLIENT_ID') 
            . "&client_secret=" . getenv('ZOHO_CLIENT_SECRETT') . "&grant_type=authorization_code";
            
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            
            $response = curl_execute($ch);
            // token found
            if(isset($response->access_token))
            {
                $this->session->user = $data = array(
                        "zoho_access_token"     =>  $response->access_token,
                        "zoho_refresh_token"    =>  $response->refresh_token
                    );
                $this->load->model("Usersmeta_model");
                $this->Usersmeta_model->insert($this->session->user["id"],$data);
            // token failed
            } else {
                log_message("error","ZOHO ".$response->error);
                log_message("error","ZOHO access failed");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        // redirect user back to entity page
        } else {
            $this->session->set_flashdata("error","Access from zoho denied");
            log_message("error","ZOHO access not allowed or user denied");
            redirect($_SERVER["HTTP_REFERER"]);
        }

    }
    /**
     * Complete task using zoho client api
     */
    public function completeTask($id)
    {
        
        if(!isSessionValid("Task_Update")) redirectSession();

        $this->load->model("ZoHo_Account");
        $this->load->model("Tasks_model");
        $this->load->model("Accounts_model");
        
        $parentid = $this->session->user["zohoId"];
        
        // validate that user as parent or entity are authority to update it
        if($this->session->user["child"]>0){
            $row = $this->Tasks_model->getOneParentId($id,$parentid);
        } else {
            $row = $this->Tasks_model->getOne($id,$parentid);
        }

        // if valid authority found
        if($row->id>0)
        {
            try{
                // real id
                $oZohoApi = $this->ZoHo_Account->getInstance("Tasks",$id);

                //$oZohoApi->setFieldValue("percent_complete",100);
                $oZohoApi->setFieldValue("Status","Completed");
                $resp = $oZohoApi->update();
                $this->session->set_flashdata("ok","Task updated successfully");

                redirect($_SERVER["HTTP_REFERER"]);
            } catch(Exception $e)
            {
                log_message("error","Zoho server errror: " . $e->getMessage());
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata("error","Permission denied, no such tasks found");
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    /**
     * Mark task complete in ZOHO CRM
     * 
     */
    public function completeTaskInZoho($id)
    {
        $this->completeTask($id);die;

        $this->load->model("Tasks_model");
        // check zoho access token exist
        if(empty($this->session->user["zoho_access_token"]))
        {
            $this->load->model("Usersmeta_model");
            $rowUsermeta = $this->Usersmeta_model->getOne($this->session->user["id"]);
            // get token
            if($rowUsermeta->id>0)
            {
                $this->session->user = array(
                    "zoho_access_token"     =>  $rowUsermeta->zoho_access_token,
                    "zoho_refresh_token"    =>  $rowUsermeta->refresh_token
                );
            // connect wit zoho api for token
            } else {
                $this->getZohoCode();
            }
        }
        // token available
        if($this->session->user["zoho_access_token"])
        {

            $row = $this->Tasks_model->getOne($id);
            // get task and update
            if($row->id>0){
                // update api call
                $url = getenv('ZOHO_RESTAPI_URI') . "/portal/" . $row->portal_id . "/projects/" . $row->project_id . "/tasks/" . $id . "/";

                $headers = array(
                    "Authorization" => "Bearer " . $this->session->user["zoho_access_token"],
                );

                $params = array(
                    "scope" =>  "ZohoProjects.tasks.UPDATE",
                    "percent_complete"  =>  100,
                );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST,1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,implode("&",$params));

                $response = curl_exec($ch);
                
                echo "Functionality not implemented, try again later";
                die;
            } else {
                $this->session->set_flashdata('error','Unable to find requested task');
                log_message("error","DB task id: " . $id . " not found on complete event");
            }
        } else {
            $this->session->set_flashdata('error','Unable to mark complete, Zoho authentication failed');
            log_message("error","ZOHO access token not found for task complete");
        }
    }

}
