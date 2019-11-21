<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Src\Services\OktaApiService as Okta;

class Tasks extends CI_Controller
{
    public function getZohoCode()
    {

        $url = getenv('ZOHO_ACCOUNTS_URL') . "/auth"
        . "?scope=ZohoProjects.tasks.UPDATE"
        . "&client_id=" . getenv('ZOHO_CLIENT_ID')
        . "&response_type=code&access_type=online"
        . "&redirect_uri=" . getenv('ZOHO_REDIRECT_URI') . "&prompt=consent";
        
        header("Location: " . $url);
    }

    public function getAccessToken()
    {

        $code = $_GET['code'];

        $url = getenv('ZOHO_ACCOUNTS_URL') . "/oauth/v2/token"
        . "?code= " . $code . "&redirect_uri=" . getenv('ZOHO_REDIRECT_URI') . "&client_id=" . getenv('ZOHO_CLIENT_ID') 
        . "&client_secret=" . getenv('ZOHO_CLIENT_SECRET') . "&grant_type=authorization_code";
        
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        
        $response = curl_execute($ch);
        if(isset($response->access_token))
        {
            $this->session->user = array(
                    "zoho_access_token"     =>  $response->access_token,
                    "zoho_refresh_token"    =>  $response->refresh_token
                );


        }
        else 
        {
            log_message("error","ZOHO ".$response->error);
        }

    }

    public function completeTaskInZoho($id)
    {
        
        $this->load->model("Tasks_model");

        $row = $this->Tasks_model->getOne($id);
        if($row->id>0){
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
            var_dump($response);die;
        } else {
            log_message("error","DB task id: " . $id . " not found on complete event");
        }

    }

}
