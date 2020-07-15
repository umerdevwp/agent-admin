<?php
header('Access-Control-Allow-Origin: *');

class AuthVendor
{
    private $auth = ['user_api'];

    public function basicAuth()
    {
        $CI =& get_instance();
        if (in_array(strtolower($CI->router->class), $this->auth)) {
            $username = $CI->input->get_request_header('username');
            $password = $CI->input->get_request_header('password');


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

                $responseJson_UserInfo = json_decode(curl_exec($curl2));;
                if ($responseJson_UserInfo->profile->organization_apihost == $CI->input->ip_address()) {
                    //passing control to controller
                    $_SESSION['eid'] = $responseJson_UserInfo->profile->organization;
                } else {
                    $returnResponse = ['status' => 401, 'message' => "Host not is unauthorized", NULL];
                    echo json_encode($returnResponse);
                    die();
                }

            } else {
                echo $response;
                die();
            }
        }
    }
}


