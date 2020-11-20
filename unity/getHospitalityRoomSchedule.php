<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Get Hospitality Room Schedule - Version 1.1 ************************
 *
 * This script gets a hospitality room schedule.
 *
 * 1. The script first checks to make sure the congress parameter isset, which is a number, and then assigns it to congressID.
 *
 * 2. We then pass the congressID variable into the getCongressById function and assign that to a new variable.
 *
 * 3. If a congress exists, we then check to make sure that the room parameter isset and assign that to a new variable. (roomID)
 *
 * 4. We then call the function getHospitalityRoomByID by passing through the variables roomID and congressID. This is assigned to a newly created variable, room.
 *
 * 5. If successful, we pass the room variable into the function getHospRoomSchedule and echo out the result.
 *
 * NOTES: all data is returned in JSON format. Other formats (eg. xml) may be supported
 * at a later date, but JSON will be the default. When other formats are supported in the future,
 * format can be specified by including a POST variable definition for "format."
 * If at any point in time a field, step, process is entered/done incorrectly a corresponding error message will display
 * so that the user can figure out how to fix it.
 *
 */

$congress = validateCongress($connection, $format);
if ($congress)
{
    $roomID = checkRoomID($connection, $format);
    if ($roomID)
    {
        $room = getHospitalityRoomByID($roomID, $congress['id'], $connection);
        if ($room)
        {
            $schedule = getHospRoomSchedule($room, $connection);
            if ($schedule)
            {
                echo packageDataForUnity($format, $schedule, "hospitalityRoomSchedule");
            }
            else
            {
                echo packageTypeErrorForUnity($format, "There is currently no hospitality room schedule.", 3);
            }
        }
        else
        {
            echo packageTypeErrorForUnity($format, "There are currently no hospitality rooms.", 2);
        }
    }
}