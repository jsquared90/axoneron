<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Get Speaker Bio - Version 1.1 ************************
 *
 * This script allows you to to get a speaker bio(s) in a congress.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 * 
 * 2. We then check to make sure the parameter congressID isset.
 *    This is assigned to $congressID.
 * 
 * 3. We then call the function getCongressById. We pass in the variables $congressID and $connection.
 *    This is assigned to $congress.
 * 
 * 4. We then call the function getBios. We pass in the $congress and $connection.
 *    This is assigned to $speakerBios.
 * 
 * 5. Finally we echo out the $bio(s).
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
        $speakerBios = getBios($congress, $connection);
        if ($speakerBios)
        {
            echo packageDataForUnity($format, $speakerBios, "speakerBios");
        }
        else
        {
            echo packageDataForUnity($format, null, "speakerBios");
        }
    }
}