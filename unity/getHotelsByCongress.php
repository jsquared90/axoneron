<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Get Hotels by Congress - Version 1.1 ************************
 *
 * The script displays all congresses.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. The script then checks to make sure congress isset and assigns it to congressID.
 *
 * 3. We then call the function getCongressById by passing through congressID and assigning it to a new variable.
 *
 * 4. If a congress exists, we then call the function getHotelsWithCongress by passing in the congress.
 *
 * 5. We assign this to a new variable, hotel, and then echo out the result.
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
        $hotels = getHotelsWithCongress($congress, $connection);
        if ($hotels)
        {
            echo packageDataForUnity($format, $hotels, "hotels");
        }
        else
        {
            echo packageTypeErrorForUnity($format, "There are currently no hotels with the congress.", 1);
        }
    }
}