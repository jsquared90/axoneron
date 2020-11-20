<div id='supportContainerDIV' class='mainDIV'>

<?php

if ($user['level'] < 2)
{
    echo "Unauthorized Access!";
    exit();
}

echo getBGDiv();
echo getHeaderDiv($user);

$users = getAllUsersExceptMe($user, $connection);

?>
    
    <div class='contentDIV'>
        <div class='pageTitleDIV'>Select a User To Modify</div>
        <div class='shortUsersDIV'>
            
<?php

    foreach ($users as $u)
    {
        if ($user['level'] >= $u['level'])
        {
            echo "
            <a href='" . HOME . "?action=" . POST_MODIFY_USER . "&user=" . $u['id'] . "'>
                <div id='shortUser_" . $u['id'] . "' class='conversationDIV2'>";
        
            if ($u['imageURL'])
            {
                echo "
                    <img id='image_" . $u['id'] . "' class='accountImage' onload='cleanImage(" . $u['id'] . ");' src='" . USER_IMAGES_PATH . $u['imageURL'] . "' style='display:none;'/>";
            }
        
            echo "
                    <div id='imageDIV_" . $u['id'] . "' class='shortUserImageDIV'";
        
            if ($u['imageURL'])
            {
                echo " style='background-image: url(\"" . USER_IMAGES_PATH . $u['imageURL'] . "\");'";
            }
        
            echo ">&nbsp;</div>
                    <div class='shortConvoDataDIV1'>
                        <div class='shortUserNameDIV'>" . $u['first'] . " " . $u['last'] . "</div>
                        <div class='shortUserTitleDIV'>" . $u['title'] . "</div>
                    </div>
                </div>
            </a>";
        }
    }

?>
            
        </div>
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