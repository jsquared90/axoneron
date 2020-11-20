<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Modify Agenda Item - Version 1.0 ************************
 *
 * This script modifies an agenda item in a congress.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. A user level needs to be over 1 in order to add a hospitality room.
 *
 * 3. The script then checks to make sure all of the required fields are filled out.
 *
 * 4. We then call the function modifyAgendaItem. We pass through the $user and the $connection. This is assigned to a new variable, $modifyAgendaItem.
 *
 * 5. The parameter itemID is assigned to $itemID. The parameter congressID is assigned to $congressID.
 *
 * 6. We then call the function getAgendaItemByID. We pass through $itemID, $congressID, and $connection. This is assigned to $result.
 *
 * 7. If successful, the $result is then echoed out.
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
        if (isset($_POST['congressID']) &&
            isset($_POST['itemID']) &&
            isset($_POST['agendaTitle']) &&
            isset($_POST['agendaSubTitle']) &&
            isset($_POST['agendaLocation']) &&
            isset($_POST['agendaType']) &&
            isset($_POST['agendaPriority']) &&
            isset($_POST['agendaCategory']) &&
            isset($_POST['agendaStartDate']) &&
            isset($_POST['agendaStartTime']) &&
            isset($_POST['agendaStartMeridian']) &&
            isset($_POST['agendaEndDate']) &&
            isset($_POST['agendaEndTime']) &&
            isset($_POST['agendaEndMeridian']) &&
            isset($_POST['agendaAssignment']) &&
            isset($_POST['agendaChair']) &&
            isset($_POST['agendaPresenters']) &&
            isset($_POST['agendaSessionName']) &&
            isset($_POST['agendaFootnotes']))
        {
            $agendaStartDate = ($_POST['agendaStartDate']);
            $agendaEndDate = ($_POST['agendaEndDate']);
            if (strtotime($agendaStartDate) <= strtotime($agendaEndDate))
            {
                $result = modifyAgendaItem($user, $connection);
                $queryError = queryError($result, POST_MODIFY_AGENDA_ITEM, 0);
                if ($queryError["code"] >= 0)
                {
                    echo packageTypeErrorForUnity($format, $queryError["message"], $queryError["code"]);
                }
                else
                {
                    echo packageDataForUnity($format, $result['record'], "userRecord");
                }
            }
            else
            {
                echo packageTypeErrorForUnity($format, "Invalid agenda date range values entered.", 2);
            }
        }
        else
        {
            sendIncompleteFormDataError($format);
        }
    }
}
