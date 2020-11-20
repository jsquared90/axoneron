<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Request Hotel - Version 1.0 ************************
 *
 * This script allows the user to make a hotel request.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. It then checks to make sure the congressID parameter isset, and assigns it to the variable congressID.
 *
 * 4. We then pass the congressID variable into the function, getCongressById. This is assigned to a new variable, congress.
 *
 * 5. If a congress exists, the script then allows the user to enter in the specified data in a form.
 *
 * 6. After the specified congress is found in the database, it is then passed through the function removeCongress.
 *
 * 7. Upon completion of entering in the required data, we call the function submitHotelRequest and pass the user through. It is then written to the database.
 *
 * 8. If successful, we echo out the newly created hotel request.
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
    if (validateCongress($connection, $format))
    {
        if (isset($_POST['roomType']) &&
            isset($_POST['occupancy']) &&
            isset($_POST['openEnd']))
        {
            if (($_POST['roomType'] == "King") || ($_POST['roomType'] == "Double") &&
                ($_POST['occupancy'] == "Single") || ($_POST['occupancy'] == "Double"))
            {
                $result = submitHotelRequest($user, $connection);
                $queryError = queryError($result, POST_REQUEST_HOTEL, 0);
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
                sendInvalidFormDataError($format);
            }
        }
        else
        {
            sendIncompleteFormDataError($format);
        }
    }
}