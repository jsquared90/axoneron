<?php

?>

<div id='loginContainerDIV' class='mainDIV'>
    <?php echo getBGDiv(); ?>
    <?php echo getHomeHeaderDiv($user); ?>
    <div class='contentDIV'>
        <div id='loginTitleDIV' class='pageTitleDIV'>Log in</div>
        <form name='validateComplete' method='post' action='<?php echo HOME; ?>'>
            <div><input type='password' name='passwordValidate' id='passwordValidate' placeholder="password"/></div>

            <input name='id' hidden='true' type='number' value='<?php echo $user['id']; ?>'/>

            <div><input type='submit' name='completeValidation' value='SUBMIT' /></div>

        </form>
    </div>
</div>

