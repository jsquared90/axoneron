<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Add Message Group - Version 1.6 ************************
 *
 * This script adds a message group to the database.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. Next, we make sure the parameter groupTitle isset.
 *
 * 3. Then we make sure the users are set.
 *
 * 4. We then check to make sure only numbers are entered. They need to be separated by commas, the entry field can't start with or end with a comma, and you can't have more than one comma entered at a time.
 *
 * 5. We then call the function addMessageGroup and assign it to a new variable, result.
 *
 * 6. If successful, we pass the user, groupTitle, and groupUsers into the function getMessageGroupByFootprint and echo out the newly created group.
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
            if (preg_match('/^[0-9,]+$/', $groupUsers) &&
                !preg_match('/^,/', $groupUsers) &&
                !preg_match('/,$/', $groupUsers) &&
                !preg_match('/(,,)/', $groupUsers))
            {
                $result = addMessageGroup($user, $connection);
                $queryError = queryError($result, POST_ADD_MESSAGE_GROUP, 0);
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