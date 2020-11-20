<div id="messagesContainerDIV" class='mainDIV'>
    
<?php echo getBGDiv(); ?>
<?php echo getHeaderDiv($user) ?>
    
    <div class='contentDIV'>
        <div class='pageTitleDIV'>Messages</div>

        <?php
        
        $recipient = isset($_GET['recipient']) ? getUserById($_GET['recipient'], $connection) : 0;
        $group = 0;
        if (isset($_GET['group']))
        {
            $group = isPresetGroup($_GET['group']) ? $_GET['group'] : getMessageGroupByID($_GET['group'], $user, $connection);
        }
        $recipientType = $recipient ? "private" : "group";
        $dateDisplayRefresh = 60 * 30;
        
        if ($recipient)
        {
            // user has clicked on a private conversation
            
            echo "
        <div class='conversationDIV1'>";
            
            echo getShortUserBlock($recipient);
                
            echo "
            <div class='separator'>&nbsp;</div>
            <div id='scrollButton'>Scroll To End</div>
            <div class='conversationDIV3'>";
            
            $conversation = getConversation($user, $recipient, $recipientType, $connection);
            $time = 0;
            if ($conversation != 0)
            {
                foreach ($conversation as $message)
                {
                    //debug($message);
                    if ($message['timeStamp'] - $time > $dateDisplayRefresh)
                    {
                        $dateTimeDisplay = getDateTimeDisplayFromTimestamp($message['timeStamp']);
                        echo "
                    <div class='messageTimestamp'>" . $dateTimeDisplay . "</div>";
                        $time = $message['timeStamp'];
                    }
                    echo getLongMessageBlock($message);
                }
            }
            
            echo "
            </div>
            <div id='openMessageDIV' class='openMessage far'>&#xf0fe;</div>
        </div>";
        }
        else if ($group)
        {
            // user has clicked on a group conversation
            
            $additionalClass = isPresetGroup($group) ? getPresetClassName($group) : "groupImageDIV";
            $identifier = isPresetGroup($group) ? $group : $group['id'];
            $messageGroup = getMessageGroupByID($identifier, $user, $connection);
            $title = isPresetGroup($group) ? getPresetDisplayName($group) : $messageGroup['title'];
            
            echo "
        <div class='conversationDIV1'>
            <div class='conversationDIV2'>
                <div class='shortUserImageDIV " . $additionalClass . "'>&nbsp;</div>
                <div class='shortConvoDataDIV1'>
                    <div class='shortUserNameDIV'>" . $title . "</div>
                </div>
            </div>
            <div class='separator'>&nbsp;</div>
            <div id='scrollButton'>Scroll To End</div>
            <div class='conversationDIV3'>";
            
            $conversation = getConversation($user, $messageGroup, $recipientType, $connection);
            $time = 0;
            
            if ($conversation)
            {
                foreach ($conversation as $message)
                {
                    if ($message['timeStamp'] - $time > $dateDisplayRefresh)
                    {
                        $dateTimeDisplay = getDateTimeDisplayFromTimestamp($message['timeStamp']);
                        echo "
                    <div class='messageTimestamp'>" . $dateTimeDisplay . "</div>";
                        $time = $message['timeStamp'];
                    }
                    echo getLongMessageBlock($message, $time);
                }
            }
            
            echo "
            </div>
            <div id='openMessageDIV' class='openMessage far'>&#xf0fe;</div>
        </div>";
            
        }
        else
        {
            // user clicked on "Messages" from the main menu
            
            $users = getAllUsersExceptMe($user, $connection);
            
            echo "
        <div class='subTitleDIV'>Private Messages</div>
        <div class='shortUsersDIV'>";
            
            foreach ($users as $u)
            {
                echo getShortUserMessageBlock($user, $u, $connection);

                if (next($users))
                {
                    echo "
                <div class='separator'>&nbsp;</div>";
                }
            }
            
            echo "
        </div>
        <div class='extraTopMargin'>&nbsp;</div>
        <div class='subTitleDIV'>Group Messages</div>
        <div class='shortUsersDIV'>";
            
            echo getShortGroupMessageBlock($user, "all", $connection);
            
            if (isMemberOfPresetGroup("axoneron", $user))
            {
                echo "
            <div class='separator'>&nbsp;</div>";
                echo getShortGroupMessageBlock($user, "axoneron", $connection);
            }
            
            if (isMemberOfPresetGroup("admin", $user))
            {
                echo "
            <div class='separator'>&nbsp;</div>";
                echo getShortGroupMessageBlock($user, "admin", $connection);
            }
            
            if (isMemberOfPresetGroup("support", $user))
            {
                echo "
            <div class='separator'>&nbsp;</div>";
                echo getShortGroupMessageBlock($user, "support", $connection);
            }
            
            if (isMemberOfPresetGroup("medical", $user))
            {
                echo "
            <div class='separator'>&nbsp;</div>";
                echo getShortGroupMessageBlock($user, "medical", $connection);
            }
            
            if (isMemberOfPresetGroup("commercial", $user))
            {
                echo "
            <div class='separator'>&nbsp;</div>";
                echo getShortGroupMessageBlock($user, "commercial", $connection);
            }
            
            if (isMemberOfPresetGroup("engage", $user))
            {
                echo "
            <div class='separator'>&nbsp;</div>";
                echo getShortGroupMessageBlock($user, "engage", $connection);
            }
            
            if (isMemberOfPresetGroup("pixelmosaic", $user))
            {
                echo "
            <div class='separator'>&nbsp;</div>";
                echo getShortGroupMessageBlock($user, "pixelmosaic", $connection);
            }  
            
            $myGroups = getMyMessageGroups($user, $connection);
            
            if ($myGroups)
            {
                echo "
            <div class='separator'>&nbsp;</div>";
                foreach ($myGroups as $messageGroup)
                {
                    //debug($messageGroup);
                    echo getShortGroupMessageBlock($user, $messageGroup['id'], $connection);
                    if (next($myGroups))
                    {
                        echo "
            <div class='separator'>&nbsp;</div>";
                    }
                }
            }
            
            echo "
        </div>
        <div id='createMessageGroupDIV' class='createMessageGroup far'>&#xf0fe;</div>";
        }
        
        ?>

    </div>

