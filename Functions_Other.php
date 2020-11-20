<?php
function getGenericHeaderDIV()
{
    $html = "<div id='headerBarDIV' class='headerDIV' class='adminColorDIV'>";
    $html .= "<div id='headerBarInnerDIV'>";
    $html .= "<div id='exitDIV'><a href='" . HOME . "'>&times;</a></div>";
    $html .= "<div id='headerBarTitleDIV'>Axoneron</div>";
    $html .= "</div></div>";
    return $html;
}

function getHeaderDiv($user)
{
    $html = "<div id='headerBarDIV' class='headerDIV ";
    if ($user['level'] > 1)
    {
        $html .= "adminColorDIV";
    }
    else
    {
        $html .= "basicUserDIV";
    }
    $html .= "'>";
    $html .= "<div id='headerBarInnerDIV'>";
    $html .= "<div id='exitDIV'><a href='" . HOME . "'>&times;</a></div>";
    $html .= "<div id='headerBarTitleDIV'><img id='homeImage' src='assets/images/Axoneron-Logo-1.png'/></div>";
    $html .= "<div id='loggedInAsDIV'>Logged in as: <span class='bold'>" . $user['first'] . " " . $user['last'] . "</span></div>";
    $html .= "</div></div>";
    return $html;
}

function getHomeHeaderDiv($user)
{
    $html = "<div id='homeHeaderDIV' class='headerDIV'><img id='homeImage' src='assets/images/Axoneron-Logo-1.png'/></div>";
    return $html;
}

function getBGDiv()
{
    $html = "<div class='BG'>&nbsp;</div>";
    return $html;
}

function getUnderConstruction($user, $preMessage, $postMessage)
{
    $html = "
<div id='constructionContainerDIV' class='mainDIV'>";
    $html .= getBGDiv();
    $html .= getHeaderDiv($user);
    $html .= "
    <div class='contentDIV'>";
    
    if (isset($preMessage))
    {
        $html .= $preMessage;
    }
    
    $html .= "
        <div class='emptyListDIV'>This feature is currently under construction</div>";
    
    if (isset($postMessage))
    {
        $html .= $postMessage;
    }
    
    $html .= "
    </div>
</div>";
    
    return $html;
}

function getSortFilterBlock($sortData)
{
    $titleLabel = $sortData['includeSort'] && $sortData['includeFilter'] ? "Sort/Filter" : ($sortData['includeSort'] ? "Sort" : "Filter");
    
    $html = "
<div class='sortFiltersDIV'>
    <div class='caret sortCaret fa'>&#xf105;</div>
    <div class='subTitleDIV sortTitleDIV'>" . $titleLabel . "</div>
    <form id='sortForm' style='display:none;'>";
    
    if ($sortData['includeSort'])
    {
        $html .= "
        <div>
            <div class='formLabel'>Sort:</div>
            <div class='hRow'>
                <div class='selectDIV sortADIV'>
                    <select id='sortA' name='sort1A'>";
        
        foreach ($sortData['sortA']['options'] as $option)
        {
            $html .= "
                        <option value='" . $option['value'] . "'>" . $option['label'] . "</option>";
        }
        
        $html .= "
                    </select>
                </div>
                <div class='selectDIV sortBDIV'>
                    <select id='sortB' name='sortB'>";
        
        foreach ($sortData['sortB']['options'] as $option)
        {
            $html .= "
                        <option value='" . $option['value'] . "'>" . $option['label'] . "</option>";
        }
        $html .= "
                    </select>
                </div>
            </div>
        </div>";
    }
    
    if ($sortData['includeFilter'])
    {
        $i = 1;
        foreach($sortData['filters'] as $filter)
        {
            $html .= "
        <div>
            <div class='formLabel'>Filter:</div>
            <div class='hRow'>
                <div id='filter" . $i . "ADIV' class='selectDIV filterADIV'>
                    <select id='filter" . $i . "A' name='filter" . $i . "A'>";
            foreach ($filter['types'] as $type)
            {
                $html .= "
                            <option value='" . $type['value'] . "'>" . $type['label'] . "</option>";
            }
            $html .= "
                    </select>
                </div>";
            $className = $filter['type'] == 'select' ? "selectDIV" : "";
            if ($filter['options'])
            {
                $html .= "
                    <div id='filter" . $i . "BDIV' class='" . $className . " filterBDIV'>
                        <select id='filter" . $i . "B' name='filter" . $i . "B'>";
                foreach ($filter['options'] as $option)
                {
                    $html .= "
                            <option value='" . $option['value'] . "'>" . $option['label'] . "</option>";
                }
                $html .= "
                        </select>
                    </div>
                </div>
            </div>";
            }
            else
            {
                $html .= "
                    <div id='filter" . $i . "BDIV' class='filterBDIV'>&nbsp;</div>
                </div>
            </div>";
            }
            
            $i++;
        }
    }
    if($sortData['includeApplyButton'])
    {
        $html .= "
        <div id='applyFilters'><input type='button' value='Apply'/></div>";
    }
    
    $html .= "
    </form>
</div>
<script>

$('.sortCaret').click(function()
{
    if($('#sortForm').is(':visible'))
    {
        $('#sortForm').hide();
        $('.sortCaret').html('&#xf105;');
    }
    else
    {
        $('#sortForm').show();
        $('.sortCaret').html('&#xf107;');
    }
});

</script>";
    
    return $html;
}

