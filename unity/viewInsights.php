<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

$user = validateUserForUnity($connection, $format);
if ($user)
{
    if (checkUserLevel($user, $format) > 1)
    {
        $congress = validateCongress($connection, $format);
        if ($congress)
        {
            $result = getAllInsightsForCongress($user, $connection);
            $queryError = queryError($result, POST_VIEW_INSIGHTS);
            if ($queryError["code"] >= 0)
            {
                echo packageTypeErrorForUnity($format, $queryError["message"], $queryError["code"]);
            }
            else
            {
                if ($result['insights'])
                {
                    echo packageDataForUnity($format, $result['insights'], "insightsPackage");
                }
                else
                {
                    echo packageDataForUnity($format, null, "insightsPackage");
                }
            }
        }
    }
}