<div id="hotelContainerDIV" class='mainDIV'>

<?php

    echo getBGDiv();
    echo getHeaderDiv($user);
    
    $congress = isset($_GET['congress']) ? getCongressById($_GET['congress'], $connection) : 0;
    
    $mostRecentHotelRecord = getMostRecentReservationRequestForUser($user, $congress["id"], $connection);
    
    if ($mostRecentHotelRecord)
    {
        if ($mostRecentHotelRecord['type'] == HOTEL_REQUEST || $mostRecentHotelRecord['type'] == MODIFIED_HOTEL_REQUEST)
        {
            $title = "Modify Hotel Request";
        }
        else
        {
            $title = "Modify Hotel Reservation";
        }
        $submit = POST_MODIFY_HOTEL_RESERVATION;
        if ($mostRecentHotelRecord['type'] == HOTEL_REQUEST || $mostRecentHotelRecord['type'] == MODIFIED_HOTEL_REQUEST)
        {
            $checkInDate = parseDateFromDateTime($congress["hotelStartDate"]);
            $checkOutDate = parseDateFromDateTime($congress["hotelEndDate"]);
            $specialRequet = $mostRecentHotelRecord['openEnd'];
        }
        else
        {
            $checkInDate = $mostRecentHotelRecord['reservation']['checkInDate'];
            $checkOutDate = $mostRecentHotelRecord['reservation']['checkOutDate'];
            $specialRequet = $mostRecentHotelRecord['reservation']['specialRequest'];
        }
        $roomType = $mostRecentHotelRecord['reservation']['roomType'];
        $occupancy = $mostRecentHotelRecord['reservation']['occupancy'];
    }
    else
    {
        $title = "Hotel Request";
        $submit = POST_REQUEST_HOTEL;
        $checkInDate = parseDateFromDateTime($congress["hotelStartDate"]);
        $checkOutDate = parseDateFromDateTime($congress["hotelEndDate"]);
        $roomType = 'King';
        $occupancy = 'Single';
        $specialRequet = '';
    }

    
    
?>
    
    <div class='contentDIV'>
        <div class='pageTitleDIV whiteBG'><?php echo $title; ?></div>
        <div class='viewCongressDIV'>
        <?php echo getLongFormatCongressBlock($congress); ?>
        </div>
        <div class='extraTopMargin'>
            <form name='hotelRequest' id='hotelRequest' method='post' action='<?php echo HOME; ?>'>
                <div>
                    <div class='formLabel'>Check In Date:</div>
                    <div><?php echo $checkInDate; ?></div>
                </div>
                <div>
                    <div class='formLabel'>Check Out Date:</div>
                    <div><?php echo $checkOutDate; ?></div>
                </div>
                <div>
                    <div class='formLabel'>Preferred Room Type:</div>
                    <div class='selectDIV'>
                        <select name='roomType' value='<?php echo $roomType; ?>'>
                            <option value='King'<?php if ($roomType == 'King'){ echo ' selected'; } ?>>King Bed</option>
                            <option value='Double'<?php if ($roomType == 'Double'){ echo ' selected'; } ?>>Double Bed</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div class='formLabel'>Occupancy:</div>
                    <div class='selectDIV'>
                        <select name='occupancy' value='<?php echo $occupancy; ?>'>
                            <option value='Single'<?php if ($occupancy == 'Single'){ echo ' selected'; } ?>>Single Occupancy</option>
                            <option value='Double'<?php if ($occupancy == 'Double'){ echo ' selected'; } ?>>Double Occupancy</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div class='formLabel'>Special Requests:</div>
                    <div><textarea rows="4" cols="50" name='openEnd'><?php echo $specialRequet; ?></textarea></div>
                </div>

                <input name='congressID' hidden='true' type='number' value='<?php echo $congress['id']; ?>'/>

                <br/>
                <input type='submit' name='<?php echo $submit; ?>' value='SUBMIT' />
            </form>
        </div>
    </div>
</div>


<script>

        //Validate function
    $(document).ready(function()
    {
        //window.alert("we have successfully called this function");
        $("#hotelRequest").validate(
        {
            rules:
            {
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
                    maxlength: 512
                }
            },
            messages:
            {

            }
        });
    });
    
</script>