function get12HoursForSelect($selected)
{
    $html = "";
    for ($i = 1 ; $i < 13 ; $i+=0.5)
    {
        //$value = $i < 10 ? "0" . floor($i) : floor($i);
        //$value .= ":";
        $value = floor($i) . ":";
        $mins = $i - floor($i) != 0 ? "30" : "00";
        $value .= $mins;
        $label = floor($i) . ":" . $mins;
        $html .= "<option value='" . $value . "'";
        if ($selected == $label)
        {
            $html.= " selected";
        }
        $html .= ">" . $label . "</option>";
    }
    
    return $html;
}

function getMeridiansForSelect($selected)
{
    $html = "
            <option value='am'";
    if ($selected == "am")
    {
        $html .= " selected";
    }
    $html .= ">AM</option>";
    $html .= "
            <option value='pm'";
    if ($selected == "pm")
    {
        $html .= " selected";
    }
    $html .= ">PM</option>";
    return $html;
}

function convertToSqlDateTime($date, $time)
{
    $timeStamp = strtotime($date . " " . $time);
    return date(getSqlDateFormat(), $timeStamp);
}

function parseDateFromDateTime($date)
{
    $timeStamp = strtotime($date);
    return date("n/d/y", $timeStamp);

}

function parseTimeFromDateTime($date)
{
    $timeStamp = strtotime($date);
    return urldecode(date("g:ia", $timeStamp));
}

function parseTimeMinusMeridianFromDateTime($date)
{
    $timeStamp = strtotime($date);
    return urldecode(date("g:i", $timeStamp));
}

function parseMeridianFromDateTime($date)
{
    $timeStamp = strtotime($date);
    return urldecode(date("a", $timeStamp));
}

function format1ForSingleDateTimeDisplay($date, $startTime, $endTime)
{
    $s = date("n/d/y", strtotime($date));
    $s .= " (";
    $s .= format1ForSingleTimeDisplay($startTime, $endTime);
    $s .= ")";
    return $s;
}

function format2ForSingleDateTimeDisplay($date, $startTime, $endTime)
{
    $s = date("n/d", strtotime($date));
    $s .= " (";
    $s .= format1ForSingleTimeDisplay($startTime, $endTime);
    $s .= ")";
    return $s;
}

function format1ForSingleTimeDisplay($startTime, $endTime)
{
    $s = rtrim(date("g:ia", strtotime($startTime)), "m");
    $s .= " - ";
    $s .= rtrim(date("g:ia", strtotime($endTime)), "m");
    return $s;
}

function getDateBlockForCalendar($date, $state)
{
    $html = "
    <div class='dateBlock" . $state . "' date='" . $date . "'>
        <div class='dateBlockInnerDIV'>
            <div class='dateBlockMonth'>" . date("M", strtotime($date)) . "</div>
            <div class='dateBlockDate'>" . date("j", strtotime($date)) . "</div>
        </div>
    </div>";

    return $html;
}

function getSqlDateFormat()
{
    return 'Y-m-d H:i:s';
}

function getSaveForFileDateFormat()
{
    return 'Y-m-d_H-i-s';
}

function getDateTimeDisplayFromTimestamp($timestamp)
{
    $html = date("n/d/y", $timestamp);
    $html .= ' ';
    $html .= date("g:ia", $timestamp);
    $html .= " (ET)";
    return $html;
}

/*
 * removes quotes and double quotes for proper display in an input field
 */
function cleanForValueField($input)
{
    $output = htmlspecialchars($input, ENT_QUOTES);
    return $output;
}

function debug($var)
{
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}

function invokeSuccess()
{
   echo "

<script type='text/javascript'>
    window.location.href = '" . HOME . "?success=true';
</script>

       ";
}

function invokeSuccessWithRedirect($action)
{
   echo "

<script type='text/javascript'>
    window.location.href = '" . HOME . "?success=true&" . $action . "';
</script>

       ";
}

function reDirect($action)
{
    echo "

<script type='text/javascript'>
    window.location.href = '" . HOME . "?" . $action . "';
</script>

       ";
}

function getDownloadNotification()
{
    /*
    $return = "
        
Have you downloaded the official Axoneron Congress App yet?

Click on link below to download:
    
iOS : " . IOS_DOWNLOAD_URL . "
Android : " . ANDROID_DOWNLOAD_URL . "
OSX : " . OSX_DOWNLOAD_URL . "
Windows : " . WINDOWS_DOWNLOAD_URL . "
        ";
     * 
     */
    
    $return = "
        
Have you downloaded the official Axoneron Congress App yet?

Click on link below to download:
    
iOS : " . IOS_DOWNLOAD_URL . "
Android : " . ANDROID_DOWNLOAD_URL . "
        ";
    
    return $return;
}

function createThumbnail($sourcePath, $targetPath, $size)
{
    //debug($sourcePath . " : " . $targetPath);
    $sourcefileName = basename($sourcePath);
    $extension = strtolower(pathinfo($sourcefileName,PATHINFO_EXTENSION));
    list($currentW, $currentH) = getimagesize($sourcePath);
    $ar = $currentW / $currentH;
    $image = $extension == 'png' ? imagecreatefrompng($sourcePath) : imagecreatefromjpeg($sourcePath);
    $w = $ar > 1 ? $size : $size * $ar;
    $h = $ar > 1 ? $size / $ar : $size;
    $image2 = imagecreatetruecolor($w, $h);
    imagecopyresampled($image2, $image, 0, 0, 0, 0, $w, $h, $currentW, $currentH);
    $targetFileName = basename($targetPath);
    $targetDir = str_replace($targetFileName, '', $targetPath);
    //debug($targetDir);
    if (!file_exists($targetDir))
    {
        mkdir($targetDir, 0777, true);
    }
    if ($extension == 'png')
    {
        imagepng($image2, $targetPath);
    }
    else
    {
        imagejpeg($image2, $targetPath);
    }
}








