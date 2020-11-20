<div id='hospitalityContainerDIV' class='mainDIV'>

<?php

    echo getBGDiv();
    echo getHeaderDiv($user);
    
    $congress = 0;
    $selectedSegments = array();
    if (isset($_GET["congress"]))
    {
        $congress = getCongressById($_GET["congress"], $connection);
    }
    else if (isset($_POST["congressID"]))
    {
        $congress = getCongressById($_POST["congressID"], $connection);
    }
    
    if ($congress)
    {
        $room = 0;
        $booking = 0;
        if (isset($_GET["room"]))
        {
            $room = getHospitalityRoomByID($_GET["room"], $congress["id"], $connection);
            $title = "Meeting Room Schedule";
            $submit = POST_BOOK_HOSP_ROOM;
            $submitTitle = "Book";
        }
        else if (isset($_POST["roomID"]))
        {
            $room = getHospitalityRoomByID($_POST["roomID"], $congress["id"], $connection);
            $title = "Meeting Room Schedule";
            $submit = POST_BOOK_HOSP_ROOM;
            $submitTitle = "Book";
        }
        else if (isset($_GET['bookingID']))
        {
            $booking = getHospitalityBookingByID($_GET['bookingID'], $congress["id"], $connection);
            $room = $booking['room'];
            $title = "Modify Meeting Room Booking";
            $submit = POST_MODIFY_HOSP_BOOKING;
            $submitTitle = "Re-Book";
            //debug($booking);
        }
        
        echo "
    <div class='contentDIV'>
        <div class='pageTitleDIV whiteBG'>" . $title . "</div>";

        if ($room)
        {
        
            echo getLongFormatHospitalityRoom($room, $congress);
            
            
            echo "
        <div class='instructionsDIV'>Each slot is 15 minutes. Please select a consecutive block of times, then give your booking a name and submit. (form below)</div>
    
        <div class='scheduleDIV'>";
            
            $schedule = getHospRoomSchedule($room, $connection);
            
            if ($schedule)
            {
                echo "
            <div class='calendarDIV1'>
                <div class='caret leftArrow fa'>&#xf0d9;</div>
                <div class='calendarDIV2'>";
                
                $i = 0;
                
                if ($booking)
                {
                    foreach ($schedule as $day)
                    {
                        if (strtotime($day['date']) < strtotime($booking['date']))
                        {
                            $i++;
                        }
                    }
                }
                $startDate = $schedule[$i]['date'];
                
                for ($j = 0 ; $j < 5 ; $j++)
                {
                    $timestamp = strtotime($startDate . "+" . $j . " day");
                    $date = Date("n/d/y", $timestamp);
                    $state = '';
                    if ($j == 0)
                    {
                        $state = ' selected';
                    }
                    else if(!checkScheduleForDate($schedule, $date))
                    {
                        $state = ' disabled';
                    }
                    echo getDateBlockForCalendar($date, $state);
                }
                
                echo "
                </div>
                <div class='caret rightArrow fa'>&#xf0da;</div>
            </div>";
                
                $i = 1;
                $j = 0;
                foreach ($schedule as $day)
                {
                    $hidden = $j == $start ? "" : " style='display:none;'";
                    echo "
            <div class='hospCalendarDIV' date='" . $day['date'] . "'" . $hidden . ">";
                    foreach ($day["segments"] as $segment)
                    {
                        if ($segment)
                        {
                            if ($booking)
                            {
                                $bST = strtotime($booking['date'] . " " . $booking['startTime']);
                                $bET = strtotime($booking['date'] . " " . $booking['endTime']);
                                $sT = strtotime($segment['date'] . " " . $segment['startTime'] . "m");
                                $eT = strtotime($segment['date'] . " " . $segment['endTime'] . "m");
                                if ($bST <= $sT && $eT <= $bET)
                                {
                                    array_push($selectedSegments, $i);
                                    $availClass = 'segmentAvailable segmentSelected';
                                    $author = '';
                                }
                                else
                                {
                                    $availClass = $segment['available'] ? 'segmentAvailable' : 'segmentUnavailable';
                                    $author = $segment['author'] ? $segment['author'] : "";
                                }
                            }
                            else
                            {
                                $availClass = $segment['available'] ? 'segmentAvailable' : 'segmentUnavailable';
                                $author = $segment['author'] ? $segment['author'] : "";
                            }
                            echo "
                    <div id='segment_" . $i . "' class='hospRoomSegmentDIV " . $availClass . "' date='" . $segment['date'] . "' startTime='" , $segment['startTime'] . "m' endTime='" . $segment['endTime'] . "m'>
                        <div class='hospRoomSegmentStart'>" . $segment['startTime'] . "</div>";
                            if ($author != "")
                            {
                                echo "
                        <div class='hospRoomSegmentAuthor'>" . str_split($author['first'])[0] . " " . $author['last'] . "</div>";
                            }
                            echo "
                    </div>";
                        }
                        else
                        {
                            echo "
                    <div id='segment_" . $i . "' class='hospRoomSegmentDIV emptySegment'><div>BREAK</div></div>";
                        }
                        $i++;
                        
                    }
                    echo "
            </div>";
                    $j++;
                } 
                    
                echo "
            <div id='bookHospFormDIV'";
                
                if (!$booking)
                {
                    echo " style='display:none;'";
                }
                
                echo ">
                <form name='hospRoomBook' id='hospRoomBook' method='post' action='" . HOME . "'>
                    <div class='bookHospNameDIV'>
                        <div class='formLabel'>Please provide a title for the booking:*</div>
                        <div><input type='text' name='bookingName'";
                
                if ($booking)
                {
                    echo " value='" . htmlentities($booking['bookingName'], ENT_QUOTES) . "'";
                }
                
                echo "></div>
                    </div>
                    <div>
                        <div class='formLabel'>Special Requests:*</div>
                        <div><textarea rows='4' cols='50' name='openEnd' ";
                
                if (!$booking)
                {
                    echo " placeholder='eg. We will need AV Equipment for this meeting.'";
                }
                echo ">";
                
                if ($booking)
                {
                    echo htmlentities($booking['openEnd'], ENT_QUOTES);
                }
                
                echo "</textarea></div>
                    </div>
                    <input name='congressID' hidden='true' type='number' value='" . $congress['id'] . "'/>
                    <input name='roomID' hidden='true' type='text' value='" . $room['id'] . "'/>
                    <input id='dateInput' name='dateInput' hidden='true' type='text'";
                
                if ($booking)
                {
                    echo " value='" . $booking['date'] . "'";
                }
                
                echo "/>
                    <input id='startTimeInput' name='startTime' hidden='true' type='text'";
                
                if ($booking)
                {
                    echo " value='" . $booking['startTime'] . "'";
                }
                
                echo "/>
                    <input id='endTimeInput' name='endTime' hidden='true' type='text'";
                
                if ($booking)
                {
                    echo " value='" . $booking['endTime'] . "'";
                }
                
                echo "/>";
                
                if ($booking)
                {
                    echo "
                    <input id='bookingID' name='bookingID' hidden='true' type='text' value='" . $booking['id'] . "'/>";
                }
                
                echo "
                    <div>
                        <div><input type='submit' name='" . $submit . "' value='" . $submitTitle . "'/></div>
                    </div>
                </form>
            </div>";
                
            }
            else
            {
                echo "
            <div class='emptyListDIV'>There is not a schedule currently configured for this room.</div>";
            }
        }
        else
        {
            echo "
            <div class='emptyListDIV'>There are no hospitality rooms currently affiliated with that congress.</div>";
        }
        echo "
        </div>";
    }
    else
    {
        echo "
        <div class='hospitalityDetailDIV'>
            <div class='emptyListDIV'>There has been an error retrieving that congress.</div>
        </div>";
    }
    
