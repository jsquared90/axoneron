<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Get Agenda - Version 1.2 ************************
 *
 * This script gets an agenda from the database.
 *
 * 1. The script first checks to make sure that the congress isset.
 *
 * 2. Next, we look to see if there's a congress in the database and return it's ID.
 *
 * 3. If a congress exists, we then check to see if an item isset.
 *
 * 4. If the item isn't set, we then get the agenda from the database by passing the congress through. It is then echoed out.
 *
 * 5. If the item is set, we then call the function getAgendaItemByIdD by passing through the item's variable, the congress, and then echoing out the result.
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
    if (isset($_POST['congress']))
    {
        $congress = validateCongress($connection, $format);
        if ($congress)
        {
            $agenda = getAgendaFromDatabase($congress, $connection);
            if ($agenda)
            {
                echo packageDataForUnity($format, $agenda, "agenda");
            }
            else
            {
                echo packageDataForUnity($format, null, "agenda");
            }
        }
    }
    elseif (isset($_POST['filter']))
    {
        if ($_POST['filter'] == 'all' || $_POST['filter'] == 'axoneron')
        {
            if ($_POST['filter'] == 'all')
            {
                $agenda = getAllAgendasFromDatabase($connection);
            }
            else
            {
                $agenda = getAxoneronAgendaItemsFromDatabase($connection);
            }
            if ($agenda)
            {
                $filteredAgenda = 0;
                $startDate = getSoonestAgendaItemDate($agenda);
                if ($startDate)
                {
                    $endDate = getLatestAgendaItemDate($agenda);
                    $i = strtotime($startDate);
                    $j = 0;
                    while ($i <= strtotime($endDate))
                    {
                        $date = Date("n/d/y", $i);
                        $items = getAgendaItemsForDate($agenda, $date);
                        if ($items)
                        {
                            foreach ($items as $item)
                            {
                                if (!$filteredAgenda){ $filteredAgenda = []; }
                                array_push($filteredAgenda, $item);
                            }
                        }
                        $j++;
                        $i = strtotime($startDate . "+" . $j . " day");
                    }
                }
                if ($filteredAgenda)
                {
                    echo packageDataForUnity($format, $filteredAgenda, "agenda");
                }
                else
                {
                    echo packageDataForUnity($format, null, "agenda");
                }
            }
            else
            {
                echo packageDataForUnity($format, null, "agenda");
            }
        }
        else
        {
            sendIncompleteFormDataError($format);
        }
        
    }
    else
    {
        sendIncompleteFormDataError($format);
    }
}