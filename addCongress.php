
<div id='congressAdminContainerDIV' class="mainDIV">
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

if ($_GET["action"] == POST_ADD_CONGRESS)
{
    echo "
    <div class='pageTitleDIV'>Add Congress</div>";
    $congress = 0;
}
else if ($_GET["action"] == POST_MODIFY_CONGRESS_DETAIL)
{
    echo "
    <div class='pageTitleDIV'>Modify Congress</div>";
    $congress = getCongressById($_GET["congress"], $connection);
}

echo "
</div>";

?>

<!-- need to add jQuery validation -->
<!-- need to apply character restrictions to fields, as dictated by database structure -->

<form class='contentDIV congressForm' name='congressAdd' id='addCongress' method='post' action='<?php echo HOME; ?>' enctype='multipart/form-data'>
    
    <div>
        <div>
            <div class='formLabel'>Congress Full Name:*</div>
            <div><input type='text' name='newCongressName' value='<?php if($congress){echo cleanForValueField($congress["name"]);}?>' ></div>
        </div>
        <div>
            <div class='formLabel'>Congress Abbreviated Name:*</div>
            <div><input type='text' name='newCongressShortName' value='<?php if($congress){echo cleanForValueField($congress["shortName"]);}?>' ></div>
        </div>
        <div>
            <div class='formLabel'>Congress URL:*</div>
            <div><input type='url' name='newCongressURL' value='<?php if($congress){echo $congress["congressURL"];}?>' ></div>
        </div>
        <div>
            <div class='formLabel'>Congress Registration URL:</div>
            <div><input type='url' name='newRegistrationURL' value='<?php if($congress){echo $congress["registrationURL"];}?>'></div>
        </div>
        <div>
            <div class='formLabel'>Congress Start Date:*</div>
            <div class='dateSelectDIV'>
                <input type='text' class="datepicker" name='newCongressStartDate' value='<?php if($congress){echo parseDateFromDateTime($congress["startDate"]);}?>' >
            </div>
            <div class='formLabel'>Congress Start Time:*</div>
            <div class='timeSelectDIV'>
                <div class='selectDIV selectTimeDIV'>
                    <select name='newCongressStartTime' placeholder='Select...' value='<?php if($congress){ echo parseTimeMinusMeridianFromDateTime($congress["startDate"]); } ?>' >
                    <?php
                    
                        $selected = $congress ? parseTimeMinusMeridianFromDateTime($congress["startDate"]) : "8:00";
                        echo get12HoursForSelect($selected);
                        
                    ?>
                    </select>
                </div>
                <div class='selectDIV selectMeridianDIV'>
                    <select name='newCongressStartMeridian' value='<?php if($congress){ echo parseMeridianFromDateTime($congress["startDate"]); } ?>' >
                    <?php
                    
                        $selected = $congress ? parseMeridianFromDateTime($congress["startDate"]) : "am";
                        echo getMeridiansForSelect($selected);
                        
                    ?>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class='formLabel'>Congress End Date:*</div>
            <div class='dateSelectDIV'>
                <input type='text' class="datepicker" name='newCongressEndDate' value='<?php if($congress){echo parseDateFromDateTime($congress["endDate"]);}?>' >
            </div>
            <div class='formLabel'>Congress End Time:*</div>
            <div class='timeSelectDIV'>
                <div class='selectDIV selectTimeDIV'>
                    <select name='newCongressEndTime' placeholder='Select...' value='<?php if($congress){ echo parseTimeMinusMeridianFromDateTime($congress["endDate"]); } ?>' >
                    <?php
                    
                        $selected = $congress ? parseTimeMinusMeridianFromDateTime($congress["endDate"]) : "6:00";
                        echo get12HoursForSelect($selected);
                        
                    ?>
                    </select>
                </div>
                <div class='selectDIV selectMeridianDIV'>
                    <select name='newCongressEndMeridian' value='<?php if($congress){ echo parseMeridianFromDateTime($congress["endDate"]); } ?>' >
                    <?php
                    
                        $selected = $congress ? parseMeridianFromDateTime($congress["endDate"]) : "pm";
                        echo getMeridiansForSelect($selected);
                        
                    ?>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class='formLabel'>Congress Venue Name:*</div>
            <div><input type='text' name='newCongressVenueName' value='<?php if($congress){echo cleanForValueField($congress["venueName"]);}?>' ></div>
        </div>
        <div>
            <div class='formLabel'>Venue Hall:</div>
            <div><input type='text' placeholder="eg. Hall A" value='<?php if($congress){echo cleanForValueField($congress["venueHall"]);}?>' name='newCongressVenueHall' ></div>
        </div>
        <div>
            <div class='formLabel'>Axoneron Booth:</div>
            <div><input type='text' placeholder="eg. Booth #100" value='<?php if($congress){echo cleanForValueField($congress["venueBooth"]);}?>' name='newCongressVenueBooth' ></div>
        </div>
        <div>
            <div class='formLabel'>Venue Address 1:*</div>
            <div><input type='text' name='newCongressVenueAddress1' value='<?php if($congress){echo cleanForValueField($congress["venueAddress1"]);}?>' ></div>
        </div>
        <div>
            <div class='formLabel'>Venue Address 2:</div>
            <div><input type='text' name='newCongressVenueAddress2' value='<?php if($congress){echo cleanForValueField($congress["venueAddress2"]);}?>'></div>
        </div>
        <div>
            <div class='formLabel'>Venue City:*</div>
            <div><input type='text' name='newCongressVenueCity' value='<?php if($congress){echo cleanForValueField($congress["venueCity"]);}?>' ></div>
        </div>
        <div>
            <div class='formLabel'>Venue State:*</div>
            <div><input type='text' class='state' name='newCongressVenueState' value='<?php if($congress){echo cleanForValueField($congress["venueState"]);}?>' ></div>
        </div>
        <div>
            <div class='formLabel'>Venue Country:*</div>
            <div><input type='text' name='newCongressVenueCountry' value='<?php if($congress){echo cleanForValueField($congress["venueCountry"]);}?>' ></div>
        </div>
        <div>
            <div class='formLabel'>Venue Zip Code:*</div>
            <div><input type='text' class='zip' name='newCongressVenueZip' value='<?php if($congress){echo cleanForValueField($congress["venueZip"]);}?>' ></div>
        </div>
        <div class='subTitleDIV'>Hotel Reservation Dates:</div>
        <div>
            <div class='formLabel'>Check In Date:*</div>
            <div class='dateSelectDIV'>
                <input type='text' class="datepicker" name='newCongressHotelStartDate' value='<?php if($congress){echo parseDateFromDateTime($congress["hotelStartDate"]);}?>' >
            </div>
        <div>
        </div>
            <div class='formLabel'>Check Out Date:*</div>
            <div class='dateSelectDIV'>
                <input type='text' class="datepicker" name='newCongressHotelEndDate' value='<?php if($congress){echo parseDateFromDateTime($congress["hotelEndDate"]);}?>' >
            </div>
        </div>
        <div class='subTitleDIV'>Image:</div>
        <div id='imageAreaDIV'>
            <div><input id='imageFile' type='file' name='imageFile' accept='.png,.jpg,.jpeg'/></div>
            <div id='imageAreaDIV2'<?php if($congress['imageURL'] == ""){ echo " style='display:none;'"; } ?>>
                <div class='imageFileName'><?php echo rawurldecode($congress['imageURL']); ?></div>
                <div class='edit imageEdit fa'><label id='imageFileLabel1' for='imageFile'>&#xf044;</label></div>
            </div>
            <div id='imageAreaDIV3'<?php if($congress['imageURL'] != ""){ echo " style='display:none;'"; } ?>>
                <div id='fileNameDIV' class='emptyListDIV'>There is not an image currently affiliated with this congress</div>
                <label id='imageFileLabel2' class='button' for='imageFile'>Choose File</label>
            </div>
        </div>
        
        <?php
        
        if ($congress)
        {
            echo "
        <div><input type='number' name='id' hidden='true' value='" . $congress['id'] . "'/></div>";
        }
        
        ?>
        
        <div><input type='submit' name='<?php if ($congress){echo POST_MODIFY_CONGRESS_DETAIL;}else{echo POST_ADD_CONGRESS;}?>' value='Next'/></div>
        </div>
    </div>
    
