<?php

header('Access-Control-Allow-Origin: *');
use \Firebase\JWT\JWT;
class Auth
{
//    add a class name here to secure for api
    //private $auth = ['api', 'example_api', 'entity_api'];
    private $auth = ['api','portal','entitytypes','contacts','states','contacttypes', 'entity', 'registeragents', 'documents', 'admin_api','notifications','tasks','message'];
    private $skipRoute = ['message/cronLogMailStatus','message/cronNotifyForSubscription','message/cronNotifyForAttachments','message/receive'];
    public function myFunction()
    {
        $CI =& get_instance();

        if(in_array(strtolower($CI->router->class)."/{$CI->router->method}",$this->skipRoute))
        {
            return "it is a cron call";
        }

        if(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']) )  {

            if (in_array(strtolower($CI->router->class), $this->auth)) {

                $token = $CI->input->get_request_header('Authorization');
                // debugging
//                $token = "eyJraWQiOiJ5T21SR3JScHBsZUs1aFA1ajhxbXFzNVlkV3dDWGZfb1Z3dVhWVUVMaGhBIiwiYWxnIjoiUlMyNTYifQ.eyJ2ZXIiOjEsImp0aSI6IkFULnR0MF9xSTl3bHFlZTI2Unk5ajJJcGd5VWp1UE96Vngtd0NtN0otalhwZVkiLCJpc3MiOiJodHRwczovL2Rldi02MTIwNjkub2t0YS5jb20vb2F1dGgyL2RlZmF1bHQiLCJhdWQiOiJhcGk6Ly9kZWZhdWx0IiwiaWF0IjoxNTk1MjcwNzkwLCJleHAiOjE1OTUyNzQzOTAsImNpZCI6IjBvYTJyeWJoemxtRDJZenJKMzU3IiwidWlkIjoiMDB1MWwzYzR4eXNCMjhvTXozNTciLCJzY3AiOlsib3BlbmlkIiwiZW1haWwiLCJhZGRyZXNzIiwicGhvbmUiLCJwcm9maWxlIl0sInN1YiI6ImNoYm95Y2VAdW5pdGVkYWdlbnRzZXJ2aWNlcy5jb20ifQ.FqW_mYwTcjSnb3LpQ-xUq2eRtKUsLF-PwKvtJI04hdEiCfNv4308t4N4eNs2i_Zwy4YeW0S_NygYZlOLEQ-bpYY5jv5TzXEvG0RqL961X9kByDd58ma-RkZ49gBpxdlD2SIlBtLnuuO-40Pja3ohfi8jnYVRYjzz8La2ilEPHtoZvAzmTyxBSATORXyXnK3Efq8ewO2PXGLi-ur_uA2ZwdjpMIfw0fPBurMkVYx2rb9pg-ZIaLlvuQiW3GUtcMkFntZiv9hsl2kOPRDuX9YrTupHtnwJteffdH3myBgMJc41t7JJyoqus1UzQ8Ddlxd0rI9hMsIn8Smw64PZNm2ZTg";
                // TODO: check email exist in admin

                // to allow functionality of login as for admin
                $oToken = $this->hasToken($token);
                $sToken = $oToken->token;

                // on token found return it after setting session
                if ($sToken) {
                    $iEid = $oToken->entity_id;
                    $sAccountType = unserialize($oToken->meta)['accountType'];
                    $this->setSession($iEid,$sAccountType,$oToken->email);
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
                                $iEid = $CI->input->get('eid');
                                $sAccountType = "";

                                // on token not found set session
                                if($CI->input->get("account")=='tester'){
                                    $sAccountType = 'tester';
                                } else if($this->checkForAdmin($response->email)){
                                    $sAccountType = 'admin';
                                }

                                $this->setSession($iEid,$sAccountType,$response->email);
                                $this->deletePreviousToken($response->sub);
                                $this->addToken($response->sub,$iEid, $response->email , $sAccountType, $token);

                                // to make role assignment if user has parent attribute, okta organization_type
                                if($CI->input->get("bit")==1)
                                {
                                    $CI->load->model("Permissions_model");
                                    $oDataPermission = $CI->Permissions_model->roleExist($_SESSION["eid"]);
                                    if(empty($oDataPermission))
                                    {
                                        $CI->Permissions_model->add($_SESSION["eid"],"parent");
                                    }
                                }
                            
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
    }

    public function checkForAdmin(string $sEmail)
    {
        $CI =& get_instance();
        $CI->load->model('Admin_model');

        $checkSuperUser = $CI->Admin_model->checkAdminExist($sEmail);

        if (!empty($checkSuperUser)) {
            $CI->Admin_model->updateAdmin($sEmail);
            return true;
        }

        return false;
    }


    public function addToken($sub,$eid, $email, $sAccountType , $token)
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
            //$eid = $CI->input->get('eid');
            //$sAccountType = $CI->input->get("account");
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
                'meta'=> serialize(['accountType'=>$sAccountType]),
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

    private function setSession($iEid,$sAccountType="",$sEmail="")
    {
        $CI =& get_instance();

        $_SESSION['eid'] = $iEid;
        $_SESSION['accountType'] = $sAccountType;
        $_SESSION['email'] = $sEmail;
        $_SESSION['request'] = strtolower($CI->router->class)."/".$CI->router->method;
    }

}
