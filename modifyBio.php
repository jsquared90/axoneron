
<div id='congressContainerDIV' class='mainDIV'>

<?php

if ($user['level'] < 2)
{
    echo "Unauthorized Access!";
    exit();
}

echo getBGDiv();
echo getHeaderDiv($user);

$congress = getCongressById($_GET['congress'], $connection);
$bioName = $_GET['bio'];
$first = explode("_", $bioName)[0];
$last = explode("_", $bioName)[1];

?>

<div class='contentDIV'>
    <div class='pageTitleDIV'>Modify Congress Speaker Bio</div>

    <div id='addBioFormDIV'>
        <form name='bioModify' method='post' action='<?php echo HOME; ?>' enctype='multipart/form-data'>
            <div>
                <div class='bioFormInput'>
                    <div class='formLabel'>Speaker First Name:*</div>
                    <div><input type='text' name='bioFirstName' value='<?php echo $first; ?>'></div>
                </div>
                <div class='bioFormInput'>
                    <div class='formLabel'>Speaker Last Name:*</div>
                    <div><input type='text' name='bioLastName' value='<?php echo $last; ?>'></div>
                </div>
            </div>
            <div><input id='bioFile' class='button' type='file' name='bioFile' accept='.pdf'/></div>
            <div id='bioAreaDIV2' style='display:none;'>
                <div class='bioFileName'></div>
                <div class='edit modifyBioEdit fa'><label id='bioFileLabel1' for='bioFile'>&#xf044;</label></div>
            </div>
            <div id='bioAreaDIV3' class="extraTopMargin">
                <label id='bioFileLabel2' class='button' for='bioFile'>Choose File</label>
            </div>
            <input name='congressID' hidden='true' type='number' value='<?php echo $congress['id']; ?>'/>
            <input name='previousBioName' hidden='true' type='text' value='<?php echo $bioName; ?>'/>
            <div id='bioSubmitDIV' class="extraTopMargin" style="display:none;">
                <div><input type='submit' name='<?php echo POST_MODIFY_BIO; ?>' value='SUBMIT'/></div>
            </div>
        </form>
    </div>
</div>
</div>

<script>
    
    $('#bioFileLabel1,#bioFileLabel2').click(function()
    {
        $('#bioFile').change(function()
        {
            var fileName = $('#bioFile')[0].files[0].name;
            $('.bioFileName').html(fileName);
            $('#bioAreaDIV3').hide();
            $('#bioAreaDIV2').show();
            $('#bioSubmitDIV').show();
        });
    });
    
    //Validate function
    $(document).ready(function()
    {
        //window.alert("we have successfully called this function");
        $("#bioModify").validate(
        {
            rules:
            {
                bioFirstName:
                {
                    required: true,
                    maxlength: 20,
                    letterswithbasicpunc: true
                },
                bioLastName:
                {
                    required: true,
                    maxlength: 30,
                    letterswithbasicpunc: true
                },
                bioFile:
                {
                    required: true,
                    accept: "pdf/*"
                }
            },
            messages:
            {

            }
        });
    });
    
</script>