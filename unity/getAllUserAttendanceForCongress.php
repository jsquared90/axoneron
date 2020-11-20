<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Get All User Attendance For Congress - Version x.y ************************
 *
 */

$user = validateUserForUnity($connection, $format);
if ($user)
{
    $congress = validateCongress($connection, $format);
    if ($congress)
    {
        $users = getAllUserAttendanceForCongress($congress['id'], $connection);
        if ($users)
        {
            echo packageDataForUnity($format, $users, "users");
        }
        else
        {
            echo packageDataForUnity($format, null, "users");
        }
    }
}