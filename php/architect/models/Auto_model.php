<?php
if(!defined('BASEPATH'))
    exit('No direct script access allowed');
class auto_model extends BaseModel {
    var $CI;
    public function __construct() {
        $this->CI = & get_instance();
        return parent::__construct();
    }
}