<?php

/*************************
 *  BASE FUNCTIONS
 * ***********************
 */

function isRowFatal($error)
{
    if ($error == 3 ||
        $error == 4 ||
        $error == 8 ||
        $error == 9 ||
        $error == 10 ||
        $error == 11)
    {
        return 0;
    }
    else if ($error == 18 &&
        $error != 3 &&
        $error != 4 &&
        $error != 8 &&
        $error != 9 &&
        $error != 10 &&
        $error != 11)
    {
        return -1;
    }
    else
    {
        return -1;
    }
}

function addUploadedAgendaToCongress($user, $connection)
{
    /*
     * Global Error Code Chart:
     * 1. Spreadsheet file size wwas too large
     * 2. Spreadsheet format was incorrect
     * 3. Spreadsheet unexpectedly failed to save to server
     * 
     * 11. Insufficient columns
     * 12. Missing a required header
     * 13. An invalid header
     * 14. Invalid item type
     * 15. Character limit for category met
     * 16. Character limit for title met
     * 17. Character limit for sub title met
     * 18. Start date format incorrect
     * 19. Start time format incorrect
     * 20. End date format incorrect
     * 21. End time format incorrect
     * 22. Character limit for location column met
     * 23. Invalid priority selected
     * 24. Contains an invalid user name in chair column
     * 25. Character limit for chair column met
     * 26. Contains an invalid user name in presenters column
     * 27. Character limit for presenters column met
     * 28. Contains an invalid user name in assignment column
     * 29. Character limit for assignment column met
     * 30. Contains an invalid session name in sessionName column
     * 31. Character limit for sessionName column met
     * 32. Character limit for footnotes column met
     */
    
    $congressID = $_POST["congressID"];
    $result = array();
    $result["code"] = 0;
    $result["errors"] = array();
    $result['record'] = 0;
    $return = uploadAgendaDataToRawFolder("agendaFile");
    if ($return["code"] < 0)
    {
        $filePath = $return["saveLocation"];
        $return = parseAgendaData($filePath, $connection);
        if ((is_array($return["errors"])) || (is_object($return["errors"])))
        {
            foreach (($return["errors"]) as $e)
            {
                $fatalRow = isRowFatal($e['code']);
                switch ($e['code'])
                {
                    case 1:
                        // Insufficient columns
                        $result = packageError($result, 11, 0, 1, $e["data"]);
                    break;
                    case 2:
                        // Missing required header
                        $result = packageError($result, 12, 0, 1, $e["data"]);
                    break;
                    case 3:
                        // Invalid header specified
                        $result = packageError($result, 13, 0, $fatalRow, $e["data"]);
                    break;
                    case 4:
                        // Invalid item type
                        $result = packageError($result, 14, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 5:
                        // Character limit for category met
                        $result = packageError($result, 15, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 6:
                        // Character limit for title met
                        $result = packageError($result, 16, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 7:
                        // Character limit for subtitle met
                        $result = packageError($result, 17, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 8:
                        // Start Date incorrect format
                        $result = packageError($result, 18, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 9:
                        // Start Time incorrect format
                        $result = packageError($result, 19, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 10:
                        // End Date incorrect format
                        $result = packageError($result, 20, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 11:
                        // End Time incorrect format
                        $result = packageError($result, 21, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 12:
                        // Character limit for location met
                        $result = packageError($result, 22, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 13:
                        // Priority incorrect format
                        $result = packageError($result, 23, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 15:
                        // Character limit for chair met
                        $result = packageError($result, 25, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 17:
                        // Character limit for presenters met
                        $result = packageError($result, 27, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 18:
                        // Invalid assignment
                        $result = packageError($result, 28, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 19:
                        // Character limit for assignment met
                        $result = packageError($result, 29, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 21:
                        // Character limit for session name met
                        $result = packageError($result, 31, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 22:
                        // Character limit for footnotes met
                        $result = packageError($result, 32, $e["row"], $fatalRow, $e["data"]);
                    break;
                }
            }
        }
        if ($return["agenda"])
        {
            $agenda = $return["agenda"];
            $sqlResults = addAgendaToDatabase($agenda, $congressID, $connection);
            if ($sqlResults["agenda"])
            {
                $recordData = array();
                $recordData['type'] = AGENDA_UPLOADED_TO_CONGRESS;
                $recordData['data'] = $congressID;
                $recordData['openEnd'] = "";
                $result2 = addRecordToUser($user, $recordData, $user, $connection);
                if ($result2)
                {
                    $record = getRecordByFootprint($recordData, $user, $connection);
                    if ($record)
                    {
                        $result["code"] = -1;
                        $result['record'] = $record;
                    }
                    else
                    {
                        $result = packageError($result, 70, 0, 1, "");
                    }
                }
                else
                {
                    $result = packageError($result, 60, 0, 1, "");
                }
            }
            else
            {
                switch ($return['errors']["code"])
                {
                    case 50:
                        // Code 50 = Agenda not an array or object
                        $result = packageError($result, 50, 0, 1, "");
                    break;
                    case 51:
                        // Code 51 = Data not added to database
                        $result = packageError($result, 51, 0, 1, "");
                    break;
                }
            }
        }
        else
        {
            switch ($return['errors'])
            {
                case 45:
                    // Code 45 = There was an error fetching $return["agenda"]
                    $result = packageError($result, 45, 0, 1, "");
                break;
            }
        }
        
    }
    else
    {
        switch ($return['errors']["code"])
        {
            case 1:
                // Code 1 = File size too large
                $result = packageError($result, 1, 0, 1, "");
            break;
            case 2:
                // Code 2 = Wrong format
                $result = packageError($result, 2, 0, 1, "");
            break;
            case 3:
                // Code 3 = Unable to save to server
                $result = packageError($result, 3, 0, 1, $return["saveLocation"]);
            break;
        }
    }
    return $result;
}

function packageError($result, $code, $row, $failureType, $details)
{
    $result["code"] = 1;
    $error = array(
        "code" => $code,
        "row" => $row,
        "failureType" => $failureType,
        "details" => $details
    );
    array_push($result["errors"], $error);
    return $result;
}

function replaceAgenda($user, $connection)
{
    $congressID = $_POST['congressID'];
    $result = array();
    $result["code"] = 0;
    $result["errors"] = array();
    $result['record'] = 0;
    $query = "DELETE FROM congressAgenda_" . $congressID;
    $result1 = $connection->query($query);
    if ($result1)
    {
        $return = uploadAgendaDataToRawFolder("agendaReplaceFile");
        if ($return["code"] < 0)
        {
            $filePath = $return["saveLocation"];
            $return = parseAgendaData($filePath, $connection);
            if ((is_array($return["errors"])) || (is_object($return["errors"])))
            {
                foreach (($return["errors"]) as $e)
                {
                    $fatalRow = isRowFatal($e['code']);
                    switch ($e['code'])
                    {
                        case 1:
                            // Insufficient columns
                            $result = packageError($result, 11, 0, 1, $e["data"]);
                        break;
                        case 2:
                            // Missing required header
                            $result = packageError($result, 12, 0, 1, $e["data"]);
                        break;
                        case 3:
                            // Invalid header specified
                            $result = packageError($result, 13, 0, $fatalRow, $e["data"]);
                        break;
                        case 4:
                            // Invalid item type
                            $result = packageError($result, 14, $e["row"], $fatalRow, $e["data"]);
                        break;
                        case 5:
                            // Character limit for category met
                            $result = packageError($result, 15, $e["row"], $fatalRow, $e["data"]);
                        break;
                        case 6:
                            // Character limit for title met
                            $result = packageError($result, 16, $e["row"], $fatalRow, $e["data"]);
                        break;
                        case 7:
                            // Character limit for subtitle met
                            $result = packageError($result, 17, $e["row"], $fatalRow, $e["data"]);
                        break;
                        case 8:
                            // Start Date incorrect format
                            $result = packageError($result, 18, $e["row"], $fatalRow, $e["data"]);
                        break;
                        case 9:
                            // Start Time incorrect format
                            $result = packageError($result, 19, $e["row"], $fatalRow, $e["data"]);
                        break;
                        case 10:
                            // End Date incorrect format
                            $result = packageError($result, 20, $e["row"], $fatalRow, $e["data"]);
                        break;
                        case 11:
                            // End Time incorrect format
                            $result = packageError($result, 21, $e["row"], $fatalRow, $e["data"]);
                        break;
                        case 12:
                            // Character limit for location met
                            $result = packageError($result, 22, $e["row"], $fatalRow, $e["data"]);
                        break;
                        case 13:
                            // Priority incorrect format
                            $result = packageError($result, 23, $e["row"], $fatalRow, $e["data"]);
                        break;
                        case 15:
                            // Character limit for chair met
                            $result = packageError($result, 25, $e["row"], $fatalRow, $e["data"]);
                        break;
                        case 17:
                            // Character limit for presenters met
                            $result = packageError($result, 27, $e["row"], $fatalRow, $e["data"]);
                        break;
                        case 18:
                            // Invalid assignment
                            $result = packageError($result, 28, $e["row"], $fatalRow, $e["data"]);
                        break;
                        case 19:
                            // Character limit for assignment met
                            $result = packageError($result, 29, $e["row"], $fatalRow, $e["data"]);
                        break;
                        case 21:
                            // Character limit for session name met
                            $result = packageError($result, 31, $e["row"], $fatalRow, $e["data"]);
                        break;
                        case 22:
                            // Character limit for footnotes met
                            $result = packageError($result, 32, $e["row"], $fatalRow, $e["data"]);
                        break;
                    }
                }
            }
            if ($return["agenda"])
            {
                $agenda = $return["agenda"];
                $sqlResults = addAgendaToDatabase($agenda, $congressID, $connection);
                if ($sqlResults["agenda"])
                {
                    $recordData = array();
                    $recordData['type'] = REPLACED_AGENDA;
                    $recordData['data'] = $congressID;
                    $recordData['openEnd'] = "";
                    $result2 = addRecordToUser($user, $recordData, $user, $connection);
                    if ($result2)
                    {
                        $record = getRecordByFootprint($recordData, $user, $connection);
                        if ($record)
                        {
                            $result["code"] = -1;
                            $result['record'] = $record;
                        }
                        else
                        {
                            $result = packageError($result, 70, 0, 1, "");
                        }
                    }
                    else
                    {
                        $result = packageError($result, 60, 0, 1, "");
                    }
                }
                else
                {
                    foreach (($return["errors"]) as $e)
                    {
                        switch ($e["code"])
                        {
                            case 50:
                                // Code 50 = Agenda not an array or object
                                $result = packageError($result, 50, 0, 1, "");
                            break;
                            case 51:
                                // Code 51 = Data not added to database
                                $result = packageError($result, 51, 0, 1, "");
                            break;
                        }
                    }
                }
            }
            else
            {
                foreach (($return["errors"]) as $e)
                {
                    switch ($e["code"])
                    {
                        case 45:
                            // Code 45 = There was an error fetching $return["agenda"]
                            $result = packageError($result, 45, 0, 1, "");
                        break;
                    }
                } 
            }
        }
        else
        {
            foreach (($return["errors"]) as $e)
            {
                switch ($e["code"])
                {
                    case 1:
                        // Code 1 = File size too large
                        $result = packageError($result, 1, 0, 1, "");
                    break;
                    case 2:
                        // Code 2 = Wrong format
                        $result = packageError($result, 2, 0, 1, "");
                    break;
                    case 3:
                        // Code 3 = Unable to save to server
                        $result = packageError($result, 3, 0, 1, $return["saveLocation"]);
                    break;
                }
            }
        }
    }
    else
    {
        foreach (($return["errors"]) as $e)
        {
            switch ($e["code"])
            {
                case 101:
                    // Code 101 = congressID not set
                    $result = packageError($result, 101, 0, 1, "");
                break;
                case 102:
                    // Code 102 = congress doesn't exist
                    $result = packageError($result, 102, 0, 1, "");
                break;
            }
        }
    }
    return $result;
}

function addUploadedAgendaToExisting($user, $connection)
{
    $congressID = $_POST["congressID"];
    $result = array();
    $result["code"] = 0;
    $result["errors"] = array();
    $result['record'] = 0;
    $return = uploadAgendaDataToRawFolder("agendaAddFile");
    if ($return["code"] < 0)
    {
        $filePath = $return["saveLocation"];
        $return = parseAgendaData($filePath, $connection);
        if ((is_array($return["errors"])) || (is_object($return["errors"])))
        {
            foreach (($return["errors"]) as $e)
            {
                $fatalRow = isRowFatal($e['code']);
                switch ($e['code'])
                {
                    case 1:
                        // Insufficient columns
                        $result = packageError($result, 11, 0, 1, $e["data"]);
                    break;
                    case 2:
                        // Missing required header
                        $result = packageError($result, 12, 0, 1, $e["data"]);
                    break;
                    case 3:
                        // Invalid header specified
                        $result = packageError($result, 13, 0, $fatalRow, $e["data"]);
                    break;
                    case 4:
                        // Invalid item type
                        $result = packageError($result, 14, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 5:
                        // Character limit for category met
                        $result = packageError($result, 15, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 6:
                        // Character limit for title met
                        $result = packageError($result, 16, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 7:
                        // Character limit for subtitle met
                        $result = packageError($result, 17, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 8:
                        // Start Date incorrect format
                        $result = packageError($result, 18, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 9:
                        // Start Time incorrect format
                        $result = packageError($result, 19, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 10:
                        // End Date incorrect format
                        $result = packageError($result, 20, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 11:
                        // End Time incorrect format
                        $result = packageError($result, 21, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 12:
                        // Character limit for location met
                        $result = packageError($result, 22, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 13:
                        // Priority incorrect format
                        $result = packageError($result, 23, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 15:
                        // Character limit for chair met
                        $result = packageError($result, 25, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 17:
                        // Character limit for presenters met
                        $result = packageError($result, 27, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 18:
                        // Invalid assignment
                        $result = packageError($result, 28, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 19:
                        // Character limit for assignment met
                        $result = packageError($result, 29, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 21:
                        // Character limit for session name met
                        $result = packageError($result, 31, $e["row"], $fatalRow, $e["data"]);
                    break;
                    case 22:
                        // Character limit for footnotes met
                        $result = packageError($result, 32, $e["row"], $fatalRow, $e["data"]);
                    break;
                }
            }
        }
        if ($return["agenda"])
        {
            $agenda = $return["agenda"];
            $sqlResults = addAgendaToDatabase($agenda, $congressID, $connection);
            if ($sqlResults["agenda"])
            {
                $recordData = array();
                $recordData['type'] = ADDED_AGENDA_ITEMS;
                $recordData['data'] = $congressID;
                $recordData['openEnd'] = "";
                $result2 = addRecordToUser($user, $recordData, $user, $connection);
                if ($result2)
                {
                    $record = getRecordByFootprint($recordData, $user, $connection);
                    if ($record)
                    {
                        $result["code"] = -1;
                        $result['record'] = $record;
                    }
                    else
                    {
                        $result = packageError($result, 70, 0, 1, "");
                    }
                }
                else
                {
                    $result = packageError($result, 60, 0, 1, "");
                }
            }
            else
            {
                switch ($return['errors']["code"])
                {
                    case 50:
                        // Code 50 = Agenda not an array or object
                        $result = packageError($result, 50, 0, 1, "");
                    break;
                    case 51:
                        // Code 51 = Data not added to database
                        $result = packageError($result, 51, 0, 1, "");
                    break;
                }
            }
        }
        else
        {
            switch ($return['errors'])
            {
                case 45:
                    // Code 45 = There was an error fetching $return["agenda"]
                    $result = packageError($result, 45, 0, 1, "");
                break;
            }
        }
        
    }
    else
    {
        switch ($return['errors']["code"])
        {
            case 1:
                // Code 1 = File size too large
                $result = packageError($result, 1, 0, 1, "");
            break;
            case 2:
                // Code 2 = Wrong format
                $result = packageError($result, 2, 0, 1, "");
            break;
            case 3:
                // Code 3 = Unable to save to server
                $result = packageError($result, 3, 0, 1, $return["saveLocation"]);
            break;
        }
    }
    return $result;
}

function downloadCurrentAgenda($user, $connection)
{
    $code = 0;
    $errors = 0;
    $downloadPath = 0;
    
    if (isset($_POST['congressID']))
    {
        $congress = getCongressById($_POST['congressID'], $connection);
        if ($congress)
        {
            $agenda = getAgendaFromDatabase($congress, $connection);
            if ($agenda)
            {
                $fileDateString = date(getSaveForFileDateFormat(), time());
                $congressName = preg_replace('/[^A-Za-z0-9\- ]/', '', $congress['shortName']);
                $downloadPath = DOWNLOAD_PATH . $congressName . "_" . $fileDateString . ".xlsx";
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setCellValue('A1', TYPE);
                $sheet->setCellValue('B1', CATEGORY);
                $sheet->setCellValue('C1', TITLE);
                $sheet->setCellValue('D1', SUB_TITLE);
                $sheet->setCellValue('E1', START_DATE);
                $sheet->setCellValue('F1', START_TIME);
                $sheet->setCellValue('G1', END_DATE);
                $sheet->setCellValue('H1', END_TIME);
                $sheet->setCellValue('I1', LOCATION);
                $sheet->setCellValue('J1', PRIORITY);
                $sheet->setCellValue('K1', CHAIR);
                $sheet->setCellValue('L1', PRESENTERS);
                $sheet->setCellValue('M1', ASSIGNMENT);
                $sheet->setCellValue('N1', SESSION_NAME);
                $sheet->setCellValue('O1', FOOTNOTES);
                $row = 2;
                foreach ($agenda as $item)
                {
                    $sheet->setCellValue('A' . $row, $item[TYPE]);
                    $sheet->setCellValue('B' . $row, $item[CATEGORY]);
                    $sheet->setCellValue('C' . $row, $item[TITLE]);
                    $sheet->setCellValue('D' . $row, $item[SUB_TITLE]);
                    
                    $start = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($item[START_DATE] . " ". $item[START_TIME]));
                    $sheet->setCellValue('E' . $row, $start);
                    $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode("mm/dd/yy");
                    $sheet->setCellValue('F' . $row, $start);
                    $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode("h:mm AM/PM");
                    
                    $end = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($item[END_DATE] . " ". $item[END_TIME]));
                    
                    if ($item[START_DATE] == $item[END_DATE])
                    {
                        $sheet->setCellValue('G' . $row, "");
                    }
                    else
                    {
                        $sheet->setCellValue('G' . $row, $end);
                        $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode("mm/dd/yy");
                    }
                    $sheet->setCellValue('H' . $row, $start);
                    $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode("h:mm AM/PM");
                    //\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME1
                            
                    $sheet->setCellValue('I' . $row, $item[LOCATION]);
                    
                    $priority = convertAgendaPriorityForDisplay($item[PRIORITY]);
                    
                    $sheet->setCellValue('J' . $row, $priority);
                    $sheet->setCellValue('K' . $row, implode(",", $item[CHAIR]));
                    $sheet->setCellValue('L' . $row, implode(",", $item[PRESENTERS]));
                    
                    $assignment = "";
                    foreach ($item[ASSIGNMENT] as $a)
                    {
                        if ($a == "axoneron")
                        {
                            $assignment .= $a;
                        }
                        else
                        {
                            $u = getUserById($a, $connection);
                            $assignment .= $u['first'] . " " . $u['last'];
                        }
                        if (next($item[ASSIGNMENT]))
                        {
                            $assignment .= ",";
                        }
                    }
                    
                    $sheet->setCellValue('M' . $row, $assignment);
                    $sheet->setCellValue('N' . $row, $item[SESSION_NAME]);
                    $sheet->setCellValue('O' . $row, $item[FOOTNOTES]);
                    $row++;
                }
                $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save($downloadPath);
                $code = -1;
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 4);
            }
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 3);
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 2);
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "downloadPath" => $downloadPath
    );
}

function getAllInsightsForCongress($author, $connection)
{
    $code = 0;
    $errors = 0;
    $allInsights = 0;
    
    if (isset($_POST['congressID']))
    {
        $congress = getCongressById($_POST['congressID'], $connection);
        if ($congress)
        {
            $users = getAllUsers($connection);
            $agenda = getAgendaFromDatabase($congress, $connection);
            if ($users && $agenda)
            {
                $typeFilter = isset($_POST['typeFilter']) ? $_POST['typeFilter'] : 0;
                $dateFilter = isset($_POST['dateFilter']) ? $_POST['dateFilter'] : 0;
                $typeFilter = $typeFilter === "" ? 0 : $typeFilter;
                $dateFilter = $dateFilter === "" ? 0 : $dateFilter;
                foreach ($users as $u)
                {
                    if (userHasInsights($u, $congress, $connection))
                    {
                        $userItems = 0;
                        foreach ($agenda as $item)
                        {
                            $insights = 0;
                            $typeSafe = 1;
                            $dateSafe = 1;
                            if ($typeFilter)
                            {
                                $typeSafe = $typeFilter == $item[TYPE] ? 1 : 0;
                            }
                            if ($dateFilter)
                            {
                                $dateSafe = 0;
                                if (strtotime($item[START_DATE]) <= strtotime($dateFilter) &&
                                        strtotime($item[END_DATE]) >= strtotime($dateFilter))
                                {
                                    $dateSafe = 1;
                                }
                            }
                            if ($typeSafe && $dateSafe)
                            {
                                $insights = getInsights($u, $congress, $item);
                                if ($insights['generalNotes'] != "" && $insights['generalNotes'] != DEFAULT_NOTEPAD_TEXT || $insights['posts'] != "")
                                {
                                    $insightsPackage = array(
                                        'item' => $item,
                                        'insights' => $insights
                                    );
                                    if (!$userItems){ $userItems = []; }
                                    array_push($userItems, $insightsPackage);
                                }
                            }
                        }
                        if ($userItems)
                        {
                            $userInsights = array(
                                'user' => $u,
                                'items' => $userItems
                            );
                            if (!$allInsights){ $allInsights = []; }
                            array_push($allInsights, $userInsights);
                        }
                    }
                }
                if ($allInsights)
                {
                    $code = -1;
                }
            }
            else if ($users && !$agenda)
            {
                $code = 1;
                $errors = packageGeneralError($errors, 4);
            }
            else if (!$users && $agenda)
            {
                $code = 1;
                $errors = packageGeneralError($errors, 5);
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 6);
            }
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 3);
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 2);
    }
    
    return array(
        "code" => $code,
        "errors" => $errors,
        "insights" => $allInsights
    );
}


/*************************
 *  SUB FUNCTIONS
 * ***********************
 */

function getAgendaFromDatabase($congress, $connection)
{
    $congressID = $congress["id"];
    $agenda = 0;
    $query = "SELECT * FROM congressAgenda_" . $congressID;
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $item = packageAgendaItemRowIntoArray($row, $congressID);
            if (!$agenda){ $agenda = []; }
            array_push($agenda, $item);
        }
    }
    if ($agenda)
    {
        $temp = array();
        foreach ($agenda as $item)
        {
            $timestamp = strtotime(date($item[START_DATE] . " " . $item[START_TIME]));
            $item['timeStamp'] = $timestamp;
            array_push($temp, $item);
        }
        $agenda = $temp;
        $timestamps = array_column($agenda, 'timeStamp');
        array_multisort($timestamps, SORT_ASC, $agenda);
        
    }
    return $agenda;
}

function getAllAgendasFromDatabase($connection)
{
    $agenda = 0;
    $congresses = getAllCongresses($connection);
    if ($congresses)
    {
        foreach ($congresses as $congress)
        {
            $query = "SELECT * FROM congressAgenda_" . $congress['id'];
            $result = $connection->query($query);
            if (mysqli_num_rows($result) > 0)
            {
                while ($row = mysqli_fetch_array($result))
                {
                    $item = packageAgendaItemRowIntoArray($row, $congress['id']);
                    if (!$agenda){ $agenda = []; }
                    array_push($agenda, $item);
                }
            }
        }
    }
    return $agenda;
}

function getAxoneronAgendaItemsFromDatabase($connection)
{
    $agenda = 0;
    $congresses = getAllCongresses($connection);
    if ($congresses)
    {
        foreach ($congresses as $congress)
        {
            $query = "SELECT * FROM congressAgenda_" . $congress['id'];
            $result = $connection->query($query);
            if (mysqli_num_rows($result) > 0)
            {
                while ($row = mysqli_fetch_array($result))
                {
                    $item = packageAgendaItemRowIntoArray($row, $congress['id']);
                    if (isAxoneronAssigned($item) || $item[TYPE] == INTERNAL)
                    {
                        if (!$agenda){ $agenda = []; }
                        array_push($agenda, $item);
                    }
                }
            }
        }
    }
    return $agenda;
}

function getAgendaItemByID($itemID, $congress, $connection)
{
    $item = 0;
    $congressID = $congress['id'];
    //$congressID = isset($_POST['congressID']) ? $_POST['congressID'] : null;
    $query = "SELECT * FROM congressAgenda_" . $congressID . " WHERE id = '$itemID'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $item = packageAgendaItemRowIntoArray($row, $congressID);
        }
    }
    return $item;
}

function getEarliestAgendaItemDate($agenda)
{
    $date = 0;
    foreach ($agenda as $item)
    {
        if (!$date)
        {
            $date = $item['startDate'];
        }
        else
        {
            if (strtotime($item['startDate']) < strtotime($date))
            {
                $date = $item['startDate'];
            }
        }
    }
    return $date;
}

function getLatestAgendaItemDate($agenda)
{
    $date = 0;
    foreach ($agenda as $item)
    {
        if (!$date)
        {
            $date = $item['endDate'];
        }
        else
        {
            if (strtotime($item['endDate']) > strtotime($date))
            {
                $date = $item['endDate'];
            }
        }
    }
    return $date;
}

function getSoonestAgendaItemDate($agenda)
{
    $date = 0;
    $now = date('Y-m-d H:i:s');
    foreach ($agenda as $item)
    {
        if (strtotime($item['startDate']) > strtotime($now))
        {
            if (!$date)
            {
                $date = $item['startDate'];
            }
            else
            {
                if (strtotime($item['startDate']) < strtotime($date))
                {
                    $date = $item['startDate'];
                }
            }
        }
    }
    return $date;
}

function getAgendaItemsForDate($agenda, $date)
{
    $items = 0;
    foreach ($agenda as $item)
    {
        $start = date("n/d/y", strtotime($item['startDate']));
        if ($start == date("n/d/y", strtotime($date)))
        {
            if (!$items){ $items = []; }
            array_push($items, $item);
        }
    }
    return $items;
}

function isAxoneronAssigned($item)
{
    $valid = 0;
    
    foreach ($item[ASSIGNMENT] as $assignment)
    {
        $valid = $assignment == "axoneron" ? 1 : $valid;
    }
    return $valid;
}

/*
 * Creates a new table 'congressAgenda_X', where X is the congress record id in the table 'congresses'
 * This table is used to list all agenda items (events) involved with a congress
 */
function generateCongressAgendaTable($congress, $connection)
{
    $query = "CREATE TABLE congressAgenda_" . $congress['congress']['id'] . " (";
    $query .= "id int(5) NOT NULL AUTO_INCREMENT,";
    $query .= "type varchar(32) NOT NULL,";
    $query .= "category varchar(32) NOT NULL,";
    $query .= "title varchar(128) NOT NULL,";
    $query .= "subTitle varchar(64) NOT NULL,";
    $query .= "location varchar(128) NOT NULL,";
    $query .= "priority int(1) NOT NULL,";
    $query .= "chair varchar(128) NOT NULL,";
    $query .= "presenters varchar(128) NOT NULL,";
    $query .= "startTime datetime NOT NULL,";
    $query .= "endTime datetime NOT NULL,";
    $query .= "assignment varchar (256) NOT NULL,";
    $query .= "sessionName varchar(256) NOT NULL,";
    $query .= "footnotes varchar(512) NOT NULL,";
    $query .= "PRIMARY KEY (id))";
    $result = $connection->query($query);

    return $result;
}

function deleteCongressAgendaTable($congress, $connection)
{
    $id = $congress['id'];
    $query = "DROP TABLE congressAgenda_" . $id;
    $result = $connection->query($query);
    return $result;
}

function modifyAgendaItem($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $congressID = $_POST['congressID'];
    $itemID = $_POST['itemID'];
    
    $title = urlencode($_POST['agendaTitle']);
    $title = strlen($title) > 128 ? substr($title, 0, 125) . "..." : $title;
    $subTitle = urlencode($_POST['agendaSubTitle']);
    $subTitle = strlen($subTitle) > 64 ? substr($title, 0, 61) . "..." : $subTitle;
    $location = urlencode($_POST['agendaLocation']);
    $location = strlen($location) > 128 ? substr($location, 0, 128) : $location;
    $type = $_POST['agendaType'];
    $priority = $_POST['agendaPriority'];
    $category = urlencode($_POST['agendaCategory']);
    $category = strlen($category) > 32 ? substr($category, 0, 29) . "..." : $category;
    $startTime = convertToSqlDateTime($_POST['agendaStartDate'], $_POST['agendaStartTime'] . $_POST['agendaStartMeridian']);
    $endTime = convertToSqlDateTime($_POST['agendaEndDate'], $_POST['agendaEndTime'] . $_POST['agendaEndMeridian']);
    
    $assignment = $_POST['agendaAssignment'];
    
    if (strlen($assignment) < 256)
    {
        $chair = "";
        $chairArray = explode(",", $_POST['agendaChair']);
        foreach ($chairArray as $chairPerson)
        {
            $chair .= urlencode($chairPerson);
            if (next($chairArray))
            {
                $chair .= ",";
            }
        }
        if (strlen($chair) < 128)
        {
            $presenters = "";
            $presentersArray = explode(",", $_POST['agendaPresenters']);
            foreach ($presentersArray as $presenter)
            {
                $presenters .= urlencode($presenter);
                if (next($presentersArray))
                {
                    $presenters .= ",";
                }
            }
            if (strlen($presenters) < 128)
            {
                $sessionName = urlencode($_POST['agendaSessionName']);
                $sessionName = strlen($sessionName) > 256 ? substr($sessionName, 0, 253) . "..." : $sessionName;
                $footnotes = urlencode($_POST['agendaFootnotes']);
                $footnotes = strlen($footnotes) > 512 ? substr($footnotes, 0, 509) . "..." : $footnotes;

                $query = "UPDATE congressAgenda_" . $congressID . " SET ";
                $query .= TYPE . " = '$type', ";
                $query .= CATEGORY . " = '$category', ";
                $query .= TITLE . " = '$title', ";
                $query .= SUB_TITLE . " = '$subTitle', ";
                $query .= LOCATION . " = '$location', ";
                $query .= PRIORITY . " = '$priority', ";
                $query .= CHAIR . " = '$chair', ";
                $query .= PRESENTERS . " = '$presenters', ";
                $query .= START_TIME . " = '$startTime', ";
                $query .= END_TIME . " = '$endTime', ";
                $query .= ASSIGNMENT . " = '$assignment', ";
                $query .= SESSION_NAME . " = '$sessionName', ";
                $query .= FOOTNOTES . " = '$footnotes' ";
                $query .= " WHERE id = '$itemID'";

                $result = $connection->query($query);
                if ($result)
                {
                    $recordData = array();
                    $recordData['type'] = POST_MODIFY_AGENDA_ITEM;
                    $recordData['data'] = $congressID . "," . $itemID;
                    $recordData['openEnd'] = '';
                    $result2 = addRecordToUser($user, $recordData, $user, $connection);
                    if ($result2)
                    {
                        $record = getRecordByFootprint($recordData, $user, $connection);
                        if ($record)
                        {
                            $code = -1;
                        }
                        else
                        {
                            $code = 1;
                            $errors = packageGeneralError($errors, 8);
                        }
                    }
                    else
                    {
                        $code = 1;
                        $errors = packageGeneralError($errors, 7);
                    }
                }
                else
                {
                    $code = 1;
                    $errors = packageGeneralError($errors, 6);
                }
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 5);
            }
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 4);
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 3);
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

function deleteAgendaItem($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $congressID = $_POST['congressID'];
    $itemID = $_POST['itemID'];
    $query = "DELETE FROM congressAgenda_" . $congressID . " WHERE id = '$itemID'";
    $result = $connection->query($query);
    if ($result)
    {
        $recordData['type'] = POST_REMOVE_AGENDA_ITEM_FROM_CONGRESS;
        $recordData['data'] = $congressID . "," . $itemID;
        $recordData['openEnd'] = '';
        $result2 = addRecordToUser($user, $recordData, $user, $connection);
        if ($result2)
        {
            $record = getRecordByFootprint($recordData, $user, $connection);
            if ($record)
            {
                $code = -1;
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 4);
            }
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 3);
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 2);
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

function uploadAgendaDataToRawFolder($inputName)
{
    $code = 0;
    $saveLocation = 0;

    if ($_FILES[$inputName]["size"] > FILE_IMPORT_SIZE_LIMIT)
    {
        $code = 1;
    }
    else
    {
        $raw_file = basename($_FILES[$inputName]["name"]);
        $extension = strtolower(pathinfo($raw_file,PATHINFO_EXTENSION));
        if($extension != "xls" && $extension != "xlsx")
        {
            $code = 2;
        }
        else
        {
            $target_dir = RAW_AGENDA_PATH;
            $fileDateString = date(getSaveForFileDateFormat(), time());
            $saveName = "Congress_" . $_POST["congressID"] . "_" . $fileDateString . "." . $extension;
            $saveLocation = $target_dir . $saveName;
            if (move_uploaded_file($_FILES[$inputName]["tmp_name"], $saveLocation))
            {
                $code = -1;
            }
            else
            {
                $code = 3;
            }
        }
    }

    return array(
        "code" => $code,
        "saveLocation" => $saveLocation
            );

}

function parseAgendaData($filePath, $connection)
{
    /*
     * Error Code Chart:
     * 1. Minimal column count not met
     * 2. Spreadsheet missing a required header
     * 3. A header is not a valid type
     * 4. Invalid item type
     * 5. Character limit for category met
     * 6. Character limit for title met
     * 7. Character limit for sub title met
     * 8. Start date format incorrect
     * 9. Start time format incorrect
     * 10. End date format incorrect
     * 11. End time format incorrect
     * 12. Character limit for location column met
     * 13. Invalid priority selected
     * 14. Contains an invalid user name in chair column
     * 15. Character limit for chair column met
     * 16. Contains an invalid user name in presenters column
     * 17. Character limit for presenters column met
     * 18. Contains an invalid user name in assignment column
     * 19. Character limit for assignment column met
     * 20. Contains an invalid session name in sessionName column
     * 21. Character limit for sessionName column met
     * 22. Character limit for footnotes column met
     */

    $errors = 0;
    $agenda = 0;

    $spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();
    
    $totalRows = $worksheet->getHighestRow();
    $highestColumn = $worksheet->getHighestColumn();
    $totalColumns = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

    // if we do not have the minimal column amount, invoke error and return/exit
    if ($totalColumns < 15)
    {
        $errors = array();
        $error = array(
            "code" => 1,
            "data" => $totalColumns
        );
        array_push($errors, $error);
        return getAgendaReturn($errors, $agenda);
    }
    
    $headerIndexes = array(
        TYPE => 0,
        CATEGORY => 0,
        TITLE => 0,
        SUB_TITLE => 0,
        START_DATE => 0,
        START_TIME => 0,
        END_DATE => 0,
        END_TIME => 0,
        LOCATION => 0,
        PRIORITY => 0,
        CHAIR => 0,
        PRESENTERS => 0,
        ASSIGNMENT => 0,
        SESSION_NAME => 0,
        FOOTNOTES => 0
    );
    
    // grab column index for all known column headers
    for ($i = 1 ; $i <= $totalColumns ; $i++)
    {
        $headerName = $worksheet->getCellByColumnAndRow($i, 1)->getValue();
        
        // check to see if the header name is invalid
        if ($headerName != TYPE &&
            $headerName != CATEGORY &&
            $headerName != TITLE &&    
            $headerName != SUB_TITLE &&
            $headerName != START_DATE &&
            $headerName != START_TIME &&
            $headerName != END_DATE &&
            $headerName != END_TIME &&
            $headerName != LOCATION &&
            $headerName != PRIORITY &&
            $headerName != CHAIR &&
            $headerName != PRESENTERS &&
            $headerName != ASSIGNMENT &&
            $headerName != SESSION_NAME &&
            $headerName != FOOTNOTES)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 3,
                "data" => $headerName
            );
        }
        
        if (isset($headerIndexes[$headerName])){ $headerIndexes[$headerName] = $i; }
    }
    
    // iterate through $headerIndexes and make sure we aren't missing anything
    $flagged = 0;
    foreach ($headerIndexes as $key => $headerIndex)
    {
        // if any header index is zero, invoke error continue until exit
        if (!$headerIndex)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 2,
                "data" => $key
            );
            array_push($errors, $error);
            $flagged = 1;
        }
    }
    if ($flagged)
    {
        return getAgendaReturn($errors, $agenda);
    }
    
    // check to see if the item (type) name is invalid
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $typeName = $worksheet->getCellByColumnAndRow($headerIndexes[TYPE], $row)->getValue();
        if ($typeName != _BREAK &&
            $typeName != EXHIBIT &&
            $typeName != EXPO_HOURS &&    
            $typeName != INTERNAL &&
            $typeName != POSTER &&
            $typeName != PRESENTATION &&
            $typeName != RECEPTION)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 4,
                "row" => $row,
                "data" => $typeName
            );
            array_push($errors, $error);
        }
    }
    
    // check to make sure the character limit isn't over 32 for each category cell
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $category = $worksheet->getCellByColumnAndRow($headerIndexes[CATEGORY], $row)->getValue();
        if (strlen($category) > 32)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 5,
                "row" => $row,
                "data" => strlen($category) > 32 ? substr($category, 0, 29) . "..." : $category
            );
            array_push($errors, $error);
        }
    }
    
    // check to make sure the character limit isn't over 128 for each title cell
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $title = $worksheet->getCellByColumnAndRow($headerIndexes[TITLE], $row)->getValue();
        if (strlen($title) > 128)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 6,
                "row" => $row,
                "data" => strlen($title) > 32 ? substr($title, 0, 29) . "..." : $title
            );
            array_push($errors, $error);
        }
    }
    
    // check to make sure the character limit isn't over 64 for each subtitle cell
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $subTitle = $worksheet->getCellByColumnAndRow($headerIndexes[SUB_TITLE], $row)->getValue();
        if (strlen($subTitle) > 64)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 7,
                "row" => $row,
                "data" => strlen($subTitle) > 32 ? substr($subTitle, 0, 29) . "..." : $subTitle
            );
            array_push($errors, $error);
        }
    }
    
    // check to make sure the startDate is in the future
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $startDate = $worksheet->getCellByColumnAndRow($headerIndexes[START_DATE], $row)->getValue();
        $item[START_DATE] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($startDate) -> format("m/d/Y");
        $todayDate = date("m/d/Y");
        $date1 = strtotime($item[START_DATE]);
        $date2 = strtotime($todayDate);
        if ($date1 < $date2)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 8,
                "row" => $row,
                "data" => $startDate
            );
            array_push($errors, $error);
        }
    }
    
    // check to make sure the startTime is the correct format
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $startTime = $worksheet->getCellByColumnAndRow($headerIndexes[START_TIME], $row)->getValue();
        if ($startTime == null)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 9,
                "row" => $row,
                "data" => $startTime
            );
            array_push($errors, $error);
        }
        else
        {
            $item[START_TIME] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($startTime) -> format("H:i");
        }
    }
    
    // check to make sure the endDate is in the future and after the startDate
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $item[END_DATE] = "";
        if (!$item[END_DATE] == "")
        {
            $startDate = $worksheet->getCellByColumnAndRow($headerIndexes[START_DATE], $row)->getValue();
            $item[START_DATE] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($startDate) -> format("m/d/Y");
            $endDate = $worksheet->getCellByColumnAndRow($headerIndexes[END_DATE], $row)->getValue();
            $item[END_DATE] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($endDate) -> format("m/d/Y");
            $date1 = strtotime($item[START_DATE]);
            $date2 = strtotime($item[END_DATE]);
            if ($date1 > $date2)
            {
                if (!$errors){ $errors = []; }
                $error = array(
                    "code" => 10,
                    "row" => $row,
                    "data" => $endDate
                );
                array_push($errors, $error);
            }
        }
    }
    
    // check to make sure the endTime is the correct format
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $endTime = $worksheet->getCellByColumnAndRow($headerIndexes[END_TIME], $row)->getValue();
        if ($endTime == null)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 11,
                "row" => $row,
                "data" => $endTime
            );
            array_push($errors, $error);
        }
        else
        {
            $item[END_TIME] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($startTime) -> format("H:i");
        }
    }
    
    // check to make sure the character limit isn't over 128 for each location cell
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $location = $worksheet->getCellByColumnAndRow($headerIndexes[LOCATION], $row)->getValue();
        if (strlen($location) > 128)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 12,
                "row" => $row,
                "data" => strlen($location) > 32 ? substr($location, 0, 29) . "..." : $location
            );
            array_push($errors, $error);
        }
    }
    
    // check to make sure priority is a correct value
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $priorityData = strtolower($worksheet->getCellByColumnAndRow($headerIndexes[PRIORITY], $row)->getValue());
        $item[PRIORITY] = $priorityData == "low" ? 1 : ($priorityData == "medium" ? 2 : ($priorityData == "high" ? 3 : 0));
        if (!$item[PRIORITY] == "")
        {
            if (($item[PRIORITY] == "undefined"))
            {
                if (!$errors){ $errors = []; }
                $error = array(
                    "code" => 13,
                    "row" => $row,
                    "data" => $priorityData
                );
                array_push($errors, $error);
            }
        }
    }

    // check to make sure the character limit isn't over 128 for each chair cell
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $chair = $worksheet->getCellByColumnAndRow($headerIndexes[CHAIR], $row)->getValue();
        if (strlen($chair) > 128)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 15,
                "row" => $row,
                "data" => strlen($chair) > 32 ? substr($chair, 0, 29) . "..." : $chair
            );
            array_push($errors, $error);
        }
    }

    // check to make sure the character limit isn't over 128 for each presenters cell
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $presenters = $worksheet->getCellByColumnAndRow($headerIndexes[PRESENTERS], $row)->getValue();
        if (strlen($presenters) > 128)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 17,
                "row" => $row,
                "data" => strlen($presenters) > 32 ? substr($presenters, 0, 29) . "..." : $presenters
            );
            array_push($errors, $error);
        }
    }
    
    // check against database for current users
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $assignments = explode(",", $worksheet->getCellByColumnAndRow($headerIndexes[ASSIGNMENT], $row)->getValue());
        $item[ASSIGNMENT] = count($assignments) > 0 ? array() : 0;

        if (is_array($item[ASSIGNMENT]))
        {
            foreach($assignments as $assignment)
            {
                $assignment = trim($assignment);
                $assignedUser = 0;
                if (strtolower($assignment) == "axoneron")
                {
                    $assignedUser = "axoneron";
                }
                else if (strpos($assignment, " ") !== false)
                {
                    $first = explode(" ", $assignment)[0];
                    $last = explode(" ", $assignment)[1];
                    $assignedUser = getUserByFirstLast($first, $last, $connection);
                }
                else
                {
                    $assignedUser = getUserByEmail($assignment, $connection);
                }
                if ($assignedUser)
                {
                    if ($assignedUser == "axoneron")
                    {
                        array_push($item[ASSIGNMENT], "axoneron");
                    }
                    else
                    {
                        array_push($item[ASSIGNMENT], $assignedUser["id"]);
                    }
                }
                else
                {
                    array_push($item[ASSIGNMENT], "");
                    if (!$errors){ $errors = []; }
                    $error = array(
                        "code" => 18,
                        "row" => $row,
                        "data" => $assignment
                    );
                    array_push($errors, $error);
                }
            }
        }
    }
    
    // check to make sure the character limit isn't over 128 for each assignments cell
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $assignments = $worksheet->getCellByColumnAndRow($headerIndexes[ASSIGNMENT], $row)->getValue();
        if (strlen($assignments) > 128)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 19,
                "row" => $row,
                "data" => strlen($assignments) > 32 ? substr($assignments, 0, 29) . "..." : $assignments
            );
            array_push($errors, $error);
        }
    }
    
    // check to make sure the character limit isn't over 256 for each session name cell
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $sessionName = $worksheet->getCellByColumnAndRow($headerIndexes[SESSION_NAME], $row)->getValue();
        if (strlen($sessionName) > 256)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 21,
                "row" => $row,
                "data" => strlen($sessionName) > 32 ? substr($sessionName, 0, 29) . "..." : $sessionName
            );
            array_push($errors, $error);
        }
    }
    
    // check to make sure the character limit isn't over 512 for each footnotes cell
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $footnotes = $worksheet->getCellByColumnAndRow($headerIndexes[FOOTNOTES], $row)->getValue();
        if (strlen($footnotes) > 512)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 22,
                "row" => $row,
                "data" => strlen($footnotes) > 32 ? substr($footnotes, 0, 29) . "..." : $footnotes
            );
            array_push($errors, $error);
        }
    }
    
    //iterate through all rows after 1 and package into agenda item objects
    for ($row = 2 ; $row <= $totalRows ; $row++)
    {
        $fatal = -1;
        foreach ($errors as $e)
        {
            if ($row == $e['row'])
            {
                $fatalCheck = isRowFatal($e['code']);
                if ($fatalCheck == 0)
                {
                    $fatal = 1;
                }
                else
                {
                    $fatal = 0;
                }
            }
        }
        if ($fatal == 0)
        {
            $result = getAgendaItemFromSpreadsheetRow($row, $headerIndexes, $worksheet, $connection);
            if (!$agenda){ $agenda = []; }
            if ($result["item"])
            {
                if (!$agenda){ $agenda = []; }
                array_push($agenda, $result["item"]);
            }
        }
        else if ($row && ($fatal == -1))
        {
            $result = getAgendaItemFromSpreadsheetRow($row, $headerIndexes, $worksheet, $connection);
            if (!$agenda){ $agenda = []; }
            if ($result["item"])
            {
                if (!$agenda){ $agenda = []; }
                array_push($agenda, $result["item"]);
            }
        }
    }
    return getAgendaReturn($errors, $agenda);
}


