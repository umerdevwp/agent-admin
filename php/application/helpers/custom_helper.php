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

    if(empty($_SESSION['eid'])){
        responseJson(['type'=>'error','message'=>'Please login and try again']);
    } else {
        responseJson(['type'=>'error','message'=>'Access not allowed']);
    }
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
        $_SERVER['REMOTE_ADDR']=='10.10.10.159' ||
        $_SERVER['HTTP_HOST']=='api.agentadmin.loc'

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
    if($_SESSION['accountType']=='admin'){
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
 * Convert user input date to MySQL YYYY-MM-DD format
 * 
 * @param String $strDate date in string format
 * @return String formated date / empty string
 */
function convToMySqlDate($strDate)
{
    $strNewDate = "";
    // try to correct user date format, then validate
    if($strDate!="")
    {
        $strFormationDate = str_replace("  "," ",$strDate);
        $posColons = strpos($strFormationDate,":");
        if($posColons>0)
        {
            // find space before colons
            $strTillColon = substr($strFormationDate,0,$posColons);
            $strFormationDate = substr($strTillColon,0,strrpos($strTillColon," "));
        }
        $strFormationDate = str_replace(" ","-",$strFormationDate);
        $strFormationDate = date("Y-m-d",strtotime($strFormationDate));

        if($strFormationDate!="1970-01-01")
        {
            $strNewDate = $strFormationDate;
        }
    }

    return $strNewDate;
}

/**
 * Get login id of user else the login admin id instead of super user id
 * 
 * @return Integer User Id
 */
function userLoginId()
{
    if($_SESSION['user']['zohoId']==getenv("SUPER_USER"))
    {
        return $_SESSION['user']['AdminId'];
    }

    return $_SESSION['user']['zohoId'];
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

/**
 * Replace column alias name with real table column names
 * @param Array $aRequestColumn Array of alias based columns required
 * @param Array $aColumnAlias Array of real and alias names, Assoc array alias=>real name
 */
function replaceColumnKeys($aRequestColumn,$aColumnAlias)
{
    $aNewAssocData = [];
    
    foreach($aRequestColumn as $k=>$v)
    {
        $aNewAssocData[$aColumnAlias[$k]] = $v;
    }
    return $aNewAssocData;
}

/**
 * Parse put stream from php://input and return as assoc array, except file upload
 * @return Array Return associative array of all variables
 * 
 */
function parsePutRequest()
{
    // Fetch content and determine boundary
    $raw_data = file_get_contents('php://input');
    $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

    // Fetch each part
    $parts = array_slice(explode($boundary, $raw_data), 1);
    $data = array();

    foreach ($parts as $part) {
        // If this is the last part, break
        if ($part == "--\r\n") break; 

        // Separate content from headers
        $part = ltrim($part, "\r\n");
        list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

        // Parse the headers list
        $raw_headers = explode("\r\n", $raw_headers);
        $headers = array();
        foreach ($raw_headers as $header) {
            list($name, $value) = explode(':', $header);
            $headers[strtolower($name)] = ltrim($value, ' '); 
        } 

        // Parse the Content-Disposition to get the field name, etc.
        if (isset($headers['content-disposition'])) {
            $filename = null;
            preg_match(
                '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/', 
                $headers['content-disposition'], 
                $matches
            );
            list(, $type, $name) = $matches;
            isset($matches[4]) and $filename = $matches[4]; 

            // handle your fields here
            switch ($name) {
                // this is a file upload
                case 'userfile':
                    file_put_contents($filename, $body);
                    break;

                // default for all other files is to populate $data
                default: 
                    $data[$name] = substr($body, 0, strlen($body) - 2);
                    break;
            } 
        }

    }
    return $data;
}

/**
 * log the error to apache error log file, also send report the error as an email
 * 
 * @param String $sTitle the common label of error
 * @param String $sContent detail or any valueable info on error
 * @param String $sType to distinguish among error type for log or mail sepration purpose
 */
function logToAdmin(string $sTitle,string $sContent,string $sType="CODE")
{
    error_log("SESSION: " . $_SESSION['eid'] . "Type: " . $sType . ", Title: " . $sTitle. ", Content: " . $sContent);
    mailto("najm.a@allshorestaffing.com",$sTitle,"SESSION: " . $_SESSION['eid'] . "\n\n" . $sContent);
}

function generateToken($iLength=32)
{
    return bin2hex(random_bytes($iLength));
}

function generateHash($sValue)
{
    return md5($sValue);
}