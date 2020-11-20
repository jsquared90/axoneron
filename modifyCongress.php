
<div id='congressContainerDIV' class='mainDIV'>

<?php

if ($user['level'] < 2)
{
    echo "Unauthorized Access!";
    exit();
}

$hotels = getHotelsWithCongress($congress, $connection);
$allHotels = getAllHotels($connection);

$hospitalityRooms = getAllHospitalityRoomsForCongress($congress, $connection);
$agenda = getAgendaFromDatabase($congress, $connection);

echo getBGDiv();
echo getHeaderDiv($user);

?>

<div class='contentDIV'>
    <div class='pageTitleDIV whiteBG'>Current Congress</div>
    <?php echo getLongFormatCongressBlockWithEditLink($congress); ?>
    <div class='pageTitleDIV'>Hotels</div>

<?php

if ($hotels)
{
    
    foreach ($hotels as $hotel)
    {
        echo "
    <div class='hotelBlockDIV'>    
        <form class='removalForm hotelRemovalForm' name='hotelRemoveFromCongress' method='post' action='" . HOME . "'>
            <div class='trash hotelTrash fa'><label for='hotelRemove" . $hotel['id'] . "'>&#xf1f8;</label></div>
            <input name='congressID' hidden='true' type='number' value='" . $congress['id'] . "'/>
            <input name='hotelID' hidden='true' type='number' value='" . $hotel['id'] . "'/>
            <input id='hotelRemove" . $hotel['id'] . "' type='submit' name='" . POST_REMOVE_HOTEL_FROM_CONGRESS . "' value=''/>
        </form>
        <a href='". HOME . "?action=" . POST_MODIFY_HOTEL . "&hotel=" . $hotel['id'] . "'>
            <form class='editForm hotelEditForm' name='hotelEdit'>
                <div class='edit hotelEdit fa'><label for='hotelEdit" . $hotel['id'] . "'>&#xf044;</label></div>
            </form>
        </a>
        <div class='shortHotelName'>" . $hotel['name'] . "</div>
        <div class='shortHotelAddress1'>" . $hotel['address1'] . "</div>
        <div class='shortHotelAddress2'>" . $hotel['address2'] . "</div>
        <div class='shortHotelAddress3'>" . $hotel['city'] . ", " . $hotel['state'] . " " . $hotel['zip'] . "</div>";

        echo "
    </div>
    
            ";
    }
    
}
else
{
    echo "
    <div class='emptyListDIV'>There are no hotels currently affiliated with this congress.</div>";
}

?>
    <div id='addHotelDIV' class='addItem far'>&#xf0fe;</div>

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
                    <div class='formLabel'>Or Choose from Existing:</div>
                    <div class='selectDIV'>
                        <select id='hotelList' name='hotelList'>
                            <option id='placeholderOption' value="" disabled selected hidden>Select...</option>
                            <option id='deselectOption' value="">Deselect</option>
                            <?php

                                foreach ($allHotels as $hotel)
                                {
                                    $alreadyAdded = false;
                                    foreach ($hotels as $addedHotel)
                                    {
                                        if ($hotel['id'] == $addedHotel['id'])
                                        {
                                            $alreadyAdded = true;
                                        }
                                    }
                                    if (!$alreadyAdded)
                                    {
                                        echo "
                            <option value='" . $hotel['id'] . "'>" . $hotel['name'] . "</option>";
                                    }
                                }

                            ?>
                        </select>
                    </div>
                </div>
                <input name='congressID' hidden='true' type='number' value='<?php echo $congress['id']; ?>'/>
                <div>
                    <div><input type='submit' name='<?php echo POST_ADD_HOTEL_TO_CONGRESS; ?>' value='ADD'/></div>
                </div>
            </div>

        </form>
    </div>
    <div class='pageTitleDIV'>Meeting Rooms</div>

<?php

