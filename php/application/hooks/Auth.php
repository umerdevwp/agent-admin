<?php

header('Access-Control-Allow-Origin: *');
use \Firebase\JWT\JWT;
class Auth
{
//    add a class name here to secure for api
    //private $auth = ['api', 'example_api', 'entity_api'];
    private $auth = ['api', 'example_api','portal'];
    public function myFunction()
    {
        $CI =& get_instance();
        if (in_array($CI->router->class, $this->auth)) {
            $token = $CI->input->get_request_header('Authorization');
            $sToken = $this->hasToken($token)->token;

            if ($sToken) {
                return $sToken;
            }

            try {
                if ($token != NULL) {
                    $url = getenv('OKTA_BASE_URL') . "oauth2/default/v1/userinfo";
                    $ch = curl_init($url);
                    $headers = array(
                        'Authorization: Bearer ' . $token,
                    );
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = json_decode(curl_exec($ch));
                    if (empty($response->sub)) {
                        $returnResponse = ['status' => 401, 'message' => "Auth Failed", NULL];
                        echo json_encode($returnResponse);
                        die();
                    } else {

                        $this->deletePreviousToken($response->sub);
                        $this->addToken($response->sub, $response->email , $token);

                        $email = $CI->input->get('email');
                        if(!empty($email)){
                           if($response->email !== $response){
                               $returnResponse = ['status' => 401, 'message' => "Invalid Email", NULL];
                               echo json_encode($returnResponse);
                               die();
                           }
                        }

                        $returnResponse = ['status' => 200, 'message' => "Success", NULL];
                    }
                } else {
                    $returnResponse = ['status' => 401, 'message' => "Auth Failed", NULL];
                    echo json_encode($returnResponse);
                    die();
                }
            } catch
            (Exception $e) {
                $response = ['status' => 401, 'message' => "Auth Failed", NULL];
                echo json_encode($response);
                die();
            }
        }
    }

    public function checkForAdmin($email = NULL)
    {
        $CI =& get_instance();
        $CI->load->model('Admin_model');
        $checkSuperUser = $CI->Admin_model->checkAdminExist($email);
        if (!empty($checkSuperUser)) {
            $_SESSION["isAdmin"] = TRUE;
            $CI->Admin_model->updateAdmin($email);
        } else {
            $_SESSION["isAdmin"] = FALSE;
        }
    }


    public function addToken($sub, $email , $token)
    {

        $CI =& get_instance();
        $CI->load->model("Auth_model");

        $jwtVerifier = (new \Okta\JwtVerifier\JwtVerifierBuilder())
            ->setDiscovery(new \Okta\JwtVerifier\Discovery\Oauth) // This is not needed if using oauth.  The other option is OIDC
            ->setAdaptor(new \Okta\JwtVerifier\Adaptors\FirebasePhpJwt)
            ->setAudience('api://default')
            ->setClientId('0oa2rybhzlmD2YzrJ357')
            ->setIssuer('https://dev-612069.okta.com/oauth2/default')
            ->build();

        $jwt = $jwtVerifier->verify($token);

//        //Returns instance of \Okta\JwtVerifier\JWT
       $expired_on = $jwt->getExpirationTime(false);

        if(!empty($expired_on)) {
            $data = array(
                'sub' => $sub,
                'email' => $email,
                'token' => $token,
                'expired_on' => date('Y-m-d H:i:s',$expired_on)
            );
            $bContactRow = $CI->Auth_model->add($data);
        }




    }

    public function hasToken($token)
    {

        $CI =& get_instance();
        $email = $CI->input->get('email');
        $CI->load->model("Auth_model");
        $auth_object = $CI->Auth_model->tokenExists($token, $email);
        if($auth_object->expired_on > date('Y-m-d H:i:s')){
           return $auth_object;
        } else {
            return null;
        }
    }
    public function deletePreviousToken($sub){
        $CI =& get_instance();
        $CI->load->model("Auth_model");
        return $CI->Auth_model->delete($sub);
    }

}
