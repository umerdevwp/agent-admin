<?php

use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\crm\crud\ZCRMModule;
use zcrmsdk\crm\crud\ZCRMRecord;
use zcrmsdk\crm\crud\ZCRMNote;
use zcrmsdk\crm\crud\ZCRMTag;
use zcrmsdk\crm\exception\ZCRMException;
use zcrmsdk\oauth\ZohoOAuth;
/**
 * ZoHo_Account class contains all logic to pull data from ZoHo
 */
class ZoHo_Account extends CI_Model
{
    var $AccountNumber;
    var $AccountData;
    var $ChildAccounts;
    var $arAttachments;
    var $Contacts;
    var $Tasks;
    var $PastDue = false;
/*
	 * {
    "access_token": "1000.5ba2ab8a0cca1ee3de59876061aa4b50.9631e7d28cc026b1bf26cfc97b797057",
    "refresh_token": "1000.171d7b086c3c49edf1177dd744159120.c81020dd26cadf54fbdfef7971e49682",
    "expires_in_sec": 3600,
    "api_domain": "https://www.zohoapis.com",
    "token_type": "Bearer",
    "expires_in": 3600000
}*/
    public function __construct()
    {
        $configuration = [
			"client_id" => getenv("ZOHO_CLIENT_ID"),
            "client_secret" => getenv("ZOHO_CLIENT_SECRET"),
			"redirect_uri" => getenv("ZOHO_REDIRECT_URI"),
			"currentUserEmail"=> "cboyce@unitedagentservices.com",
			"token_persistence_path" => "zohoauth",
            "sandbox"=>(isDev()?"true":"false"),
            "apiVersion"=>"v2",



        ];
        
        ZCRMRestClient::initialize($configuration);
        // to generate new token just provide grant token
        // scopes: ZohoCRM.modules.ALL,aaaserver.profile.READ
        // scopes2: ZohoCRM.modules.ALL,aaaserver.profile.READ,ZohoCRM.settings.all
        // https://accounts.zoho.com/oauth/v2/auth?scope=ZohoCRM.modules.ALL,aaaserver.profile.READ,ZohoCRM.settings.all&client_id=1000.CF9P2PV0P6H66EY284HQZ8G1HVMU3H&response_type=code&access_type=online&redirect_uri=http://api.agentadmin.loc/
        $sGrantToken = getenv("ZOHO_GRANT_TOKEN");
        
        if(!empty($sGrantToken))
        {
            echo "Token exist::-- ";
            $oAuthClient = ZohoOAuth::getClientInstance();
        
            try {
                echo "Generating token::-- ";
                $oAuthTokens = $oAuthClient->generateAccessToken($sGrantToken);
            } catch(Exception $e)
            {
                //var_dump($e);
                echo "CRM denied site access: " . $e->getMessage();
                die;
            }
        }
        
    }


    public function getAllModules()
    {
        $rest = ZCRMRestClient::getInstance(); // to get the rest client
        $modules = $rest->getAllModules()->getData(); // to get the the modules in form of ZCRMModule instances array
        foreach ($modules as $module) {
            echo $module->getModuleName(); // to get the name of the module
            echo $module->getSingularLabel(); // to get the singular label of the module
            echo $module->getPluralLabel(); // to get the plural label of the module
            echo $module->getBusinessCardFieldLimit(); // to get the business card field limit of the module
            echo $module->getAPIName(); // to get the api name of the module
            echo $module->isCreatable(); // to check wther the module is creatable
            echo $module->isConvertable(); // to check wther the module is Convertable
            echo $module->isEditable(); // to check wther the module is editable
            echo $module->isDeletable(); // to check wther the module is deletable
            echo $module->getWebLink(); // to get the weblink
            $user = $module->getModifiedBy(); // to get the user who modified the module in form of ZCRMUser instance
            if ($user != null) {
                $user->getId(); // to get the user id
                $user->getName(); // to get the user name
            }
            echo $module->getModifiedTime(); // to get the modified time of the module in iso 8601 format
            echo $module->isViewable(); // to check whether the module is viewable
            echo $module->isApiSupported(); // to check whether the module is api supported
            echo $module->isCustomModule(); // to check whether it is a custom module
            echo $module->isScoringSupported(); // to check whether the scoring is supported
            echo $module->getId(); // to get the module id
            $BusinessCardFields = $module->getBusinessCardFields(); // to get the business card fields of the module
            foreach ($BusinessCardFields as $BusinessCardField) {
                echo $BusinessCardField;
            }
            $profiles = $module->getAllProfiles(); // to get the profiles of the module in form of ZCRMProfile array instances
            foreach ($profiles as $profile) {
                echo $profile->getId(); // to get the profile id
                echo $profile->getName(); // to get the profile name
            }
            echo $module->isGlobalSearchSupported(); // to check whether the module is global search supported
            echo $module->getSequenceNumber(); // to get the sequence number of the module
			echo "<br /><br />---------------------<br /><br />";
        }
    }

