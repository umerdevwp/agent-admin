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
function isSessionValid($action)
{
    $CI =& get_instance();
    if(isset($CI->session->user["zohoId"]))
    {
        if(in_array($action,$CI->session->user["permissions"]))
        {
            return true;
        }
    }

    return false;
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

/**
 * Debug class or object or array, print_r optional debug class methods
 * @param $var Dynamic variable 
 * @param $bFindMethod debug available methods and props
 */
function debug($var,$bFindMethod=false)
{
    if($bFindMethod) getClassMethods($var);
    echo "<pre>";
    print_r($var);
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