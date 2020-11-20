<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Upload Agenda - Version 1.0 ************************
 *
 * This script replaces an agenda in a congress.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. A user level needs to be over 2 in order to replace an agenda.
 *
 * 3. The script then checks to make sure the parameter congressID isset. This is assigned to a new variable, $congressID
 *
 * 4. We then call the function getCongressById by passing in the $congressID and $connection. This is assigned to a new variable, $congress.
 * 
 * 5. We then check to make sure the parameter agendaReplaceFile is set. This is assigned to a new variable, $newAgenda.
 *
 * 6. We then call the function replaceAgenda. We pass in the variables $user and $connection. This is assigned to a new variable, $replaceAgenda.
 *
 * 7. We then call the function getAgendaFromDatabase. We pass in the variables $congress and $connection. This is assigned to a new variable, $agenda.
 *
 * 8. If successful, the newly added $agenda is then echoed out.
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
            if (isset($_FILES['agendaFile']))
            {
                $result = addUploadedAgendaToCongress($user, $connection);
                $count = count($result['errors']);
                if ($result['code'] == -1 && $count == 0)
                {
                    echo packageDataForUnity($format, $result['record'], "userRecord");
                }
                else
                {
                    $queryError = queryError($result, POST_UPLOAD_AGENDA, 0);
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