    public function getModule()
    {
        $rest = ZCRMRestClient::getInstance(); // to get the rest client
        $module = $rest->getModule("{module_api_name}")->getData(); // to get the module in form of ZCRMModule instance
        echo $module->getModuleName(); // to get the name of the module
        echo $module->getSingularLabel(); // to get the singular label of the module
        echo $module->getPluralLabel(); // to get the plural label of the module
        echo $module->getBusinessCardFieldLimit(); // to get the business card field limit of the module
        echo $module->getAPIName(); // to get the api name of the module
        echo $module->isCreatable(); // to check wther the module is creatable
        echo $module->isConvertable(); // to check wther the module is Convertable
        echo $module->isEditable(); // to check wther the module is editable
        echo $module->isDeletable(); // to check wther the module is deletable
        echo $module->getWebLink(); // to get the weblink
        $user = $module->getModifiedBy(); // to get the user who modified the module in form of ZCRMUser instance
        if ($user != null) {
            $user->getId(); // to get the user id
            $user->getName(); // to get the user name
        }
        echo $module->getModifiedTime(); // to get the modified time of the module in iso 8601 format
        echo $module->isViewable(); // to check whether the module is viewable
        echo $module->isApiSupported(); // to check whether the module is api supported
        echo $module->isCustomModule(); // to check whether it is a custom module
        echo $module->isScoringSupported(); // to check whether the scoring is supported
        echo $module->getId(); // to get the module id
        $BusinessCardFields = $module->getBusinessCardFields(); // to get the business card fields of the module
        foreach ($BusinessCardFields as $BusinessCardField) {
            echo $BusinessCardField;
        }
        $profiles = $module->getAllProfiles(); // to get the profiles of the module in form of ZCRMProfile array instances
        foreach ($profiles as $profile) {
            echo $profile->getId(); // to get the profile id
            echo $profile->getName(); // to get the profile name
        }
        echo $module->getDisplayFieldName(); // to get the display field name
        echo $module->getDisplayFieldId(); // to get the display field id
        $relatedlists = $module->getRelatedLists(); // to get the related list of the module in form of ZCRMModuleRelatedList
        if ($relatedlists != null) {
            foreach ($relatedlists as $relatedlist) {
                echo $relatedlist->getApiName(); // to get the api name of the related list
                echo $relatedlist->getModule(); // to get the module api name of the related list
                echo $relatedlist->getDisplayLabel(); // to get the display labelof the related list
                echo $relatedlist->isVisible(); // to check whether the related list is visible
                echo $relatedlist->getName(); // to get the related list's name
                echo $relatedlist->getId(); // to get the related list's id
                echo $relatedlist->getHref(); // to get the related list's href
                echo $relatedlist->getType(); // to get the related lists's type
            }
        }
        $RelatedListProperties = $module->getRelatedListProperties(); // to get the related list properties in form of ZCRMRelatedListProperties instance array

        if ($RelatedListProperties != null) {
            echo $RelatedListProperties->getSortBy(); // to get the sort by field of the related list
            echo $RelatedListProperties->getSortOrder(); // to get the sort order of the related list
            $fields = $RelatedListProperties->getFields(); // to get the fields of the related list
            foreach ($fields as $field) {
                echo $field;
            }
        }
        $properties = $module->getProperties(); // to get the properties of the module
        if ($properties != null) {
            foreach ($properties as $property) {
                echo $property;
            }
        }
        echo $module->getPerPage(); // to get the records per page for the module
        $fields = $module->getSearchLayoutFields(); // to get the search layout fields
        if ($fields != null) {
            foreach ($fields as $field) {
                echo $field;
            }
        }
        echo $module->getDefaultTerritoryName(); // to get the default territory name
        echo $module->getDefaultTerritoryId(); // to get the default territory id
        $customview = $module->getDefaultCustomView(); // to get the default custom view of the module in form of ZCRMCustomView instance

        if ($customview != null) {
            echo $customview->getDisplayValue(); // to get the display value of the custom view
            echo $customview->isDefault(); // to check whether the custom view is default
            echo $customview->getId(); // to get the id of the custom view
            echo $customview->getName(); // to get the name of the custom view
            echo $customview->getSystemName(); // to get the system name
            echo $customview->getSortBy(); // to get the sort by field of the custom view

            $fields = $customview->getFields(); // to get the field names of the custom view
            foreach ($fields as $field) {
                echo $field;
            }
            echo $customview->isFavorite(); // to check whether the custom view is favourite
            echo $customview->getSortOrder(); // to get the sort order
            echo $customview->getCriteriaPattern(); // to get the criteria patter
            $criterias = $customview->getCriteria(); // to get the criterias in form of ZCRMCustomViewCriteria array
            foreach ($criterias as $criteria) {
                echo $criteria->getField(); // to get the field of the criteria
                echo $criteria->getValue(); // to get the value of the criteria
                echo $criteria->getComparator(); // to get the comparator of the criteria
            }
            echo $customview->getModuleAPIName(); // to get the api name of the module to whoich the custom view belongs to
            echo $customview->isOffLine(); // to check whther the custom view is offline
            $categories = $customview->getCategoriesList(); // to get the categories list as an array of ZCRMCustomViewCategory
            foreach ($categories as $category) {
                echo $category->getDisplayValue(); // to get the display value of the category
                echo $category->getActualValue(); // to get the actual value of the category
            }
        }
        echo $module->isGlobalSearchSupported(); // to check whether the module is global search supported
        echo $module->getSequenceNumber(); // to get the sequence number of the module
        echo $module->getDefaultCustomViewId(); // to get the default custom view id
    }

