<div id='userAdminContainerDIV' class='mainDIV'>
<?php

echo getBGDiv();
echo getHeaderDiv($user);

$pageTitle = "Create Message Group";
$messageGroup = 0;
$submit = "Create Group";
$submitName = POST_ADD_MESSAGE_GROUP;
$groupTitle = "";
$groupUsers = "";

if (isset($_GET['action']) && $_GET['action'] == POST_MODIFY_MESSAGE_GROUP)
{
    $pageTitle = "Modify Message Group";
    $groupID = $_GET['group'];
    $messageGroup = getMessageGroupByID($groupID, $user, $connection);
    if ($messageGroup['author']['id'] != $user['id'])
    {
        echo "Unauthorized Access!";
        exit();
    }
    $submit = "Modify Group";
    $submitName = POST_MODIFY_MESSAGE_GROUP;
    $groupTitle = $messageGroup['title'];
    $users = $messageGroup['users'];
    foreach ($users as $u2)
    {
        $groupUsers .= $u2['id'];
        if (next($users))
        {
            $groupUsers .= ",";
        }
    }
}

?>

    <div class='contentDIV'>

        <div class='pageTitleDIV'><?php echo $pageTitle; ?></div>

        <form name='messageGroupAdd' id='messageGroupAdd' method='post' action='<?php echo HOME; ?>'>
            
            <div>
                <div class='formLabel'>Group Title:</div>
                <div><input type='text' name='groupTitle' value='<?php echo $groupTitle; ?>'/></div>
            </div>
            
            <div class='subTitleDIV'>Click To Add:</div>
            <div class='shortUsersDIV'>
        
            <?php

            $users = getAllUsersExceptMe($user, $connection);
            
            foreach ($users as $u)
            {
                $selected = 0;
                if ($messageGroup)
                {
                    foreach ($messageGroup['users'] as $u2)
                    {
                        if ($u2['id'] == $u['id'])
                        {
                            $selected = 1;
                        }
                    }
                }
                
                echo getShortUserMessageBlockWithoutLink($u, $selected);
                
                if (next($users))
                {
                    echo "
                <div class='separator'>&nbsp;</div>";
                }
            }

            ?>
            
            </div>
            <input id='groupUsersInput' type='text' name='groupUsers' value='<?php echo $groupUsers; ?>' hidden='true'/>
            <?php
            
            if ($messageGroup)
            {
                echo "
            <input type='number' name='groupID' value='" . $groupID . "' hidden='true'/>";
            }
            
            ?>
            <div class='extraTopMargin'>
                <div><input type='submit' name='<?php echo $submitName; ?>' value='<?php echo $submit; ?>'/></div>
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
        var groupUsersInputVal = $('#groupUsersInput').val();
        var users = groupUsersInputVal != "" ? groupUsersInputVal.split(",") : [];
        var newUsers = [];
        if($(this).hasClass('selectedForGroup'))
        {
            $(this).removeClass('selectedForGroup');
            for (var i = 0 ; i < users.length ; i++)
            {
                if (users[i] != selectedID)
                {
                    newUsers.push(users[i]);
                }
            }
        }
        else
        {
            $(this).addClass('selectedForGroup');
            newUsers = users;
            newUsers.push(selectedID);
        }
        $('#groupUsersInput').val(newUsers.join(","));
    });
    
    //Validate function
    $(document).ready(function()
    {
        //window.alert("we have successfully called this function");
        $("#messageGroupAdd").validate(
        {
            rules:
            {
                groupTitle:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 64,
                    letterswithbasicpunc: true
                },
                groupUsers:
                {
                    required: true,
                    maxlength: 256
                }
            },
            messages:
            {

            }
        });
    });
    
</script>