function getAgendaReturn($errors, $agenda)
{
    return array(
        "errors" => $errors,
        "agenda" => $agenda
    );
}

function getAgendaItemFromSpreadsheetRow($row, $headerIndexes, $worksheet, $connection)
{
    $item = array();

    $item[TYPE] = $worksheet->getCellByColumnAndRow($headerIndexes[TYPE], $row)->getValue();
    $item[SUB_TITLE] = $worksheet->getCellByColumnAndRow($headerIndexes[SUB_TITLE], $row)->getValue();
    $item[CATEGORY] = $worksheet->getCellByColumnAndRow($headerIndexes[CATEGORY], $row)->getValue();
    $item[LOCATION] = $worksheet->getCellByColumnAndRow($headerIndexes[LOCATION], $row)->getValue();
    $item[SESSION_NAME] = $worksheet->getCellByColumnAndRow($headerIndexes[SESSION_NAME], $row)->getValue();
    $item[FOOTNOTES] = $worksheet->getCellByColumnAndRow($headerIndexes[FOOTNOTES], $row)->getValue();

    $startDate = $worksheet->getCellByColumnAndRow($headerIndexes[START_DATE], $row)->getValue();
    $item[START_DATE] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($startDate) -> format("m/d/Y");

    $startTime = $worksheet->getCellByColumnAndRow($headerIndexes[START_TIME], $row)->getValue();
    $item[START_TIME] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($startTime) -> format("H:i");

    $endDate = $worksheet->getCellByColumnAndRow($headerIndexes[END_DATE], $row)->getValue();
    $endDate = $endDate ? $endDate : $startDate;
    $item[END_DATE] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($endDate) -> format("m/d/Y");

    $endTime = $worksheet->getCellByColumnAndRow($headerIndexes[END_TIME], $row)->getValue();
    $item[END_TIME] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($endTime) -> format("H:i");

    $item[TITLE] = $item[TYPE] == EXPO_HOURS ? "Exposition Hours" : $worksheet->getCellByColumnAndRow($headerIndexes[TITLE], $row)->getValue();
    $priorityData = strtolower($worksheet->getCellByColumnAndRow($headerIndexes[PRIORITY], $row)->getValue());
    $item[PRIORITY] = $priorityData == "low" ? 1 : ($priorityData == "medium" ? 2 : ($priorityData == "high" ? 3 : 0));
    
    $chair = explode(",", $worksheet->getCellByColumnAndRow($headerIndexes[CHAIR], $row)->getValue());
    $item[CHAIR] = count($chair) > 0 ? array() : 0;
    if (is_array($item[CHAIR]))
    {
        foreach($chair as $c)
        {
            $c = trim($c);
            array_push($item[CHAIR], $c);
        }
    }
    
    $presenters = explode(",", $worksheet->getCellByColumnAndRow($headerIndexes[PRESENTERS], $row)->getValue());
    $item[PRESENTERS] = count($presenters) > 0 ? array() : 0;
    if (is_array($item[PRESENTERS]))
    {
        foreach($presenters as $p)
        {
            $p = trim($p);
            array_push($item[PRESENTERS], $p);
        }
    }

    $assignments = explode(",", $worksheet->getCellByColumnAndRow($headerIndexes[ASSIGNMENT], $row)->getValue());
    $item[ASSIGNMENT] = count($assignments) > 0 ? array() : 0;
    if (is_array($item[ASSIGNMENT]))
    {
        foreach($assignments as $assignment)
        {
            $assignment = trim($assignment);
            $assignedUser = 0;
            if (strtolower($assignment) == "axoneron")
            {
                $assignedUser = "axoneron";
            }
            else if (strpos($assignment, " ") !== false)
            {
                $first = explode(" ", $assignment)[0];
                $last = explode(" ", $assignment)[1];
                $assignedUser = getUserByFirstLast($first, $last, $connection);
            }
            else
            {
                $assignedUser = getUserByEmail($assignment, $connection);
            }
            if ($assignedUser)
            {
                if ($assignedUser == "axoneron")
                {
                    array_push($item[ASSIGNMENT], "axoneron");
                }
                else
                {
                    array_push($item[ASSIGNMENT], $assignedUser["id"]);
                }
            }
        }
    }
    
    return array(
        "item" => $item
    );
}