    public static function getRecordInstance()
    {
        $rest = ZCRMRestClient::getInstance(); // to get the rest client
        $record_Instance = $rest->getRecordInstance("{module_API_Name}", "record_id"); // to get dummy record object
        return $record_Instance;
    }

    public static function getModuleInstance()
    {
        $rest = ZCRMRestClient::getInstance(); // to get the rest client
        $module_Instance = $rest->getModuleInstance("{module_API_Name}"); // to get dummy module object
        return $module_Instance;
    }

    public static function getOrganizationInstance()
    {
        $rest = ZCRMRestClient::getInstance(); // to get the rest client
        $organization_Instance = $rest->getOrganizationInstance(); // to get dummy organization object
        return $organization_Instance;
    }

    public function getCurrentUser()
    {
        $rest = ZCRMRestClient::getInstance(); // to get the rest client
        $users = $rest->getCurrentUser()->getData(); // to get the users in form of ZCRMUser instances array
        foreach ($users as $userInstance) {
            echo $userInstance->getId(); // to get the user id
            echo $userInstance->getCountry(); // to get the country of the user
            $roleInstance = $userInstance->getRole(); // to get the role of the user in form of ZCRMRole instance
            echo $roleInstance->getId(); // to get the role id
            echo $roleInstance->getName(); // to get the role name
            $customizeInstance = $userInstance->getCustomizeInfo(); // to get the customization information of the user in for of the ZCRMUserCustomizeInfo form
            if ($customizeInstance != null) {
                echo $customizeInstance->getNotesDesc(); // to get the note description
                echo $customizeInstance->getUnpinRecentItem(); // to get the unpinned recent items
                echo $customizeInstance->isToShowRightPanel(); // to check whether the right panel is shown
                echo $customizeInstance->isBcView(); // to check whether the business card view is enabled
                echo $customizeInstance->isToShowHome(); // to check whether the home is shown
                echo $customizeInstance->isToShowDetailView(); // to check whether the detail view is shows
            }
            echo $userInstance->getCity(); // to get the city of the user
            echo $userInstance->getSignature(); // to get the signature of the user
            echo $userInstance->getNameFormat(); // to get the name format of the user
            echo $userInstance->getLanguage(); // to get the language of the user
            echo $userInstance->getLocale(); // to get the locale of the user
            echo $userInstance->isPersonalAccount(); // to check whther this is a personal account
            echo $userInstance->getDefaultTabGroup(); // to get the default tab group
            echo $userInstance->getAlias(); // to get the alias of the user
            echo $userInstance->getStreet(); // to get the street name of the user
            $themeInstance = $userInstance->getTheme(); // to get the theme of the user in form of the ZCRMUserTheme
            if ($themeInstance != null) {
                echo $themeInstance->getNormalTabFontColor(); // to get the normal tab font color
                echo $themeInstance->getNormalTabBackground(); // to get the normal tab background
                echo $themeInstance->getSelectedTabFontColor(); // to get the selected tab font color
                echo $themeInstance->getSelectedTabBackground(); // to get the selected tab background
            }
            echo $userInstance->getState(); // to get the state of the user
            echo $userInstance->getCountryLocale(); // to get the country locale of the user
            echo $userInstance->getFax(); // to get the fax number of the user
            echo $userInstance->getFirstName(); // to get the first name of the user
            echo $userInstance->getEmail(); // to get the email id of the user
            echo $userInstance->getZip(); // to get the zip code of the user
            echo $userInstance->getDecimalSeparator(); // to get the decimal separator
            echo $userInstance->getWebsite(); // to get the website of the user
            echo $userInstance->getTimeFormat(); // to get the time format of the user
            $profile = $userInstance->getProfile(); // to get the user's profile in form of ZCRMProfile
            echo $profile->getId(); // to get the profile id
            echo $profile->getName(); // to get the name of the profile
            echo $userInstance->getMobile(); // to get the mobile number of the user
            echo $userInstance->getLastName(); // to get the last name of the user
            echo $userInstance->getTimeZone(); // to get the time zone of the user
            echo $userInstance->getZuid(); // to get the zoho user id of the user
            echo $userInstance->isConfirm(); // to check whether it is a confirmed user
            echo $userInstance->getFullName(); // to get the full name of the user
            echo $userInstance->getPhone(); // to get the phone number of the user
            echo $userInstance->getDob(); // to get the date of birth of the user
            echo $userInstance->getDateFormat(); // to get the date format
            echo $userInstance->getStatus(); // to get the status of the user
        }
    }

