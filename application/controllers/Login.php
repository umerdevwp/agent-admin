<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Src\Services\OktaApiService as Okta;
class Login extends CI_Controller {

	protected $okta;
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
		
		if(valid_email($this->session->user["email"])) redirect("/portal");

        $this->load->view('login');
        
	}

	public function callback()
    {
        $this->load->library(['session']);
        $this->load->helper(["email"]);

		$this->getProfile();

		//$this->exchangeCode();
        if(valid_email($this->session->user["email"])) redirect("/portal");
        else redirect(getenv("SITE_URL")."?msg=Unable to verify your account");
        

    }

    public function logout()
    {
        unset(
            $_SESSION['user']
        );
        redirect(getenv("OKTA_BASE_URL") . "login/signout?fromURI=" . getenv("SITE_URL"));
    }

	public function getProfile()
	{
//	echo $_GET['id_token'];die;
	$ch = curl_init(getenv('OKTA_BASE_URL') . "api/v1/users/" . $_GET['user_id']);
    
    $headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
	'Authorization: SSWS ' . getenv('OKTA_API_TOKEN')
    );
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = json_decode(curl_exec($ch));

$this->session->user = array(
    "oktaId"=>$this->input->get("user_id"),
    "email"=>$response->profile->email,
    "organization"=>$response->profile->organization,
    "firstName"=>$response->profile->firstName,
    "lastName"=>$response->profile->lastName,
);

	}
/*
    function exchangeCode() {
        $authHeaderSecret = base64_encode( getenv("OKTA_CLIENT_ID").':'.getenv("OKTA_CLIENT_SECRET") );
    
        $headers = [
            'Authorization: Basic ' . $authHeaderSecret,
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Connection: close',
            'Content-Length: 0'
        ];
        $url = getenv('OKTA_BASE_URL') . 'oauth2/default/v1/introspect?token='.$_GET['access_token'].'&token_type_hint=access_token';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
    //	curl_setopt($ch, CURLOPT_POSTFIELDS,"token=eyJraWQiOiJ6Z0RaUGtQQjFfTEtPa1pOWkwyVFROVUlUVGhXLVNSQUd2cUZhWkJTNzU4IiwiYWxnIjoiUlMyNTYifQ.eyJ2ZXIiOjEsImp0aSI6IkFULmF1V011UGxfTERPdWt4eFRQZjJDeVBSNjZIV1pZeVYyRWZyWm9Bb2ROZGsiLCJpc3MiOiJodHRwczovL2Rldi00OTM0MzAub2t0YS5jb20vb2F1dGgyL2RlZmF1bHQiLCJhdWQiOiJhcGk6Ly9kZWZhdWx0IiwiaWF0IjoxNTczNjY3NzYwLCJleHAiOjE1NzM2NzEzNjAsImNpZCI6IjBvYTFzZHYwbmNWM05USjgzMzU3IiwidWlkIjoiMDB1MXNmOHJsYTF1OVVBVXYzNTciLCJzY3AiOlsiZW1haWwiLCJvcGVuaWQiXSwic3ViIjoidGVzdDFAZ21haWwuY29tIn0.UajdgEwu8vWdQw84l7_-7fWaHNu5sfxWnF5x2ydqboPpBdEg9wupO4BnHuNiMV1gmReijEVT92IqjmMRMJHYI9hxXMMb3EN_khG9pyZcE7-UaENS-TUtbpedjETD_hMDBBf4iA6bZmm8eDaC_Bh0znA965eywSZUXPpjINbNCFbSuT_tt5dYgSyONOvATAjA6oJ4GocV0nCuQJk1esKhR3iTtmo0iXS96iksMJIXl4W4wLbWtZgdHAWsEYZgHD88-Y7Q1Vj6hm-ymQ3nRr6REJKefYbhjxZCu5_IzLi2ixMxFBWu9eATVvTISaS9MuuQvOKGiu27DbTTfi5T1lCjUg&token_type_hint=access_token");
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_error($ch)) {
            $httpcode = 500;
        }
        curl_close($ch);
        var_dump($output);
        die;
    }
*/    

}
