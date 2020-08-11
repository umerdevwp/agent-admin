<?php

// use Src\Services\OktaApiService as Okta;
header('Access-Control-Allow-Origin: *');

use chriskacerguis\RestServer\RestController;

class Attachment_api extends RestController
{
    public function __construct()
    {
        parent::__construct();

    }

    public function attachment_get()
    {
        $this->response([
            'status' => false,
            'message' => 'The entity information does not belongs to you sdasds'
        ], 404);
    }


    function attachment_post()
    {


        $data = array(
            'file_id' => $_POST["inputFileId"],
            'entity_id' => $_POST["entityId"],
            'name' => $_POST["inputFileName"],
            'file_size' =>  $_POST["inputFileSize"],
        );
        $this->load->model("LoraxAttachments_model");
        $id = $this->LoraxAttachments_model->insert($data);
        if(!empty($id)) {
            $this->response([
                'status' => true,
                'message' => 'File is attached'
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Some thing went wrong.'
            ], 400);
        }
    }

}

