<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Remove Congress - Version 1.2 ************************
 *
 * This script allows elevated users to remove a congress.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. It then checks to make sure the user level is over 2.
 *
 * 4. We then check to make sure the congress parameter isset. It is then assigned to a new variable, congressID.
 *
 * 5. We then pass congressID into the function getCongressById which is then assigned to congress.
 *
 * 6. After the specified congress is found in the database, it as well as the user, are then passed through the function removeCongress.
 *
 * 7. The congress isn't actually removed, it is placed in a trash bin, but no longer accessible from the app/web.
 *
 * 8. If successful, we echo out congress removed.
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
            $result = removeCongress($congress, $user, $connection);
            $queryError = queryError($result, POST_REMOVE_CONGRESS, 0);
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