<div id='supportContainerDIV' class='mainDIV'>

<?php

    echo getBGDiv();
    echo getHeaderDiv($user);

?>
    
    <div class='contentDIV buttonListDIV'>
        <div class='pageTitleDIV'>Support/Help</div>
        <div><a href='<?php echo HOME . "?page=" . POST_VIEW_GROUP . "&type=support" ?>'><div class='button'>Contact Support</div></a></div>
        <div><a href='<?php echo USER_MANUAL_URL; ?>'><div class='button'>View User Manual</div></a></div>
        <div><a href='<?php echo USER_MANUAL_URL; ?>' download><div class='button'>Download User Manual</div></a></div>
    </div>


</div>

