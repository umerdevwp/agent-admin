<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH . '/libraries/CommonDbTrait.php';

use chriskacerguis\RestServer\RestController;

class RegisterAgents extends RestController
{
    use CommonDbTrait;
    private $sModule = "REGISTER_AGENTS";
    function list_get()
    {
        $this->checkPermission("VIEW", $this->sModule);
        $this->load->model("RegisterAgents_model");
        $aResults = $this->RegisterAgents_model->getAll();
        $data['aAgents'] = $aResults;
        $this->response(['data' => $data], 200);

    }
}
