<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Modify Speaker Bio - Version 1.0 ************************
 *
 * This script allows you to to modify a speaker bio in a congress.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. We then check to make sure the user level is over 1.
 * 
 * 3. We then check to make sure the parameter congressID isset.
 *    This is assigned to $congressID.
 * 
 * 4. We then call the function getCongressById. We pass in the variables $congressID and $connection.
 *    This is assigned to $congress.
 * 
 * 5. We then check to make sure all of the form parameters are filled out.
 * 
 * 6. We next call the function modifyBio. We pass in the variables $user and $connection.
 *    This is assigned to $result.
 * 
 * 7. We then call the function getBios. We pass in the $congress and $connection.
 *    This is assigned to $bio
 * 
 * 8. Finally we echo out the $bio.
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
            if (isset($_POST['previousBioName']) &&
                isset($_POST['bioFirstName']) &&
                isset($_POST['bioLastName']) &&
                isset($_FILES['bioFile']))
            {
                $result = modifyBio($user, $connection);
                $queryError = queryError($result, POST_MODIFY_BIO, 0);
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