<?php

use chriskacerguis\RestServer\RestController;

defined('BASEPATH') OR exit('No direct script access allowed');

class Control404 extends RestController {

	public function index()
	{
        $this->response([
            'errors' => ['status' => false, 'message'=>'Request not found']
        ], 404);
	}

}
