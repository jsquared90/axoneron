<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Remove Hotel Reservation - Version 1.0 ************************
 *
 * This script removes a hotel reservation from the database for a specified user.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. We next check to make sure the parameter congressID isset. This is assigned to $congressID.
 * 
 * 3. Next we call the function getCongressById. $congressID and $connection are passed through.
 *    This is assigned to $congress.
 * 
 * 4. Next we check to make sure the parameter authorID isset. This is assigned to $authorID.
 * 
 * 5. We then call the function getRecordByID. We pass through $authorID, $user, and $connection.
 *    This is assigned to $author.
 * 
 * 6. We then call the function removeHotelRequest. $author, $congressID, $user, and $connection are passed through.
 *    This is assigned to $removeHotelReservation.
 * 
 * 7. POST_REMOVE_HOTEL_RESERVATION is then checked to make sure it was submitted successfully.
 * 
 * 8. We then call the function getAllMostRecentHotelReservationsForUser. This is assigned to $hotels.
 * 
 * 9. Finally we echo out the remaining $hotels.
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
        $authorID = isset($_POST['authorID']) ? $_POST['authorID'] : null;
        if ($authorID)
        {
            $author = getUserById($authorID, $connection);
            if ($author)
            {
                $result = removeHotelRequest($author, $congress['id'], $user, $connection);
                $queryError = queryError($result, POST_REMOVE_HOTEL_RESERVATION, 0);
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
                echo packageTypeErrorForUnity($format, "Unauthorized Access.", 3);
            }
        }
        else
        {
            sendIncompleteFormDataError($format);
        }
    }
}