    public static function getOrganizationDetails(){
        $rest=ZCRMRestClient::getInstance();//to get the rest client
        $orgIns=$rest->getOrganizationDetails()->getData();//to get the organization in form of ZCRMOrganization instance
        echo $orgIns->getCompanyName();//to get the company name of the organization
        echo $orgIns->getOrgId();//to get the organization id of the organization
        echo $orgIns->getCountryCode();//to get the country code of the organization
        echo $orgIns->getCountry();//to get the the country of the organization
        echo $orgIns->getCurrencyLocale();//to get the country locale of the organization
        echo $orgIns->getFax();//to get the fax number of the organization
        echo $orgIns->getAlias();//to get the alias  of the organization
        echo $orgIns->getDescription();//to get the description of the organization
        echo $orgIns->getStreet();//to get the street name of the organization
        echo $orgIns->getCity();//to get the city name  of the organization
        echo $orgIns->getState();//to get the state  of the organization
        echo $orgIns->getZgid();//to get the zoho group id of the organization
        echo $orgIns->getWebSite();//to get the website  of the organization
        echo $orgIns->getPrimaryEmail();//to get the primary email of the organization
        echo $orgIns->getPrimaryZuid();//to get the primary zoho user id of the organization
        echo $orgIns->getIsoCode();//to get the iso code of the organization
        echo $orgIns->getPhone();//to get the phone number of the organization
        echo $orgIns->getMobile();//to get the mobile number of the organization
        echo $orgIns->getEmployeeCount();//to get the employee count of the organization
        echo $orgIns->getCurrencySymbol();//to get the currency symbol of the organization
        echo $orgIns->getTimeZone();//to get the time zone of the organization
        echo $orgIns->getMcStatus();//to get the multicurrency status of the organization
        echo $orgIns->isGappsEnabled();//to check whether the google apps is enabled
        echo $orgIns->isPaidAccount();//to check whether the account is paid account
        echo $orgIns->getPaidExpiry();//to get the paid expiration
        echo $orgIns->getPaidType();//to get the paid type
        echo $orgIns->getTrialType();//to get the trial type
        echo $orgIns->getTrialExpiry();//to get the trial expiration
        echo $orgIns->getZipCode();//to get the zip code of the organization
    }

