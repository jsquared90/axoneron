<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Add Insights - Version 1.3 ************************
 *
 * This script allows a user to add an insight to a given congress.
 *
 * 1. First we check to make sure the user is validated.
 * 
 * 2. Then we check to make sure the congressID and itemID are set.
 * 
 * 
 * 
 * ///// POST_ADD_INSIGHTS
 * 
 * 3a. We first check to see if POST_ADD_INSIGHTS isset.
 * 
 * 4a. We then call the function getCongressById by passing the congressID POST parameter through it. This is assigned to a new variable, $congress.
 * 
 * 5a. We then call the function getAgendaItemByID. We pass through the itemID POST parameter, the $congress variable, and the $connection. This is assigned to a new variable, $item.
 * 
 * 6a. We now call the fucnction createInsightsDirectory by passing in the $user and the $connection. This is assigned to a new variable, $createInsightsDirectory.
 * 
 * 7a. Now we call the function getInsights. We pass through the $user, $congress, and $item. This is assigned to a new variable, $insights.
 * 
 * 8a. Finally, if successful, we echo out the result.
 * 
 * 
 * 
 * ///// POST_ADD_GENERAL_INSIGHTS
 * 
 * 3b. We first check to see if POST_ADD_GENERAL_INSIGHTS isset.
 * 
 * 4b. Then we check to see if notePadData isset.
 * 
 * 5b. We then call the function getCongressById by passing the congressID POST parameter through it. This is assigned to a new variable, $congress.
 * 
 * 6b. We then call the function getAgendaItemByID. We pass through the itemID POST parameter, the $congress variable, and the $connection. This is assigned to a new variable, $item.
 * 
 * 7b. We now call the fucnction addGeneralInsight by passing in the $user and the $connection. This is assigned to a new variable, $generalInsight.
 * 
 * 8b. Now we call the function getInsights. We pass through the $user, $congress, and $item. This is assigned to a new variable, $insights.
 * 
 * 9b. Finally, if successful, we echo out the result.
 * 
 * 
 * 
 * ///// POST_ADD_INSIGHT
 * 
 * 3c. We first check to see if POST_ADD_INSIGHT isset.
 * 
 * 4c. Then we check to see if insightPostTitle isset.
 * 
 * 5c. We then call the function getCongressById by passing the congressID POST parameter through it. This is assigned to a new variable, $congress.
 * 
 * 6c. We then call the function getAgendaItemByID. We pass through the itemID POST parameter, the $congress variable, and the $connection. This is assigned to a new variable, $item.
 * 
 * 7c. We now call the fucnction addInsightPost by passing in the $user and the $connection. This is assigned to a new variable, $addInsight.
 * 
 * 8c. Now we call the function getInsights. We pass through the $user, $congress, and $item. This is assigned to a new variable, $insights.
 * 
 * 9c. Finally, if successful, we echo out the result.
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
        if (isset($_POST[POST_ADD_INSIGHTS]))
        {
            $congress = validateCongress($connection, $format);
            if ($congress)
            {
                $itemID = checkItemID($connection, $format);
                if ($itemID)
                {
                    $result = createInsightsDirectory($user, $connection);
                    $queryError = queryError($result, POST_ADD_INSIGHTS, 0);
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
        else if (isset($_POST[POST_ADD_GENERAL_INSIGHT]))
        {
            if (isset($_POST['notePadData']))
            {
                $congress = validateCongress($connection, $format);
                if ($congress)
                {
                    $itemID = checkItemID($connection, $format);
                    if ($itemID)
                    {
                        $result = addGeneralInsight($user, $connection);
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
        else if (isset($_POST[POST_ADD_INSIGHT]))
        {
            if (isset($_POST['insightPostTitle']))
            {
                $congress = validateCongress($connection, $format);
                if ($congress)
                {
                    $itemID = checkItemID($connection, $format);
                    if ($itemID)
                    {
                        $result = addInsightPost($user, $connection);
                        $queryError = queryError($result, POST_ADD_INSIGHT, 0);
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
            echo packageTypeErrorForUnity($format, "Insight method not defined.", 300);
        }
    }
    else
    {
        sendIncompleteFormDataError($format);
    }
}