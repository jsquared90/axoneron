<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Add Congress - Version 1.1 ************************
 *
 * This script adds a congress to the database provided all of the pertinent information is given.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. A user level needs to be over 1 in order to add a congress.
 *
 * 3. The congress data is then checked to make sure all of the required fields are entered.
 *
 * 4. The congress is then added to the database.
 *
 * 5. The name of the congress is then urlencoded and returned to display the newly created congress.
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
        if (isCongressDataSet(false))
        {
            $startDate = ($_POST['newCongressStartDate']);
            $endDate = ($_POST['newCongressEndDate']);
            if (strtotime($startDate) < strtotime($endDate))
            {
                $hotelStart = ($_POST['newCongressHotelStartDate']);
                $hotelEnd = ($_POST['newCongressHotelEndDate']);
                if (strtotime($hotelStart) < strtotime($hotelEnd))
                {
                    $result = addCongress($user, $connection);
                    $queryError = queryError($result, POST_ADD_CONGRESS, 0);
                    if ($queryError["code"] >= 0)
                    {
                        echo packageTypeErrorForUnity($format, $queryError["message"], $queryError["code"]);
                    }
                    else
                    {
                        //echo packageDataForUnity($format, $result['record'], "userRecord");
                        echo packageDataForUnity($format, $result['congress'], "selectedCongress");
                    }
                }
                else
                {
                    echo packageTypeErrorForUnity($format, "Invalid hotel date range values entered.", 11);
                }
            }
            else
            {
                echo packageTypeErrorForUnity($format, "Invalid congress date range values entered.", 10);
            }
        }
        else
        {
            sendIncompleteFormDataError($format);
        }
    }
}