    public static function dumpObject($record){
        echo "EntityID: " . $record->getEntityId() . "<br />";
        echo "API Name: " . $record->getModuleApiName() . "<br />";
        echo "Lookup Label: " . $record->getLookupLabel() . "<br />";
        echo "Created By: " . $record->getCreatedBy()->getId() . "<br />";
        echo "Modified By: " . $record->getModifiedBy()->getId() . "<br />";
        echo "Owner: " . $record->getOwner()->getId() . "<br />";
        echo "Create Time: " . $record->getCreatedTime() . "<br />";
        echo "Modified Time: " . $record->getModifiedTime() . "<br />";
        $map=$record->getData();
        foreach ($map as $key=>$value)
        {
            if($value instanceof ZCRMRecord)
            {
                echo "\nZCRMRecord: ".$value->getEntityId().":".$value->getModuleApiName().":".$value->getLookupLabel() . "<br />";
            }
            else
            {
                echo $key.":".$value . "<br />";
            }
        }
    }

    public function dumpAll(){
        echo "<br /><br /><br /><br />Main Record: -----------------------------------------------------------<br />";
        if($this->AccountData){
            ZoHo_Account::dumpObject($this->AccountData);
        }
        else{
            echo "No results match your query";
        }

        echo "<br /><br /><br /><br />Child Records: -----------------------------------------------------------<br />";
        for($i = 0; $i < count($this->ChildAccounts); $i++){
            echo "Child Record: -----------------------<br /><br />";
            $record = $this->ChildAccounts[$i];
            ZoHo_Account::dumpObject($record);
        }

        echo "<br /><br /><br /><br />Contacts: -----------------------------------------------------------<br />";
        for($i = 0; $i < count($this->Contacts); $i++){
            echo "Contact: ---- <br />";
            ZoHo_Account::dumpObject($this->Contacts[$i]);
        }
    }

    public static function getChildObjects($account, $recordsArray){
        $children = array();
        for($i = 0; $i < count($recordsArray); $i++){
            $rec = $recordsArray[$i];
            $map=$rec->getData();
            foreach ($map as $key=>$value)
            {
                if($value instanceof ZCRMRecord)
                {
                    if($value->getModuleApiName() == "Parent_Account"){
                        //echo "\n".$value->getEntityId().":".$value->getModuleApiName().":".$value->getLookupLabel() . "<br />";
                    }
                    if($account == $value->getEntityId()){
                        array_push($children, $rec);
                    }
                }
            }
        }
        return $children;
    }

