<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use zcrmsdk\crm\crud\ZCRMRecord;
use zcrmsdk\crm\exception\ZCRMException;

class Portal extends CI_Controller {
	var $account = "";
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->library(["session"]);
		$this->load->helper(["email"]);

		//okta have returned valid login email
		if(!valid_email($this->session->user["email"]))
		{
			redirect("");
		}

		// if organization field in okta is empty
		if(empty($this->session->user['zohoId'])) die("Org is blank");
		//var_dump($this->session->user['zohoId']);die;
		// set zoho id from okta
		$this->account = $this->session->user['zohoId'];
		
		$this->load->model('ZoHo_Account');
		
		$res = $this->ZoHo_Account->LoadAccount($this->account);
		/*
		$accessToken = (new ZCRMConfigUtil())->getAccessToken();
        echo $accessToken;
        echo "------------";
		die;*/
        //$this->ZoHo_Account->dumpAll();
		$data['account'] = $this->ZoHo_Account;
		
		$this->load->model('Accounts_model');
		$data['entity'] = $this->Accounts_model->loadAccount($this->session->user['zohoId']);
		$data['arChildEntity'] = $this->Accounts_model->loadChildAccounts($this->session->user['zohoId']);

		//var_dump($data['account']);die;
        $this->load->view('header');
		$this->load->view('portal', $data);
        $this->load->view('footer');
	}

    public function entity($id)
    {
		$this->load->model('ZoHo_Account');
		$this->load->model('Accounts_model');
		$this->load->model('Tasks_model');
		$this->load->model('Contacts_model');
		$this->load->model('Attachments_model');

		$this->load->helper("custom");

		// fetch data from zoho api
        /*$this->ZoHo_Account->LoadAccount($id);
        //$this->ZoHo_Account->dumpAll();
		$data['account'] = $this->ZoHo_Account;*/
		
		// fetch data from DB
		$data['entity'] = $this->Accounts_model->loadAccount($id);
		$data['tasks'] = $this->Tasks_model->getAll($id);
		$data['contacts'] = $this->Contacts_model->getAll($id);
		$data['attachments'] = $this->Attachments_model->getAll($id);
		
		//var_dump($data['account']);die;
		$this->load->view('header');
		$this->load->view('entity', $data);
		$this->load->view('footer');
	}
	
	public function attachments($owner,$id)
	{
		$this->load->library("session");
		// TODO: test download attachment zoho code
		$this->load->model("Attachments_model");
		$id = $this->Attachments_model->checkOwnership($owner,$id);
		//var_dump($id);die;
		if($id>0){
			try{
				$record = ZCRMRecord::getInstance(“Accounts”,$owner);
				$fileResponseIns = $record->downloadAttachment($id); // 410405000001519501 - Attachment ID.
				header("Location: " . $filePath.$fileResponseIns->getFileName());
				/*
				$fp=fopen($filePath.$fileResponseIns->getFileName(),"w"); // $filePath - absolute path where downloaded file has to be stored.
				echo "HTTP Status Code:".$fileResponseIns->getHttpStatusCode();
				echo "File Name:".$fileResponseIns->getFileName();
				$stream=$fileResponseIns->getFileContent();
				fputs($fp,$stream);
				fclose($fp);
				*/
			}catch (ZCRMException $e)
			{
				/*
				echo $e->getMessage();
				echo $e->getExceptionCode();
				echo $e->getCode();
				*/

				log_message('error',$e->getMessage());
				$this->session->set_flashdata('error','Download failed, server error');
				redirect($_SERVER['HTTP_REFERER']);
			}
		} else {
			$this->session->set_flashdata('error','Unable to authorize attachment');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
}