function addAgendaToDatabase($agenda, $congressID, $connection)
{
    $results = 0;
    $errors = 0;
    if (is_array($agenda) || is_object($agenda))
    {
        foreach($agenda as $item)
        {
            $return = addItemToAgendaTable($item, $congressID, $connection);
            if ($return['result'])
            {
                if (!$results){ $results = []; }
                array_push($results, $return['result']);
            }
            else
            {
                if (!$errors){ $errors = []; }
                $error = array(
                    "code" => 51
                );
                array_push($errors, $error);
            }
        }
    }
    else
    {
        if (!$errors){ $errors = []; }
        $error = array(
            "code" => 50
        );
        array_push($errors, $error);
    }
    return getAgendaReturn($errors, $agenda);
}

function addItemToAgendaTable($item, $congressID, $connection)
{
    $result = 0;
    $query = "";
    $chair = "";
    foreach ($item[CHAIR] as $chairPerson)
    {
        $chair .= urlencode($chairPerson);
        if (next($item[CHAIR]))
        {
            $chair .= ",";
        }
    }
    $presenters = "";
    foreach ($item[PRESENTERS] as $presenter)
    {
        $presenters .= urlencode($presenter);
        if (next($item[PRESENTERS]))
        {
            $presenters .= ",";
        }
    }
    $assignment = implode(",",$item[ASSIGNMENT]);
    if (strlen($chair) > 128 || strlen($presenters) > 128 || strlen($assignment) > 256)
    {
      $query = $item[TITLE] . " : ";
      $query .= strlen($chair) > 128 ? "chair too long (128)" : (strlen($presenters) > 128 ? "presenters too long (128)" : "assignment too long (256)");
    }
    else
    {
      $category = urlencode($item[CATEGORY]);
      $category = strlen($category) > 32 ? substr($category, 0, 29) . "..." : $category;
      $title = urlencode($item[TITLE]);
      $title = strlen($title) > 128 ? substr($title, 0, 125) . "..." : $title;
      $subTitle = urlencode($item[SUB_TITLE]);
      $subTitle = strlen($subTitle) > 64 ? substr($subTitle, 0, 61) . "..." : $subTitle;
      $startTime = convertToSqlDateTime($item[START_DATE], $item[START_TIME]);
      $endTime = convertToSqlDateTime($item[END_DATE], $item[END_TIME]);
      $location = urlencode($item[LOCATION]);
      $location = strlen($location) > 128 ? substr($location, 0, 128) : $location;
      $sessionName = urlencode($item[SESSION_NAME]);
      $sessionName = strlen($sessionName) > 256 ? substr($sessionName, 0, 253) . "..." : $sessionName;
      $footnotes = urlencode($item[FOOTNOTES]);
      $footnotes = strlen($footnotes) > 512 ? substr($footnotes, 0, 509) . "..." : $footnotes;
      $query = "INSERT INTO congressAgenda_" . $congressID . " (id, ";
      $query .= TYPE . ", ";
      $query .= CATEGORY . ", ";
      $query .= TITLE . ", ";
      $query .= SUB_TITLE . ", ";
      $query .= LOCATION . ", ";
      $query .= PRIORITY . ", ";
      $query .= CHAIR . ", ";
      $query .= PRESENTERS . ", ";
      $query .= START_TIME . ", ";
      $query .= END_TIME . ", ";
      $query .= ASSIGNMENT . ", ";
      $query .= SESSION_NAME . ", ";
      $query .= FOOTNOTES . ") VALUES (NULL, '";
      $query .= $item[TYPE] ."', '";
      $query .= $category ."', '";
      $query .= $title ."', '";
      $query .= $subTitle ."', '";
      $query .= $location ."', '";
      $query .= $item[PRIORITY] ."', '";
      $query .= $chair ."', '";
      $query .= $presenters ."', '";
      $query .= $startTime ."', '";
      $query .= $endTime ."', '";
      $query .= $assignment ."', '";
      $query .= $sessionName ."', '";
      $query .= $footnotes ."')";
      $result = $connection->query($query);
    }
    return array(
        'result' => $result,
        'query' => $query
    );
}

