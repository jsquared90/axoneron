
<div id='congressContainerDIV' class="mainDIV">
    
<?php

if ($user['level'] < 2)
{
    echo "Unauthorized Access!";
    exit();
}

echo getBGDiv(); 
echo getHeaderDiv($user);

$congress = getCongressById($_GET['congress'], $connection);
$item = getAgendaItemByID($_GET['item'], $congress, $connection);

?>
    
<div class='contentDIV'>
    <div class='pageTitleDIV whiteBG'>Modify Agenda Item</div>
    
<?php

echo getLongFormatCongressBlock($congress);

?>

<!-- need to add jQuery validation -->
<!-- need to apply character restrictions to fields, as dictated by database structure -->

<form class='agendaItemForm' name='agendaItemModify' id='agendaItemModify' method='post' action='<?php echo HOME; ?>'>
    
    <div>
        <div>
            <div class='formLabel'>Title:*</div>
            <div><input class='agendaFormSmall' type='text' name='agendaTitle' value='<?php echo $item[TITLE]; ?>'></div>
        </div>
        <div>
            <div class='formLabel'>Sub-Title:</div>
            <div><input class='agendaFormSmall' type='text' name='agendaSubTitle' value='<?php echo $item[SUB_TITLE]; ?>'></div>
        </div>
        <div>
            <div class='formLabel'>Location:</div>
            <div><input class='agendaFormSmall' type='text' name='agendaLocation' value='<?php echo $item[LOCATION]; ?>'></div>
        </div>
        <div>
            <div class='formLabel'>Type:*</div>
            <div class='selectDIV'>
                <select id='agendaType' name='agendaType' value='<?php echo $item[TYPE]; ?>'>
                    <option value='<?php echo _BREAK; ?>'<?php if($item[TYPE] == _BREAK){ echo ' selected'; }?>>Break</option>
                    <option value='<?php echo EXHIBIT; ?>'<?php if($item[TYPE] == EXHIBIT){ echo ' selected'; }?>>Exhibit</option>
                    <option value='<?php echo EXPO_HOURS; ?>'<?php if($item[TYPE] == EXPO_HOURS){ echo ' selected'; }?>>Exposition Hours</option>
                    <option value='<?php echo INTERNAL; ?>'<?php if($item[TYPE] == INTERNAL){ echo ' selected'; }?>>Internal</option>
                    <option value='<?php echo POSTER; ?>'<?php if($item[TYPE] == POSTER){ echo ' selected'; }?>>Poster Session</option>
                    <option value='<?php echo PRESENTATION; ?>'<?php if($item[TYPE] == PRESENTATION){ echo ' selected'; }?>>Presentation</option>
                    <option value='<?php echo RECEPTION; ?>'<?php if($item[TYPE] == RECEPTION){ echo ' selected'; }?>>Reception</option>
                </select>
            </div>
        </div>
        <div>
            <div class='formLabel'>Priority:</div>
            <div class='selectDIV'>
                <select id='agendaPriority' name='agendaPriority' value='<?php echo $item[PRIORITY]; ?>'>
                    <option value='0'<?php if($item[PRIORITY] == 0){ echo ' selected'; }?>>None</option>
                    <option value='1'<?php if($item[PRIORITY] == 1){ echo ' selected'; }?>>Low</option>
                    <option value='2'<?php if($item[PRIORITY] == 2){ echo ' selected'; }?>>Medium</option>
                    <option value='3'<?php if($item[PRIORITY] == 3){ echo ' selected'; }?>>High</option>
                </select>
            </div>
        </div>
        <div>
            <div class='formLabel'>Category:</div>
            <div><input type='text' name='agendaCategory' value='<?php echo $item[CATEGORY]; ?>'></div>
        </div>
        <div>
            <div class='formLabel'>Start Date:*</div>
            <div class='dateSelectDIV'>
                <input type='text' class="datepicker" name='agendaStartDate' value='<?php echo parseDateFromDateTime($item[START_DATE]); ?>'>
            </div>
            <div class='formLabel'>Start Time:*</div>
            <div class='timeSelectDIV'>
                <div class='selectDIV selectTimeDIV'>
                    <select name='agendaStartTime' value='<?php echo parseTimeMinusMeridianFromDateTime($item[START_TIME]); ?>'>
                    <?php
                    
                        $selected = parseTimeMinusMeridianFromDateTime($item[START_TIME]);
                        echo get12HoursForSelect($selected);
                        
                    ?>
                    </select>
                </div>
                <div class='selectDIV selectMeridianDIV'>
                    <select name='agendaStartMeridian' value='<?php parseMeridianFromDateTime($item[START_TIME]); ?>'>
                    <?php
                    
                        $selected = parseMeridianFromDateTime($item[START_TIME]);
                        echo getMeridiansForSelect($selected);
                        
                    ?>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class='formLabel'>End Date:*</div>
            <div class='dateSelectDIV'>
                <input type='text' class="datepicker" name='agendaEndDate' value='<?php echo parseDateFromDateTime($item[END_DATE]); ?>'>
            </div>
            <div class='formLabel'>End Time:*</div>
            <div class='timeSelectDIV'>
                <div class='selectDIV selectTimeDIV'>
                    <select name='agendaEndTime' value='<?php parseTimeMinusMeridianFromDateTime($item[END_TIME]); ?>'>
                    <?php
                    
                        $selected = parseTimeMinusMeridianFromDateTime($item[END_TIME]);
                        echo get12HoursForSelect($selected);
                        
                    ?>
                    </select>
                </div>
                <div class='selectDIV selectMeridianDIV'>
                    <select name='agendaEndMeridian' value='<?php echo parseMeridianFromDateTime($item[END_TIME]); ?>'>
                    <?php
                    
                        $selected = parseMeridianFromDateTime($item[END_TIME]);
                        echo getMeridiansForSelect($selected);
                        
                    ?>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class='formLabel'>Assignment:</div>
            <div id='assignmentDIV' class='shortUsersDIV'>
        <?php
        
        foreach ($item[ASSIGNMENT] as $id)
        {
            if ($id != 'axoneron')
            {
                $u = getUserById($id, $connection);
            
                if ($u)
                {
                    echo "
                    <div id='shortUser_" . $id . "' class='conversationDIV2'>";

                    if ($u['imageURL'])
                    {
                        echo "
                        <img id='image_" . $id . "' class='accountImage' onload='cleanImage(" . $id . ");' src='" . USER_IMAGES_PATH . $u['imageURL'] . "' style='display:none;'/>";
                    }

                    echo "
                        <div id='imageDIV_" . $id . "' class='shortUserImageDIV'";

                    if ($u['imageURL'])
                    {
                        echo " style='background-image: url(\"" . USER_IMAGES_PATH . $u['imageURL'] . "\");'";
                    }

                    echo ">&nbsp;</div>
                        <div class='shortConvoDataDIV1'>
                            <div class='shortUserNameDIV'>" . $u['first'] . " " . $u['last'] . "</div>
                        </div>
                        <div id='removeAssignment_" . $id . "' class='trash assignmentTrash fa'><label for='assignmentTrash'>&#xf1f8;</label></div>
                    </div>";
                }
            }
            else
            {
                echo "
                    <div id='shortUser_axoneron' class='conversationDIV2'>
                        <div class='shortUserImageDIV'>&nbsp;</div>
                        <div class='shortConvoDataDIV1'>
                            <div class='shortUserNameDIV'>Axoneron</div>
                        </div>
                        <div id='removeAssignment_axoneron' class='trash assignmentTrash fa'><label for='assignmentTrash'>&#xf1f8;</label></div>
                    </div>";
            }
            
            
            
        }
        
        ?>
            </div>
            <div id='addAssignmentDIV' class='addItem far'>&#xf0fe;</div>
            
            <div id='usersListDIV' style='display:none;'>
                <div class='formLabel'>Add Assignment:</div>
                <div class='selectDIV'>
                    <select id='userSelect'>
                        <option id='placeholderOption' value="" disabled selected hidden>Select...</option>
                        <option id='addAssignment_axoneron' value='axoneron'>Axoneron</option>
                    
                <?php
                
                $allUsers = getAllUsers($connection);
                
                foreach ($allUsers as $u)
                {
                    echo "
                        <option id='addAssignment_" . $u['id'] . "'";
                    echo " imageURL='" . $u['imageURL'] . "'";
                    echo " first='" . $u['first'] . "'";
                    echo " last='" . $u['last'] . "'";
                    echo " value='" . $u['id'] . "'";
                    echo ">" . $u['first'] . " " . $u['last'] . "</option>";
                }
                    
                ?>
                    </select>
                </div>
            </div>
            
        </div>  
        <div>
            <div class='formLabel'>Chairperson(s):</div>
            <div class='copyStyle4'>(please separate with commas)</div>
            <div><input class='agendaFormSmall' type='text' name='agendaChair' value='<?php echo implode(",", $item[CHAIR]); ?>'></div>
        </div>   
        <div>
            <div class='formLabel'>Presenter(s):</div>
            <div class='copyStyle4'>(please separate with commas)</div>
            <div><input class='agendaFormSmall' type='text' name='agendaPresenters' value='<?php echo implode(",", $item[PRESENTERS]); ?>'></div>
        </div>  
        <div>
            <div class='formLabel'>Session Name:</div>
            <div><input class='agendaFormSmall' type='text' name='agendaSessionName' value='<?php echo $item[SESSION_NAME]; ?>'></div>
        </div> 
        <div>
            <div class='formLabel'>Footnotes:</div>
            <div><textarea class='agendaFormSmall' type='text' name='agendaFootnotes' value='<?php echo $item[FOOTNOTES]; ?>'><?php echo $item[FOOTNOTES]; ?></textarea></div>
        </div>
        
        
        <input type='number' name='congressID' hidden='true' value='<?php echo $congress['id']; ?>'/>
        <input type='number' name='itemID' hidden='true' value='<?php echo $item['id']; ?>'/>
        <input id='agendaAssignment' type='text' name='agendaAssignment' hidden='true' value='<?php echo implode(",", $item[ASSIGNMENT]); ?>'/>
        
        <div><input class='extraTopMargin' type='submit' name='<?php echo POST_MODIFY_AGENDA_ITEM; ?>' value='Submit'/></div>
        </div>
    </div>
    
