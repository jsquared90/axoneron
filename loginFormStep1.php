<?php

?>

<div id='loginContainerDIV' class='mainDIV'>
    <?php echo getBGDiv(); ?>
    <?php echo getHomeHeaderDiv($user); ?>
    <div class='contentDIV'>
        <div id='loginTitleDIV' class='pageTitleDIV'>Log in</div>
        <form name='validateUserEmail' method='post' action='<?php echo HOME; ?>'>
            <div><input type='email' name='emailValidate' placeholder="email" value='<?php if (isset($_GET['email'])){ echo $_GET['email']; } ?>'/></div>
            <div><input type='submit' name='<?php echo POST_VALIDATE_EMAIL; ?>' value='Next' /></div>
        </form>
    </div>
</div>

