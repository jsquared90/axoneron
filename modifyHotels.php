
<div id='congressContainerDIV' class='mainDIV'>

<?php

if ($user['level'] < 2)
{
    echo "Unauthorized Access!";
    exit();
}

$allHotels = getAllHotels($connection);

echo getBGDiv();
echo getHeaderDiv($user);

?>

<div class='contentDIV'>
    <div class='pageTitleDIV'>Select A Hotel To Modify</div>

<?php

if ($allHotels)
{
    foreach ($allHotels as $hotel)
    {
        echo "
    <a href='" . HOME . "?action=" . POST_MODIFY_HOTEL . "&hotel=" . $hotel['id'] . "'><div class='hotelBlockDIV'>
        <div class='shortHotelName'>" . $hotel['name'] . "</div>
        <div class='shortHotelAddress1'>" . $hotel['address1'] . "</div>
        <div class='shortHotelAddress2'>" . $hotel['address2'] . "</div>
        <div class='shortHotelAddress3'>" . $hotel['city'] . ", " . $hotel['state'] . " " . $hotel['zip'] . "</div>";
        echo "
    </div></a>";
    }
    
}
else
{
    echo "
    <div class='emptyListDIV'>There are no hotels currently in the database.</div>";
}

?>

    <div id='addHotelDIV' class='addItem far'>&#xf0fe;</div>

<!-- need to add jQuery validation -->
<!-- need to apply character restrictions to fields, as dictated by database structure -->
    <div id='addHotelFormDIV' style='display:none;'>
        <form name='hotelAdd' id='hotelAdd' method='post' action='<?php echo HOME; ?>'>

            <div>
                <div class='hotelFormInput'>
                    <div class='formLabel'>Hotel Name:*</div>
                    <div><input type='text' name='newHotelName' ></div>
                </div>
                <div class='hotelFormInput'>
                    <div class='formLabel'>Hotel URL:</div>
                    <div><input type='url' name='newHotelUrl' ></div>
                </div>
                <div class='hotelFormInput'>
                    <div class='formLabel'>Hotel Address 1:*</div>
                    <div><input type='text' name='newHotelAddress1' ></div>
                </div>
                <div class='hotelFormInput'>
                    <div class='formLabel'>Hotel Address 2:</div>
                    <div><input type='text' name='newHotelAddress2' ></div>
                </div>
                <div class='hotelFormInput'>
                    <div class='formLabel'>Hotel City:*</div>
                    <div><input type='text' name='newHotelCity' ></div>
                </div>
                <div class='hotelFormInput'>
                    <div class='formLabel'>Hotel State:*</div>
                    <div><input type='text' name='newHotelState' ></div>
                </div>
                <div class='hotelFormInput'>
                    <div class='formLabel'>Hotel Zip Code:*</div>
                    <div><input type='text' name='newHotelZip' ></div>
                </div>
                <div class='hotelFormInput'>
                    <div class='formLabel'>Hotel Phone:</div>
                    <div><input type='text' name='newHotelPhone' ></div>
                </div>
                <div>
                    <div><input type='submit' name='<?php echo POST_ADD_HOTEL; ?>' value='ADD'/></div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>

<script>
    
    $( "#addHotelDIV" ).click(function()
    {
        if ($("#addHotelFormDIV").is(":hidden"))
        {
            $("#addHotelFormDIV").show();
            $( "#addHotelDIV" ).html("&#xf146;");
        }
        else
        {
            $("#addHotelFormDIV").hide();
            $( "#addHotelDIV" ).html("&#xf0fe;");
        }
        
    });

    //Validate function
    $(document).ready(function()
    {
        //window.alert("we have successfully called this function");
        $("#hotelAdd").validate(
        {
            rules:
            {
                newHotelName:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 32
                },
                newHotelUrl:
                {
                    required: false,
                    url: true,
                    maxlength: 512
                },
                newHotelAddress1:
                {
                    required: true,
                    maxlength: 32
                },
                newHotelAddress2:
                {
                    required: false,
                    maxlength: 16
                },
                newHotelCity:
                {
                    required: true,
                    maxlength: 32
                },
                newHotelState:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 16
                },
                newHotelZip:
                {
                    required: true,
                    maxlength: 12
                },
                newHotelPhone:
                {
                    required: false,
                    phoneUS: true,
                    maxlength: 36
                }
            },
            messages:
            {

            }
        });
    });

</script>

