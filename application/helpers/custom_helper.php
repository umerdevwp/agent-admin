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
