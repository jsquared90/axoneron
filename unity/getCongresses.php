<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Get Congresses - Version 1.4 ************************
 *
 * The script displays all future congresses.
 *
 * 1. We call the function getAllCongresses by passing through the $connection.
 *    This is assigned to $congresses.
 *    We then assign a new variable, $filteredCongresses, to an empty new array.
 * 
 * 2. We then run through each congress and check to make sure they're in the future.
 *    We then push each future $congress into the new empty array of $filteredCongress.
 * 
 * 3. If the number of future $congresses, which are referred to as $filteredCongresses now,
 *    is greater than 0 we echo out the updated result.
 *
 * NOTES: all data is returned in JSON format. Other formats (eg. xml) may be supported
 * at a later date, but JSON will be the default. When other formats are supported in the future,
 * format can be specified by including a POST variable definition for "format."
 * If at any point in time a field, step, process is entered/done incorrectly a corresponding error message will display
 * so that the user can figure out how to fix it.
 *
 */

$congresses = getAllCongresses($connection);
if ($congresses)
{
    echo packageDataForUnity($format, $congresses, "congresses");
}
else
{
    echo packageDataForUnity($format, null, "congresses");
}
