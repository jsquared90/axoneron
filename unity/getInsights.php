<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Get Insights - Version 1.3 ************************
 *
 * This script retrieves insights for a particular user if the $itemID isset, these steps are denoted by an "a". Or displays all insights if the $itemID isn't set and the user's level is above 1, a "b" is attached to these steps.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 * 
 * 
 * 
 * 2a. If the itemID isset, it is assigned to a new variable, $itemID.
 * 
 * 3a. We then call the function getAgendaItemByID by passing through the $itemID, $congress, and $connection. We assign this to a new variable, $item.
 * 
 * 4a. We then check to make sure the parameter congressID isset and assign this to a new variable, $congressID.
 * 
 * 5a. Next we call the function, getCongressById, and pass through the variable $congressID. We assign this result to a new variable, $congress.
 * 
 * 6a. We then call the function, getInsights. We pass the variables: $user, $congress, and $item into it. It is then assigned to a new variable, $insights.
 * 
 * 7a. The last step is then echoing out $insights.
 * 
 * 
 * 
 * 2b. If the itemID isn't set and the user level is above 1, we move on to the next part of the script.
 * 
 * 3b. We first check to make sure the congress parameter isset. This is assigned to a new variable, $congressID.
 * 
 * 4b. We then call the function getCongressById by passing through the $congressID. This is assigned to a new variable, $congress.
 * 
 * 5b. Next we call the function, getAllInsightsForCongress, by passing through the $user variable. This is assigned to a new variable $result.
 * 
 * 6b. The if the 'code' aspect of that array is under 0, the script continues.
 * 
 * 7b. Finally we echo out any of the 'insights' that are in the array.
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
        $itemID = checkItemID($connection, $format);
        if ($itemID)
        {
            $item = getAgendaItemByID($itemID, $congress, $connection);
            if ($item)
            {
                $result = getInsights($user, $congress, $item);
                if ($result)
                {
                    echo packageDataForUnity($format, $result, "insights");
                }
                else
                {
                    echo packageDataForUnity($format, null, "insights");
                }
            }
            else
            {
                echo packageTypeErrorForUnity($format, "An agenda item could not be located.", 1);
            }
        }
    }
}