function packageAgendaItemRowIntoArray($row, $congressID)
{
    $chair = 0;
    $rawChair = explode(",", $row[CHAIR]);
    if (count($rawChair) > 0)
    {
        $chair = [];
        foreach ($rawChair as $chairperson)
        {
            array_push($chair, urldecode($chairperson));
        }
    }

    $presenters = 0;
    $rawPresenters = explode(",", $row[PRESENTERS]);
    if (count($rawPresenters) > 0)
    {
        $presenters = [];
        foreach ($rawPresenters as $presenter)
        {
            array_push($presenters, urldecode($presenter));
        }
    }

    $item = array(
        'id' => $row['id'],
        TYPE => $row[TYPE],
        CATEGORY => urldecode($row[CATEGORY]),
        TITLE => urldecode($row[TITLE]),
        SUB_TITLE => urldecode($row[SUB_TITLE]),
        LOCATION => urldecode($row[LOCATION]),
        PRIORITY => $row[PRIORITY],
        CHAIR => $chair,
        PRESENTERS => $presenters,
        START_DATE => parseDateFromDateTime($row[START_TIME]),
        START_TIME => parseTimeFromDateTime($row[START_TIME]),
        END_DATE => parseDateFromDateTime($row[END_TIME]),
        END_TIME => parseTimeFromDateTime($row[END_TIME]),
        ASSIGNMENT => explode(",", $row[ASSIGNMENT]),
        SESSION_NAME => urldecode($row[SESSION_NAME]),
        FOOTNOTES => urldecode($row[FOOTNOTES]),
        "congressID" => $congressID
    );

    return $item;
}