</form>
</div>

<script>
    
    $('#imageFileLabel1,#imageFileLabel2').click(function()
    {
        $('#imageFile').change(function()
        {
            var fileName = $('#imageFile')[0].files[0].name;
            $('.imageFileName').html(fileName);
            $('#imageAreaDIV3').hide();
            $('#imageAreaDIV2').show();
        });
    });
    
    //Validate function
    $(document).ready(function()
    {
        //window.alert("we have successfully called this function");
        $("#addCongress").validate(
        {
            rules:
            {
                newCongressName:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 48
                },
                newCongressShortName:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 24
                },
                newCongressURL:
                {
                    required: true,
                    url: true,
                    minlength: 8,
                    maxlength: 512
                },
                newRegistrationURL:
                {
                    required: false,
                    url: true,
                    minlength: 8,
                    maxlength: 512
                },
                newCongressStartDate:
                {
                    required: true,
                    date: true
                },
                newCongressStartTime:
                {
                    required: true
                },
                newCongressStartMeridian:
                {
                    required: true
                },
                newCongressEndDate:
                {
                    required: true,
                    date: true
                },
                newCongressEndTime:
                {
                    required: true
                },
                newCongressEndMeridian:
                {
                    required: true
                },
                newCongressVenueName:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 48,
                    letterswithbasicpunc: true
                },
                newCongressVenueHall:
                {
                    required: false,
                    minlength: 2,
                    maxlength: 16
                },
                newCongressVenueBooth:
                {
                    required: false,
                    minlength: 2,
                    maxlength: 16
                },
                newCongressVenueAddress1:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 32
                },
                newCongressVenueAddress2:
                {
                    required: false,
                    minlength: 2,
                    maxlength: 32
                },
                newCongressVenueCity:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 16
                },
                newCongressVenueState:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 16
                },
                newCongressVenueCountry:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 16
                },
                newCongressVenueZip:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 12
                },
                newCongressHotelStartDate:
                {
                    required: true,
                    date: true
                },
                newCongressHotelEndDate:
                {
                    required: true,
                    date: true
                },
                imageFile:
                {
                    required: false,
                    accept: "image/*"
                }
            },
            messages:
            {

            }
        });
    });
    
</script>