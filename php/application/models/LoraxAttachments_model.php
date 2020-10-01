<?php

use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\crm\crud\ZCRMModule;
use zcrmsdk\crm\crud\ZCRMRecord;

class LoraxAttachments_model extends CI_Model
{

    private $table = "lorax_attachments";

    private $aColumns = [
        "id"    =>  "id",
        "fid"     =>  "file_id",
        "eid"   =>        "entity_id",
        "name"=>        "name",
        "created"=> "added",
        "fileSize"  =>  "file_size",
        "token"     => "token",
    ];

    public function __construct()
    {
        $this->load->database();
    }

    public function getAllFromParentId($id,$aColumns=[])
    {
        $data = [
            'za.parent_account' => $id,
        ];

        if(count($aColumns)>0)
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","name","fid","eid","fileSize","created"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }


        foreach($aMyColumns as $k=>$v){
            $this->db->select("la.$v as `$k`");
        }
        $this->db->from($this->table . " la");

        $this->db->join("zoho_accounts za","za.id=la.entity_id");
        $this->db->where($data);
        $query = $this->db->get();
        $result = $query->result_object();
        if (! is_array($result)) {
            return ['message'=>'No attachments available','type'=>'error'];
        }

        return ['type'=>'ok','results'=>$result];
    }

    private function refreshTokens($whereEntityId)
    {
        // update the download tokens 1st
        $this->db->set('token','md5(concat(rand(),"",file_id))',false);
        
        if(is_array($whereEntityId)) $this->db->where_in($whereEntityId);
        else $this->db->where($aWhereData);
        
        $this->db->update($this->table);
    }

    public function getAllFromEntityId($id,$aColumns=[])
    {
        if($id>0)
        {
            $data = [
                'entity_id' => $id,
            ];

            $this->refreshTokens($id);
            
            // get the rows
            if(count($aColumns)>0)
                $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
            else {
                $aMyColumns = [
                    "id","name","fid","eid",'fileSize',"created","token"
                ];
                $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
            }

            foreach($aMyColumns as $k=>$v){
                $this->db->select("$v as `$k`");
            }

            $query = $this->db->get_where($this->table,$data);

            $result = $query->result_object();

            if (! is_array($result)) {
                return ['message'=>'No attachments available','type'=>'error'];
            }
            
            return ['type'=>'ok','results'=>$result];
        } else {
            return ['message'=>'Input id is missing','type'=>'error'];
        }
    }

    public function getAllFromEntityList($aCommaIds,$aColumns=[])
    {
        $this->refreshTokens($aCommaIds);

        if(count($aColumns)>0)
            $aMyColumns = arrayKeysExist($aColumns,$this->aColumns);
        else {
            $aMyColumns = [
                "id","name","fid","eid","fileSize","created","token"
            ];
            $aMyColumns = arrayKeysExist($aMyColumns,$this->aColumns);
        }
        foreach($aMyColumns as $k=>$v)
            $this->db->select("$v as `$k`");

        $this->db->from($this->table);
        $query = $this->db->where_in('entity_id',$aCommaIds);

        $query = $this->db->get();
        $result = false;
        
        if($query)
        {
            $result = $query->result_object();
        }

        
        if (! is_array($result)) {
            return ['msg'=>'No contacts available','msg_type'=>'error'];
        }

        return ['type'=>'ok','results'=>$result];
    }

    public function checkOwnership($iOwner,$id)
    {
        $data = array(
            "entity_id" =>  $iOwner,
            "id"    =>  $id
        );
        $query = $this->db->get_where($this->table,$data);

        $row = null;
        if($query){
            $row = $query->row();
        }

        if ($row)
        {
            return $row;
        }
        return false;
    }

    public function replace($id,$data)
    {

        // update
        if($id>0)
        {
            $this->db->replace($this->table, $data);
        }
    }

