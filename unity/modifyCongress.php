<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Modify Congress - Version 1.0 ************************
 *
 * This script allows you to modify a congress in the database.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. The user data is then checked to make sure all of the required fields are entered.
 *
 * 4. If everything is entered correctly, we then call the function modifyCongress by passing in the user variable, and then assigning the result to a new variable, result.
 *
 * 5. If successful it returns a -1 and continues on.
 *
 * 6. Then the ID that's associated with the congress is then assigned to a new variable, id.
 *
 * 7. We then pass the variable, id, into the function getCongressById and assign it to modifyCongress.
 *
 * 8. If successful, we echo out modifyCongress.
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
        if (isCongressDataSet(true))
        {
            $startDate = ($_POST['newCongressStartDate']);
            $endDate = ($_POST['newCongressEndDate']);
            if (strtotime($startDate) < strtotime($endDate))
            {
                $hotelStart = ($_POST['newCongressHotelStartDate']);
                $hotelEnd = ($_POST['newCongressHotelEndDate']);
                if (strtotime($hotelStart) < strtotime($hotelEnd))
                {
                    $result = modifyCongress($user, $connection);
                    $queryError = queryError($result, POST_MODIFY_CONGRESS_DETAIL, 0);
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
                    echo packageTypeErrorForUnity($format, "Invalid hotel date range values entered.", 3);
                }
            }
            else
            {
                echo packageTypeErrorForUnity($format, "Invalid congress date range values entered.", 2);
            }
        }
        else
        {
            sendIncompleteFormDataError($format);
        }
    }
}