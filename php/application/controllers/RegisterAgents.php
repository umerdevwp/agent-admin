<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class RegisterAgents extends CI_Controller
{
    function index()
    {
        if(!isSessionValid("RegisterAgents")) redirectSession();

        $this->load->model("RegisterAgents_model");
        
        $aResults = $this->RegisterAgents_model->getAll();
        
        $data['aAgents'] = $aResults;
        
        $this->load->view("header");
        $this->load->view('register-agent',$data);
        $this->load->view("footer");
    }
}
