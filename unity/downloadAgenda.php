<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Download Agenda - Version 1.1 ************************
 *
 * This script allows an admin to download the current agenda.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 * 
 * 2. The script then checks to make sure the user's level is over 1.
 *
 * 3. The script then checks to make sure the parameter congressID isset.
 *    This is assigned to a new variable, $congressID
 *
 * 4. We then call the function getCongressById by passing in the $congressID and $connection.
 *    This is assigned to a new variable, $congress.
 *
 * 5. We then check to make sure POST_DOWNLOAD_AGENDA isset.
 * 
 * 6. The function downloadCurrentAgenda is then called. $user and $connection are passed through.
 *    This is assigned to $agenda.
 * 
 * 7. Finally if successful, "Agenda_Downloaded is echoed out.
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
    if (checkUserLevel($user, $format) > 1)
    {
        $congress = validateCongress($connection, $format);
        if ($congress)
        {
            $result = downloadCurrentAgenda($user, $connection);
            $queryError = queryError($result, POST_DOWNLOAD_AGENDA, 0);
            if ($queryError["code"] >= 0)
            {
                echo packageTypeErrorForUnity($format, $queryError["message"], $queryError["code"]);
            }
            else
            {
                echo packageDataForUnity($format, $result['downloadPath'], "downloadPath");
            }
        }
    }
}
