<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Get Attendence - Version 1.0 ************************
 *
 * This script looks and returns whether or not a congress exists.
 *
 * 1. The script first checks to make sure congress isset and assigns it to congressID.
 *
 * 2. We then call the function getCongressById by passing through congressID and assigning it to a new variable.
 *
 * 3. If we find a congress with a matching ID, we then echo out the result.
 *
 * NOTES: all data is returned in JSON format. Other formats (eg. xml) may be supported
 * at a later date, but JSON will be the default. When other formats are supported in the future,
 * format can be specified by including a POST variable definition for "format."
 * If at any point in time a field, step, process is entered/done incorrectly a corresponding error message will display
 * so that the user can figure out how to fix it.
 *
 */

$user = validateUserForUnity($connection, $format);
if ($user)
{
    $congress = validateCongress($connection, $format);
    if ($congress)
    {
        $attendance = getCongressAttendance($user, $congress['id'], $connection);
        if ($attendance == 1 || $attendance == 0 || $attendance == -1)
        {
            $congress['attendance'] = $attendance;
            echo packageDataForUnity($format, $congress, "selectedCongress");
        }
        else
        {
            echo packageTypeErrorForUnity($format, "Invalid congress attendance value submitted.", 1);
        }
    }
}