    public function LoadAccount($account_number){
        $this->AccountNumber = $account_number;
        $zcrmModuleIns = ZCRMModule::getInstance("Accounts");
        $bulkAPIResponse = $zcrmModuleIns->searchRecordsByCriteria("id:equals:$account_number", 1, 1);
        $recordsArray = $bulkAPIResponse->getData();
        if(count($recordsArray) > 0){
            $record = $recordsArray[0];
            $this->AccountData = $record;
        }

        $bulkAPIResponse = $zcrmModuleIns->getRecords();
        $recordsArray = $bulkAPIResponse->getData();
        $this->ChildAccounts = ZoHo_Account::getChildObjects($account_number, $recordsArray);

        $this->Contacts = null;
        try{
            $subAccounts = ZCRMRecord::getInstance("Accounts", $account_number);
            $bulkAPIResponse = $subAccounts->getRelatedListRecords("Contacts");
            if($bulkAPIResponse){
                $this->Contacts = $bulkAPIResponse->getData();
            }
        }
        catch(Exception $e){ }

        $this->Tasks = array();
        $entityId = strval($this->AccountData->getEntityId());
        $zcrmModuleTsk = ZCRMModule::getInstance("Tasks");
        $bulkAPIResponse = $zcrmModuleTsk->searchRecordsByCriteria("Status:equals:Not Started", 1, 200);
        $taskArray = $bulkAPIResponse->getData();
        for($i = 0; $i < count($taskArray); $i++){
            $record = $taskArray[$i];

            $map=$record->getData();
            foreach ($map as $key=>$value)
            {
                if($value instanceof ZCRMRecord)
                {
                    if($value->getEntityId() == $entityId){
                        array_push($this->Tasks, $record);
                    }
                }
            }
        }

        //$entityId = strval($this->AccountData->getEntityId());
        //$zcrmModuleTsk = ZCRMModule::getInstance("Attachments");

        //$bulkAPIResponse = $zcrmModuleTsk->searchRecordsByCriteria("Status:equals:Not Started", 1, 200);
        $arAttachments = $this->AccountData->getAttachments()->getData();//$zcrmModuleTsk->getData();
        $this->arAttachments = array();

        for($i = 0; $i < count($arAttachments); $i++){
            $record = $arAttachments[$i];

            $map = array(
                "id"            =>  $record->getId(),
                "modified_by"   =>  $record->getModifiedBy()->getId(),
                "owner"         =>  $record->getOwner()->getId(),
                "parent_id"     =>  $record->getParentId(),
                "create_time"   =>  $record->getCreatedTime(),
                "modified_time" =>  $record->getModifiedTime(),
                "created_by"    =>  $record->getCreatedBy()->getId(),
                "file_name"     =>  $record->getFileName(),
                "size"          =>  $record->getSize(),
                "created_by_name"=> $record->getCreatedBy()->getName(),
                "modified_by_name"=>$record->getModifiedBy()->getName(),
                "owner_name"    =>  $record->getOwner()->getName(),
            );
            $this->arAttachments[] =  (object)$map;
        }

        $now = new DateTime();
        for($i = 0; $i < count($this->Tasks); $i++){
            $date = date_create($this->Tasks[$i]->getFieldValue('Due_Date'));
            if($date < $now){
                $this->PastDue = true;
            }
        }
    }


    public function loadAttachments($zohoId)
    {
        $this->load->helpers("custom");

        $zcrmModuleIns = ZCRMModule::getInstance("Accounts");
        $bulkAPIResponse = $zcrmModuleIns->searchRecordsByCriteria("id:equals:{$this->input->get("id")}", 1, 1);
        $recordsArray = $bulkAPIResponse->getData();

        echo $recordsArray[0]->getFieldValue('Account_Name');
        echo "<pre>";print_r($recordsArray[0]->getAttachments()->getData());

        return $recordsArray[0]->getAttachments()->getData();
    }

    public function LoadAccountOnly($account_number){
        $id = $account_number;
        $account = null;
        if($id>0){
            $zcrmModuleIns = ZCRMModule::getInstance("Accounts");
            $bulkAPIResponse = $zcrmModuleIns->searchRecordsByCriteria("id:equals:$account_number", 1, 1);
            $recordsArray = $bulkAPIResponse->getData();
            if(count($recordsArray) > 0){
                $account = $recordsArray[0];
            }
        }
        return $account;
    }

