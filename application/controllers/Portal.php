<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use zcrmsdk\crm\crud\ZCRMRecord;
use zcrmsdk\crm\exception\ZCRMException;

class Portal extends CI_Controller {
	var $account = "";

	public function __construct()
    {
        parent::__construct();
        validAdminCheck();  

    }
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

		
		// has no child, show entity page
		if($this->session->user["child"]==0 and $this->session->user['zohoId'] != getenv("SUPER_USER"))
		{
			redirect("/entity/".$this->session->user["zohoId"]);
		}

		//okta have returned valid login email
		if(!valid_email($this->session->user["email"]))
		{
			redirect("");
		}

		// if organization field in okta is empty
		if(empty($this->session->user['zohoId'])) redirect("support");
		//var_dump($this->session->user['zohoId']);die;
		// set zoho id from okta
		$this->account = $this->session->user['zohoId'];
		
		//$this->load->model('ZoHo_Account');
		
		//$res = $this->ZoHo_Account->LoadAccount($this->account);
		
        //$this->ZoHo_Account->dumpAll();
		//$data['account'] = $this->ZoHo_Account;
		
		$this->load->model('Accounts_model');
		$this->load->model('Tempmeta_model');
		$data['entity'] = false;		
		
		// user is administrator
		if($this->session->user['zohoId'] == getenv("SUPER_USER")){
			$data['entity'] = $this->Accounts_model->loadAccount($this->session->user['zohoId']);
            $data['arChildEntity'] = $this->Accounts_model->getAll();
		// users from zoho
		} else {
			$aDataEntity = $this->Accounts_model->loadAccount($this->session->user['zohoId']);
			$data['entity'] = false;
			if($aDataEntity['type']=='ok') $data['entity'] = $aDataEntity['results'];
		
			$aDataChild = $this->Accounts_model->loadChildAccounts($this->session->user['zohoId']);
			if($aDataChild['type']=='ok')
			{
				$aDataTemp = $this->Tempmeta_model->getOne(
					$this->session->user['zohoId'],
					$this->Tempmeta_model->slugNewEntity
				);

				if($aDataTemp['type']=='ok')
				{
				
					$data['arChildEntity'] = array_merge(
												$aDataChild['results'],
												json_decode($aDataTemp['results']->json_data)
											);
				} else {
					$data['arChildEntity'] = $aDataChild['results'];
				}
			} else {
				$data['arChildEntity'] = $this->Tempmeta_model->getOne(
					$this->session->user['zohoId'],
					$this->Tempmeta_model->slugNewEntity
				);
			}
		}

		//var_dump($data['account']);die;
        $this->load->view('header');
		$this->load->view('portal', $data);
        $this->load->view('footer');
	}

    public function entity($id)
    {
		//$this->load->model('ZoHo_Account');
		$this->load->model('Accounts_model');
		$this->load->model('Tasks_model');
		$this->load->model('Contacts_model');
		$this->load->model('Attachments_model');

		$this->load->helper("custom");

		// fetch data from zoho api
        //$this->ZoHo_Account->LoadAccount($id);
        //$this->ZoHo_Account->dumpAll();
		//$data['account'] = $this->ZoHo_Account;
		
		// fetch data from DB
		$aDataEntity = $this->Accounts_model->loadAccount($id);
		$data['entity'] = false;
		if($aDataEntity['type']=='ok') $data['entity'] = $aDataEntity['results'];

		$data['tasks'] = $this->Tasks_model->getAll($id);
		$data['contacts'] = $this->Contacts_model->getAllFromEntityId($id);
		//$data['attachments'] = $this->ZoHo_Account->arAttachments;
		$data['attachments'] = $this->Attachments_model->getAll($id);
		
		//var_dump($data['account']);die;
		$this->load->view('header');
		$this->load->view('entity', $data);
		$this->load->view('footer');
	}
	
	public function attachments($owner,$id)
	{
		$this->load->model('ZoHo_Account');
		$this->load->helper("custom");
		
		// TODO: check parent is allowed to download this child entity file
		// TODO: track number of downloads in table, for unique filename move

		$this->load->library("session");
		
		$this->load->model("Attachments_model");
		// here check parent is allowed to download entity file
		$row = $this->Attachments_model->checkOwnership($owner,$id);
		//var_dump($id);die;
		if($row->id>0){
			$id = $row->id;
			if($row->link_url=="Attachment"){
				try{
					$entity = $this->ZoHo_Account->LoadAccountOnly($owner);
					$fileResponseIns = $entity->downloadAttachment($id);
					
					$file = "temp786/".$fileResponseIns->getFileName();
					$fp=fopen($file,"w");
					$stream=$fileResponseIns->getFileContent();
					fputs($fp,$stream);
					fclose($fp);

					$quoted = sprintf('"%s"', addcslashes(basename($file), '"\\'));
					$size   = filesize($file);

					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename=' . $quoted); 
					header('Content-Transfer-Encoding: binary');
					header('Connection: Keep-Alive');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					//header('Pragma: public');
					header('Content-Length: ' . $size);
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
				//header("Location: " . $row->link_url);
			}
		} else {
			$this->session->set_flashdata('error','Unable to find attachment requested');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function attachments2()
	{
		
		/*$this->load->model('ZoHo_Account');
        $this->ZoHo_Account->LoadAccount($this->input->get("id"));
        //$this->ZoHo_Account->dumpAll();
		$account = $this->ZoHo_Account;
		//echo "<pre>";print_r($account);die;
		echo $account->AccountData->getFieldValue('Account_Name');
		die;*/
		$this->load->model("Attachments_model");

		$this->Attachments_model->getAllApi();
	}

	public function download()
	{
		$this->load->model('ZoHo_Account');
		$this->ZoHo_Account->downloadAttachments();

		header("Location: ". $_SERVER['HTTP_REFERER']);
	}
}
