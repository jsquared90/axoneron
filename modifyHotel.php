
<div id='congressContainerDIV' class='mainDIV'>

<?php

if ($user['level'] < 2)
{
    echo "Unauthorized Access!";
    exit();
}

$hotel = getHotelById($_GET['hotel'], $connection);

echo getBGDiv();
echo getHeaderDiv($user);

?>

<div class='contentDIV'>
    <div class='pageTitleDIV'>Modify Hotel Details</div>

<!-- need to add jQuery validation -->
<!-- need to apply character restrictions to fields, as dictated by database structure -->
    <div id='addHotelFormDIV'>
        <form name='hotelAdd' id='hotelAdd' method='post' action='<?php echo HOME; ?>'>

            <div>
                <div class='hotelFormInput'>
                    <div class='formLabel'>Hotel Name:*</div>
                    <div><input type='text' name='newHotelName' value='<?php echo $hotel['name'] ?>'></div>
                </div>
                <div class='hotelFormInput'>
                    <div class='formLabel'>Hotel URL:</div>
                    <div><input type='url' name='newHotelUrl' value='<?php echo $hotel['url']; ?>'></div>
                </div>
                <div class='hotelFormInput'>
                    <div class='formLabel'>Hotel Address 1:*</div>
                    <div><input type='text' name='newHotelAddress1' value='<?php echo $hotel['address1']; ?>'></div>
                </div>
                <div class='hotelFormInput'>
                    <div class='formLabel'>Hotel Address 2:</div>
                    <div><input type='text' name='newHotelAddress2' value='<?php echo $hotel['address2']; ?>'></div>
                </div>
                <div class='hotelFormInput'>
                    <div class='formLabel'>Hotel City:*</div>
                    <div><input type='text' name='newHotelCity' value='<?php echo $hotel['city']; ?>'></div>
                </div>
                <div class='hotelFormInput'>
                    <div class='formLabel'>Hotel State:*</div>
                    <div><input type='text' name='newHotelState' value='<?php echo $hotel['state']; ?>'></div>
                </div>
                <div class='hotelFormInput'>
                    <div class='formLabel'>Hotel Zip Code:*</div>
                    <div><input type='text' name='newHotelZip' value='<?php echo $hotel['zip']; ?>'></div>
                </div>
                <div class='hotelFormInput'>
                    <div class='formLabel'>Hotel Phone:</div>
                    <div><input type='text' name='newHotelPhone' value='<?php echo $hotel['phone']; ?>'></div>
                </div>
                <div>
                    <div><input type='submit' name='<?php echo POST_MODIFY_HOTEL; ?>' value='Submit'/></div>
                </div>
                <input name='hotelID' hidden='true' type='number' value='<?php echo $hotel['id']; ?>'/>
            </div>
        </form>
    </div>
</div>
</div>

<script>

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