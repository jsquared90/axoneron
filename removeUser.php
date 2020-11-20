<div id='adminContainerDIV' class='mainDIV'>
    
<?php

echo getBGDiv();
echo getHeaderDiv($user);

?>

    <div class='contentDIV'>
        <form>
            <div class='pageTitleDIV'>Remove User</div>
            <div class='shortUsersDIV'>

                <?php

                $users = getAllUsersExceptMe($user, $connection);
                foreach ($users as $u)
                {
                    echo getShortUserMessageBlockWithoutLink($u, null);

                    if (next($users))
                    {
                        echo "
                    <div class='separator'>&nbsp;</div>";
                    }
                }

                ?>

            </div>
        </form>
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

    $('.shortUserMessageSelectDIV').click(function()
    {
        var selectedID = $(this).attr('id').replace("shortUser_", "");
        if (confirm("Remove User?"))
        {
            window.location = "<?php echo HOME; ?>?action=<?php echo POST_REMOVE_USER; ?>&user=" + selectedID + "&confirmed=1";
        }
    });

</script>

