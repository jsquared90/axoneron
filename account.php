<div id="accountContainerDIV" class='mainDIV'>
    
<?php echo getBGDiv(); ?>
<?php echo getHeaderDiv($user) ?>
    
<?php

    $phoneString = strval($user['phone']);
    $phoneDisplay = "";
    if (strlen($phoneString) == 10)
    {
        $phoneDisplay .= "(" . substr($phoneString, 0, 3) . ") " . substr($phoneString, 3, 3) . " - " . substr($phoneString, 6);
    }
    elseif (strlen($phoneString) == 11)
    {
        $phoneDisplay .= "+" . substr($phoneString, 0, 1) . " (" . substr($phoneString, 1, 3) . ") " . substr($phoneString, 4, 3) . " - " . substr($phoneString, 7);
    }
    else
    {
        $phoneDisplay = $phoneString;
    }

?>

<div class='contentDIV'>
    <div class='pageTitleDIV'>My Account</div>
    
    <div id='accountProfileDIV1'>
        <div class='edit accountEdit fa'><a href='<?php echo HOME . "?action=" . POST_MODIFY_ACCOUNT; ?>'>&#xf044;</a></div>
        
        <?php
        
        if ($user['imageURL'])
        {
            echo "<img id='accountImage' onload='getExif();' src='" . USER_IMAGES_PATH . $user['imageURL'] . "' style='display:none;'/>";
        }
        
        ?>
        
        <div id='accountImageDIV'<?php if ($user['imageURL']){ echo " style='background-image: url(\"" . USER_IMAGES_PATH . $user['imageURL'] . "\");'"; } ?>>&nbsp;</div>
        <div id='accountNameDIV' class='subTitleDIV'><?php echo $user['first'] . " " . $user['last']; ?></div>
        <div id='accountTitleDIV'><?php echo $user['title']; ?></div>
    </div>
    <div class='separator'>&nbsp;</div>
    <div id='accountProfileDIV2'>
        <div>
            <div class='accountLabel'>Phone:</div>
            <div class='accountData'><?php echo $phoneDisplay; ?></div>
        </div>
        <div>
            <div class='accountLabel'>Email:</div>
            <div class='accountData'><?php echo $user['email']; ?></div>
        </div>
        <div>
            <div class='accountLabel'>Role:</div>
            <div class='accountData'><?php echo getUserRole($user); ?></div>
        </div>
    </div>
    
</div>
    
<script>
    
    window.onload = getExif();
    
    function getExif()
    {
        //window.alert("exif");
        var accountImage = document.getElementById('accountImage');
        EXIF.getData(accountImage, function()
        {
            var orientation = EXIF.getTag(this, "Orientation");
            
            if(orientation == 6)
            {
                $('#accountImageDIV').addClass("rotate90");
            }
            else if(orientation == 8)
            {
                $('#accountImageDIV').addClass("rotate270");
            }
            else if(orientation == 3)
            {
                $('#accountImageDIV').addClass("rotate180");
            }
        });
    };
    
</script>

