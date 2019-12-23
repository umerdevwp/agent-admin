<?php
class Tempmeta_model extends CI_Model
{

    private $table = "tempmeta";

    public $slugContactNew = "new_contacts";

    public $slugTasksComplete = "tasks_complete";

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
            return ['results'=>'No record available','type'=>'error'];
        }

        return $result;
    }

    public function update($iUserid,$sSlugname,$sJsonEncoded)
    {
        $aData = [
            "json"  =>  $sJsonEncoded
        ];
        $aWhere = [
            "userid"    =>  $iUserid,
            "slug"      =>  $sSlugname,
        ];
        // check 
        $row = $this->getOne($iUserid,$sSlugname);
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

    public function appendRow($iId,$sSlug,$aData)
    {
        $aResult = $this->getOne($iId,$sSlug);

        $aTempData = [];

        if($aResult['type']=='ok')
        {
            $aTempData = json_decode($aResult['results']->json);
        }

        $aTempData[] = $aData;
        
        $this->update($iId,$sSlug,json_encode($aTempData));

    }

    public function deduceRow($iId,$sSlug,$aData)
    {
        $aResult = $this->getOne($iId,$sSlug);

        $aTempData = $aNewData = [];

        if($aResult['type']=='ok')
        {
            $aTempData = json_decode($aResult['results']->json);
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
}