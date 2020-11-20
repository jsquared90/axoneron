<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Modify Insight - Version 1.1 ************************
 *
 * This script allows a user to modify an insight to a given congress.
 * 
 * 1. First the script checks to make sure there's a valid user.
 * 
 * 2. Then we check to make sure both congressID and itemID are set.
 * 
 * ///// POST_ADD_GENERAL_INSIGHT
 * 
 * 3a. We first check to see if POST_ADD_GENERAL_INSIGHT isset.
 * 
 * 4a. We then check to see if notePadData isset.
 * 
 * 5a. We then pass the parameter congressID into the function getCongressById.
 *     The returned value is then assigned to a new variable, $congress.
 * 
 * 6a. We then pass the parameter itemID into the function getAgendaItemByID.
 *     The returned value is then assigned to a new variable, $item.
 * 
 * 7a. We then pass the $user and the $connection into the function addGeneralInsight.
 *     The returned value is then assigned to a new variable, $result.
 * 
 * 8a. We then pass the $user, $congress, and $item into the function getInsights.
 *     This is then assigned to a new variable, $insights.
 * 
 * 9a. If successful, we echo out the $insights.
 * 
 * ///// POST_MODIFY_INSIGHT
 * 
 * 3b. We then check to see if POST_MODIFY_INSIGHT isset.
 * 
 * 4b. We then pass the parameter congressID into the function getCongressById.
 *     The returned value is then assigned to a new variable, $congress.
 * 
 * 5b. We then pass the parameter itemID into the function getAgendaItemByID.
 *     The returned value is then assigned to a new variable, $item.
 * 
 * 6b. We then pass the $user and the $connection into the function editInsightPost.
 *     The returned value is then assigned to a new variable, $result.
 * 
 * 7b. We then pass the $user, $congress, and $item into the function getInsights.
 *     This is then assigned to a new variable, $insights.
 * 
 * 8b. If successful, we echo out the $insights.
 * 
 * ///// INSIGHT_SELECTION_FAILURE
 * 
 * 3c. If neither of the insight fields are selected, an insight_selection_error is displayed.
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
    if (isset($_POST['congressID']) &&
        isset($_POST['itemID']))
    {
        if (isset($_POST[POST_ADD_GENERAL_INSIGHT]))
        {
            if (isset($_POST['notePadData']))
            {
                $congress = validateCongress($connection, $format);
                if ($congress)
                {
                    $itemID = checkItemID($connection, $format);
                    if ($itemID)
                    {
                        $result = modifyGeneralInsight($user, $connection);
                        $queryError = queryError($result, POST_ADD_GENERAL_INSIGHT, 0);
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
            else
            {
                sendIncompleteFormDataError($format);
            }
        }
        elseif (isset($_POST['previousTitle']) &&
                isset($_POST['insightEditTitle']))
        {
            $congress = validateCongress($connection, $format);
            if ($congress)
            {
                $itemID = checkItemID($connection, $format);
                if ($itemID)
                {
                    $result = editInsightPost($user, $connection);
                    $queryError = queryError($result, POST_MODIFY_INSIGHT, 0);
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
        else
        {
            sendIncompleteFormDataError($format);
        }
    }
    else
    {
        sendIncompleteFormDataError($format);
    }
}