if ($hospitalityRooms)
{
    
    foreach ($hospitalityRooms as $room)
    {
        echo "
    <div class='hospRoomBlockDIV modCongressHosp'>
        <form class='removalForm hospRoomRemovalForm' name='hospRoomRemoveFromCongress' onsubmit='confirmHospRoomDelete();' method='post' action='" . HOME . "'>
            <div class='trash hotelTrash fa'><label for='hospRoomRemove" . $room['id'] . "'>&#xf1f8;</label></div>
            <input name='congressID' hidden='true' type='number' value='" . $congress['id'] . "'/>
            <input name='hospRoomID' hidden='true' type='number' value='" . $room['id'] . "'/>
            <input id='hospRoomRemove" . $room['id'] . "' type='submit' name='" . POST_REMOVE_HOSP_ROOM_FROM_CONGRESS . "' value=''/>
        </form>
        <a href='" . HOME . "?page=" . POST_MODIFY_HOSP_ROOM . "&congress=" . $congress['id'] . "&hospRoomID=" . $room['id'] . "'>
            <form class='editForm hospRoomEditForm' name='hospRoomEdit' id='hospRoomEdit'>
                <div class='edit hospRoomEdit fa'><label for='hospRoomEdit'>&#xf044;</label></div>
            </form>
        </a>
        <div class='shortHospRoomName'>" . $room['name'] . "</div>
        <div class='shortHospRoomLocation'>" . $room['location'] . "</div>
        <div class='shortHospRoomSize'>" . $room['size'] . "</div>
        <div class='subTitleDIV hospRoomAvailListDIV'><div>Room Availability:</div>";

            foreach ($room['timeSlots'] as $timeSlot)
            {
                echo "
            <div class='shortHospRoomTime'>" . format1ForSingleDateTimeDisplay($timeSlot['start'],$timeSlot['start'],$timeSlot['end']) . "</div>";
            }

        echo "    
        </div>
    </div>
    
            ";
    }
    
}
else
{
    echo "
    <div class='emptyListDIV'>There are no meeting rooms currently affiliated with this congress.</div>";
}

