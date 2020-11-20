
<div id='congressContainerDIV' class='mainDIV'>

<?php

if ($user['level'] < 2)
{
    echo "Unauthorized Access!";
    exit();
}

$hospRoom = 0;
$times = 1;
$congress = isset($_GET['congress']) ? getCongressById($_GET['congress'], $connection) : 0;
if ($congress)
{
    $hospRoom = isset($_GET['hospRoomID']) ? getHospitalityRoomByID($_GET['hospRoomID'], $congress['id'], $connection) : 0;
}

echo getBGDiv();
echo getHeaderDiv($user);

?>

<div class='contentDIV'>
    <div class='pageTitleDIV whiteBG'>Modify Meeting Room Details</div>

<?php

if ($hospRoom)
{
    $name = htmlentities($hospRoom['name'], ENT_QUOTES);
    $location = htmlentities($hospRoom['location'], ENT_QUOTES);
    $size = htmlentities($hospRoom['size'], ENT_QUOTES);
    echo getLongFormatCongressBlock($congress);
    echo "
    <div id='modifyHospRoomFormDIV'>
        <form name='hospRoomModify' id='hospRoomModify' method='post' action='" . HOME . "'>
            <div>
                <div class='hospRoomFormInput'>
                    <div class='formLabel'>Room Name:*</div>
                    <div><input type='text' name='newHospRoomName' value='" . $name . "'></div>
                </div>
                <div class='hospRoomFormInput'>
                    <div class='formLabel'>Room Location:</div>
                    <div><input type='text' name='newHospRoomLocation' value='" . $location . "'></div>
                </div>
                <div class='hospRoomFormInput'>
                    <div class='formLabel'>Room Dimensions:</div>
                    <div><input type='text' name='newHospRoomSize' value='" . $size . "'></div>
                </div>
                <div class='hospRoomFormInput'>
                    <div class='formLabel'>Availability:</div>
                    <div id='availabilityContainerDIV'>";
    foreach ($hospRoom['timeSlots'] as $timeSlot)
    {
        //debug($timeSlot);
        $date = parseDateFromDateTime($timeSlot['start']);
        $start = parseTimeMinusMeridianFromDateTime($timeSlot['start']);
        $startMeridian = parseMeridianFromDateTime($timeSlot['start']);
        $end = parseTimeMinusMeridianFromDateTime($timeSlot['end']);
        $endMeridian = parseMeridianFromDateTime($timeSlot['end']);
        echo "
                        <div id='availabilityDIV_" . $times . "' class='availabilityDIV'>
                            <!-- <div class='subTitleDIV'>Time Slot " . $times . ":</div> -->
                            <div class='availabilityRemove trash timeSlotTrash fa' onclick='timeSlotRemoveClickHandler(" . $times . ");'>&#xf1f8;</div>
                            <div>
                                <div class='formLabel'>Date:</div>
                                <div class='dateSelectDIV'>
                                    <input id='startDate_" . $times . "' type='text' class='datepicker timeSlotDate' name='startDate" . $times . "'";
        echo " value='" . $date . "'/>
                                </div>
                                <div>
                                    <div class='formLabel'>Start:</div>
                                    <div class='timeSelectDIV'>
                                        <div class='selectDIV selectTimeDIV'>
                                            <select id='startTime_" . $times . "' class='timeSlotTime' name='startTime" . $times . "'";
        echo " value='" . $start . "'>";
        echo get12HoursForSelect($start);
        echo "
                                            </select>
                                        </div>
                                        <div class='selectDIV selectMeridianDIV'>
                                            <select id='startMeridian_" . $times . "' class='timeSlotMeridian' name='startMeridian" . $times . "'";
        echo " value='" . $startMeridian . "'>";
        echo getMeridiansForSelect($startMeridian);
        echo "
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class='formLabel'>End:</div>
                                    <div class='timeSelectDIV'>
                                        <div class='selectDIV selectTimeDIV'>
                                            <select id='endTime_" . $times . "' class='timeSlotTime' name='endTime" . $times . "'";
        echo " value='" . $end . "'>";
        echo get12HoursForSelect($end);
        echo "
                                            </select>
                                        </div>
                                        <div class='selectDIV selectMeridianDIV'>
                                            <select id='endMeridian_" . $times . "' class='timeSlotMeridian' name='endMeridian" . $times . "'";
        echo " value='" . $endMeridian . "'>";
        echo getMeridiansForSelect($endMeridian);
        echo "
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>";
        $times++;
    }
    echo "
                    </div>
                    <div id='addHospTimeDIV' class='addItem far'>&#xf0fe;</div>
                </div>
            </div>
            <input name='congressID' hidden='true' type='number' value='" . $congress['id'] . "'/>
            <input name='hospRoomID' hidden='true' type='number' value='" . $hospRoom['id'] . "'/>
            <input id='numTimeSlotsInput' name='numTimeSlots' hidden='true' type='number' value='" . count($hospRoom['timeSlots']) . "'/>
            <div>
                <div><input type='submit' name='" . POST_MODIFY_HOSP_ROOM . "' value='SUBMIT'/></div>
            </div>
        </form>
    </div>";
}
else
{
    echo "
    <div class='emptyListDIV'>The specified hospitality room was not located.</div>";
}



?>
    
</div>
    
</div>

<script>
    
    var times = <?php echo $times; ?>
    
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
    
    //Validate function
    $(document).ready(function()
    {
        //window.alert("we have successfully called this function");
        $("#hospRoomModify").validate(
        {
            rules:
            {
                newHospRoomName:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 32,
                    letterswithbasicpunc: true
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

</script>

