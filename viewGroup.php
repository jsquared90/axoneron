<div id='supportContainerDIV' class='mainDIV'>

<?php

    echo getBGDiv();
    echo getHeaderDiv($user);
    
    $type = isset($_GET['type']) ? $_GET['type'] : 0;
    $users = 0;
    switch ($type)
    {
        case 'support':
            $users = getAllSupportUsers($connection);
            $pageTitle = "Contact Support";
            break;
        case 'onsite':
            $congressID = isset($_GET['congress']) ? $_GET['congress'] : 0;
            $congress = getCongressById($congressID, $connection);
            if ($congress)
            {
                $users = getAllUsersAttendingCongress($congress, $connection);
            }
            $pageTitle = "On Site Team at " . $congress['shortName'];
            break;
        default:
            $pageTitle = "View Group";
            break;
    }
    
?>
    
    <div class='contentDIV'>
        <div class='pageTitleDIV'><?php echo $pageTitle; ?></div>
        
<?php
    
    if ($type)
    {
        if ($users)
        {
            
            echo "
        <div class='shortUsersDIV'>";
            
            foreach ($users as $u)
            {
                echo getShortUserBlock($u);
                if (next($users))
                {
                    echo "
            <div class='separator'>&nbsp;</div>";
                }
            }
            
            echo "
        </div>";
            
        }
        else
        {
            echo "<div class='emptyListDIV'>No users were found of the specified type.</div>";
        }
    }
    else
    {
        echo "<div class='emptyListDIV'>A group type was not specified.</div>";
    }

?>
        
    </div>

    
</div>

<script>
    
    window.onload = cleanAllImages();
    
    function cleanAllImages()
    {
        $('.accountImage').each(function()
        {
            var imageID = $(this).attr('id');
            var id = imageID.replace("image_", "");
            cleanImage(id);
        });
    }
    
    function cleanImage(id)
    {
        var image = document.getElementById('image_' + id);
        EXIF.getData(image, function()
        {
            var orientation = EXIF.getTag(this, "Orientation");
            
            if(orientation == 6)
            {
                $('#imageDIV_' + id).addClass("rotate90");
            }
            else if(orientation == 8)
            {
                $('#imageDIV_' + id).addClass("rotate270");
            }
            else if(orientation == 3)
            {
                $('#imageDIV_' + id).addClass("rotate180");
            }
        });
    }
    
</script>
