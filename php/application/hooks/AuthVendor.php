<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
class AuthVendor
{
    private $auth = ['user_api','entity','tasks','documents'];

    public function basicAuth()
    {
        $CI =& get_instance();
        if(!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])){
            if (in_array(strtolower($CI->router->class), $this->auth)) {
                $username = $_SERVER['PHP_AUTH_USER'];
                $password = $_SERVER['PHP_AUTH_PW'];

                $url = getenv("OKTA_BASE_URL") . '/api/v1/authn';
                $okta_token = 'SSWS ' . getenv('OKTA_API_TOKEN');
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\n  \"username\": \"$username\",\n  \"password\": \"$password\",\n  \"options\": {\n    \"multiOptionalFactorEnroll\": true,\n    \"warnBeforePasswordExpired\": true\n  }  \n}",
                    CURLOPT_HTTPHEADER => array(
                        "Accept: application/json",
                        "Content-Type: application/json",
                    ),
                ));
                $response = curl_exec($curl);
                $responseJson = json_decode(curl_exec($curl));
                curl_close($curl);

                if ($responseJson->status == 'SUCCESS') {
                    $curl2 = curl_init();
                    $userInfo_url = getenv("OKTA_BASE_URL") . '/api/v1/users/' . $responseJson->_embedded->user->id;
                    curl_setopt_array($curl2, array(
                        CURLOPT_URL => $userInfo_url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            "Accept: application/json",
                            "Content-Type: application/json",
                            "Authorization: $okta_token"
                        ),
                    ));

                    $responseJson_UserInfo = json_decode(curl_exec($curl2));
                    $host_list = explode(";",$responseJson_UserInfo->profile->organization_apihost);
                    //error_log("vendor ip login: " . $CI->input->ip_address());
                    if (in_array(strtolower($CI->input->ip_address()), $host_list) || isDev()) {
                        $_SESSION['eid'] = $responseJson_UserInfo->profile->organization;
                    } else {
                        $returnResponse = ['status' => 401, 'message' => "Host not is unauthorized"];
                        echo json_encode($returnResponse);
                        die();
                    }
                } else {
                    $aJson = json_decode($response,true);
                    echo json_encode(['status' => 401, 'message' => $aJson['errorSummary']]);
                    die();
                }
            } else {
                $returnResponse = ['status' => 401, 'message' => "Invalid request"];
                echo json_encode($returnResponse);
                die();
            }
        }
        
    }
}


