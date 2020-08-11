<?php
trait CommonDbTrait{
    
    /**
     * Check user is allowed to access module
     * @param $sMethodName requested action to perform
     * @param $sRoute requested Module name
     */
    public function checkPermission($sMethodName="VIEW",$sRoute="ENTITY")
    {
        $this->load->model("Permissions_model");
        
        $aData = $this->Permissions_model->getPermissionsEntityRow($_SESSION['eid'],$sRoute);
        // check method is allowed, else response permission denied
        if(!isSessionValid($sMethodName,$aData)) exit();
        
        return true;
    }

}