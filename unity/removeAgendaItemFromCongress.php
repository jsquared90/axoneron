<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Remove Agenda Item - Version 1.0 ************************
 *
 * This script removes a hospitality room from the database for a specified congress.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. A user level needs to be over 2 in order to remove a hospitality room.
 *
 * 3. The script then checks to make sure the parameter congressID isset. This is assigned to a new variable, $congressID
 *
 * 4. We then call the function getCongressById by passing in the $congressID and $connection. This is assigned to a new variable, $congress.
 *
 * 5. We then check to make sure the parameter itemID isset. This is assigned to a new variable, $item.
 *
 * 6. The function deleteAgendaItem is then called. We pass in the $user and the $connection. This is assigned to a new variable, $result.
 *
 * 7. The function getAgendaFromDatabase is then called. We pass in the $congress and the $connection. This is assigned to a new variable, $agenda.
 * 
 * 8. Finally the result, $agenda, is then echoed out.
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
            $item = checkItemID($connection, $format);
            if ($item)
            {
                $result = deleteAgendaItem($user, $connection);
                $queryError = queryError($result, POST_REMOVE_AGENDA_ITEM_FROM_CONGRESS, 0);
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
    }
}
