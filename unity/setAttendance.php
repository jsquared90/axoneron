<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Set Attendance - Version 1.2 ************************
 *
 * This script allows a user to submit if they're attending a congress or not.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 * 
 * 2. The parameter congress is then checked to make sure it's set. This is assigned to $congressID.
 * 
 * 3. The function getCongressById is then called. $congressID and $connection are passed through.
 *    This is assigned to $congress.
 * 
 * 4. The parameter congressAckCC is then checked to make sure it's set. This is assigned to $attendanceValue.
 * 
 * 5. $attendanceValue must be either 0 or 1.
 * 
 * 6. The function setCongressAttendance is then called. $user and $connection are passed through.
 * 
 * 7. A new key, attendance, is inserted into the $congress array. It's value is the return of the function
 *    getCongressAttendance. $user, $congressID, and $connection are passed through.
 *    
 * 8. If successful the newly updated $congress is then echoed out.
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
        $result = setCongressAttendance($user, $connection);
        $queryError = queryError($result, POST_VIEW_CONGRESS, 0);
        if ($queryError["code"] >= 0)
        {
            echo packageTypeErrorForUnity($format, $queryError["message"], $queryError["code"]);
        }
        else
        {
            echo packageDataForUnity($format, $result['record'], "userRecord");
        }
    }
}