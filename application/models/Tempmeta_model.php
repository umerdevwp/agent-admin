<?php
class Tempmeta_model extends CI_Model
{

    private $table = "tempmeta";

    public $slugNewContact = "new_contacts";

    public $slugTasksComplete = "tasks_complete";

    public $slugNewEntity = "new_entities";

    public $slugNewAttachment = "new_attachments";

    public function __construct()
    {
        $this->load->database();
    }

    public function getAll($iUserid,$sSlug="",$bAsArray=true)
    {
        if($this->isDbSynched())
        {
            $result = $this->deleteAll();
            if($result)
            {
                log_message("error","Unable to delete tempmeta");
            }
        } else {

            $data = [
                'userid'    =>  $iUserid,
            ];

            if(!empty($sSlug))
                $data["slug"] = $sSlug;

            $query = $this->db->get_where($this->table, $data);

            if($bAsArray)
                $result = $query->result_array();
            else
                $result = $query->result();
            //echo $this->db->last_query();
            if (! is_array($result)) {
                return ['results'=>['message'=>'No slugs available'],'type'=>'error'];
            } else {
                return ['type'=>'ok','results'=>$result];
            }
        }

        return $result;
    }

    private function resetTempmeta()
    {
        if($this->isDbSynched())
        {
            $result = $this->deleteAll();
            if(!$result)
            {
                log_message("error","Unable to delete tempmeta");
            }
        }
    }

    private function deleteAll()
    {
        $query = "DELETE FROM {$this->table}";
        
        $result = $this->db->query($query);

        if($result)
        {
            $this->updateDbSynchTime();
            //echo $this->db->last_query();
            return true;
        } else {
            return false;
        }
    }

    public function getOne($iUserid, $sSlugname)
    {
        $data = [
            'slug'    =>  $sSlugname
        ];

        if($iUserid>0)
        {
            $data['userid'] = $iUserid;
        }

        $query = $this->db->get_where($this->table, $data);
        $result = $query->row();
        
        if ($result) {
            return ['results'=>$result,'type'=>'ok'];
        } else {
            return ['results'=>$result,'type'=>'error'];
        }

        return $result;
    }

    public function update($iUserid,$sSlugname,$sJsonEncoded)
    {
        $aData = [
            "json_data"  =>  $sJsonEncoded
        ];
        $aWhere = [
            "userid"    =>  $iUserid,
            "slug"      =>  $sSlugname,
        ];
        // check 
        $row = $this->getOne($iUserid,$sSlugname);
        //var_dump($row);
        if($row['type']=='ok')
        {
            $result = $this->db->update($this->table, $aData,$aWhere);
        // insert
        } else {
            $aData["userid"] = $iUserid;
            $aData["slug"] = $sSlugname;
            $result = $this->db->insert($this->table, $aData);
        }

        if($result)
        {
            return true;
        } else {
            return false;
        }
    }

    private function updateDbSynchTime()
    {

        $query = 'SELECT `UPDATE_TIME` AS updatetime FROM information_schema.tables'
        . '  WHERE TABLE_SCHEMA="'.getenv("DB_NAME").'" AND table_name="zoho_status_history"';
        
        $dbResponse = $this->db->query($query);
        $row = $dbResponse->row();
        
        $aData = [
            "json_data"     =>  json_encode(["datetime" => $row->updatetime])
        ];

        if($row)
        {
            $this->db->update("appmeta",$aData,"slug='synched_datetime'");
        }

        //echo $this->db->last_query();
    }