function createInsightsDirectory($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    
    if (isset($_POST['congressID']) &&
        isset($_POST['itemID']))
    {
        $congress = getCongressById($_POST['congressID'], $connection);
        if ($congress)
        {
            $item = getAgendaItemByID($_POST['itemID'], $congress, $connection);
            if ($item)
            {
                $insights = getInsights($user, $congress, $item);
                if (!$insights)
                {
                    $path = getInsightPath($user['id'], $congress['id'], $item['id']);
                    mkdir($path, 0777, true);
                    $insights = getInsights($user, $congress, $item);
                    if ($insights)
                    {
                        $recordData = array();
                        $recordData['type'] = ADDED_INSIGHT;
                        $recordData['data'] = $congress['id'] . "," . $item['id'];
                        $recordData['openEnd'] = "";
                        $result = addRecordToUser($user, $recordData, $user, $connection);
                        if ($result)
                        {
                            $record = getRecordByFootprint($recordData, $user, $connection);
                            if ($record)
                            {
                                $code = -1;
                            }
                            else
                            {
                                // record footprint error
                                $code = 1;
                                $errors = packageGeneralError($errors, 7);
                            }
                        }
                        else
                        {
                            // add record to user error
                            $code = 1;
                            $errors = packageGeneralError($errors, 6);
                        }
                    }
                    else
                    {
                        // no insights path could be found
                        $code = 1;
                        $errors = packageGeneralError($errors, 5);
                    }
                }
                else
                {
                    // no insights could be found
                    $code = 1;
                    $errors = packageGeneralError($errors, 4);
                }
            }
            else
            {
                // no agenda item could be found
                $code = 1;
                $errors = packageGeneralError($errors, 3);
            }
        }
        else
        {
            // no congress could be found
            $code = 1;
            $errors = packageGeneralError($errors, 2);
        }
    }
    $return = array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
    return $return;
}

function addGeneralInsight($user, $connection)
{
    return updateGeneralInsight($user, $connection, ADDED_GENERAL_INSIGHT);
}

