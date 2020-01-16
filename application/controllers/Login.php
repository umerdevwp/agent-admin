<?php
defined('BASEPATH') or exit('No direct script access allowed');
use Src\Services\OktaApiService as Okta;

class Login extends CI_Controller
{
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

        if (valid_email($this->session->user["email"])) {
                $this->redirectToDashboard();
        }
        
        $this->load->view('login');
    }

    public function callback()
    {
        $this->load->library(['session']);
        $this->load->helper(["email"]);
        // if session exist
        if (!valid_email($this->session->user["email"])) {

            if(empty($_GET['code'])) $this->validateOktaState();
            else if(empty($this->session->user['access_token'])) $this->getAccessToken($_GET['code']);

            if(!valid_email($this->session->user["email"])) $this->createProfileSession($this->getOktaId());
            
            if (valid_email($this->session->user["email"])) {

                $this->redirectToDashboard();
                //error_log("Valid email, redirecting");
            } else {
                redirect(getenv("SITE_URL")."?msg=Unable to verify your account");
                //error_log("Failed email, going login");
            }
        } else {
            $this->redirectToDashboard();
        }
    }

    private function redirectToDashboard()
    {
        if($this->session->user['child'])
        {
            redirect("/portal");
        } else {
           if($this->session->user['zohoId'] == getenv("SUPER_USER")){
            redirect("/portal");
           } else{
            redirect("/entity/" . $this->session->user["zohoId"]);
           }
        }
    }

    public function validateOktaState()
    {
        // working url for code return as querystring
        
        $url = getenv('OKTA_BASE_URL') . "oauth2/default/v1/authorize?client_id=". getenv('OKTA_CLIENT_ID') 
        . "&redirect_uri=". getenv('OKTA_REDIRECT_URI') 
        . "&state=".$_COOKIE['okta-oauth-state']
        . "&nonce=".$_COOKIE['okta-oauth-nonce'] 
        . "&response_type=code&scope=openid&prompt=none";

        header("Location: " . $url);
        /*
        $ch = curl_init( $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        echo "<pre>";
        print_r($response);*/
        die;
    }

    public function getAccessToken($code)
    {
        $url = getenv('OKTA_BASE_URL') . "oauth2/default/v1/token?client_id=". getenv('OKTA_CLIENT_ID') 
        . "&client_secret=" . getenv('OKTA_CLIENT_SECRET')
        . "&redirect_uri=". getenv('OKTA_REDIRECT_URI') 
        . "&grant_type=authorization_code"
        . "&code=".$code;

        $ch = curl_init($url);
        $headers = array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded'
            );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = json_decode(curl_exec($ch));
        
        if(isset($response->access_token))
            $this->session->user = ["access_token"=>$response->access_token];
        else 
            log_message("error","OKTA " . $response->error_description);

        //echo "Got Access token in session: " . $this->session->user["access_token"];
        //echo "----";
        
        //die;
    }

    public function getOktaId()
    {

        $url = getenv('OKTA_BASE_URL') . "oauth2/default/v1/userinfo";
        
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer '. $this->session->user['access_token']
        );
        
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $response = json_decode(curl_exec($ch));

        return trim($response->sub);
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(getenv("OKTA_BASE_URL") . "login/signout?fromURI=" . getenv("SITE_URL"));
    }


    /**
     * Get user profile from OKTA
     */
    public function createProfileSession($oktaId)
    {
        // initiate okta api call, verify login user
        $ch = curl_init(getenv('OKTA_BASE_URL') . "api/v1/users/" . $oktaId);
    
        $headers = array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: SSWS ' . getenv('OKTA_API_TOKEN')
            );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = json_decode(curl_exec($ch));
        
        // user verified with valid email
        if (isset($response->profile) && valid_email($response->profile->email)) {

            $this->session->user = array(
                "oktaId"=>$oktaId,
                "zohoId"=>$response->profile->organization,
                "email"=>$response->profile->email,
                "organization"=>$response->profile->organization,
                "firstName"=>$response->profile->firstName,
                "lastName"=>$response->profile->lastName,
            );
            // set child bit
            addToSessionKey("user",['child'=>$this->hasChild()]);

            // add permission based on child
            addToSessionKey('user',['permissions'=>$this->getPermissions($this->session->user['child'])]);
        } else {
            // log error for back tracking
            log_message("error","OKTA_API: " . json_encode($response));
        }
    }

    private function getPermissions($bParent=true)
    {
        // if entity is parent, permissions are
        $aPermission = array(
                            "Entity",
                            "Entity_Add",
                            
                            "Contacts",
                            "Contacts_Add",
                            
                            "Attachments",
                            "Task_Update",
                            "Theme_Update",
                            "Theme_View",
                        );
        // if not parent, cut down permissions
        if(!$bParent)
        {   
            // remove permission
            $aRemovePermission = array(
                "Entity_Add"
            );

            foreach($aRemovePermission as $v){
                $iIndex = array_search($v,$aPermission);
                unset($aPermission[$iIndex]);
            }
        }

        return $aPermission;
        
    }

    private function hasChild()
    {
        $this->load->model("Accounts_model");
        
        $bParentAccount = $this->Accounts_model->hasEntities($this->session->user["zohoId"]);

        return (int)$bParentAccount;
    }
}