    public function isDbSynched()
    {
        $query = 'SELECT (TO_SECONDS(NOW())-TO_SECONDS(REPLACE(json_data->\'$.datetime\',\'"\',\'\'))) last_synch_seconds FROM appmeta';
        $dbname = getenv("DB_NAME");
        $query =<<<HC
        SELECT (
        TO_SECONDS(
        (
            SELECT `UPDATE_TIME` FROM information_schema.tables WHERE TABLE_SCHEMA="{$dbname}" 
            AND table_name="zoho_status_history")
        ) - TO_SECONDS(REPLACE(json_data->"$.datetime",'"',''))) last_synch_seconds FROM appmeta
HC;

        $oResponse = $this->db->query($query);
        $row = $oResponse->row();
        //$row = (object)["last_synch_seconds"=>110];
        //echo $this->db->last_query();die;

        if($row->last_synch_seconds>0)
        {
            return true;
        } else {
            return false;
        }

    }

    public function appendRow($iId,$sSlug,$aData,$sRowKey="id")
    {
        // reset Tempmeta table for schedule synchs done by zoho
        $this->resetTempmeta();

        $aResult = $this->getOne($iId,$sSlug);

        $aTempData = $aNewData = array();

        if($aResult['type']=='ok' && !is_null($aResult['results']))
        {
            $aTempData = json_decode($aResult['results']->json_data);

            if(count($aTempData)){
                $bDataFound = false;
                foreach($aTempData as $k=>$v)
                {
                    $aRow = (array)$v;
                    // replace the complete row if exists
                    if($aRow[$sRowKey]==$aData[$sRowKey])
                    {
                        $bDataFound = true;
                        $aNewData[] = $aData;
                    } else { // add the row in the list
                        $aNewData[] = $v;
                    }
                }
                // if data doesn't exist already, add new data after previous temp entity records
                if(!$bDataFound) $aNewData[] = $aData;
            } else { // add the row when list was blank
                $aNewData[] = $aData;
            }
        } else { // add the row when no record in table
            $aNewData[] = $aData;
        }
        
        $this->update($iId,$sSlug,json_encode($aNewData));
        
    }

    public function deduceRow($iId,$sSlug,$aData)
    {
        $aResult = $this->getOne($iId,$sSlug);

        $aTempData = $aNewData = [];

        if($aResult['type']=='ok')
        {
            $aTempData = json_decode($aResult['results']->json_data);
            foreach($aTempData as $k=>$v)
            {
                if($v==$aData)
                {
                    continue;
                }
                $aNewData[] = $v;
            }

        }

        $this->update($iId,$sSlug,json_encode($aNewData));

    }

    public function checkRowExistInJson($aData)
    {
        $aData = $this->getOneInJson($aData,true);
        $row = null;

        if($aData['type']=='ok') $row = $aData['results'];
        
        $bResult = false;

        if($row)
        {
            $bResult = true;
        }

        return $bResult;

    }

    public function getOneInJson($aData,$bCheckExistOnly=false)
    {
        $aNewData = [];

        $strWhere = " WHERE 1=1";
        $strJsonSearch = "";
        foreach($aData as $k=>$v)
        {
            $iJsonPos = strpos($k,"json_");
            if($iJsonPos!==false)
            {
                //$strWhere .= ' AND json_data->\'$.'.substr($k,5).'\'=\''.$v.'\'';
                // "one" means reaturn index of data
                $strWhere .= ' AND !ISNULL(JSON_SEARCH(json_data, "one", "'.$v.'"))';

                $strJsonSearch = 'JSON_SEARCH(json_data, "one", "'.$v.'")';
            } else {
                
                $strWhere .= " AND $k='".$v."'";
                
            }
        }

        $sQuery = 'SELECT ' . $strJsonSearch . ' as at_index,json_data FROM ' . $this->table . $strWhere;

        $oQuery = $this->db->query($sQuery);
        $row = $oQuery->row();

        if($row)
        {
            // return table row
            if($bCheckExistOnly) return ['type'=>'ok','results'=>$row];

            // return json column indexed row
            $aData = json_decode($row->json_data);
            
            $iIndex = substr($row->at_index,3,1);
            $oRow = $aData[$iIndex];

            return ['type'=>'ok','results'=>$oRow];
        }
        
        return ['type'=>'error','message'=>'Data not found'];
    }


}