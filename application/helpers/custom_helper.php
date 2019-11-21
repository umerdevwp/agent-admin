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