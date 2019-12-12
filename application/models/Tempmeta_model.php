<?php
class Tempmeta_model extends CI_Model
{

    private $table = "tempmeta";

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
            echo $this->db->last_query();
            if (! is_array($result)) {
                return ['msg'=>'No slugs available','msg_type'=>'error'];
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
            echo $this->db->last_query();
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
        
        if (!$result) {
            return ['msg'=>'No record available','type'=>'error'];
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

        if($row->id>0)
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

        echo $this->db->last_query();
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

}