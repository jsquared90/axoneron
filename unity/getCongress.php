<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Get Congress - Version 1.2 ************************
 *
 * This script looks and returns whether or not a congress exists.
 *
 * 1. The script first checks to make sure congress isset and assigns it to congressID.
 *
 * 2. We then call the function getCongressById by passing through congressID and assigning it to a new variable.
 *
 * 3. If we find a congress with a matching ID, we then echo out the result.
 *
 * NOTES: all data is returned in JSON format. Other formats (eg. xml) may be supported
 * at a later date, but JSON will be the default. When other formats are supported in the future,
 * format can be specified by including a POST variable definition for "format."
 * If at any point in time a field, step, process is entered/done incorrectly a corresponding error message will display
 * so that the user can figure out how to fix it.
 *
 */

$congress = validateCongress($connection, $format);
if ($congress)
{
    $queryError = queryError($result, POST_VIEW_CONGRESS, 0);
    if ($queryError["code"] >= 0)
    {
        echo packageTypeErrorForUnity($format, $queryError["message"], $queryError["code"]);
    }
    else
    {
        echo packageDataForUnity($format, $congress, "congress");
    }
}
