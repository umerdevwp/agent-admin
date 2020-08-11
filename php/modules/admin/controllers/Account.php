<?php

if(!defined('BASEPATH'))
    exit('No direct script access allowed');

use zcrmsdk\crm\setup\restclient\ZCRMRestClient;

class Account extends MX_Controller {
	private $data = array();
	private $db;

    public function __construct() {
		$this->load->model('account_model', 'account');
		$this->layout->header = 'user';
		$this->db = $this->dbfactory;
		 $configuration = [
			"client_id" => "1000.6FGQIEZ8QIA8WCKOSLP7KNUKM5U9PH",
            "client_secret" => "1dd268acffb2ed484e86a27b578a028fe641fff249",
			"redirect_uri" => "localhonst/zoho/rest.php",
			"currentUserEmail"=> "sovankendur@gmail.com"
			
		];
        

        ZCRMRestClient::initialize($configuration);
		
		parent::__construct();
    }

	public function index() {
		$this->login();
    }
	
	public function dashboard(){
		$this->layout->selected_manu = "/";
		$this->layout->view('dashboard',  $this->data); 
	}	
}
