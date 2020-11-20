<div id='reservationContainerDIV' class='mainDIV'>

<?php

    echo getBGDiv();
    echo getHeaderDiv($user);
    
    echo "
    <div class='contentDIV'>";
    
    $congress = isset($_GET["congress"]) ? getCongressById($_GET["congress"], $connection) : 0;
    $sourceOfRecord = isset($_GET['user']) ? getUserById($_GET['user'], $connection) : 0;
    $recordID = isset($_GET['record']) ? $_GET['record'] : 0;
    
    if (!$congress && !$sourceOfRecord && !$recordID)
    {
        
        // user has clicked on the general "Reservations" button
        
        $hotelRezRecords = getAllMostRecentHotelReservationsForUser($user, $connection);
        $hospBookings = getAllHospBookingsForUser($user, $connection);
        
        if ($hotelRezRecords)
        {
            echo "
        <div class='pageTitleDIV extraTopMargin'>Hotel Reservations</div>
        <div id='reservationsListDIV'>
        ";
            foreach ($hotelRezRecords as $hotelRezRecord)
            {
                echo getShortFormatReservationBlock($hotelRezRecord, $connection);
            }
            echo "
        </div>";
        }
        if ($hospBookings)
        {
            echo "
        <div class='pageTitleDIV'>Meeting Room Reservations</div>
        <div id='reservationsListDIV'>";
            
            foreach ($hospBookings as $booking)
            {
                echo getShortFormatHospitalityBooking($booking, $connection);
            }
            
            echo "
        </div>";
        }
        if (!$hotelRezRecords && !$hospBookings)
        {
            echo "
        <div class='emptyListDIV'>You do not have any upcoming reservations.</div>";
        }
    }
    else
    {
        
        // user has reached this page through the selection of a congress
        
        if (isset($_GET["booking"]))
        {
            
            // user has clicked "Hospitality Bookings" from the sub menu for a congress
            
            $booking = getHospitalityBookingByID($_GET["booking"], $congress["id"], $connection);
            $record = getUserRecordForHospBooking($booking, $connection);
            $recordTimeStamp = $record["timeStamp"];
            
            if ($booking['openEnd'] != "" && isset($booking['adminComment']))
            {
                $confirmationRecord = getRecordByID($booking['confirmationRecordID'], $user, $connection);
                setReservationToViewed($confirmationRecord, $user, $connection);
            }
            
            echo "
        <div class='pageTitleDIV whiteBG'>Meeting Room</div>";
                
            echo getLongFormatCongressBlock($congress);
            
            echo "
        <div class='reservationDetailDIV'>";
            
            if ($booking['openEnd'] != "" && !isset($booking['adminComment']))
            {
                echo "
            <div class='copyStyle1'>Special request pending response from administrator.</div>
            <div class='marginStyle1'><span class='titleStyle2'>Date Of Request:</span> " . date("n/d/y", $recordTimeStamp) . "</div>";
            }
            else if ($booking['openEnd'] != "")
            {
                echo "
            <div class='marginStyle1'><span class='titleStyle2'>Date Of Request:</span> " . date("n/d/y", $recordTimeStamp) . "</div>
            <div class='marginStyle1'>
                <div class='titleStyle2'>Note from Administrator:</div>
                <div class='commentsDisplayDIV'>" . $booking['adminComment'] . "</div>
            </div>";
            }
            else
            {
                echo "
            <div class='marginStyle1'><span class='titleStyle2'>Date Of Request:</span> " . date("n/d/y", $recordTimeStamp) . "</div>";
            }
            
            echo getLongFormat1HospitalityBooking($booking, $connection);
            
            echo "
        </div>";
            
        }
        else
        {
            
            if ($sourceOfRecord && $recordID)
            {
                // admin has clicked "View previous reservation" from a modified hotel request
                $targetRecord = getRecordByID($recordID, $sourceOfRecord, $connection);
                $congressID = $targetRecord['reservation']['congressID'];
                $congress = getCongressById($congressID, $connection);
            }
            else
            {
                // user has clicked "Hotel Reservations" from the sub menu for a congress
                $targetRecord = getMostRecentReservationRequestForUser($user, $congress["id"], $connection);
            }

            if ($targetRecord["type"] == HOTEL_REQUEST || $targetRecord["type"] == MODIFIED_HOTEL_REQUEST)
            {
                echo "
        <div class='pageTitleDIV whiteBG'>Hotel Request</div>";
                
                echo getLongFormatCongressBlock($congress);
                
                echo "
        <div class='copyStyle1'>Request Pending</div>
        <div class='reservationDetailDIV'>";
            
                echo getLongFormatReservationBlock($targetRecord, $connection);
                
                echo "
        </div>";
            
                if ($sourceOfRecord && $recordID)
                {
                    echo "
        <a href='" . HOME . "?action=" . POST_VIEW_REQUEST . "&request=" . $_GET['newRecord'] . "'>
            <div class='copyStyle1 italic highlight1'>Return To Current Request</div>
        </a>";
                }
                else
                {
                    echo "
        <div class='buttonListDIV'>
            <div class='button'><a href='" . CANYON_CREEK_URL . "' target='_blank'>Book A Flight</a></div>
        </div>";
                }

            }
            else if ($targetRecord["type"] == HOTEL_CONFIRMATION)
            {
                if (!$sourceOfRecord || !$recordID)
                {
                    setReservationToViewed($targetRecord, $user, $connection);
                }

                echo "
        <div class='pageTitleDIV whiteBG'>Hotel Confirmation</div>";
                
                echo getLongFormatCongressBlock($congress);
                
                echo "
        <div class='titleStyle1 indented'>You're Booked!</div>
        <div class='reservationDetailDIV'>
            <div class='marginStyle1'><span class='titleStyle2'>Confirmation #:</span> " . $targetRecord['reservation']['confirmationNumber'] . "</div>";
                
                if ($targetRecord['openEnd'] != "")
                {
                    echo "
            <div class='marginStyle1'>
                <div class='titleStyle2'>Note from Administrator:</div>
                <div class='commentsDisplayDIV'>" . $targetRecord['openEnd'] . "</div>
            </div>";
                }
            
                echo getLongFormatReservationBlock($targetRecord, $connection);
                
                if ($sourceOfRecord && $recordID)
                {
                    echo "
        <a href='" . HOME . "?action=" . POST_VIEW_REQUEST . "&request=" . $_GET['newRecord'] . "'>
            <div class='copyStyle1 italic highlight1'>Return To Current Request</div>
        </a>";
                }
                else
                {
                    echo "
        </div>
        <div class='buttonListDIV'>
            <div class='button'><a href='" . CANYON_CREEK_URL . "' target='_blank'>Book A Flight</a></div>
        </div>";
                }
            
            }
            else
            {
                echo "
        <div class='pageTitleDIV whiteBG'>Hotel Request</div>";
                
                echo getLongFormatCongressBlock($congress);
                
                echo "
        <div class='reservationDetailDIV'>
            <div class='emptyListDIV'>You have no hotel requests or reservations at this time.</div>
        </div>
        <div class='buttonListDIV'>
            <div class='button'><a href='" . HOME . "?page=" . POST_REQUEST_HOTEL . "&congress=" . $congress["id"] . "'>Request Hotel</a></div>
            <div class='button'><a href='" . CANYON_CREEK_URL . "' target='_blank'>Book A Flight</a></div>
        </div>";
            }
        }
    }
    
?>
    </div>
</div>

<script>

    function confirmHotelRezDelete()
    {
        if (confirm("Are you sure you would like to cancel your hotel reservation?"))
        {
            return true;
        }
        else
        {
            event.preventDefault();
            return false;
        }
    }
    
    function confirmHospBookingDelete()
    {
        if (confirm("Are you sure you would like to cancel your meeting room booking?"))
        {
            return true;
        }
        else
        {
            event.preventDefault();
            return false;
        }
    }

</script>


