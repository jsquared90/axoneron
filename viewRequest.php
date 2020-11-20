<div id="congressContainerDIV" class='mainDIV'>

<?php

if ($user['level'] < 2)
{
    echo "Unauthorized Access!";
    exit();
}

echo getBGDiv();
echo getHeaderDiv($user);
echo "
<div class='contentDIV'>";

$prID = $_GET["request"];
$pendingRequest = getPendingRequestByID($prID, $user, $connection);

if ($pendingRequest)
{
    $congress = 0;
    if ($pendingRequest["userRecord"]["type"] == HOTEL_REQUEST)
    {
        $reservation = $pendingRequest["userRecord"]["reservation"];
        $congressID = $reservation["congressID"];
        $congress = getCongressById($congressID, $connection);
        $checkInDate = parseDateFromDateTime($congress["hotelStartDate"]);
        $checkOutDate = parseDateFromDateTime($congress["hotelEndDate"]);
        $hotels = getHotelsWithCongress($congress, $connection);
        $title = "Confirm/Review Hotel Request";
    }
    else if ($pendingRequest["userRecord"]["type"] == MODIFIED_HOTEL_REQUEST)
    {
        $reservation = $pendingRequest["userRecord"]["reservation"];
        $congressID = $reservation["congressID"];
        $congress = getCongressById($congressID, $connection);
        $checkInDate = parseDateFromDateTime($congress["hotelStartDate"]);
        $checkOutDate = parseDateFromDateTime($congress["hotelEndDate"]);
        $hotels = getHotelsWithCongress($congress, $connection);
        $title = "Confirm/Review Modified Hotel Request";
    }
    else
    {
        $booking = $pendingRequest['userRecord']['booking'];
        $congressID = $booking['room']['congressID'];
        $title = "Review Meeting Room Request";
        $congress = getCongressById($congressID, $connection);
    }
    
    $sourceOfRequest = $pendingRequest["sourceOfRequest"];
    
    echo "
    <div class='pageTitleDIV'>" . $title . "</div>
    ";
    
    if ($pendingRequest["userRecord"]["type"] == MODIFIED_HOTEL_REQUEST)
    {
        $previousRecordID = getPreviousReservationRequestForUser($sourceOfRequest, $congressID, $connection)['id'];
        echo "
    <a href='" . HOME . "?page=reservations&user=" . $sourceOfRequest['id'] . "&record=" . $previousRecordID . "&newRecord=" . $prID . "'>
        <div class='copyStyle1 italic highlight1'>View Previous Request/Confirmation</div>
    </a>";
    }
    
    echo getLongFormatCongressBlock($congress);
    echo "
    <div class='pageTitleDIV'><span class='genericLabel'>Requester : </span>" . $sourceOfRequest["first"] . " " . $sourceOfRequest["last"] . "</div>
";
    
    if ($pendingRequest["userRecord"]["type"] == HOTEL_REQUEST || $pendingRequest["userRecord"]["type"] == MODIFIED_HOTEL_REQUEST)
    {
        echo "
    <form name='hotelConfirmation' id='hotelConfirm' method='post' action='" . HOME , "'>
        <div>
            <div>Check In Date:*</div>
            <div><input type='text' class='datepicker' name='checkInDate' value='" . $checkInDate .  "'></div>
        </div>
        <div>
            <div>Check Out Date:*</div>
            <div><input type='text' class='datepicker' name='checkOutDate' value='" . $checkOutDate .  "'></div>
        </div>
        <div>
            <div><span>Room Type:</span></div>
            <select name='roomType' value='" . $reservation['roomType'] . "'>
                <option value='King'";
        if ($reservation['roomType'] == 'King')
        {
            echo " selected='true'";

        }
        echo "'>King Bed</option>
                <option value='Double'";
        if ($reservation['roomType'] == 'Double')
        {
            echo " selected='true'";

        }
        echo "'>Double Bed</option>
            </select>
        </div>
        <div>
            <div><span>Occupancy:</span></div>
            <select name='occupancy' value='" . $reservation['occupancy'] . "'>
                <option value='Single'";
        if ($reservation['occupancy'] == 'Single')
        {
            echo " selected='true'";
        
        }
        echo "'>Single Occupancy</option>
                <option value='Double'";
        if ($reservation['occupancy'] == 'Double')
        {
            echo " selected='true'";
        
        }
        echo "'>Double Occupancy</option>
            </select>
        </div>
        <br/>
        <div>
            <div>Special Request:</div>
            <div>" . $pendingRequest['userRecord']['openEnd'] . "</div>
        </div>
        <br/>
        <div>Select Hotel:*</div>
        <select name='hotelList' required='true'>
            <option value='' disabled selected hidden>Select...</option>";
        foreach ($hotels as $hotel)
        {
            echo "
            <option value='" . $hotel['id'] . "'>" . $hotel['name'] . "</option>";
        }
        echo "
        </select>
        <br/>
        <br/>
        <div>
            <div>Confirmation #:*</div>
            <div><input type='text' name='confirmationNumber'></div>
        </div>
        <div>
            <div>Comments back to user:</div>
            <div><textarea name='openEnd' ></textarea></div>
        </div>
        <input name='recordID' hidden='true' type='number' value='" . $pendingRequest['recordID'] . "'/>
        <input name='congressID' hidden='true' type='number' value='" . $congressID . "'/>
        <br/>
        <input type='submit' name='" . POST_CONFIRM_HOTEL . "' value='Confirm' />
    </form>
    ";
    }
    elseif ($pendingRequest["userRecord"]["type"] == HOSP_ROOM_BOOKED || $pendingRequest["userRecord"]["type"] == MODIFIED_HOSP_ROOM_BOOKING)
    {
        echo "
    <div class='hospRoomBlockDIV'>";
        echo getShortFormatHospitalityBooking($booking, $connection);
        echo "
    </div>
    <form name='hospRoomConfirmation' method='post' action='" . HOME , "'>
        <div>
            <div>Request:</div>
            <div>" . $booking['openEnd'] . "</div>
        </div>
        <br/>
        <div>
            <div>Comments back to user:</div>
            <div><textarea name='openEnd'></textarea></div>
        </div>
        <input name='recordID' hidden='true' type='number' value='" . $pendingRequest['recordID'] . "'/>
        <input name='congressID' hidden='true' type='number' value='" . $congressID . "'/>
        <br/>
        <input type='submit' name='" . POST_CONFIRM_HOSP_REQUEST . "' value='Confirm' />
    </form>";
    }
    
    
    
}
else
{
    echo "<div class='emptyListDIV'>There was an error locating the selected request. Please contact support.</div>";
}

?>

</div>
</div>
</div>

<script>
    
        //Validate function
    $(document).ready(function()
    {
        //window.alert("we have successfully called this function");
        $("#hotelConfirm").validate(
        {
            rules:
            {
                checkInDate:
                {
                    required: true,
                    date: true
                },
                checkOutDate:
                {
                    required: true,
                    date: true
                },
                roomType:
                {
                    required: true
                },
                occupancy:
                {
                    required: true
                },
                openEnd:
                {
                    required: false,
                    maxlength: 1024
                },
                hotelList:
                {
                    required: true
                },
                confirmationNumber:
                {
                    required: true
                }
            },
            messages:
            {

            }
        });
    });
    
</script>
