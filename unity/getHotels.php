<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Get Hotels - Version 1.1 ************************
 *
 * The script displays all hotels.
 *
 * 1. We assign a new variable to the function getAllHotels.
 *
 * 2. We then echo out the result.
 *
 * NOTES: all data is returned in JSON format. Other formats (eg. xml) may be supported
 * at a later date, but JSON will be the default. When other formats are supported in the future,
 * format can be specified by including a POST variable definition for "format."
 * If at any point in time a field, step, process is entered/done incorrectly a corresponding error message will display
 * so that the user can figure out how to fix it.
 *
 */

$hotels = getAllHotels($connection);
if ($hotels)
{
    echo packageDataForUnity($format, $hotels, "hotels");
}
else
{
    echo packageDataForUnity($format, null, "hotels");
}
