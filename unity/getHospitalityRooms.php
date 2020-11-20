<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Get Hospitality Rooms - Version 1.2 ************************
 *
 * This script checks for hospitality rooms and then echos out the result.
 *
 * 1. The script first checks to make sure the congress parameter isset, which is a number, and then assigns it to congressID.
 *
 * 2. We then pass the congressID variable into the getCongressById function and assign that to a new variable.
 *
 * 3. If a congress exists, we then call the function getAllHospitalityRoomsForCongress by passing in the congress.
 *
 * 4. We assign the result to a new variable, hospitalityRooms, and echo out the result.
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
    $hospitalityRooms = getAllHospitalityRoomsForCongress($congress, $connection);
    if ($hospitalityRooms)
    {
        echo packageDataForUnity($format, $hospitalityRooms, "hospitalityRooms");
    }
    else
    {
        echo packageTypeErrorForUnity($format, "There are currently no hospitality rooms for the congress.", 1);
    }
}