function modifyGeneralInsight($user, $connection)
{
    return updateGeneralInsight($user, $connection, MODIFIED_GENERAL_INSIGHT);
}

function updateGeneralInsight($user, $connection, $actionType)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    
    if (isset($_POST['congressID']) &&
        isset($_POST['itemID']) &&
        isset($_POST['notePadData']))
    {
        $congress = getCongressById($_POST['congressID'], $connection);
        if ($congress)
        {
            $item = getAgendaItemByID($_POST['itemID'], $congress, $connection);
            if ($item)
            {
                $insights = getInsights($user, $congress, $item);
                if ($insights)
                {
                    $path = getInsightPath($user['id'], $congress['id'], $item['id']);
                    $path .= "/General Notes.txt";
                    $fp = fopen($path,"w");  
                    fputs($fp,$_POST['notePadData']);
                    fclose($fp);
                    $recordData = array();
                    $recordData['type'] = $actionType;
                    $recordData['data'] = $congress['id'] . "," . $item['id'];
                    $recordData['openEnd'] = "";
                    $result = addRecordToUser($user, $recordData, $user, $connection);
                    if ($result)
                    {
                        $record = getRecordByFootprint($recordData, $user, $connection);
                        if ($record)
                        {
                            $code = -1;
                        }
                        else
                        {
                            // record footprint error
                            $code = 1;
                            $errors = packageGeneralError($errors, 6);
                        }
                    }
                    else
                    {
                        // add record to user error
                        $code = 1;
                        $errors = packageGeneralError($errors, 5);
                    }
                }
                else
                {
                    // no insights could be found
                    $code = 1;
                    $errors = packageGeneralError($errors, 4);
                }
            }
            else
            {
                // no agenda item could be found
                $code = 1;
                $errors = packageGeneralError($errors, 3);
            }
        }
        else
        {
            // no congress could be found
            $code = 1;
            $errors = packageGeneralError($errors, 2);
        }
    }
    $return = array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
    return $return;
}

function addInsightPost($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    
    if (isset($_POST['congressID']) &&
        isset($_POST['itemID']) &&
        isset($_POST['insightPostTitle']))
    {
        $congress = getCongressById($_POST['congressID'], $connection);
        if ($congress)
        {
            $item = getAgendaItemByID($_POST['itemID'], $congress, $connection);
            if ($item)
            {
                $insights = getInsights($user, $congress, $item);
                if ($insights)
                {
                    $path = getInsightPath($user['id'], $congress['id'], $item['id']);
                    $folder = preg_replace('/[^A-Za-z0-9\- ]/', '', $_POST['insightPostTitle']);
                    $path .= "/" . $folder;
                    mkdir($path, 0777, true);
                    uploadInsightImage($path, "imageFile");
                    if (isset($_POST['insightPostNotes']))
                    {
                        if ($_POST['insightPostNotes'] != "")
                        {
                            $path .= "/Notes.txt";
                            $fp = fopen($path,"w");
                            fputs($fp,$_POST['insightPostNotes']);
                            fclose($fp);
                        }
                    }
                    $recordData = array();
                    $recordData['type'] = ADDED_INSIGHT_POST;
                    $recordData['data'] = $congress['id'] . "," . $item['id'] . "," . urlencode($folder);
                    $recordData['openEnd'] = "";
                    $result = addRecordToUser($user, $recordData, $user, $connection);
                    if ($result)
                    {
                        $record = getRecordByFootprint($recordData, $user, $connection);
                        if ($record)
                        {
                            $code = -1;
                        }
                        else
                        {
                            // record footprint error
                            $code = 1;
                            $errors = packageGeneralError($errors, 6);
                        }
                    }
                    else
                    {
                        // add record to user error
                        $code = 1;
                        $errors = packageGeneralError($errors, 5);
                    }
                }
                else
                {
                    // no insights could be found
                    $code = 1;
                    $errors = packageGeneralError($errors, 4);
                }
            }
            else
            {
                // no agenda item could be found
                $code = 1;
                $errors = packageGeneralError($errors, 3);
            }
        }
        else
        {
            // no congress could be found
            $code = 1;
            $errors = packageGeneralError($errors, 2);
        }
    }
    $return = array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
    return $return;
}

function editInsightPost($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    
    if (isset($_POST['congressID']) &&
            isset($_POST['itemID']) &&
            isset($_POST['previousTitle']) &&
            isset($_POST['insightEditTitle']))
    {
        $congress = getCongressById($_POST['congressID'], $connection);
        if ($congress)
        {
            $item = getAgendaItemByID($_POST['itemID'], $congress, $connection);
            if ($item)
            {
                $insights = getInsights($user, $congress, $item);
                if ($insights)
                {
                    $path = getInsightPath($user['id'], $congress['id'], $item['id']);
                    $folder = preg_replace('/[^A-Za-z0-9\- ]/', '', $_POST['insightEditTitle']);
                    $previousFolder = $_POST['previousTitle'];
                    $currentPath = $path . "/" . $folder;
                    if (!file_exists($currentPath))
                    {
                        $previousPath = $path . "/" . $previousFolder;
                        rename($previousPath, $currentPath);
                    }
                    if (isset($_POST['removeImage']))
                    {
                        $imagePath = $currentPath . "/Image.png";
                        if (file_exists($imagePath))
                        {
                            unlink($imagePath);
                        }
                    }
                    else
                    {
                        uploadInsightImage($currentPath, "editFile");
                    }
                    if (isset($_POST['insightEditNotes']))
                    {
                        if ($_POST['insightEditNotes'] != "")
                        {
                            $currentPath .= "/Notes.txt";
                            $fp = fopen($currentPath,"w");
                            fputs($fp,$_POST['insightEditNotes']);
                            fclose($fp);
                        }
                    }
                    $recordData = array();
                    $recordData['type'] = MODIFIED_INSIGHT_POST;
                    $recordData['data'] = $congress['id'] . "," . $item['id'] . "," . urlencode($previousFolder) . "," . urlencode($folder);
                    $recordData['openEnd'] = "";
                    $result = addRecordToUser($user, $recordData, $user, $connection);
                    if ($result)
                    {
                        $record = getRecordByFootprint($recordData, $user, $connection);
                        if ($record)
                        {
                            $code = -1;
                        }
                        else
                        {
                            // record footprint error
                            $code = 1;
                            $errors = packageGeneralError($errors, 6);
                        }
                    }
                    else
                    {
                        // add record to user error
                        $code = 1;
                        $errors = packageGeneralError($errors, 5);
                    }
                }
                else
                {
                    // no insights could be found
                    $code = 1;
                    $errors = packageGeneralError($errors, 4);
                }
            }
            else
            {
                // no agenda item could be found
                $code = 1;
                $errors = packageGeneralError($errors, 3);
            }
        }
        else
        {
            // no congress could be found
            $code = 1;
            $errors = packageGeneralError($errors, 2);
        }
    }
    $return = array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
    return $return;
}

function deleteInsightPost($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    
    if (isset($_POST['congressID']) &&
        isset($_POST['itemID']) &&
        isset($_POST['postTitle']))
    {
        $congress = getCongressById($_POST['congressID'], $connection);
        if ($congress)
        {
            $item = getAgendaItemByID($_POST['itemID'], $congress, $connection);
            if ($item)
            {
                $path = getInsightPath($user['id'], $congress['id'], $item['id']);
                $path .= $_POST['postTitle'];
                if (file_exists($path))
                {
                    array_map('unlink', glob("$path/*"));
                    rmdir($path);
                    $recordData = array();
                    $recordData['type'] = REMOVED_INSIGHT_POST;
                    $recordData['data'] = $congress['id'] . "," . $item['id'] . "," . $_POST['postTitle'];
                    $recordData['openEnd'] = "";
                    $result = addRecordToUser($user, $recordData, $user, $connection);
                    if ($result)
                    {
                        $record = getRecordByFootprint($recordData, $user, $connection);
                        if ($record)
                        {
                            $code = -1;
                        }
                        else
                        {
                            // record footprint error
                            $code = 1;
                            $errors = packageGeneralError($errors, 6);
                        }
                    }
                    else
                    {
                        // add record to user error
                        $code = 1;
                        $errors = packageGeneralError($errors, 5);
                    }
                }
                else
                {
                    // file doesnt exist
                    $code = 1;
                    $errors = packageGeneralError($errors, 4);
                }
            }
            else
            {
                // no agenda item could be found
                $code = 1;
                $errors = packageGeneralError($errors, 3);
            }
        }
        else
        {
            // no congress could be found
            $code = 1;
            $errors = packageGeneralError($errors, 2);
        }
    }
    $return = array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
    return $return;
}

function uploadInsightImage($path, $inputName)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    
    if (isset($_FILES))
    {
        if (isset($_FILES[$inputName]))
        {
            if (isset($_FILES[$inputName]['name']))
            {
                if ($_FILES[$inputName]['name'] != '')
                {
                    if ($_FILES[$inputName]["size"] > FILE_IMPORT_SIZE_LIMIT)
                    {
                        // file import size too large
                        $code = 1;
                        $errors = packageGeneralError($errors, 14);
                    }
                    else
                    {
                        $raw_file = basename($_FILES[$inputName]["name"]);
                        $extension = strtolower(pathinfo($raw_file,PATHINFO_EXTENSION));
                        if($extension != "png" && $extension != "jpg" && $extension != "jpeg")
                        {
                            // incorrect file extension
                            $code = 1;
                            $errors = packageGeneralError($errors, 15);
                        }
                        else
                        {
                            $saveName = "Image.png";
                            $thumbLocation = $path . "/Thumb.png";
                            $saveLocation = $path . "/" . $saveName;
                            
                            if ($extension != "png")
                            {
                                $exif = exif_read_data($_FILES[$inputName]["tmp_name"]);
                                list($currentW, $currentH) = getimagesize($_FILES[$inputName]["tmp_name"]);
                                $w = $currentW;
                                $h = $currentH;
                                $image = imagecreatefromjpeg($_FILES[$inputName]["tmp_name"]);
                                if ($currentW > 2048 || $currentH > 2048)
                                {
                                    if ($currentW > 6144 || $currentH > 6144)
                                    {
                                        // resolution of file too large
                                        $code = 1;
                                        $errors = packageGeneralError($errors, 17);
                                    }
                                    else
                                    {
                                        $w = 2048;
                                        $h = (int) ((2048 / $currentW) * $currentH);
                                        $image2 = imagecreatetruecolor($w, $h);
                                        imagecopyresampled($image2, $image, 0, 0, 0, 0, $w, $h, $currentW, $currentH);
                                        $image = $image2;
                                        if (!empty($exif['Orientation']))
                                        {
                                            switch ($exif['Orientation'])
                                            {
                                                case 3: // 180 rotate left
                                                    $image = imagerotate($image, 180, 0);
                                                    break;
                                                case 6: // 90 rotate right
                                                    $image = imagerotate($image, -90, 0);
                                                    echo "complete";
                                                    break;
                                                case 8:    // 90 rotate left
                                                    $image = imagerotate($image, 90, 0);
                                                    break;
                                            }
                                        }
                                        imagepng($image, $saveLocation, 7);
                                    }
                                }
                                else if (!empty($exif['Orientation']))
                                {
                                    switch ($exif['Orientation'])
                                    {
                                        case 3: // 180 rotate left
                                            $image = imagerotate($image, 180, 0);
                                            break;
                                        case 6: // 90 rotate right
                                            $image = imagerotate($image, -90, 0);
                                            echo "complete";
                                            break;
                                        case 8:    // 90 rotate left
                                            $image = imagerotate($image, 90, 0);
                                            break;
                                    }
                                }
                                else
                                {
                                    imagepng(imagecreatefromstring(file_get_contents($_FILES[$inputName]["tmp_name"])), $saveLocation, 7);
                                }
                                createThumbnail($saveLocation, $thumbLocation, 256);
                                $code = -1;
                            }
                            else
                            {
                                if (move_uploaded_file($_FILES[$inputName]["tmp_name"], $saveLocation))
                                {
                                    createThumbnail($saveLocation, $thumbLocation, 256);
                                    $code = -1;
                                }
                                else
                                {
                                    // file not moved to temp location
                                    $code = 1;
                                    $errors = packageGeneralError($errors, 16);
                                }
                            }
                        }
                    }
                }
                else
                {
                    // check to make sure name isn't blank
                    $code = 1;
                    $errors = packageGeneralError($errors, 13);
                }
            }
            else
            {
                // name parameter not set
                $code = 1;
                $errors = packageGeneralError($errors, 12);
            }
        }
        else
        {
            // $_FILES input name not set
            $code = 1;
            $errors = packageGeneralError($errors, 11);
        }
    }
    else
    {
        // $_FILES not set
        $code = 1;
        $errors = packageGeneralError($errors, 10);
    }
    
    $return = array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
    return $return;
}

