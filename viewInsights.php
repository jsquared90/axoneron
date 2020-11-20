<div id='agendaContainerDIV' class='mainDIV'>

<?php

if ($user['level'] < 2)
{
    echo "Unauthorized Access!";
    exit();
}

    echo getBGDiv();
    echo getHeaderDiv($user);
    $congress = isset($_POST['congressID']) ? getCongressById($_POST['congressID'], $connection) : 0;
    
    echo "
    <div class='contentDIV'>
        <div class='pageTitleDIV whiteBG'>Insights for " . $congress['shortName'] . "</div>";
    
    if ($userInsightPackages)
    {
        foreach ($userInsightPackages as $userInsightPackage)
        {
            $u = $userInsightPackage['user'];
            
            echo "
        <div class='viewInsightDIV1'>
            <div id='userInsightProfile_" . $u['id'] . "' class='userInsightProfileDIV'>
            <div id='caret" . $u['id'] . "' class='userInsightCaret caret fa'>&#xf105;</div>";
            
            if ($u['imageURL'] != "")
            {
                echo "<img id='image_" . $u['id'] . "' class='accountImage' onload='cleanImage(" . $u['id'] . ");' src='" . USER_IMAGES_PATH . $u['imageURL'] . "' style='display:none;'/>";
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
            <div id='userItems" . $u['id'] . "' class='userInsightItemsDIV' style='display:none;'>" ;
            
            // iterate through items
            
            foreach ($userInsightPackage['items'] as $insightItem)
            {
                $item = $insightItem['item'];
                $insights = $insightItem['insights'];
                
                echo "
                <div class='userInsightItemDIV'>
                    <div id='caret_" . $u['id'] . "_" . $item['id'] . "' class='userInsightItemCaret caret fa'>&#xf105;</div>
                    <div class='userInsightItemTitleDIV'>" . $item['title'] . "</div>
                    <div id='item_" . $u['id'] . "_" . $item['id'] . "' style='display:none;'>
                        <div class='userInsightItemGenNotesDIV'>" . nl2br($insights['generalNotes']) . "</div>";
                
                $i = 1;
                foreach ($insights['posts'] as $post)
                {
                    $class2 = $i % 2 == 0 ? "evenLine" : "oddLine";
                    echo "
                        <div class='userInsightPostDIV " . $class2 . "'>
                            <div id='caret_" . $u['id'] . "_" . $item['id'] . "_" . $i . "' class='userInsightPostCaret caret fa'>&#xf105;</div>
                            <div class='userInsightPostTitleDIV'>" . $post['title'] . "</div>
                            <div id='post_" . $u['id'] . "_" . $item['id'] . "_" . $i . "'  style='display:none;'>
                                <div class='userInsightPostNotesDIV'>" . nl2br($post['notes']) . "</div>";
                    
                    if ($post['image'] != "")
                    {
                        echo "
                                <div id='imageDIV_" . $u['id'] . "_" . $item['id'] . "_" . $i . "' class='userInsightPostImageDIV'>
                                    <img id='image_" . $u['id'] . "_" . $item['id'] . "_" . $i . "' src='uploads/insights/" . $post['identifier'] . "/" . $post['title'] .  "/Image." . $post['image'] . "'/>
                                </div>";
                    }
                    
                    echo "
                            </div>
                        </div>";
                    $i++;
                }
                
                echo "
                    </div>
                </div>";
            }
            
            echo "
            </div>";
            
            if (next($userInsightPackages))
            {
                echo "
            <div class='separator'>&nbsp;</div>";
            }
            
            echo "
        </div>";
            
            
        }
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
    
    $('.userInsightCaret').click(function()
    {
        var userID = $(this).attr("id").replace("caret", "");
        if($('#userItems' + userID).is(':visible'))
        {
            $('#userItems' + userID).hide();
            $(this).html('&#xf105;');
        }
        else
        {
            $('#userItems' + userID).show();
            $(this).html('&#xf107;');
        }
    });
    
    $('.userInsightItemCaret').click(function()
    {
        var data = $(this).attr("id").split("_");
        var userID = data[1];
        var itemID = data[2];
        if($('#item_' + userID + "_" + itemID).is(':visible'))
        {
            $('#item_' + userID + "_" + itemID).hide();
            $(this).html('&#xf105;');
        }
        else
        {
            $('#item_' + userID + "_" + itemID).show();
            $(this).html('&#xf107;');
        }
    });
    
    $('.userInsightPostCaret').click(function()
    {
        var data = $(this).attr("id").split("_");
        var userID = data[1];
        var itemID = data[2];
        var postID = data[3];
        if($('#post_' + userID + "_" + itemID + "_" + postID).is(':visible'))
        {
            $('#post_' + userID + "_" + itemID + "_" + postID).hide();
            $(this).html('&#xf105;');
        }
        else
        {
            $('#post_' + userID + "_" + itemID + "_" + postID).show();
            $(this).html('&#xf107;');
        }
    });
    
    async function addImageToClipboard(imgURL)
    {
        try
        {
            const data = await fetch(imgURL);
            const blob = await data.blob();
            navigator.clipboard.write([new ClipboardItem({[blob.type]: blob})]);
        }
        catch (error)
        {
            console.error(error);
        }
    }
    
    async function addTextToClipboard(text)
    {
        try
        {
            await navigator.clipboard.writeText(text);
        }
        catch (error)
        {
            console.error(error);
        }
    }
    
    
    $('.userInsightItemDIV').bind('contextmenu', function (e)
    {
        var _this = $(this);
        $html = "<div class='insightsContextMenu' style='left:" + e.pageX + "px;top:" + e.pageY + "px;'>";
        $html += "<div class='contextCloseIcon'>X</div>";
        $html += "<div class='copyUserInsights'>Copy Insight</div>";
        $html += "</div>";
        $(document.body).append($html);
        
        $('.contextCloseIcon').click(function()
        {
           $('.insightsContextMenu').remove();
        });
        
        $('.copyUserInsights').click(function()
        {
            copyData = _this.find('.userInsightItemTitleDIV').html() + " :\n";
            copyData += _this.find('.userInsightItemGenNotesDIV').html() + "\n\n";
            _this.find('.userInsightPostDIV').each(function()
            {
                copyData += $(this).find('.userInsightPostTitleDIV').html() + " :\n";
                copyData += $(this).find('.userInsightPostNotesDIV').html() + "\n";
                $(this).find('.userInsightPostImageDIV').each(function()
                {
                    copyData += "\t\t\n\n---- IMAGE ----\n\n";
                });
                copyData += "\n";
            });
            copyData = copyData.replace(/<br>/g, "");
            addTextToClipboard(copyData);
            $('.insightsContextMenu').remove();
        });
        
        return false;
    });
        
    
    $('.userInsightPostImageDIV').bind('contextmenu', function (e)
    {
        const imgURL = $(this).find('img').attr('src');
        
        $html = "<div class='insightsContextMenu' style='left:" + e.pageX + "px;top:" + e.pageY + "px;'>";
        $html += "<div class='contextCloseIcon'>X</div>";
        $html += "<div class='copyUserInsights'>Copy Image</div>";
        $html += "</div>";
        $(document.body).append($html);

        $('.contextCloseIcon').click(function()
        {
           $('.insightsContextMenu').remove();
        });
        
        $('.copyUserInsights').click(function()
        {
            addImageToClipboard(imgURL);
           $('.insightsContextMenu').remove();
        });
        
        return false;
    });
    
    $('.shortUserImageDIV').bind('contextmenu', function (e)
    {
        var userID = $(this).attr('id').replace("imageDIV_","");
        $html = "<div class='insightsContextMenu' style='left:" + e.pageX + "px;top:" + e.pageY + "px;'>";
        $html += "<div class='contextCloseIcon'>X</div>";
        $html += "<div class='copyUserInsights'>Copy User's Insights</div>";
        $html += "</div>";
        $(document.body).append($html);
        
        $('.contextCloseIcon').click(function()
        {
           $('.insightsContextMenu').remove();
        });
        
        $('.copyUserInsights').click(function()
        {
            var copyData = "Insights by " + $('#userInsightProfile_' + userID).find('.shortConvoDataDIV1').find('.shortUserNameDIV').html() + " :\n\n\n";
           
            $('#userItems' + userID).find('.userInsightItemDIV').each(function()
            {
                copyData += $(this).find('.userInsightItemTitleDIV').html() + " :\n";
                copyData += $(this).find('.userInsightItemGenNotesDIV').html() + "\n\n";
                $(this).find('.userInsightPostDIV').each(function()
                {
                    copyData += $(this).find('.userInsightPostTitleDIV').html() + " :\n";
                    copyData += $(this).find('.userInsightPostNotesDIV').html() + "\n";
                    $(this).find('.userInsightPostImageDIV').each(function()
                    {
                        copyData += "\t\t\n\n---- IMAGE ----\n\n";
                    });
                    copyData += "\n";
                });
                copyData += "\n\n";
            });
            copyData = copyData.replace(/<br>/g, "");
            addTextToClipboard(copyData);
            $('.insightsContextMenu').remove();
        });
        
        return false;
    });
    
</script>