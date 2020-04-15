<?php
function getFileSize($iBytes)
{
    $KB = round($iBytes/1024,2);
    $MB = round($KB/1024,2);
    $GB = round($MB/1024,2);

    if($GB >= 1)
        return $GB." GB";
    elseif($MB>=1)
        return $MB." MB";
    elseif($KB>=0)
        return $KB." KB";
    else
        return $iBytes." kbs";
}

function isDateDue($strDateTime)
{
    $now = new DateTime();
    $bResult = false;
    if(!empty($strDateTime))
    {
        $date = date_create($strDateTime);
        if($date < $now){
            $bResult = true;
        }
    } else {
        $bResult = true;
    }
    
    return $bResult;
}

/**
 * Validate file size
 * 
 * $strFileFullPath file path
 */
function validateFileSize($strFileFullPath,$iSize)
{
    $iFileSize = filesize($strFileFullPath);

    if($iFileSize > $iSize)
    {
        return false;
    }

    return true;

}

/**
 * Validate file extention
 * 
 * @param string $strFileFullPath File full path
 * @param array $arAllowedExts Allowed extention numeric array
 * 
 * @return bool 
 */
function validateFileExt($strFileFullPath,$arAllowedExts)
{
    $type = false;

    if(file_exists($strFileFullPath))
    {
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type
        $type = finfo_file($finfo, $strFileFullPath);
        finfo_close($finfo);
    }
    
    if(!in_array($type,$arAllowedExts))
    {
        return false;
    } else {
        return true;
    }
}


/**
 * Validate user session for system and permission of an action allowed
 * 
 * @param String $action name of method callee 
 */
function isSessionValid($action,$aData)
{
    foreach($aData as $k=>$v)
    {
        if(strtoupper($k)=="CAN_$action" && $v=="Y")
        {
            return true;
        }
    }

    responseJson(['type'=>'error','message'=>'Access not allowed']);
    exit();
}

/**
 * Redirect user based on session made, else to home page
 */
function redirectSession()
{
    $CI =& get_instance();
    if(isset($CI->session->user["defaultRedirect"]))
    {
        redirect($CI->session->user["defaultRedirect"]);
    } else {
        redirect(getenv("SITE_URL"));
    }
}


function getClassMethods($class)
{
    $class_props = get_class_vars($class);
    foreach ($class_props as $props_name) 
    {
        echo "$props_name<br/>";
    }
    echo "-----------------------<br />";
    $class_methods = get_class_methods($class);
    foreach ($class_methods as $method_name) 
    {
        echo "$method_name<br/>";
    }
}

function isDev()
{
    if(getenv("ENVIRONMENT")=="development") return true;
    else return false;
}

/**
 * Identify the site access from developer or client based on machine ips
 * 
 * @return Bool true/false
 */
function isDeveloperIp()
{
    if(
        $_SERVER['REMOTE_ADDR']=='180.92.132.234' ||
        $_SERVER['REMOTE_ADDR']=='192.168.0.187' ||
        $_SERVER['REMOTE_ADDR']=='58.65.211.74'  ||
        $_SERVER['REMOTE_ADDR']=='10.10.10.159' 

    ) return true;
    else return false;
}

/**
 * Identify loged in session is admin or not, by tracking super user id
 * 
 * @return Bool true/false
 */
function isAdmin()
{
    if($_SESSION['user']['ZohoId']==getenv("SUPER_USER")){
        return true;
    }
    return false;
}


/**
 * Identify loged in session is parent entity or not, by tracking child flag
 * 
 * @return Bool true/false
 */
function isParent()
{
    if($_SESSION['user']["child"]>0){
        return true;
    }
    return false;
}

/**
 * Identify request is for json or not, by tracking SERVER['CONTENT_TYPE']
 * 
 * @return Bool true/false
 */
function isJsonRequest()
{
    if($_SERVER['CONTENT_TYPE'] == 'application/json'){
        header('Content-Type: application/json; charset=utf-8');
        if(isOriginAllowed("",explode(",",getenv("ORIGIN_ALLOWED"))))
        {
            header("Access-Control-Allow-Origin: ". $_SERVER["HTTP_ORIGIN"]);            
        }
        header("Access-Control-Allow-Methods: PUT, GET, POST");
        return true;
    }
    return false;
}