    public function insert($data)
    {
        $bInserted = $this->db->insert($this->table,$data);
        $iId = 0;

        if($bInserted)
        {
            $iId = $this->db->insert_id();
        } else {
            $iId = -1;
        }

        return $iId;
    }
    /**
     * Upload the file from $_FILES variable to lorax storage
     * 
     * @param String $sInputTmpName variable from $_FILES['inputName']['tmp_name'] 
     * @param String $sInputFileName variable from $_FILES['inputName']['name']
     * @param String $sInputFileName variable from $_FILES['inputName']['type']
     * 
     * @return Mixed Error details or Uploaded file details 
     */
    public function upload($sInputTmpName,$sInputFileName,$sInputFileType)
    {
        $token = "3cJe5YXiSBGUAqYd0uFC2cKYvgfBIUswmXTudN3HQfvzIGvddfVYjPmakGOkGVM9g5YRKJR2FF9iYuZQ0GsbGw";
        $cFile = curl_file_create($sInputTmpName,
                                 $sInputFileType,
                                 $sInputFileName
                              );
        $curl = curl_init();
        curl_setopt_array($curl, [
            "CURLOPT_URL" => "http://lorax-server.local/api/v1/upload",
            "CURLOPT_RETURNTRANSFER" => true,
            "CURLOPT_REFERER" => "https://api.youragentservices.com",
            "CURLOPT_FOLLOWLOCATION" => true,
            "CURLOPT_HTTP_VERSION" => CURL_HTTP_VERSION_1_1,
            "CURLOPT_CUSTOMREQUEST" => 'POST',
            "CURLOPT_POSTFIELDS" => ['file' => $cFile],
            "CURLOPT_HTTPHEADER" => ['authorization: ' . $token],
        ]);
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        return $response;
    }


    public function download($sLoraxFileId)
    {
        
        $aParams = [
            "expire_url_in_mins"    =>  120,// between 5 - 120 minutes

        ];
        $sLoraxUrl = "https://lorax-api-sandbox.filemystuff.com/api/v1/download/" . $sLoraxFileId;
        
        $sResponse = $this->curlGetUrl($sLoraxUrl);
        
        $aGoogleFile = json_decode($sResponse,true);

        $cURLConnection = curl_init();
        curl_setopt($cURLConnection, CURLOPT_URL, $aGoogleFile['url']);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
            'x-goog-encryption-algorithm:' . $aGoogleFile['x-goog-encryption-algorithm'],
            'x-goog-encryption-key:' . $aGoogleFile['x-goog-encryption-key'],
            'x-goog-encryption-key-sha256:' . $aGoogleFile['x-goog-encryption-key-sha256'],

        ));
        $filePath = tmpfile() . '.pdf';
        $fileOpen = fopen($filePath, 'w');
        curl_setopt($cURLConnection, CURLOPT_FILE, $fileOpen);
        $fileData = curl_exec($cURLConnection);
        $contentType = curl_getinfo($cURLConnection, CURLINFO_CONTENT_TYPE);

        curl_close($cURLConnection);
        header("Content-type: application/pdf");
        readfile($filePath);

    }

    
    private function curlGetUrl($sUrl)
    {
        $cURLConnection = curl_init();
        curl_setopt($cURLConnection, CURLOPT_URL, $sUrl);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        
        $sResponse = curl_exec($cURLConnection);
        curl_close($cURLConnection);

        return $sResponse;
    }

     /**
     * Get notification with where array
     * 
     * @param Array $aWhere columns criterea
     * 
     * @return Array Record row / Error message no row found
     */
    public function getOne(array $aWhere=[])
    {
        if(count($aWhere)>0)
        {
            $query = $this->db->get_where($this->table, $aWhere);
            $result = $query->row();

            if (!$result) {
                return ['message'=>'No such record found','type'=>'error'];
            }
            return $result;
        } else {
            return ['message'=>'Incomplete arguments','type'=>'error'];
        }
    }

    /**
     * Get notification with where array
     * 
     * @param Array $aWhere columns criterea
     * 
     * @return Array Record row / Error message no row found
     */
    public function updateToken(array $aWhere=[])
    {
        if(count($aWhere)>0)
        {
            $this->db->set('token','');
            $this->db->where($aWhere);
            $this->db->update($this->table);

            if($this->db->affected_rows()!=1)
            {
                logToAdmin("Document token refresh fail","File clause: " . print_r($aWhere,true));
            }
        }
    }
}