</form>

</div>

<script>
    
    window.onload = cleanAllImages();
    
    function cleanAllImages()
    {
        $('.accountImage').each(function()
        {
            var imageID = $(this).attr('id');
            var id = imageID.replace("image_", "");
            cleanImage(id);
        });
    }
    
    function cleanImage(id)
    {
        var image = document.getElementById('image_' + id);
        EXIF.getData(image, function()
        {
            var orientation = EXIF.getTag(this, "Orientation");
            
            if(orientation == 6)
            {
                $('#imageDIV_' + id).addClass("rotate90");
            }
            else if(orientation == 8)
            {
                $('#imageDIV_' + id).addClass("rotate270");
            }
            else if(orientation == 3)
            {
                $('#imageDIV_' + id).addClass("rotate180");
            }
        });
    }
    
    $( "#addAssignmentDIV" ).click(function()
    {
        if ($("#usersListDIV").is(":hidden"))
        {
            $("#usersListDIV").show();
            $( "#addAssignmentDIV" ).html("&#xf146;");
        }
        else
        {
            $("#usersListDIV").hide();
            $( "#addAssignmentDIV" ).html("&#xf0fe;");
        }
        
    });
    
    $("#userSelect").change(function()
    {
        var id = $(this).val();
        var assignments = $("#agendaAssignment").val().split(",");
        var valid = true;
        for (var i = 0 ; i < assignments.length ; i++)
        {
            if (assignments[i] == id)
            {
                valid = false;
            }
        }
        if (valid)
        {
            if (id != "axoneron")
            {
                var first = $("#addAssignment_" + id).attr("first");
                var last = $("#addAssignment_" + id).attr("last");
                var imageURL = $("#addAssignment_" + id).attr("imageURL");
                var html = "<div id='shortUser_" + id + "' class='conversationDIV2'>";
                if (imageURL !== "" && imageURL !== null)
                {
                    html += "<img id='image_" + id + "' class='accountImage' onload='cleanImage(" + id + ");' src='<?php echo USER_IMAGES_PATH; ?>" + imageURL + "' style='display:none;'/>";
                }
                html += "<div id='imageDIV_" + id + "' class='shortUserImageDIV'";
                if (imageURL !== "" && imageURL !== null)
                {
                    html += " style='background-image: url(\"<?php echo USER_IMAGES_PATH; ?>" + imageURL + "\");'";
                }
                html += ">&nbsp;</div>";
                html += "<div class='shortConvoDataDIV1'>";
                html += "<div class='shortUserNameDIV'>" + first + " " + last + "</div>";
                html += "</div>";
                html += "<div id='removeAssignment_" + id + "' class='trash assignmentTrash fa'><label for='assignmentTrash'>&#xf1f8;</label></div>";
                html += "</div>";
            }
            else
            {
                var html = "<div id='shortUser_axoneron' class='conversationDIV2'>";
                html += "<div class='shortUserImageDIV'>&nbsp;</div>";
                html += "<div class='shortConvoDataDIV1'>";
                html += "<div class='shortUserNameDIV'>Axoneron</div>";
                html += "</div>";
                html += "<div id='removeAssignment_axoneron' class='trash assignmentTrash fa'><label for='assignmentTrash'>&#xf1f8;</label></div>";
                html += "</div>";
            }
            
            $('#assignmentDIV').html($('#assignmentDIV').html() + html);
            assignments = assignments.toString();
            if (assignments.length > 0)
            {
                assignments += ",";
            }
            assignments += id;
            $("#agendaAssignment").val(assignments);
        }
        else
        {
            window.alert("This user has already been assigned to this item");
        }
        $('.assignmentTrash').click(function()
        {
            var id = $(this).prop('id').replace("removeAssignment_", "");
            removeAssignment(id);
        });
        $("#usersListDIV").hide();
        $( "#addAssignmentDIV" ).html("&#xf0fe;");
    });
    
    $('.assignmentTrash').click(function()
    {
        var id = $(this).prop('id').replace("removeAssignment_", "");
        removeAssignment(id);
    });
    
    function removeAssignment(id)
    {
        var oldAssignments = $("#agendaAssignment").val().split(",");
        var newAssignments = [];
        for (var i = 0 ; i < oldAssignments.length ; i++)
        {
            if (oldAssignments[i] != id)
            {
                newAssignments.push(oldAssignments[i]);
            }
        }
        var assignments = newAssignments.toString();
        $("#agendaAssignment").val(assignments);
        $("#shortUser_" + id).remove();
    }
    
    //Validate function
    $(document).ready(function()
    {
        //window.alert("we have successfully called this function");
        $('#agendaItemModify').validate(
        {
            rules:
            {
                agendaTitle:
                {
                    required: true,
                    maxlength: 128
                },
                agendaSubTitle:
                {
                    required: false,
                    maxlength: 64
                },
                agendaLocation:
                {
                    required: false,
                    maxlength: 128
                },
                agendaType:
                {
                    required: true,
                    maxlength: 32
                },
                agendaPriority:
                {
                    required: false,
                    digits: true
                },
                agendaCategory:
                {
                    required: false,
                    maxlength: 32
                },
                agendaStartDate:
                {
                    required: true,
                    date: true
                },
                agendaStartTime:
                {
                    required: true
                },
                agendaStartMeridian:
                {
                    required: true
                },
                agendaEndDate:
                {
                    required: true,
                    date: true
                },
                agendaEndTime:
                {
                    required: true
                },
                agendaEndMeridian:
                {
                    required: true
                },
                agendaChair:
                {
                    required: false,
                    maxlength: 128
                },
                agendaPresenters:
                {
                    required: false,
                    maxlength: 128
                },
                agendaSessionName:
                {
                    required: false,
                    maxlength: 256
                },
                agendaFootnotes:
                {
                    required: false,
                    maxlength: 512
                },
                agendaAssignment:
                {
                    required: false,
                    maxlength: 256
                }
            },
            messages:
            {

            }
        });
    });
    
</script>