?>

    <div id='addHospRoomDIV' class='addItem far'>&#xf0fe;</div>

    <div id='addHospRoomFormDIV' style='display:none;'>
        <form name='hospRoomAdd' id='hospRoomAdd' method='post' action='<?php echo HOME; ?>'>

            <div>
                <div class='hospRoomFormInput'>
                    <div class='formLabel'>Room Name:*</div>
                    <div><input type='text' name='newHospRoomName' ></div>
                </div>
                <div class='hospRoomFormInput'>
                    <div class='formLabel'>Room Location:</div>
                    <div><input type='text' name='newHospRoomLocation' placeholder='eg. NE corner of booth'></div>
                </div>
                <div class='hospRoomFormInput'>
                    <div class='formLabel'>Room Dimensions:</div>
                    <div><input type='text' name='newHospRoomSize' placeholder="eg. 15'x15'x10'"></div>
                </div>
                <div class='hospRoomFormInput'>
                    <div class='formLabel'>Availability: <span class='footnote1'>(click below to add time slots)</span></div>
                    <div id="availabilityContainerDIV">&nbsp;</div>
                    <div id='addHospTimeDIV' class='addItem far'>&#xf0fe;</div>
                </div>
                <input name='congressID' hidden='true' type='number' value='<?php echo $congress['id']; ?>'/>
                <input id="numTimeSlotsInput" name='numTimeSlots' hidden='true' type='number'/>
                <div>
                    <div><input type='submit' name='<?php echo POST_ADD_HOSP_ROOM_TO_CONGRESS; ?>' value='ADD'/></div>
                </div>
            </div>

        </form>
    </div>

    <div class='pageTitleDIV'>Speaker BIOS</div>
    
    <?php
    
    if ($congress['bios'] != "")
    {
        $bios = getBios($congress, $connection);
        foreach ($bios as $bio)
        {
            $identifier = $bio['first'] . "_" . $bio['last'];
            $cleanID = str_replace(" ", "_", $identifier);
            echo "
    <div class='bioBlockDIV modCongressBio'>
        <form class='removalForm bioRemovalForm' name='bioRemoveFromCongress' onsubmit='confirmSpeakerBioDelete();' method='post' action='" . HOME . "'>
            <div class='trash bioTrash fa'><label for='bioRemove_" . $cleanID . "'>&#xf1f8;</label></div>
            <input name='congressID' hidden='true' type='number' value='" . $congress['id'] . "'/>
            <input name='bioName' hidden='true' type='text' value='" . $identifier . "'/>
            <input id='bioRemove_" . $cleanID . "' type='submit' name='" . POST_REMOVE_BIO . "' value=''/>
        </form>
        <a href='". HOME . "?page=" . POST_MODIFY_BIO . "&congress=" . $congress['id'] . "&bio=" . $identifier . "'>
            <form class='editForm bioEditForm' name='bioEdit'>
                <div class='edit bioEdit fa'><label for='bioEdit" . $identifier . "'>&#xf044;</label></div>
            </form>
        </a>
        <div class='shortBioName'>
            <a href='" . $bio['url'] . "'>" . $bio['first'] . " " . $bio['last'] . "</a>
        <div class='copyStyle4'>Click speaker name to view</div>
        </div>
    </div>
    
            ";
        }
    }
    
    ?>
    
    <div id='addBioDIV' class='addItem far'>&#xf0fe;</div>
    
    <div id='addBioFormDIV' style='display:none;'>
        <form name='bioAdd' id='bioAdd' method='post' action='<?php echo HOME; ?>' enctype='multipart/form-data'>
            <div>
                <div class='bioFormInput'>
                    <div class='formLabel'>Speaker First Name:*</div>
                    <div><input type='text' name='bioFirstName' ></div>
                </div>
                <div class='bioFormInput'>
                    <div class='formLabel'>Speaker Last Name:*</div>
                    <div><input type='text' name='bioLastName' ></div>
                </div>
            </div>
            <div><input id='bioFile' class='button' type='file' name='bioFile' accept='.pdf'/></div>
            <div id='bioAreaDIV2' style='display:none;'>
                <div class='bioFileName'></div>
                <div class='edit bioEdit fa'><label id='bioFileLabel1' for='bioFile'>&#xf044;</label></div>
            </div>
            <div id='bioAreaDIV3'>
                <label id='bioFileLabel2' class='button' for='bioFile'>Choose File</label>
            </div>
            <input name='congressID' hidden='true' type='number' value='<?php echo $congress['id']; ?>'/>
            <div id='bioSubmitDIV' style="display:none;">
                <div><input type='submit' name='<?php echo POST_ADD_SPEAKER_BIO; ?>' value='SUBMIT'/></div>
            </div>
        </form>
    </div>
    

    <div class='pageTitleDIV'>Agenda</div>

<?php

