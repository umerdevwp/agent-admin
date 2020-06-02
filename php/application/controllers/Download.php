<?php

// use Src\Services\OktaApiService as Okta;
header('Access-Control-Allow-Origin: *');

use zcrmsdk\crm\crud\ZCRMTag;

defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;


include APPPATH . '/libraries/CommonDbTrait.php';

class Download extends CI_Controller
{
    public function file($sLoraxFileId)
    {
        $sFileName = $this->input->get('name');

        $aParams = [
            "expire_url_in_mins"    =>  120,// between 5 - 120 minutes

        ];
        $sLoraxUrl = "https://lorax-api-sandbox.filemystuff.com/api/v1/download/" . $sLoraxFileId;

        $sResponse = $this->curlGetUrl($sLoraxUrl);

        $aGoogleFile = json_decode($sResponse,true);

        if($aGoogleFile['error'])
        {
            echo "Sorry, unable to process download";
            error_log("Download Lorax: " . $aGoogleFile['message']);
        } else {

            $cURLConnection = curl_init();
            curl_setopt($cURLConnection, CURLOPT_URL, $aGoogleFile['url']);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
                'x-goog-encryption-algorithm:' . $aGoogleFile['x-goog-encryption-algorithm'],
                'x-goog-encryption-key:' . $aGoogleFile['x-goog-encryption-key'],
                'x-goog-encryption-key-sha256:' . $aGoogleFile['x-goog-encryption-key-sha256'],

            ));

            //$filePath = tmpfile();
            //$fileOpen = fopen($filePath, 'w');
            //curl_setopt($cURLConnection, CURLOPT_FILE, $fileOpen);
            $fileData = curl_exec($cURLConnection);
            $contentType = curl_getinfo($cURLConnection, CURLINFO_CONTENT_TYPE);

            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="'.$sFileName.'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fileData));
            flush(); // Flush system output buffer
            echo $fileData;
            //            readfile($fileData);
        }
    }

    private function curlGetUrl($sUrl)
    {
        $cURLConnection = curl_init();
        curl_setopt($cURLConnection, CURLOPT_URL, $sUrl);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURLConnection, CURLOPT_REFERER, getenv("SITE_URL"));
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
            'authorization:' . getenv(LORAX_TOKEN)
        ));
        $sResponse = curl_exec($cURLConnection);
        curl_close($cURLConnection);

        return $sResponse;
    }
}
