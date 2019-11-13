<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

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
		$this->load->view('login');
	}

	public function callback()
    {
		//$this->getProfile();

		redirect('/portal');

    }

	public function getProfile()
	{

	$ch = curl_init("https://dev-612069.okta.com/api/v1/users/me");

    $headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
	'Authorization: SSWS ' . '00ZyFQxPdYbOmLGd1Fd5W3b3Z_MJ9ChASu0sV-wYIY'
    );
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
var_dump($response);
die;
/*
        $userId = $this->users_model->find_or_create($result['username']);

        $this->session->userId = $userId;
        $this->session->username = $result['username'];
        redirect('/portal');*/
	}

}
