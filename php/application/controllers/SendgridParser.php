<?php

// use Src\Services\OktaApiService as Okta;
header('Access-Control-Allow-Origin: *');

use zcrmsdk\crm\crud\ZCRMTag;

defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;


include APPPATH . '/libraries/CommonDbTrait.php';

class SendgridParser extends CI_Controller
{
    public function index($sToken)
    {
      $this->load->model("SgParseLog_model");
//echo "HH";
      if($sToken==getenv("SENDGRID_POST_TOKEN"))
      {
//        error_log("Recorded maile started");
        $to = $_POST["to"];
        $from = $_POST["from"];
        $body = $_POST["text"];
        $subject = $_POST["subject"];
        $num_attachments = (int)$_POST["attachments"];
//        error_log("Uploading files");
        $aFileName = $this->uploadMailFiles($num_attachments);
//        $from = '"Najm A." <najm.a@allshorestaffing.com>';
//echo        $from = substr($from,strpos($from,"<")+1,-1);
//echo strpos($from,"<");
//die("KKK");
//$sData = ["post_content" => "","from" => '"Najm A." <najm.a@allshorestaffing.com>',
//"subject" => "Subject 10","body" => "Simple 10//
//\n\nNAJM A \xe2\x80\xa2\nSenior Software Engineer\n1818 W. Lindsey St, Ste C-100 \xe2\x80\xa2\nNorman, OK 73069\nO: (888) 326-5611\n \xe2\x80\xa2\nE: najm.a@allshorestaffing.com\n\n    ",
//"attachments" => ""];//a:5:{i:0;s:13:"5fb566155dabd";i:1;s:13:"5fb566155dac5";i:2;s:13:"5fb566155dac9";i:3;s:13:"5fb566155dacd";i:4;s:13:"5fb566155dad0";}\n);
//$iId = $this->SgParseLog_model->insert($sData);
//echo $iId;
//die;
//print_r($sData);die;
//error_log("from: " . json_encode($_POST));
//error_log("---");
//echo substr($from,strpos($from,"<")+1);
//var_dump(filter_var(substr($from,strpos($from,"<")+1,-1), FILTER_VALIDATE_EMAIL));
//echo $_SERVER['DOCUMENT_ROOT']."temp786/";
//die;
        if(filter_var(substr($from,strpos($from,"<")+1,-1), FILTER_VALIDATE_EMAIL))
        {
//          error_log("Inside insert");
          $aData = [
              'post_content' => json_encode($_POST),
            'from'=>$from,
            'subject'=>$subject,
            'body'=>$body,
            'attachments'=>json_encode($aFileName)
          ];
//          error_log(print_r($aData,true));
//error_log("INSERTINGGG");
          $iId = $this->SgParseLog_model->insert($aData);

        }
                error_log("Recorded mail ends id: " . $iId);
      } else {
        logToAdmin("SendgridParser: Path accessed without token");
        die("Permission denied");
      }
    }

    private function uploadMailFiles(int $iNumAttachments=0)
    {
      $aFileName = [];

      if($iNumAttachments){
        foreach($_FILES as $aFile) {
//          $aFile = $_FILES['attachment'+$i];
          $sName = uniqid()."-".$aFile['name'];
          
//          error_log("Upload: " . $aFile['tmp_name'] . " > " . $_SERVER['DOCUMENT_ROOT']."/"."temp786/".$sName);          
          if(!empty($aFile['tmp_name']) && strpos($aFile['type'],"pdf")!==false && $aFile['size']<100000)
          {

//            error_log("Upload: " . $aFile['tmp_name'] . " > " . $_SERVER['DOCUMENT_ROOT']."/"."temp786/".$sName);

            $result = move_uploaded_file(
              $aFile['tmp_name'],
              $_SERVER['DOCUMENT_ROOT']."/"."temp786/".$sName
            );
            $aFileName[] = $sName;
//            error_log("Uploaded: " . print_r($result,true));
          } else {
            error_log("File in mail not valid: " . print_r($aFile,true));
          }
//          error_log(print_r($attachment,true));
            // $attachment will have all the parameters expected in a the PHP $_FILES object
            // http://www.php.net/manual/en/features.file-upload.post-method.php#example-369
        }
      }

      return $aFileName;
    }
}
