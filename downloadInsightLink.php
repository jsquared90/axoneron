<div id="congressContainerDIV" class='mainDIV'>

<?php

if (!$user)
{
    echo "Unauthorized Access!";
    exit();
}

echo getBGDiv();
echo getHeaderDiv($user);

if (isset($downloadPath))
{
    $folders = explode('/', $downloadPath);
    $fileName = explode('.', $folders[1])[0];
    $fields = explode("_", $fileName);
    $time = str_replace("-", ":", $fields[2]);
    $htmlName = $fields[0] . " as of " . parseDateFromDateTime($fields[1]) . " " . parseTimeFromDateTime($time) . " (EST)";
    $recordData = array();
    $recordData['type'] = DOWNLOADED_INSIGHTS;
    $recordData['data'] = $fields[0] . "," . urlencode($downloadPath);
    $recordData['openEnd'] = "";
    $result = addRecordToUser($user, $recordData, $user, $connection);
}
else
{
    exit();
}

?>
    <div class='contentDIV'>
        <div class='pageTitleDIV centered extraTopMargin'>Insights Download Link:</div>
        <div class='downloadLinkDIV'><a class='button' href = '<?php echo $downloadPath; ?>'><?php echo $htmlName; ?></a></div>
    </div>
    
</div>
    