</div>

<script>
    
    window.onload = cleanAllImages();
    
    <?php
    
    /*
    if ($recipient)
    {
        //window.scrollTo(0,document.body.scrollHeight);
        echo "window.onload = function()
    {
        window.alert(document.body.scrollHeight);
        window.scrollTo(0,document.body.scrollHeight);
    };
    ";
    }
     * 
     */
    
    ?>
    
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
    
    $('#scrollButton').click(function()
    {
        window.scrollTo(0,document.body.scrollHeight);
    });
    
    $('#createMessageGroupDIV').click(function()
    {
        window.location = "<?php echo HOME; ?>?page=<?php echo POST_ADD_MESSAGE_GROUP; ?>";
    });
    
    $('.openMessage').click(function()
    {
        <?php
        
        $notePadCopy = "";
        if ($recipient)
        {
            if (isset($_COOKIE["conversation_recipient_" . $recipient['id']]))
            {
                $notePadCopy = $_COOKIE["conversation_recipient_" . $recipient['id']];
            }
        }
        elseif ($group)
        {
            $identifier = isPresetGroup($group) ? $group : $group['id'];
            if (isset($_COOKIE["conversation_group_" . $identifier]))
            {
                $notePadCopy = $_COOKIE["conversation_group_" . $identifier];
            }
        }
        $notePadCopy = $notePadCopy == "null" ? "" : $notePadCopy;
        
        ?>
        
        
        var notePadCopy = "<?php echo $notePadCopy; ?>";
        var html = "<div class='notePadDIV'>";
        html += "<div class='notePadFormDIV'>";
        html += "<form name='messageSend' method='post' action='<?php echo HOME; ?>'>";
        html += "<input name='recipient' hidden='true' type='number' value='<?php if ($recipient){ echo $recipient['id']; } ?>'/>";
        html += "<input name='group' hidden='true' type='text' value='<?php if ($group){ echo $identifier; } ?>'/>";
        html += "<div class='notePadTextDIV'>";
        html += "<textarea id='notePad' name='notePadData'>" + notePadCopy + "</textarea>";
        html += "</div>";
        html += "<div class='notePadButton'>";
        html += "<input name='<?php echo POST_SEND_MESSAGE; ?>' type='submit' value='Send'/>";
        html += "</div>";
        html += "<div id='headerBarInnerDIV'>";
        html += "<input id ='cancelMessage' type='cross' value='×'/>";
        html += "</div></form></div></div>";
        $('#messagesContainerDIV').hide();
        $(document.body).append(html);
        
        $('#notePad').on('input selectionchange propertychange', function()
        {
            var identifier = "conversation_";
            
                <?php
                
                    if ($recipient)
                    {
                        echo "
            identifier += 'recipient_" . $recipient['id'] . "';
            Cookies.set(identifier, $('#notePad').val());";
                        
                    }
                    else if ($group)
                    {
                        echo "
            identifier += 'group_" . $identifier . "';
            Cookies.set(identifier, $('#notePad').val());";
                        
                    }
                ?>
        });
        
        $('#cancelMessage').click(function()
        {
            $('.notePadDIV').remove();
            $('#messagesContainerDIV').show();
        });
    });
    
</script>