    public function downloadAttachments()
    {
        // TODO: check user is admin type
        // check user is loggedin
        if(!empty($this->session->user["oktaId"])){

            $this->load->model("Attachments_model");
            $this->load->helper("custom");

            $account = $this->LoadAccountOnly($this->input->get("id"));
            $arAttachments = $account->getAttachments()->getData();//$zcrmModuleTsk->getData();

            $data = array();
            //getClassMethods($arAttachments[0]);

            for($i = 0; $i < count($arAttachments); $i++){
                $record = $arAttachments[$i];

                $map = array(
                    "id"            =>  $record->getId(),
                    "modified_by"   =>  $record->getModifiedBy()->getId(),
                    "owner"         =>  $record->getOwner()->getId(),
                    "parent_id"     =>  $record->getParentId(),
                    "create_time"   =>  $record->getCreatedTime(),
                    "modified_time" =>  $record->getModifiedTime(),
                    "created_by"    =>  $record->getCreatedBy()->getId(),
                    "file_name"     =>  $record->getFileName(),
                    "size"          =>  $record->getSize(),
                    "created_by_name"=> $record->getCreatedBy()->getName(),
                    "modified_by_name"=>$record->getModifiedBy()->getName(),
                    "owner_name"    =>  $record->getOwner()->getName(),
                    "document_count"=>  0,// it is not available in API response
                    "link_url"      =>  $record->getAttachmentType(),
                );
                $data[] =  (object)$map;
            }

            if(count($data)>0)
            {
                for($i=0;$i<count($data);$i++)
                {
                    $this->Attachments_model->replace($data[$i]->id,$data[$i]);
                }
            }
        }
    }

    /**
     * Get instance of zcrm for api calls
     */
    public function getInstance($module_name="",$id=-1)
    {
        if(empty($module_name))
        {
            return ZCRMRestClient::getInstance();
        } else {
            if($id==-1)
            {
                return ZCRMRestClient::getInstance()->getModuleInstance($module_name); // to get dummy record object
            } else {
                return ZCRMRestClient::getInstance()->getRecordInstance($module_name, $id); // to get dummy record object
            }
        }
    }

    /**
     *
     * @param Array $aData Associative array of keys: First_Name,Last_Name,Email,Phone,Contact_Type,Mailing_Street,Mailing_City,Mailing_State,Mailing_Zip
     */
    public function newZohoContact($sForAccountName,$aData)
    {
        $arError = ['type'=>'ok','results'=>"Added successfully"];

        extract($aData);

        $oApi = $this->getInstance("Contacts",null);

        $oApi->setFieldValue("Account_Name", $sForAccountName);

        $oApi->setFieldValue("First_Name",$First_Name);
        $oApi->setFieldValue("Last_Name",$Last_Name);

        $oApi->setFieldValue("Email",$Email);
        $oApi->setFieldValue("Phone",$Phone);
        $oApi->setFieldValue("Contact_Type",$Contact_Type);

        $oApi->setFieldValue("Mailing_Street",$Mailing_Street);
        $oApi->setFieldValue("Mailing_City",$Mailing_City);
        $oApi->setFieldValue("Mailing_State",$Mailing_State);
        $oApi->setFieldValue("Mailing_Zip",$Mailing_Zip);

        try {
            $response = $oApi->create();
            $object = $response->getDetails();
            $arError = ['type'=>'ok','results'=> $object['id'] ];
        } catch(Exception $e){
            $arError = ['type'=>'error','results'=>"Server failed to add contact.",'message'=>$e->getMessage()];
        }

        return $arError;
    }