function getInsights($user, $congress, $item)
{
    $insights = 0;
    $path = getInsightPath($user['id'], $congress['id'], $item['id']);
    if (file_exists($path))
    {
        $insights = packageInsightsIntoArray($user['id'], $congress['id'], $item['id']);
    }
    return $insights;
}

function userHasInsights($user, $congress, $connection)
{
    $valid = 0;
    $agenda = getAgendaFromDatabase($congress, $connection);
    if ($agenda)
    {
        foreach ($agenda as $item)
        {
            $valid = !getInsights($user, $congress, $item) ? $valid : 1;
        }
    }
    return $valid;
}

function congressHasInsights($congress, $connection)
{
    $valid = 0;
    $agenda = getAgendaFromDatabase($congress, $connection);
    if ($agenda)
    {
        $users = getAllUsers($connection);
        foreach ($users as $user)
        {
            $valid = userHasInsights($user, $congress, $connection) ? 1 : $valid;
        }
    }
    return $valid;
}

/*
function packageInsights($author, $connection)
{
    $code = 0;
    $downloadPath = 0;
    
    if (isset($_POST['congressID']))
    {
        $congress = getCongressById($_POST['congressID'], $connection);
        if ($congress)
        {
            $users = getAllUsers($connection);
            $agenda = getAgendaFromDatabase($congress, $connection);
            if ($users && $agenda)
            {
                
                $typeFilter = isset($_POST['typeFilter']) ? $_POST['typeFilter'] : 0;
                $dateFilter = isset($_POST['dateFilter']) ? $_POST['dateFilter'] : 0;
                $typeFilter = $typeFilter === "" ? 0 : $typeFilter;
                $dateFilter = $dateFilter === "" ? 0 : $dateFilter;
                
                $fileDateString = date(getSaveForFileDateFormat(), time());
                $folderName = preg_replace('/[^A-Za-z0-9\- ]/', '', $congress['shortName']);
                $downloadPath = DOWNLOAD_PATH . $folderName . "_" . $fileDateString;
                $textFileData = "
------------------ INSIGHTS FOR '" . $congress['shortName'] . "' ------------------

";
                foreach ($users as $u)
                {
                    if (userHasInsights($u, $congress, $connection))
                    {
                        $textFileData .= "


-------------- " . $u['first'] . " " . $u['last'] . " insights:
";
                        foreach ($agenda as $item)
                        {
                            $typeSafe = 1;
                            $dateSafe = 1;
                            
                            if ($typeFilter)
                            {
                                $typeSafe = $typeFilter == $item[TYPE] ? 1 : 0;
                            }
                            if ($dateFilter)
                            {
                                $dateSafe = 0;
                                if (strtotime($item[START_DATE]) <= strtotime($dateFilter) &&
                                        strtotime($item[END_DATE]) >= strtotime($dateFilter))
                                {
                                    $dateSafe = 1;
                                }
                            }
                            
                            if ($typeSafe && $dateSafe)
                            {
                                if (!file_exists($downloadPath))
                                {
                                    mkdir($downloadPath, 0777, true);
                                }
                                $insights = getInsights($u, $congress, $item);
                                $path = getInsightPath($u['id'], $congress['id'], $item['id']);
                                $genNotesPath = $path . "General Notes.txt";
                                if (file_exists($genNotesPath))
                                {
                                    $title = preg_replace('/[^A-Za-z0-9\- ]/', '', $item[TITLE]);
                                    $textFileData .= "

    ------ '" .  $item[TITLE] . "'
    General Insights:
    " . $insights["generalNotes"] . "
    ";
                                    foreach ($insights['posts'] as $post)
                                    {
                                        $textFileData .= "
    Specific insight ('" .  $post['title'] . "'):
    " . $post['notes'] . "
    ";
                                    }
                                    $newSavePath = $downloadPath . "/" . $u['last'] . "_" . $u['first'] . "/" . $title;

                                    foreach (scandir($path) as $folder)
                                    {
                                        if (strpos($folder, ".") === false)
                                        {
                                            $jpgSourcePath = $path . "/" . $folder . "/Image.jpg";
                                            $jpegSourcePath = $path . "/" . $folder . "/Image.jpeg";
                                            $pngSourcePath = $path . "/" . $folder . "/Image.png";
                                            if (file_exists($jpgSourcePath) ||
                                                    file_exists($jpegSourcePath) ||
                                                    file_exists($pngSourcePath))
                                            {
                                                mkdir($newSavePath, 0777, true);
                                                if (file_exists($jpgSourcePath))
                                                {
                                                    $pastePath = $newSavePath . "/" . $folder . ".jpg";
                                                    copy($jpgSourcePath, $pastePath);
                                                }
                                                if (file_exists($jpegSourcePath))
                                                {
                                                    $pastePath = $newSavePath . "/" . $folder . ".jpeg";
                                                    copy($jpegSourcePath, $pastePath);
                                                }
                                                if (file_exists($pngSourcePath))
                                                {
                                                    $pastePath = $newSavePath . "/" . $folder . ".png";
                                                    copy($pngSourcePath, $pastePath);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                }
                
                
                if (file_exists($downloadPath))
                {
                    $fp = fopen($downloadPath . "/Insights.txt","w");
                    fputs($fp,$textFileData);
                    fclose($fp);
                    new GoodZipArchive($downloadPath, $downloadPath . '.zip') ;
                    $downloadPath = $downloadPath . ".zip";
                    $recordData = array();
                    $recordData['type'] = PACKAGED_INSIGHTS;
                    $recordData['data'] = $congress['id'] . "," . $typeFilter . "," . $dateFilter . "," . urlencode($downloadPath);
                    $recordData['openEnd'] = "";
                    $result = addRecordToUser($author, $recordData, $author, $connection);
                    if ($result)
                    {
                        $code = -1;
                    }
                    else
                    {
                        $code = 5;
                    }
                }
                else
                {
                    $code = 4;
                }
                
            }
            else
            {
                $code = 3;
            }
            
        }
        else
        {
            $code = 2;
        }
    }
    else
    {
        $code = 1;
    }
    
    return array(
        "code" => $code,
        "downloadPath" => $downloadPath
    );
}
 * 
 */

function getInsightPath($userID, $congressID, $itemID)
{
    $path = INSIGHTS_PATH . getInsightIdentifier($userID, $congressID, $itemID) . "/";
    return $path;
}

function getInsightIdentifier($userID, $congressID, $itemID)
{
    return $userID . "_" . $congressID . "_" . $itemID;
}

function packageInsightsIntoArray($userID, $congressID, $itemID)
{
    $path = getInsightPath($userID, $congressID, $itemID);
    $identifier = getInsightIdentifier($userID, $congressID, $itemID);
    $generalNotes = "";
    $posts = array();
    
    if (file_exists($path))
    {
        $genNotesPath = $path . "/General Notes.txt";
        if (file_exists($genNotesPath))
        {
            $generalNotes = '';
            $fp = fopen($path . "/General Notes.txt","r");
            while(! feof($fp))
            {
                $generalNotes .= fgets($fp);
            }
            fclose($fp);
        }
        foreach (scandir($path) as $folder)
        {
            if (strpos($folder, ".") === false)
            {
                $notesPath = $path . $folder . "/Notes.txt";
                $jpgSourcePath = $path . "/" . $folder . "/Image.jpg";
                $jpegSourcePath = $path . "/" . $folder . "/Image.jpeg";
                $pngSourcePath = $path . "/" . $folder . "/Image.png";
                $notes = "";
                if (file_exists($notesPath))
                {
                    $fp = fopen($notesPath,"r");
                    while(! feof($fp))
                    {
                        $notes .= fgets($fp);
                    }
                    fclose($fp);
                }
                $imageType = "";
                $imageType = file_exists($jpgSourcePath) ? "jpg" : $imageType;
                $imageType = file_exists($jpegSourcePath) ? "jpeg" : $imageType;
                $imageType = file_exists($pngSourcePath) ? "png" : $imageType;
                $post = array(
                    "congressID" => $congressID,
                    "itemID" => $itemID,
                    "title" => $folder,
                    "notes" => $notes,
                    "image" => $imageType,
                    "identifier" => $identifier
                );
                if (!$posts){ $posts = []; }
                array_push($posts, $post);
            }
        }
    }
    
    $insights = array(
        "userID" => $userID,
        "congressID" => $congressID,
        "itemID" => $itemID,
        "generalNotes" => $generalNotes,
        "posts" => $posts,
        "identifier" => $identifier
    );
    return ($insights);
}

/*
 * Produces html for a short format block version display of an agenda item
 */
function getShortFormatAgendaItem($item)
{
    $line3 = $item[START_DATE] . " " . $item[START_TIME] . " - ";
    if ($item[START_DATE] != $item[END_DATE])
    {
        $line3 .= $item[END_DATE] . " ";
    }
    $line3 .= $item[END_TIME];

    $class2 = $item['id'] % 2 == 0 ? "evenLine" : "oddLine";

    $html = "
    <div class='shortAgendaItemBlockDIV " . $class2 . "'><a href='" . HOME . "?page=viewAgendaItem&congress=" . $item['congressID'] . "&item=" . $item['id'] . "'>
        <div class='shortAgendaItemLine1'>" . convertAgendaTermForDisplay($item[TYPE]) . "</div>";
    if ($item[TYPE] != EXPO_HOURS)
    {
        $html .= "
        <div class='shortAgendaItemLine2'>" . $item[TITLE] . "</div>";
    }
    $html .= "
        <div class='shortAgendaItemLine3'>" . $line3 . "</div>
    </a></div>
    ";
    return $html;
}

/*
 * Produces html for a short format block version display of an agenda item minus date
 */
function getShortFormatAgendaItem2($item)
{
    $html = "
    <div class='shortAgendaItemBlockDIV2'><a href='" . HOME . "?page=viewAgendaItem&congress=" . $item['congressID'] . "&item=" . $item['id'] . "'>
        <div class='agendaColumn'>
            <div class='shortAgendaTime2'>" . format1ForSingleTimeDisplay($item[START_TIME], $item[END_TIME]) . "</div>
        </div>
        <div class='agendaColumn'>
            <div class='shortAgendaTitle2'>" . $item[TITLE] . "</div>
            <div class='shortAgendaSubTitle2'>" . $item[SUB_TITLE] . "</div>
        </div>
    </a></div>";
    return $html;
}

/*
 * Produces html for a long/expandable format block version display of an agenda item
 */
