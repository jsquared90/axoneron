<div id="congressContainerDIV" class='mainDIV'>

<?php

    $congress = isset($_GET['congress']) ? getCongressById($_GET["congress"], $connection) : 0;

    echo getBGDiv();
    echo getHeaderDiv($user);
    
    echo "
<div class='contentDIV bioContentDIV'>
    <div class='pageTitleDIV'>Speaker Bios for " . $congress['shortName'] . "</div>
    <div class='copyStyle3'>(Click speaker name to view)</div>";
    
    
    if ($congress)
    {
        $bios = getBios($congress, $connection);
        if ($bios)
        {
            foreach ($bios as $bio)
            {
            echo "
    <div class='bioBlockDIV modCongressBio'>
        <div class='shortBioName'>
            <a href='" . $bio['url'] . "'>" . $bio['first'] . " " . $bio['last'] . "</a>
        </div>
    </div>
            ";
            }
        }
        else
        {
            echo "
    <div class='emptyListDIV'>There are no speaker bios currently affiliated with this congress.</div>";
        }
    }
    else
    {
        echo "
    <div class='emptyListDIV'>There has been an error retrieving that congress.</div>";
    }
    
    echo "
</div>";
    
?>
    
</div>