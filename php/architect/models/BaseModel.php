<?php

class BaseModel extends CI_Model
{
    public function __construct() {
        return parent::__construct();
    }
    
    public function insert($table, $fileds)
    {
        return $fileds;
    }
}