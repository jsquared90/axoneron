<div id="congressContainerDIV" class='mainDIV'>

<?php

    echo getBGDiv();
    echo getHeaderDiv($user);
    
    echo "
<div class='contentDIV'>";
    
    $congress = isset($_GET['congress']) ? getCongressById($_GET["congress"], $connection) : (isset($_POST['congress']) ? getCongressById($_POST["congress"], $connection) : 0);
    
    if ($congress)
    {
        $attendance = getCongressAttendance($user, $congress['id'], $connection);
        
        echo "
    <div id='viewCongressDIV'>";
        echo getLongFormatCongressBlock($congress);
        echo "
        <div class='buttonListDIV'>";
        if ($attendance)
        {
            echo "
            <div><a href='" . $congress["registrationURL"] . "' target='_blank'><div class='button'>Register</div></a></div>";
        }
        echo "
            <div><a href='" . $congress["congressURL"] . "' target='_blank'><div class='button'>General Information</div></a></div>";
        if ($attendance)
        {
            echo "
            <div><a href='" . HOME . "?page=reservations&congress=" . $congress["id"] . "'><div class='button'>Hotel Reservation</div></a></div>
            <div><a href='" . HOME . "?page=hospitality&congress=" . $congress["id"] . "'><div class='button'>Meeting Room Bookings</div></a></div>";
        }
        echo "
            <div><a href='" . HOME . "?page=agenda&congress=" . $congress["id"] . "'><div class='button'>Agenda</div></a></div>
            <div><a href='" . HOME . "?page=bios&congress=" . $congress["id"] . "'><div class='button'>Speaker Bios</div></a></div>
            <div><a href='" . HOME . "?page=viewGroup&type=onsite&congress=" . $congress["id"] . "'><div class='button'>On Site Team</div></a></div>";
        if ($attendance)
        {
            echo "
            <form id='congressAck' name='congressAck' method='post' action='" . HOME . "'>
                <input name='congress' value='" . $congress['id'] . "' hidden/>
                <input name='congressAckCC' value='0' hidden/>
                <input type='submit' name='" . POST_VIEW_CONGRESS . "' value='Cancel Attendance'/>
            </form>";
        }
        else
        {
            echo "
            <form id='congressAck' name='congressAck' method='post' action='" . HOME . "'>
                <input name='congress' value='" . $congress['id'] . "' hidden/>
                <input name='congressAckCC' value='1' hidden/>
                <input type='submit' name='" . POST_VIEW_CONGRESS . "' value='Confirm Attendance'/>
            </form>";
        }
        echo "
        </div>
    </div>";
    }
    else
    {
        echo "
    <div class='emptyListDIV'>There has been an error retrieving that congress.</div>";
    }
    
?>
    
</div>
