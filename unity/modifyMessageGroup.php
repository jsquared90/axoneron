<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Modify Message Group - Version 1.0 ************************
 *
 * This script modifys a message group in the database.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. Next, we make sure the parameter groupTitle isset. This is assigned to a new variable, $groupTitle.
 *
 * 3. Then we make sure the parameter groupUsers isset. This is assigned to a new variable, $groupUsers.
 * 
 * 4. Next we make sure the parameter groupID isset. This is assigned to a new variable, $group.
 *
 * 4. We then check to make sure only numbers are entered. They need to be separated by commas, the entry field can't start with or end with a comma, and you can't have more than one comma entered at a time.
 *
 * 5. We then call the function addMessageGroup and assign it to a new variable, $result.
 *
 * 6. If successful, we pass the $user, $groupTitle, and $groupUsers into the function getMessageGroupByFootprint and echo out the newly created group.
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
    $groupTitle = isset($_POST['groupTitle']) ? $_POST['groupTitle'] : null;
    if ($groupTitle)
    {
        $groupUsers = isset($_POST['groupUsers']) ? $_POST['groupUsers'] : null;
        if ($groupUsers)
        {
            $group = isset($_POST['groupID']) ? $_POST['groupID'] : null;
            if ($group)
            {
                if (preg_match('/^[0-9,]+$/', $groupUsers) &&
                    !preg_match('/^,/', $groupUsers) &&
                    !preg_match('/,$/', $groupUsers) &&
                    !preg_match('/(,,)/', $groupUsers))
                {
                    $result = modifyMessageGroup($user, $connection);
                    $queryError = queryError($result, POST_MODIFY_MESSAGE_GROUP, 0);
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
    else
    {
        sendIncompleteFormDataError($format);
    }
}