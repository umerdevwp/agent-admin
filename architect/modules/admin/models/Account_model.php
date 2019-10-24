<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
 
class account_model extends BaseModel {
	
	private $db;
	
    public function __construct() {
		$this->db = $this->dbfactory;
        parent::__construct();
    }

}
?>