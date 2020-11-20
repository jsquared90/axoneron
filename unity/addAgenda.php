<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Add Agenda - Version 1.0 ************************
 *
 * This script adds an agenda to a congress.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. The script then checks to make sure the user level is over 1.
 * 
 * 3. We then check to make sure the parameter congressID isset.
 *    This is assigned to $congressID.
 *
 * 4. We then check to make sure the parameter agendaAddFile isset. As well as POST_UPLOAD_AGENDA.
 *
 * 5. The function addUploadedAgendaToExisting is then called. $user and $connection are passed through.
 *    This is assigned to $createAgenda.
 * 
 * 6. The function getAgendaFromDatabase is then called. $congress and $connection are passed through.
 *    This is assigned to $getAgenda.
 * 
 * 7. If successful the newly added agenda, $getAgenda, is then echoed out.
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
            if (isset($_FILES['agendaAddFile']))
            {
                $result = addUploadedAgendaToExisting($user, $connection);
                $count = count($result['errors']);
                if ($result['code'] == -1 && $count == 0)
                {
                    echo packageDataForUnity($format, $result['record'], "userRecord");
                }
                else
                {
                    $queryError = queryError($result, POST_ADD_AGENDA, 0);
                    if ($queryError)
                    {
                        echo packageTypeErrorForUnity($format, $queryError["message"], $queryError["code"]);
                    }
                    else
                    {
                        echo packageDataForUnity($format, $result['record'], "userRecord");
                    }
                }
            }
            else
            {
                sendIncompleteFormDataError($format);
            }
        }
    }
}