    /**
     * Edit zoho contact, using the zoho api.
     *
     * @param Array $aData Associative array of keys: First_Name,Last_Name,Email,Phone,Contact_Type,Mailing_Street,Mailing_City,Mailing_State,Mailing_Zip
     */
    public function editZohoContact($iContactId,$aData)
    {
        $arError = ['type'=>'ok','results'=>"Edit successfully"];

        extract($aData);

        $oApi = $this->getInstance("Contacts",$iContactId);

        $oApi->setFieldValue("First_Name",$First_Name);
        $oApi->setFieldValue("Last_Name",$Last_Name);

        $oApi->setFieldValue("Email",$Email);
        $oApi->setFieldValue("Phone",$Phone);
        $oApi->setFieldValue("Contact_Type",$Contact_Type);

        $oApi->setFieldValue("Mailing_Street",$Mailing_Street);
        $oApi->setFieldValue("Mailing_City",$Mailing_City);
        $oApi->setFieldValue("Mailing_State",$Mailing_State);
        $oApi->setFieldValue("Mailing_Zip",$Mailing_Zip);

        try {
            $response = $oApi->update();
            $object = $response->getDetails();
            $arError = ['type'=>'ok','id'=> $object['id'] ];
        } catch(Exception $e){
            $arError = ['type'=>'error','results'=>"Server failed to add contact.",'message'=>$e->getMessage()];
        }

        return $arError;
    }

    /**
     * Add note to zoho records entity/contact
     *
     * @param $sNoteFor String can be Accounts/Contacts
     * @param $iRelatedTo Integer record id, the note will be attached to
     */
    public function newZohoNote($sNoteFor,$iRlateToId,$sNoteTitle,$sNoteContent)
    {

            $record = $this->getInstance($sNoteFor,$iRlateToId);

            $noteIns = ZCRMNote::getInstance($record, NULL); // to get the note instance

            $noteIns->setTitle($sNoteTitle); // to set the note title
            $noteIns->setContent($sNoteContent); // to set the note content
            $aError = [];
            try {
                $response=$record->addNote($noteIns);
                $result = $response->getDetails();

                $aError = ['type'=>'ok','result'=>$result['id']];

            } catch(Exception $e){
                $aError = ['type'=>'error','message'=>$e->getMessage()];
            }

           return $aError;
    }

    public function zohoCreateNewTags($iEntityId,$aTags)
    {
        $aTagsExist = $this->getTags($iEntityId);

        foreach($aTags as $k=>$v)
        {
            if(!in_array($v,$aTagsExist))
            {
                $oTag = ZCRMTag::getInstance();
                $oTag->setName($v);
                $aTagsNonExist[] = $oTag;
            }
        }

        try {
            if(count($aTagsNonExist)>0)
            {
                $oApiTags = $this->ZoHo_Account->getInstance("Tags");
                $oResponseTags = $oApiTags->createTags($aTagsNonExist);
            }
        } catch(ZCRMException $e){
            throw $e;
        }

        return ['type'=>'ok','results'=>true];
    }

    public function getTags($iEntityId)
    {
        $oApiTags = $this->getInstance("tags",$iEntityId);

        return $oApiTags->getTags();
    }


     /**
     * Add tasks to zoho records entity/account
     *
     * @param Integer $iRelatedTo entity/record id, the task will relate to
     * @param String $sTaskSubject task subject
     * @param String $sDueDate is the date it ends
     */
    public function newZohoTask($iRelateToId,$sTaskSubject,$sDueDate)
    {

            $oTaskRecord = $this->getInstance("Tasks",null); // to get the task instance

            $oTaskRecord->setFieldValue("\$se_module", "Accounts");// tasks can relate to contacts, therefore need this must
            $oTaskRecord->setFieldValue("Subject", $sTaskSubject); // task subject
            $oTaskRecord->setFieldValue("Due_Date", $sDueDate);// due date field
            $oTaskRecord->setFieldValue("What_Id", $iRelateToId);//what id field

            $aResult = [];
            try {
                $oResponseIns=$oTaskRecord->create();
                $oData = $oResponseIns->getData();
//                $createdrecord = $responseIns->getData();

                $aResult = ['type'=>'ok','id'=>$oData->getEntityId()];

            } catch(Exception $e){
                logToAdmin("ZOHO API Fail",$e->getMessage(),"zoho");
                $aResult = ['type'=>'error','message'=>$e->getMessage()];
            }

           return $aResult;
    }
}