function getLongFormatAgendaItem($item, $connection)
{
    $html = "
    <div id='agendaItem" . $item['id'] . "' class='agendaItemDIV' assignment='" . implode(",", $item[ASSIGNMENT]) . "'>";
    
    $html .= getLongAgendaDetail($item, $connection, true);
    
    $html .= " 
    </div>";
    
    $html .= getLongAgendaScript($item);
    
    return $html;
}

/*
 * Produces html for a long/expandable format block version display of an agenda item with edit and delete features
 */
function getLongFormatAgendaItemWithEdit($item, $connection)
{
    $html = "
    <div id='agendaItem" . $item['id'] . "' class='agendaItemDIV' assignment='" . implode(",", $item[ASSIGNMENT]) . "'>
    <form class='removalForm agendaItemRemovalForm' name='agendaItemRemoveFromCongress' onsubmit='confirmAgendaItemDelete();' method='post' action='" . HOME . "'>
        <div class='trash agendaItemTrash fa'><label for='agendaItemRemove" . $item['id'] . "'>&#xf1f8;</label></div>
        <input name='congressID' hidden='true' type='number' value='" . $item['congressID'] . "'/>
        <input name='itemID' hidden='true' type='number' value='" . $item['id'] . "'/>
        <input id='agendaItemRemove" . $item['id'] . "' type='submit' name='" . POST_REMOVE_AGENDA_ITEM_FROM_CONGRESS . "' value=''/>
    </form>
    <a href='" . HOME . "?page=" . POST_MODIFY_AGENDA_ITEM . "&congress=" . $item['congressID'] . "&item=" . $item['id'] . "'>
        <form class='editForm agendaItemEditForm'>
            <div class='edit agendaItemEdit fa'><label for='agendaItemEdit'>&#xf044;</label></div>
        </form>
    </a>";
    
    $html .= getLongAgendaDetail($item, $connection, false);
    
    $html .= " 
    </div>";
    
    $html .= getLongAgendaScript($item);
    
    return $html;
}

function getLongAgendaDetail($item, $connection, $provideLink)
{
    $congress = getCongressById($item['congressID'], $connection);
    $names = "";
    foreach ($item[ASSIGNMENT] as $assignment)
    {
        if ($assignment != "axoneron")
        {
            $user = getUserById($assignment, $connection);
            if ($user)
            {
                $names .= $user['first'] . " " . $user['last'];
            }
            if (next($item[ASSIGNMENT]))
            {
                $names .= ", ";
            }
        }
        else
        {
            $names .= "Axoneron";
        }
        
    }
    
    $identifier = "_" . $item['congressID'] . "_" . $item['id'];
    $html = "
        <div id='caret" . $identifier . "' class='caret fa'>&#xf105;</div>
        <div class='shortAgendaItemBlockDIV2'>
            <div class='agendaColumn'>
                <div class='shortAgendaTime2'>" . format2ForSingleDateTimeDisplay($item[START_DATE], $item[START_TIME], $item[END_TIME]) . "</div>
            </div>";
    
    $html .= getLongAgendaTitleBlock($item, $provideLink);
    
    $html .= "
        </div>
        <div id='agendaExt" . $identifier . "' class='agendaExtendedDetail' style='display:none;'>
            <div class='hRow'>
                <div class='width2 right'>Type:</div>
                <div class='bold agendaDetailItem'>" . convertAgendaTermForDisplay($item[TYPE]) . "</div>
            </div>
            <div class='hRow'>
                <div class='width2 right'>Category:</div>
                <div class='bold agendaDetailItem'>" . $item[CATEGORY] . "</div>
            </div>
            <div class='hRow'>
                <div class='width2 right'>Location:</div>
                <div class='bold agendaDetailItem'>" . $item[LOCATION] . "</div>
            </div>
            <div class='hRow'>
                <div class='width2 right'>Chairperson(s):</div>
                <div class='bold agendaDetailItem'>" . implode(",", $item[CHAIR]) . "</div>
            </div>
            <div class='hRow'>
                <div class='width2 right'>Presenter(s):</div>
                <div class='bold agendaDetailItem'>" . implode(",", $item[PRESENTERS]) . "</div>
            </div>
            <div class='hRow'>
                <div class='width2 right'>Session Name:</div>
                <div class='bold agendaDetailItem'>" . $item[SESSION_NAME] . "</div>
            </div>
            <div class='hRow'>
                <div class='width2 right'>Assignment:</div>
                <div class='bold agendaDetailItem'>" . $names . "</div>
            </div>
            <div class='hRow'>
                <div class='width2 right'>Priority:</div>
                <div class='bold agendaDetailItem'>" . convertAgendaPriorityForDisplay($item[PRIORITY]) . "</div>
            </div>
            <div class='hRow'>
                <div class='width2 right'>Congress:</div>
                <div class='bold agendaDetailItem'>" . $congress['shortName'] . "</div>
            </div>
        </div>
        <div class='separator'>&nbsp;</div>";
    return $html;
}

function getLongAgendaTitleBlock($item, $provideLink)
{
    $html = "
            <div class='agendaColumn'>";
    
    if ($provideLink)
    {
        $html .= "
                <a href='" . HOME . "?page=agenda&congress=" . $item['congressID'] . "&item=" . $item['id'] . "'>";
    }
    
    $html .= "
                <div class='shortAgendaTitle2'>" . $item[TITLE] . "</div>
                <div class='shortAgendaSubTitle2'>" . $item[SUB_TITLE] . "</div>";
    
    if ($provideLink)
    {
        $html .= "
                </a>";
    }
    
    $html .= "
            </div>";
    
    return $html;
}

function getLongAgendaScript($item)
{
    $identifier = "_" . $item['congressID'] . "_" . $item['id'];
    $html ="
    <script>
        $('#caret" . $identifier . "').click(function()
        {
            if($('#agendaExt" . $identifier . "').is(':visible'))
            {
                $('#agendaExt" . $identifier . "').hide();
                $('#caret" . $identifier . "').html('&#xf105;');
            }
            else
            {
                $('#agendaExt" . $identifier . "').show();
                $('#caret" . $identifier . "').html('&#xf107;');
            }
        });
    </script>";
    return $html;
}

function getShortInsightBlock($post)
{
    $title = str_replace(" ", "_", $post['title']);
    $identifier = $post['identifier'] . "_" . $title;
    if (isset($_COOKIE[$identifier]) && $_COOKIE[$identifier] != "null")
    {
        $notes = $_COOKIE[$identifier];
    }
    else
    {
        $notes = $post['notes'];
    }
    
    $html = "
    <div id='insight_" . $title . "' class='insightPostDIV'>
        <div id='postCaret_" . $title . "' class='caret fa postCaret noteCaretContract'>&#xf105;</div>
        <form class='removalForm insightRemovalForm' name='insightRemove' method='post' action='" . HOME . "' onsubmit='return confirmDelete(\"" . $title . "\");'>
            <div class='trash insightTrash fa'><label for='insightRemove_" . $title . "'>&#xf1f8;</label></div>
            <input name='congressID' hidden='true' type='number' value='" . $post['congressID'] . "'/>
            <input name='itemID' hidden='true' type='number' value='" . $post['itemID'] . "'/>
            <input name='postTitle' hidden='true' type='text' value='" . $post['title'] . "'/>
            <input id='insightRemove_" . $title . "' type='submit' name='" . POST_REMOVE_INSIGHT . "' value=''/>
        </form>
        <div id='editInsight_" . $title . "' class='edit insightEdit fa'>&#xf044;</div>
        <div id='editTitle_" . $title . "' class='insightShortTitleDIV'>" . $post['title'] . "</div>
        <div id='postDataDIV_" . $title . "' class='postShortDataDIV'>
            <div id='editNotes_" . $title . "' style='display:none;'>" . $notes . "</div>
            <div class='insightShortNotesDIV'>" . nl2br($post['notes']) . "</div>";
    
    if ($post['image'] != "")
    {
        $html .= "
            <div class='userInsightPostImageDIV'>
                <img id='image_" . $identifier . "' class='postImage' src='uploads/insights/" . $post['identifier'] . "/" . $post['title'] .  "/Image." . $post['image'] . "' onload='cleanImage(\"image_" . $identifier . "\");'/>
            </div>";
    }
    
    $html .= "
        </div>
    </div>
    <script>
    
    $('#postCaret_" . $title . "').click(function()
    {
        if($('#postDataDIV_" . $title . "').hasClass('postDataDivExpanded'))
        {
            $('#postDataDIV_" . $title . "').removeClass('postDataDivExpanded');
            $('#postCaret_" . $title . "').removeClass('noteCaretExpand');
            $('#postCaret_" . $title . "').addClass('noteCaretContract');
            $('#postCaret_" . $title . "').html('&#xf105;');
        }
        else
        {
            $('#postDataDIV_" . $title . "').addClass('postDataDivExpanded');
            $('#postCaret_" . $title . "').removeClass('noteCaretContract');
            $('#postCaret_" . $title . "').addClass('noteCaretExpand');
            $('#postCaret_" . $title . "').html('&#xf107;');
        }
    });

    </script>";
    return $html;
}

function getAgendaBlockItems($agenda, $connection)
{
    $html = '';
    if ($agenda)
    { 
        $startDate = getSoonestAgendaItemDate($agenda);
        if ($startDate)
        {
            $endDate = getLatestAgendaItemDate($agenda);
            $html .= "
        <div class='buttonBar1DIV'>
            <div id='allItems' class='button selected'>ALL</div>
            <div id='assignedItems' class='button'>ASSIGNED</div>
        </div>
        <div class='scheduleDIV'>
            <div class='calendarDIV1'>
                <div class='caret leftArrow fa'>&#xf0d9;</div>
                <div class='calendarDIV2'>";
            for ($i = 0 ; $i < 5 ; $i++)
            {
                $timestamp = strtotime($startDate . "+" . $i . " day");
                $date = Date("n/d/y", $timestamp);
                $items = getAgendaItemsForDate($agenda, $date);
                $state = !$items ? ' disabled' : ($i == 0 ? ' selected' : '');
                $html .= getDateBlockForCalendar($date, $state);
            }
            $html .= "
                </div>
                <div class='caret rightArrow fa'>&#xf0da;</div>
            </div>
            <div class='agendaListDIV'>";
            $i = strtotime($startDate);
            $j = 0;
                
            while ($i <= strtotime($endDate))
            {
                $date = Date("n/d/y", $i);
                $items = getAgendaItemsForDate($agenda, $date);
                if ($items)
                {
                    $display = $j == 0 ? "" : " style='display:none;'";
                    $html .= "
                <div class='agendaGroupDIV'" . $display . " date='" . $date . "'>";
                    foreach ($items as $item)
                    {
                        $html .= getLongFormatAgendaItem($item, $connection, next($items));
                    }
                    $html .= "
                </div>";
                }
                $j++;
                $i = strtotime($startDate . "+" . $j . " day");
            }
            $html .= "
            </div>
        </div>";
        }
        else
        {
            $html .= "
<div class='emptyListDIV'>There are no agenda items in the future for the selected criteria.</div>";
        }
    }
    else
    {
        $html .= "
<div class='emptyListDIV'>The selected criteria did not result in any agenda information.</div>";
    }
    return $html;
}


function convertAgendaTermForDisplay($term)
{
    switch ($term)
    {
        case _BREAK:
            return "Session Break";
        case EXHIBIT:
            return "Exhibit";
        case EXPO_HOURS:
            return "Exposition Hours";
        case INTERNAL:
            return "Internal Event";
        case POSTER:
            return "Poster Session";
        case PRESENTATION:
            return "Speaker Presentation";
        case RECEPTION:
            return "Reception Event";
        default:
            return "Undefined";
    }
}

function convertAgendaPriorityForDisplay($priority)
{
    switch($priority)
    {
        case 1:
            return "Low";
        case 2:
            return "Medium";
        case 3:
            return "High";
        default:
            return "Undefined";
    }
}