if ($agenda)
{
    foreach ($agenda as $item)
    {
        echo getLongFormatAgendaItemWithEdit($item, $connection);
    }
    
    echo "
    <div id='agendaReplaceDIV' class='extraTopMargin'>
        <form name='agendaReplace' method='post' action='" . HOME . "' enctype='multipart/form-data'>
            <input id='agendaReplaceFile' type='file' name='agendaReplaceFile' accept='.xls,.xlsx'/>
            <input name='congressID' hidden='true' type='number' value='" . $congress['id'] . "'/>
            <div id='agendaReplaceDIV2' style='display:none;'>
                <div class='agendaReplaceFile'></div>
                <div class='edit agendaReplace fa'>
                    <label id='agendaReplaceLabel1' for='agendaReplaceFile'>&#xf044;</label>
                </div>
            </div>
            <div id='agendaReplaceDIV3'>
                <label id='agendaReplaceLabel2' class='button' for='agendaReplaceFile'>Replace Current</label>
            </div>
            <div id='agendaReplaceDIV4' style='display:none;'>
                <div><input type='submit' name='" . POST_REPLACE_AGENDA . "' value='UPLOAD'/></div>
            </div>
        </form>
    </div>
    <div id='agendaAddDIV' class='extraTopMargin'>
        <form name='agendaAdd' method='post' action='" . HOME . "' enctype='multipart/form-data'>
            <input id='agendaAddFile' type='file' name='agendaAddFile' accept='.xls,.xlsx'/>
            <input name='congressID' hidden='true' type='number' value='" . $congress['id'] . "'/>
            <div id='agendaAddDIV2' style='display:none;'>
                <div class='agendaAddFile'></div>
                <div class='edit agendaAdd fa'>
                    <label id='agendaAddLabel1' for='agendaAddFile'>&#xf044;</label>
                </div>
            </div>
            <div id='agendaAddDIV3'>
                <label id='agendaAddLabel2' class='button' for='agendaAddFile'>Add To Current</label>
            </div>
            <div id='agendaAddDIV4' style='display:none;'>
                <div><input type='submit' name='" . POST_ADD_AGENDA . "' value='UPLOAD'/></div>
            </div>
        </form>
    </div>
    <div id='agendaDownloadDIV' class='extraTopMargin'>
        <form name='agendaDownload' method='post' action='" . HOME . "' enctype='multipart/form-data'>
            <input name='congressID' hidden='true' type='number' value='" . $congress['id'] . "'/>
            <div><input type='submit' name='" . POST_DOWNLOAD_AGENDA . "' value='Download Current'/></div>
        </form>
    </div>
    <script>
    
    $('#agendaReplaceLabel1,#agendaReplaceLabel2').click(function()
    {
        $('#agendaReplaceFile').change(function()
        {
            var fileName = $('#agendaReplaceFile')[0].files[0].name;
            $('.agendaReplaceFile').html(fileName);
            $('#agendaReplaceDIV2').show();
            $('#agendaReplaceDIV3').hide();
            $('#agendaReplaceDIV4').show();
            $('#agendaAddDIV').hide();
            $('#agendaDownloadDIV').hide();
        });
    });
    
    $('#agendaAddLabel1,#agendaAddLabel2').click(function()
    {
        $('#agendaAddFile').change(function()
        {
            var fileName = $('#agendaAddFile')[0].files[0].name;
            $('.agendaAddFile').html(fileName);
            $('#agendaAddDIV2').show();
            $('#agendaAddDIV3').hide();
            $('#agendaAddDIV4').show();
            $('#agendaReplaceDIV').hide();
            $('#agendaDownloadDIV').hide();
        });
    });
    
    </script>";
    
}
else
{
    echo "
    <div id='agendaAreaDIV'>
        <form name='agendaAdd' method='post' action='" . HOME . "' enctype='multipart/form-data'>
            <div><input id='agendaFile' class='button' type='file' name='agendaFile' accept='.xls,.xlsx'/></div>
            <div id='agendaAreaDIV2' style='display:none;'>
                <div class='agendaFileName'></div>
                <div class='edit agendaEdit fa'><label id='agendaFileLabel1' for='agendaFile'>&#xf044;</label></div>
            </div>
            <div id='agendaAreaDIV3'>
                <div id='agendaNameDIV' class='emptyListDIV'>There is not an agenda currently affiliated with this congress</div>
                <label id='agendaFileLabel2' class='button' for='agendaFile'>Choose File</label>
            </div>
            <input name='congressID' hidden='true' type='number' value='" . $congress['id'] . "'/>
            <div id='agendaUploadSubmitDIV' style='display:none;'>
                <div><input type='submit' name='" . POST_UPLOAD_AGENDA . "' value='UPLOAD'/></div>
            </div>
        </form>
    </div>
    
    <script>
    
    $('#agendaFileLabel1,#agendaFileLabel2').click(function()
    {
        $('#agendaFile').change(function()
        {
            var fileName = $('#agendaFile')[0].files[0].name;
            $('.agendaFileName').html(fileName);
            $('#agendaAreaDIV3').hide();
            $('#agendaAreaDIV2').show();
            $('#agendaUploadSubmitDIV').show();
        });
    });
    
    </script>";
}

?>
    
</div>
</div>

