<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH.'/libraries/CommonDbTrait.php';

class RegisterAgents extends CI_Controller
{
    use CommonDbTrait;
    
    private $sModule = "REGISTER_AGENTS";

    function index()
    {
        $this->checkPermission("VIEW",$this->sModule);

        $this->load->model("RegisterAgents_model");
        
        $aResults = $this->RegisterAgents_model->getAll();
        
        $data['aAgents'] = $aResults;
        
            responseJson(['data'=>$data]);
    }
}
