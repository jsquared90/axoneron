<div id='hospitalityContainerDIV' class='mainDIV'>

<?php

    echo getBGDiv();
    echo getHeaderDiv($user);
    
    $congress = isset($_GET["congress"]) ? getCongressById($_GET["congress"], $connection) : 0;
    
    if ($congress)
    {

        echo "
    <div class='contentDIV'>
        <div class='pageTitleDIV whiteBG'>Meeting Rooms</div>";
        
        echo getLongFormatCongressBlock($congress);

        $hospitalityRooms = getAllHospitalityRoomsForCongress($congress, $connection);
        if ($hospitalityRooms)
        {
            echo "
        <div id='reservationsListDIV' class='extraTopMargin'>";
            foreach ($hospitalityRooms as $room)
            {
                echo getShortFormatHospitalityRoom($room, $congress["id"]);
            }
            echo "
        </div>";
        }
        else
        {
        echo "
        <div class='emptyListDIV'>There are no hospitality rooms currently affiliated with that congress.</div>";
        }
        echo "
    </div>";
    }
    else
    {
        echo "
    <div class='hospitalityDetailDIV'>
        <div class='emptyListDIV'>There has been an error retrieving that congress.</div>
    </div>";
    }
    
    ?>

</div>

