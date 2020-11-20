<div id="congressContainerDIV" class='mainDIV'>

<?php

if ($user['level'] < 2)
{
    echo "Unauthorized Access!";
    exit();
}

echo getBGDiv();
echo getHeaderDiv($user);

$congresses = getAllCongresses($connection);

if ($congresses)
{
    echo "
<div class='contentDIV'>
    <div class='pageTitleDIV whiteBG'>Select A Congress To Remove</div>
    <div id='congressListDIV'>";
    foreach ($congresses as $congress)
    {
        echo "
        <div class='shortCongressBlockDIV'";
        
        if ($congress["imageURL"] != "")
        {
            echo " style='background-image: linear-gradient(to right, rgba(70, 99, 32, 0.7), rgba(60, 101, 124, 0.7)), url(\"" . CONGRESS_IMAGES_PATH . $congress['imageURL'] . "\");opacity:1;'";
        }
        
        echo "><a class='clickable' onclick='handleItemClick(" . $congress['id'] . ");'>
            <div class='shortCongressShortName'>" . $congress['shortName'] . "</div>
            <div class='shortCongressShortDates'>" . congressDatesForHtmlShortFormat($congress) . "</div>
            </a>
        </div>";
    }
    echo "
    </div>
</div>";
}
else
{
    echo "
<div class='contentDIV'>
    <div class='emptyListDIV'>There are no congresses currently in the database.</div>
</div>";
}

?>

</div>
    
<script>
    
    function handleItemClick(congressID)
    {
        if (confirm("Remove Congress?"))
        {
            window.location = "<?php echo HOME; ?>?action=<?php echo POST_REMOVE_CONGRESS; ?>&congress=" + congressID + "&confirmed=1";
        }
    }

</script>
    