?>

    </div>
</div>

<script>
    
    var selectedSegments =[<?php
    
    foreach($selectedSegments as $i)
    {
        echo "$('#segment_" . $i . "')";
        if (next($selectedSegments))
        {
            echo ",";
        }
    }
    
    ?>];
    var lastClickedSegment = 0;
    var availableDates = <?php
    
    echo "[";
    
    if ($congress && $schedule)
    {
        foreach ($schedule as $day)
        {
            echo "'" . $day['date'] . "'";
            if (next($schedule))
            {
                echo ",";
            }
        }
    }
    
    echo "];";
    
    ?>
    
    var selectedDate = availableDates.length > 0 ? availableDates[0] : 0;
        
    const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
        
    $(".leftArrow").click(function()
    {
        $(".dateBlock").each(function()
        {
            var date = $(this).attr('date');
            var rawDate = Date.parse(date);
            var ms = rawDate - <?php echo ONE_DAY; ?>;
            var nextDay = new Date(ms);
            if (nextDay.getHours() == 1)
            {
                // daylight savings. let's subtract an hour and reset next day
                ms -= <?php echo ONE_HOUR; ?>;
                var nextDay = new Date(ms);
            }
            var month = nextDay.getMonth() + 1;
            var year = nextDay.getFullYear().toString().slice(2, 4);
            var day = nextDay.getDate() < 10 ? "0" + nextDay.getDate() : nextDay.getDate();
            var dateString = month + "/" + day + "/" + year;
            $(this).attr('date', dateString);
            $(this).find('.dateBlockMonth').html(months[nextDay.getMonth()]);
            $(this).find('.dateBlockDate').html(nextDay.getDate());
            if (dateString === selectedDate)
            {
                $(this).addClass("selected");
            }
            else
            {
                $(this).removeClass("selected");
            }
            var disabled = true;
            for (var availableDate of availableDates)
            {
                if (dateString === availableDate)
                {
                    disabled = false;
                }
            }
            if (disabled)
            {
                $(this).addClass("disabled");
            }
            else
            {
                $(this).removeClass("disabled");
            }
        });
        
    });
    
    $(".rightArrow").click(function()
    {
        $(".dateBlock").each(function()
        {
            var date = $(this).attr('date');
            var rawDate = Date.parse(date);
            var ms = rawDate + <?php echo ONE_DAY; ?>;
            var nextDay = new Date(ms);
            if (nextDay.getHours() == 23)
            {
                // daylight savings. let's add an hour and reset next day
                ms += <?php echo ONE_HOUR; ?>;
                var nextDay = new Date(ms);
            }
            var month = nextDay.getMonth()+1;
            var year = nextDay.getFullYear().toString().slice(2, 4);
            var day = nextDay.getDate() < 10 ? "0" + nextDay.getDate() : nextDay.getDate();
            var dateString = month + "/" + day + "/" + year;
            $(this).attr('date', dateString);
            $(this).find('.dateBlockMonth').html(months[nextDay.getMonth()]);
            $(this).find('.dateBlockDate').html(nextDay.getDate());
            if (dateString === selectedDate)
            {
                $(this).addClass("selected");
            }
            else
            {
                $(this).removeClass("selected");
            }
            var disabled = true;
            for (var availableDate of availableDates)
            {
                if (dateString === availableDate)
                {
                    disabled = false;
                }
            }
            if (disabled)
            {
                $(this).addClass("disabled");
            }
            else
            {
                $(this).removeClass("disabled");
            }
        });
        
    });
    
    
    
    $(".dateBlock").click(function()
    {
        if (!$(this).hasClass('selected') && !$(this).hasClass('disabled'))
        {
            $(this).addClass('selected');
            selectedDate = $(this).attr('date');
            $(".dateBlock").each(function()
            {
                if (selectedDate !== $(this).attr('date') && !$(this).hasClass('disabled'))
                {
                   $(this).removeClass('selected');
                }
            });
            $(".dateBlock").each(function()
            {
                if (selectedDate !== $(this).attr('date') && !$(this).hasClass('disabled'))
                {
                   $(this).removeClass('selected');
                }
            });
            $(".hospCalendarDIV").each(function()
            {
                if (selectedDate !== $(this).attr('date'))
                {
                    $(this).hide();
                }
                else
                {
                    $(this).show();
                }
            });
            $('#bookHospFormDIV').hide();
        }
    });
    
    $(".hospRoomSegmentDIV").click(function()
    {
        if ($(this).hasClass("segmentAvailable"))
        {
            if ($(this).hasClass("segmentSelected"))
            {
                if (selectedSegments.length <= 1)
                {
                    clearCurrentSelection();
                    lastClickedSegment = 0;
                }
                else
                {
                    clearCurrentSelection();
                    singleClickSegment($(this));
                    lastClickedSegment = $(this);
                }
            }
            else
            {
                if (selectedSegments.length <= 1 && lastClickedSegment)
                {
                    var lastID = getID(lastClickedSegment);
                    var thisID = getID($(this));
                    if (lastID > thisID)
                    {
                        selectRange(thisID, lastID);
                    }
                    else
                    {
                        selectRange(lastID, thisID);
                    }
                    lastClickedSegment = $(this);
                }
                else
                {
                    clearCurrentSelection();
                    singleClickSegment($(this));
                    lastClickedSegment = $(this);
                }
            }
        }
        
    });
    
    function singleClickSegment(segment)
    {
        selectSegment(segment);
        var id = getID(segment);
        var date = $("#segment_" + id).attr('date');
        $('#dateInput').val(date);
        var startTime = $("#segment_" + id).attr('startTime');
        $('#startTimeInput').val(startTime);
        var endTime = $("#segment_" + id).attr('endTime');
        $('#endTimeInput').val(endTime);
    }
    
    function selectSegment(segment)
    {
        segment.addClass("segmentSelected");
        selectedSegments.push(segment);
        $('#bookHospFormDIV').show();
    }
    
    function deselectSegment(segment)
    {
        segment.removeClass("segmentSelected");
    }
    
    function getID(segment)
    {
        return parseInt(segment.attr("id").split("_")[1]);
    }
    
    function selectRange(firstID, lastID)
    {
        clearCurrentSelection();
        var last = firstID;
        for (var i = firstID ; i <= lastID ; i++)
        {
            if ($("#segment_" + i).hasClass("segmentUnavailable") || $("#segment_" + i).hasClass("emptySegment"))
            {
                lastClickedSegment = 0;
                break;
            }
            else
            {
                selectSegment($("#segment_" + i));
                last = i;
            }
        }
        var date = $("#segment_" + firstID).attr('date');
        $('#dateInput').val(date);
        var startTime = $("#segment_" + firstID).attr('startTime');
        $('#startTimeInput').val(startTime);
        var endTime = $("#segment_" + last).attr('endTime');
        $('#endTimeInput').val(endTime);
    }
    
    function clearCurrentSelection()
    {
        $('#bookHospFormDIV').hide();
        for (var i = 0 ; i < selectedSegments.length ; i++)
        {
            deselectSegment(selectedSegments[i]);
        }
        selectedSegments = [];
    }
    
    //Validate function
    $(document).ready(function()
    {
        //window.alert("we have successfully called this function");
        $("#hospRoomBook").validate(
        {
            rules:
            {
                bookingName:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 128
                },
                openEnd:
                {
                    required: false,
                    maxlength: 1024
                }
            },
            messages:
            {

            }
        });
    });
    
</script>