<script>
    
    var times = 1;
    
    $( "#hotelList" ).change(function()
    {
        if ($(this).val() > 0)
        {
            $(".hotelFormInput").hide();
            $("input[name=newHotelName]").attr('required',false);
            $("input[name=newHotelAddress1]").attr('required',false);
            $("input[name=newHotelCity]").attr('required',false);
            $("input[name=newHotelState]").attr('required',false);
            $("input[name=newHotelZip]").attr('required',false);
        }
        else
        {
            $('#placeholderOption').prop("selected","selected");
            $('#deselectOption').prop("selected","");
            $(".hotelFormInput").show();
            $("input[name=newHotelName]").attr('required',true);
            $("input[name=newHotelAddress1]").attr('required',true);
            $("input[name=newHotelCity]").attr('required',true);
            $("input[name=newHotelState]").attr('required',true);
            $("input[name=newHotelZip]").attr('required',true);
        }
    });
    
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
    
    $( "#addHospRoomDIV" ).click(function()
    {
        if ($("#addHospRoomFormDIV").is(":hidden"))
        {
            $("#addHospRoomFormDIV").show();
            $( "#addHospRoomDIV" ).html("&#xf146;");
        }
        else
        {
            $("#addHospRoomFormDIV").hide();
            $( "#addHospRoomDIV" ).html("&#xf0fe;");
        }
        
    });
    
    $( "#addHospTimeDIV" ).click(function()
    {
        var code = "<div id='availabilityDIV_" + times + "' class='availabilityDIV'>";
        code += getAvailDivHTML(times,"","","","","");
        code += "</div>";
        
        $('#availabilityContainerDIV').append(code);
        $('#startDate_' + times).datepicker();
        $('#numTimeSlotsInput').val(times);
        times++;
    });
    
    $( "#addBioDIV" ).click(function()
    {
        if ($("#addBioFormDIV").is(":hidden"))
        {
            $("#addBioFormDIV").show();
            $( "#addBioDIV" ).html("&#xf146;");
        }
        else
        {
            $("#addBioFormDIV").hide();
            $( "#addBioDIV" ).html("&#xf0fe;");
        }
        
    });
    
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
    
    function timeSlotRemoveClickHandler(index)
    {
        //window.alert(index);
        var div = document.getElementById("availabilityDIV_" + index);
        div.parentNode.removeChild(div);
        
        for (var i = index+1 ; i < times ; i++)
        {
            var sD = $('#startDate_' + i).val();
            var sT = $('#startTime_' + i).val();
            var sM = $('#startMeridian_' + i).val();
            var eT = $('#endTime_' + i).val();
            var eM = $('#endMeridian_' + i).val();
            $('#availabilityDIV_' + i).html(getAvailDivHTML((i-1), sD, sT, sM, eT, eM));
            $('#availabilityDIV_' + i).attr('id', 'availabilityDIV_' + (i-1));
            
            $('#startDate_' + (i-1)).datepicker();
        }
        
        times--;
        $('#numTimeSlotsInput').val(times-1);
    }
    
    function getAvailDivHTML(index, sD, sT, sM, eT, eM)
    {
        
        var code = "<div class='availabilityRemove trash timeSlotTrash fa' onclick='timeSlotRemoveClickHandler(" + index + ");'>&#xf1f8;</div>";
        code += "<div>";
        code += "<div class='formLabel'>Date:</div>";
        code += "<div class='dateSelectDIV'>";
        code += "<input id='startDate_" + index + "' type='text' class='datepicker timeSlotDate' name='startDate" + index + "'";
        code += sD === "" ? "/></div>" : "value='" + sD + "'/></div>";
        code += "</div>";
        code += "<div>";
        code += "<div class='formLabel'>Start:</div>";
        code += "<div class='timeSelectDIV'>";
        code += "<div class='selectDIV selectTimeDIV'>";
        var startTime = sT === "" ? "9:00" : sT;
        code += "<select id='startTime_" + index + "' class='timeSlotTime' name='startTime" + index + "' value='" + startTime + "'>";
        code += get12HoursForSelect(startTime);
        code += "</select>";
        code += "</div>";
        code += "<div class='selectDIV selectMeridianDIV'>";
        var startMeridian = sM === "" ? "am" : sM;
        code += "<select id='startMeridian_" + index + "' class='timeSlotMeridian' name='startMeridian" + index + "' value='" + startMeridian + "'>";
        code += getMeridiansForSelect(startMeridian);
        code += "</select>";
        code += "</div>";
        code += "</div>";
        code += "</div>";
        code += "<div>";
        code += "<div class='formLabel'>End:</div>";
        code += "<div class='timeSelectDIV'>";
        code += "<div class='selectDIV selectTimeDIV'>";
        var endTime = eT === "" ? "5:00" : eT;
        code += "<select id='endTime_" + index + "' class='timeSlotTime' name='endTime" + index + "' value='" + endTime + "'>";
        code += get12HoursForSelect(endTime);
        code += "</select>";
        code += "</div>";
        code += "<div class='selectDIV selectMeridianDIV'>";
        var endMeridian = eM === "" ? "pm" : eM;
        code += "<select id='endMeridian_" + index + "' class='timeSlotMeridian' name='endMeridian" + index + "' value='" + endMeridian + "'>";
        code += getMeridiansForSelect(endMeridian);
        code += "</select>";
        code += "</div>";
        code += "</div>";
        code += "</div>";
        
        return code;
    }
    
    function get12HoursForSelect(selected)
    {
        var html = "";
        for (var i = 1 ; i < 13 ; i += 0.5)
        {
            var mins = i - Math.floor(i) !== 0 ? "30" : "00";
            var value = Math.floor(i) + ":" + mins;
            html += "<option value='" + value + "'";
            if (selected === value)
            {
                html += " selected";
            }
            html += ">" + value + "</option>";
        }

        return html;
    }
    
    function getMeridiansForSelect(selected)
    {
        var html = "<option value='am'";
        if (selected === "am")
        {
            html += " selected";
        }
        html += ">AM</option>";
        html += "<option value='pm'";
        if (selected === "pm")
        {
            html += " selected";
        }
        html += ">PM</option>";
        return html;
    }
    
    function confirmAgendaItemDelete()
    {
        if (confirm("Are you sure you would like to delete this agenda item?"))
        {
            return true;
        }
        else
        {
            event.preventDefault();
            return false;
        }
    }
    
    function confirmSpeakerBioDelete()
    {
        if (confirm("Are you sure you would like to remove this speaker bio?"))
        {
            return true;
        }
        else
        {
            event.preventDefault();
            return false;
        }
    }
    
    function confirmHospRoomDelete()
    {
        if (confirm("Are you sure you would like to remove this meeting room?"))
        {
            return true;
        }
        else
        {
            event.preventDefault();
            return false;
        }
    }
    
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
                    digits: true,
                    maxlength: 36
                }
            },
            messages:
            {

            }
        });
    });
    
    //Validate function
    $(document).ready(function()
    {
        //window.alert("we have successfully called this function");
        $("#hospRoomEdit").validate(
        {
            rules:
            {
                newHospRoomName:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 32
                },
                newHospRoomLocation:
                {
                    required: false,
                    maxlength: 32
                },
                newHospRoomSize:
                {
                    required: false,
                    maxlength: 32
                }
            },
            messages:
            {

            }
        });
    });
    
    //Validate function
    $(document).ready(function()
    {
        //window.alert("we have successfully called this function");
        $("#hospRoomAdd").validate(
        {
            rules:
            {
                newHospRoomName:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 32
                },
                newHospRoomLocation:
                {
                    required: false,
                    maxlength: 32
                },
                newHospRoomSize:
                {
                    required: false,
                    maxlength: 32
                }
            },
            messages:
            {

            }
        });
    });
    
    //Validate function
    $(document).ready(function()
    {
        //window.alert("we have successfully called this function");
        $("#bioAdd").validate(
        {
            rules:
            {
                bioFirstName:
                {
                    required: true,
                    maxlength: 20
                },
                bioLastName:
                {
                    required: true,
                    maxlength: 30
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

