<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portal extends CI_Controller {
    var $account = "4071993000000247062";

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
        $this->load->model('ZoHo_Account');
        $this->ZoHo_Account->LoadAccount($this->account);
        //$this->ZoHo_Account->dumpAll();
        $data['account'] = $this->ZoHo_Account;
        $this->load->view('header');
		$this->load->view('portal', $data);
        $this->load->view('footer');
	}

    public function entity($id)
    {
        $this->load->model('ZoHo_Account');
        $this->ZoHo_Account->LoadAccount($id);
        //$this->ZoHo_Account->dumpAll();
        $data['account'] = $this->ZoHo_Account;
		$this->load->view('header');
		$this->load->view('entity', $data);
		$this->load->view('footer');
    }
}
