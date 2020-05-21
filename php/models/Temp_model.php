<?php
class Tempm_model extends CI_Model
{

    private $table = "tempmeta";

    public function __construct()
    {
        $this->load->database();
    }

    public function getAll($iUserid,$sSlug="")
    {
        $data = [
            'userid'    =>  $iUserid,
        ];

        if(!empty($sSlug))
            $data["slug"] = $sSlug;

        $query = $this->db->get_where($this->table, $data);
        $result = $query->result_object();
        var_dump($result);die;
        if (! is_array($result)) {
            return ['msg'=>'No slugs available','msg_type'=>'error'];
        }

        return $result;
    }

    public function tempTableToAssoc($key,$records)
    {
        $assocArray = [];
        foreach($records as $row)
        {
            $rowArray = json_encode($row->json);
            if(isset($assocArray[$row[$key]]))
            {
                $assocArray[$row[$key]] = $rowArray;
            }
        }

        return $assocArray;
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
        $row = $this->db->getOne($iUserid,$sSlugname);

        if($row->id>0)
        {
            $result = $this->db->update($this->table, $aWhere, $aData);
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

}