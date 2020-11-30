<?php
defined('BASEPATH') or exit('No direct script access allowed');

class QuickFix extends CI_Controller {
    
    public function index()
    {
        $iEid = $this->input->get("eid");

        $this->load->model("entity_model");
        $aEntity = $this->entity_model->getOne($iEid);
        if($aEntity['type']=='ok')
        {

            $this->load->model("Notifications_model");
            header("Content-Type","application/xhtml");
            $oEntity = $aEntity['results'];
//            echo "<html><body>";
            // print entity
            print_r($oEntity);

            $oRule = $this->oRule = $this->Notifications_model->getRules(
                $oEntity->filingState,
                $oEntity->entityStructure,
                $oEntity->formationDate,
                date("Y-12-31")
            );

            if(!empty($oRule->duedate))
            {   
             // print rule
                print_r($oRule);
                if($this->input->get("yes")==1)
                {
                    $this->load->model("ZoHo_Account");
                    $aTaskResult = $this->ZoHo_Account->newZohoTask($iEntityId,$oRule->description,$oRule->duedate);
                    print_r($aTaskResult);
                    if($aTaskResult['type']=='ok')
                    {
                        echo "Task has been updated to crm";
                    } else {
                        echo "Unable to create task for entity";
                        print_r($aTaskResult);
                    }
                } else {
                    echo "<a href='/index.php/QuickFix/?eid=$iEid&yes=1'>Are you sure ? Add this to CRM</a>";
                }
            } else {
                echo "Unable to find filing date for entity state/structure.";
            }

        } else {
            echo "Sorry no such entity found in DB";
        }
    }
}