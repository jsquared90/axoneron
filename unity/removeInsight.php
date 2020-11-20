<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Remove Insight - Version 1.0 ************************
 *
 * This script removes an insight for a given agenda item in a congress.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. A user level needs to be over 2 in order to remove an insight.
 *
 * 3. The script then checks to make sure the parameter congressID isset. This is assigned to a new variable, $congressID
 *
 * 4. We then call the function getCongressById. We pass through the variables $congressID and $connection.
 *    This is assigned to a new variable, $congress.
 * 
 * 5. We then check to make sure the parameter itemID isset. This is assigned to a new variable, $itemID.
 * 
 * 6. The function getAgendaItemByID is then called. We pass through the variables $itemID, $congress, and $connection.
 *    This is assigned to a new variable, $item.
 * 
 * 7. The parameter postTitle is then checked to make sure it's set. This is assigned to a new variable $post.
 * 
 * 8. The function deleteInsightPost is then called. $user and $connection are passed through. This is assigned to $deleteInsight.
 * 
 * 9. getInsights is then assigned to a new variable, $insights. $user, $congress, and $item are passed through.
 * 
 * 10. We then finally echo out the result, $insights.
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
            $itemID = checkItemID($connection, $format);
            if ($itemID)
            {
                $post = isset($_POST['postTitle']) ? $_POST['postTitle'] : null;
                if ($post)
                {
                    $result = deleteInsightPost($user, $connection);
                    $queryError = queryError($result, POST_REMOVE_INSIGHT, 0);
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
                    sendIncompleteFormDataError($format);
                }
            }
        }
    }
}
