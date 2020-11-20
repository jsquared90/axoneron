<div id='congressContainerDIV' class='mainDIV'>

<?php

    echo getBGDiv();
    echo getHeaderDiv($user) . "
    <div class='contentDIV'>
        <div class='pageTitleDIV'>Confirm Attendance</div>
    </div>
    <div>
    ";
    
    $congress = isset($_GET['congress']) ? getCongressById($_GET['congress'], $connection) : 0;
    if ($congress)
    {
        echo "
        <form class='contentDIV congressForm congressAckForm' name='congressAck' method='post' action='" . HOME . "'>
            <div class='centered'>
                <div class='subTitleDIV'>Will you be attending " . $congress['shortName'] . "?</div>
                <div class='hRow'>
                    <div>No<input type='radio' value='0' name='congressAckCC'/></div>
                    <div>Yes<input type='radio' value='1' name='congressAckCC' checked/></div>
                </div>
            </div>
            <input name='congress' value='" . $congress['id'] . "' hidden/>
            <div>
                <div><input type='submit' name='" . POST_VIEW_CONGRESS . "' value='Submit'/></div>
            </div>
        </form>";
    }
    else
    {
        echo "<div class='emptyListDIV'>There are no congresses currently in the database.</div>";
    }

    echo "
    </div>
    ";
    
?>

</div>