/**
 * Debug class or object or array, print_r optional debug class methods
 * @param $var Dynamic variable 
 * @param $bFindMethod debug available methods and props
 */
function debug($var,$bFindMethod=false)
{
    if(isDeveloperIp()){
        if($bFindMethod) getClassMethods($var);
        echo "<pre>";
        print_r($var);
    }
}

/**
 * Spit converted data to json response for client api calls
 * @Param $aData An array of response 
 */
function responseJson($aInData=[])
{
    // without input it throws this
    if(count($aInData)==0) $aOutData = ['errors'=>['status'=>'400','message'=>'Unable to proceed request']];
    else {
        $aOutData = $aInData;
    }
    header("Content-Type: application/json");
    echo json_encode($aOutData);
}

/**
 * Convert table result array to associative array using key from rows
 * @param String $key column name that will be key of associative array
 * @param Array $records Sql records as indexed array
 * 
 * @return Array Associative array of all records
 */
function tempTableToAssoc($key,$records)
{
    $assocArray = [];
    foreach($records as $row)
    {
        $rowArray = json_decode($row['json']);
        if(!isset($assocArray[$row[$key]]))
        {
            $assocArray[$row[$key]] = $rowArray;
        }
    }

    return $assocArray;
}

/**
 * Set session by Merge/Append provided data array 
 * to existing session key
 * 
 * @param String $sKey the existed key name in the session
 * @param Array $aData any assocciative or index array
 * 
 */
function addToSessionKey($sKey,$aData)
{
    $CI =& get_instance();
    $aThisData = [];
    $sThisKey = "";
    foreach($CI->session->userdata as $k=>$v)
    {
        if($sKey==$k)
        {
            $sThisKey = $k;
            $aThisData = array_merge($v,$aData);
            break;
        }
    }
    if(count($aThisData))
    {
        $_SESSION[$sThisKey] = $aThisData;
    }

}


/**
 * This function will check if the user is a valid admin or not
 * If 'yes' then it is going to enter a isAdmin variable in the current session
 * If user has the zoho_id 999999 and it is not registered in the database it will redirect the user to permission deind page
 * This function returns boolein.
 */

function validAdminCheck()
{
    $CI = get_instance();
    if (isset($CI->session->user["isAdmin"])) {
        return true;
    } else {
        return false;
    }
}

function restrictForAdmin()
{
    $CI = get_instance();
    if (isset($CI->session->user["isAdmin"]) && $CI->session->user["isAdmin"] == true) {
        return true;
    } else {
        $CI->session->set_flashdata('error', 'Permission denied');
            redirect(base_url('/portal'));
        
    }
}

function arrayKeysExist($aNeedle,$aHaystack)
{
    $aMyColumns = [];
    foreach($aHaystack as $k=>$v)
    {
        if(in_array($k,$aNeedle) && !is_numeric($k))
        {
            $aMyColumns[$k] = $v;
        }
    }

    if(count($aNeedle)!=count($aMyColumns)) return false;

    return $aMyColumns;
}

/**
 * Checks weather 
 */
function isOriginAllowed($sOrigin="", $aAllowedOrigins)
{
    // must have http_origin headers
    $sOrigin = array_key_exists('HTTP_ORIGIN', $_SERVER) ? $_SERVER['HTTP_ORIGIN'] : NULL;
    // list of origins allowed
    $aAllowedOrigins    = $_SERVER['HTTP_HOST'];
    
    $bAllow = false;
    for($i=0;$i<count($aAllowedOrigins);$i++)
    {
        // match hosts/domains
        $pattern = '/^http:\/\/([\w_-]+\.)*' . $aAllowedOrigins[$i] . '$/';
        // mached true / false
        $allow = preg_match($pattern, $sOrigin);
        // not allowed exit
        if ($sOrigin !== null && isOriginAllowed($sOrigin, $aAllowedOrigins))
        {
            // not allowed check with others
            continue;
        } else {
            $bAllow = true;
            break;
        }
    }

    return $bAllow;
}
/**
 * Fetch fields query parameter and return csv input to array
 * 
 * @param $sQueryName query string name variable
 */
function getInputFields($sQueryName="fields")
{
    $CI =& get_instance();

    $aColumns = [];
    // check column parameter set
    $csvColumns = $CI->input->get($sQueryName);
    if(!empty($csvColumns)) $aColumns = explode(",",$csvColumns);
    
    return $aColumns;
}