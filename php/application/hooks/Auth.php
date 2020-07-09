<?php

header('Access-Control-Allow-Origin: *');
use \Firebase\JWT\JWT;
class Auth
{
//    add a class name here to secure for api
    //private $auth = ['api', 'example_api', 'entity_api'];
    private $auth = ['api', 'user_api','portal','entitytypes','contacts','states','contacttypes', 'entity', 'registeragents', 'attachments', 'admin_api'];
    public function myFunction()
    {
        $CI =& get_instance();
        if (in_array(strtolower($CI->router->class), $this->auth)) {
            $token = $CI->input->get_request_header('Authorization');
            // TODO: check email exist in admin

            // to allow functionality of login as for admin
            $oToken = $this->hasToken($token);
            $sToken = $oToken->token;
            $_SESSION['eid'] = "not set yet";
            
            
            if ($sToken) {
                
                $_SESSION['eid'] = $oToken->entity_id;
                
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
                        $returnResponse = ['status' => 401, 'message' => "No response from okta", NULL];
                        echo json_encode($returnResponse);
                        die();
                    } else {

                        // zoho_id should be in the request else don't save token


                        if(!empty($CI->input->get('eid')))
                        {
                            $this->deletePreviousToken($response->sub);
                            $this->addToken($response->sub, $response->email , $token);
                            if((int)$CI->input->get("bit")==1)
                            {
                                $CI->load->model("Permissions_model");
                                $oDataPermission = $CI->Permissions_model->roleExist($_SESSION["eid"]);
                                
                                if(!$oDataPermission)
                                {
                                    $CI->Permissions_model->add($_SESSION["eid"],"parent");
                                }
                            }
                        $_SESSION['eid'] = $CI->input->get('eid');
                        }
                        $returnResponse = ['status' => 200, 'message' => "Success", NULL];
                    }
                } else {
                    $returnResponse = ['status' => 401, 'message' => "Auth Failed", NULL];
                    echo json_encode($returnResponse);
                    die();
                }
            } catch(Exception $e) {
                $response = ['status' => 401, 'message' => $e->getMessage(), NULL];
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
            ->setClientId(getenv("OKTA_CLIENT_ID"))
            ->setIssuer(getenv("OKTAISSUER"))
            ->build();

        $jwt = $jwtVerifier->verify($token);

//        //Returns instance of \Okta\JwtVerifier\JWT
        $expired_on = $jwt->getExpirationTime(false);

        if(!empty($expired_on)) {
            // search in entity table
            //$email = "chboyce@unitedagentservices.com";
            //$CI->load->model("Entity_model");
            //$aEntityData = $CI->Entity_model->getEmailId($email);
            $eid = $CI->input->get('eid');

//            if($aEntityData['type']=='ok')
//                $eid = $aEntityData['results']->id;

            // not found, search in admins
            /*if($eid==0)
            {
                $CI->load->model("Admin_model");
                $aAdminData = $CI->Admin_model->checkAdminExist($email);
                if(count($aAdminData))
                    $eid = $aAdminData[0]->zoho_id;
            }*/

            // set data to store token
            $data = array(
                'sub' => $sub,
                'email' => $email,
                'token' => $token,
                'expired_on' => date('Y-m-d H:i:s',$expired_on),
                "entity_id" =>  $eid
            );
            $bContactRow = $CI->Auth_model->add($data);
        }




    }

    public function hasToken($token)
    {

        $CI =& get_instance();
        $CI->load->model("Auth_model");
        $auth_object = $CI->Auth_model->tokenExists($token);
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

    private function getEntityId($sEmail)
    {
        $CI =& get_instance();
        $CI->load->model("Entity_model");
        $aEntityData = $CI->Entity_model->getEmailId($sEmail);
        $eid = -1;

        if($aEntityData['type']=='ok')
            $eid = $aEntityData['results']->id;

        return $eid;
    }

}