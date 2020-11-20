<div id="congressContainerDIV" class='mainDIV'>

<?php

    echo getBGDiv();
    echo getHeaderDiv($user);
    
?>

    <div class='contentDIV'>
        <div class='pageTitleDIV whiteBG'>Schedule<span class='copyStyle3'>(select option below)</span></div>
    </div>
    <div class='contentDIV buttonListDIV'>
        <div><a href='<?php echo HOME; ?>?page=agenda&filter=all'><div class='button'>All Upcoming Events</div></a></div>
        <div><a href='<?php echo HOME; ?>?page=agenda&filter=axoneron''><div class='button'>Upcoming Axoneron Events</div></a></div>
    </div>
    
</div>

