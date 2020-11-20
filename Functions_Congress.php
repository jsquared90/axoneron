<?php

/*************************
 *  BASE FUNCTIONS
 * ***********************
 */

/*
 * Base function for an admin adding a congress to the database
 */
function addCongress($user, $connection)
{
    /*
     *  NOTES:
     *  Need to construct and route error codes and messages that are less esoteric.
     *  Namely, need to return a code specific for when a user attempts to add a congress
     *  that already exists. Currently the user just gets a generic error code. Need to
     *  route a specific error message.
     */
    
    $code = 0;
    $errors = 0;
    $record = 0;
    $congressResult = addCongressToCongresses($user, $connection);
    if ($congressResult['errors'] == 0)
    {
        $congressTable = generateCongressAgendaTable($congressResult, $connection);
        if ($congressTable)
        {
            $hospitalityRoomsTable = generateCongressHospitalityRoomsTable($congressResult, $connection);
            if ($hospitalityRoomsTable)
            {
                $hospitalityScheduleTable = generateCongressHospitalityScheduleTable($congressResult, $connection);
                if ($hospitalityScheduleTable)
                {
                    $recordData = array();
                    $recordData['type'] = ADDED_CONGRESS;
                    $recordData['data'] = $congressResult['congress']['id'];
                    $recordData['openEnd'] = "";
                    $result = addRecordToUser($user, $recordData, $user, $connection);
                    if ($result)
                    {
                        //$title = "Axoneron Congress App";
                        $record = getRecordByFootprint($recordData, $user, $connection);
                        if ($record)
                        {
                            $code = -1;
                        }
                        else
                        {
                            $code = 1;
                            $errors = packageGeneralError($errors, 9);
                        }
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
        foreach ($congressResult['errors'] as $error)
        {
            $code = 1;
            $errors = packageGeneralError($errors, $error['code']);
        }
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record,
        "congress" => $congressResult['congress']
    );
}

/*
 * Base function for an admin modifying a congress to the database
 */
function modifyCongress($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $congressResult = modifyCongressRecord($connection);
    if ($congressResult['errors'] == 0)
    {
        $recordData = array();
        $recordData['type'] = MODIFIED_CONGRESS_RECORD;
        $recordData['data'] = $congressResult['congress']['id'];
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
                $code = 1;
                $errors = packageGeneralError($errors, 9);
            }
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 8);
        }
    }
    else
    {
        foreach ($congressResult['errors'] as $error)
        {
            $code = 1;
            $errors = packageGeneralError($errors, $error['code']);
        }
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

function removeCongress($congress, $user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $result = sendRecordToTrash(CONGRESS_RECORD, $congress['id'], $connection);
    if ($result)
    {
        $recordData = array();
        $recordData['type'] = REMOVED_CONGRESS;
        $recordData['data'] = $congress['id'];
        $recordData['openEnd'] = "";
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

function deleteCongress($user, $congress, $connection)
{
    /*
     * 1. Validate admin level
     * 2. Remove all pending requests pertaining to congress
     * 3. Remove all hotel requests and hotel confirmations pertaining to congress
     * 4. Remove congress agenda table
     * 5. Remove congress hospitality rooms table
     * 6. Remove congress hospitality shcedule table
     * 7. Remove congress from congresses table
     * 8. Record action
     */

    $code = 0;
    $errors = 0;
    $record = 0;

    if ($user['level'] > 1)
    {
        $pendingRequests = getPendingRequestsForCongress($congress, $connection);
        foreach ($pendingRequests as $pendingRequest)
        {
            deleteFromPendingRequests($pendingRequest, $connection);
        }
        $users = getAllUsers($connection);
        foreach ($users as $user2)
        {
            $records = getHotelReservationDataForUser($user2, $congress['id'], $connection);
            foreach ($records as $record)
            {
                deleteUserRecord($user2, $record, $connection);
            }
        }
        $result = deleteCongressAgendaTable($congress, $connection);
        if ($result)
        {
            $result2 = deleteCongressHospitalityRoomsTable($congress, $connection);
            if ($result2)
            {
                $result3 = deleteCongressHospitalityScheduleTable($congress, $connection);
                if ($result3)
                {
                    $result4 = deleteCongressFromCongresses($congress, $connection);
                    if ($result4)
                    {
                        $recordData = array();
                        $recordData['type'] = REMOVED_CONGRESS;
                        $recordData['data'] = $congress['id'];
                        $recordData['openEnd'] = "";
                        $result5 = addRecordToUser($user, $recordData, $user, $connection);
                        if ($result5)
                        {
                            $record = getRecordByFootprint($recordData, $user, $connection);
                            if ($record)
                            {
                                $code = -1;
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
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 2);
        }
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

function addBioToCongress($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;

    if ($_FILES["bioFile"]["size"] <= FILE_IMPORT_SIZE_LIMIT)
    {
        $congressID = $_POST['congressID'];
        $congress = getCongressById($congressID, $connection);
        if ($congress)
        {
            $saveLocation = 0;
            $raw_file = basename($_FILES["bioFile"]["name"]);
            $target_dir = CONGRESS_BIOS_PATH . $congressID . "/";
            if (!file_exists($target_dir))
            {
                mkdir($target_dir, 0777, true);
            }
            $firstName = $_POST["bioFirstName"];
            $lastName = $_POST["bioLastName"];
            $saveName = $firstName . "_" . $lastName . ".pdf";
            $saveLocation = $target_dir . $saveName;
            if (move_uploaded_file($_FILES["bioFile"]["tmp_name"], $saveLocation))
            {
                $bios = $congress['bios'];
                if ($bios != "")
                {
                    $bios .= ",";
                }
                $bios .= urlencode($firstName . "_" . $lastName);
                $query = "UPDATE congresses SET bios = '$bios' WHERE id = '$congressID'";
                $result = $connection->query($query);
                if ($result)
                {
                    $recordData = array();
                    $recordData['type'] = BIO_ADDED_TO_CONGRESS;
                    $recordData['data'] = $congress['id'] . "," . urlencode($firstName . "_" . $lastName);
                    $recordData['openEnd'] = "";
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

function modifyBio($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;

    if ($_FILES["bioFile"]["size"] <= FILE_IMPORT_SIZE_LIMIT)
    {
        $congressID = $_POST['congressID'];
        $congress = getCongressById($congressID, $connection);
        if ($congress)
        {
            $saveLocation = 0;
            $raw_file = basename($_FILES["bioFile"]["name"]);
            $target_dir = CONGRESS_BIOS_PATH . $congressID . "/";
            if (!file_exists($target_dir))
            {
                mkdir($target_dir, 0777, true);
            }
            $firstName = $_POST["bioFirstName"];
            $lastName = $_POST["bioLastName"];
            $saveName = $firstName . "_" . $lastName . ".pdf";
            $saveLocation = $target_dir . $saveName;
            if (move_uploaded_file($_FILES["bioFile"]["tmp_name"], $saveLocation))
            {
                $bios = explode(",", $congress['bios']);
                $newBios = array();
                foreach ($bios as $bio)
                {
                    if (urldecode($bio) == $_POST['previousBioName'])
                    {
                        array_push($newBios, urlencode($firstName . "_" . $lastName));
                    }
                    else
                    {
                        array_push($newBios, $bio);
                    }
                }
                $bios = implode(",", $newBios);
                $query = "UPDATE congresses SET bios = '$bios' WHERE id = '$congressID'";
                $result = $connection->query($query);
                if ($result)
                {
                    $recordData = array();
                    $recordData['type'] = MODIFIED_BIO;
                    $recordData['data'] = $congress['id'] . "," . $_POST['previousBioName'] . "," . urlencode($firstName . "_" . $lastName);
                    $recordData['openEnd'] = "";
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

function removeBioFromCongress($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $records = $_POST['congressID'] . "," . urlencode($_POST['bioName']);
    $result =  sendRecordToTrash(BIO_RECORD, $records, $connection);
    if ($result)
    {
        $recordData = array();
        $recordData['type'] = REMOVED_BIO;
        $recordData['data'] = $records;
        $recordData['openEnd'] = "";
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


/*************************
 *  SUB FUNCTIONS
 * ***********************
 */


/*
 * For adding a new congress to the table "congresses". Returns congress object.
 */
function addCongressToCongresses($user, $connection)
{
    $congress = 0;
    $errors = 0;
    
    $name = urlencode($_POST['newCongressName']);
    if (strlen($name) > 32)
    {
        substr($name, 0, 32);
    }
    
    $shortName = urlencode($_POST['newCongressShortName']);
    if (strlen($shortName) > 24)
    {
        substr($shortName, 0, 24);
    }
    
    $footprintData = array(
        "name" => urldecode($name),
        "shortName" => urldecode($shortName)
        );
    
    $duplicate = checkForDuplicateCongress($footprintData, $connection);
    if (!$duplicate)
    {
        $congressURL = urlencode($_POST['newCongressURL']);
        $registrationURL = urlencode($_POST['newRegistrationURL']);
        if (strlen($congressURL) > 512)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 2,
                "data" => $congressURL
            );
            array_push($errors, $error);
        }
        else if (strlen($registrationURL) > 512)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 3,
                "data" => $registrationURL
            );
            array_push($errors, $error);
        }
        else
        {
            $startDate = convertToSqlDateTime($_POST['newCongressStartDate'], $_POST['newCongressStartTime'] . $_POST['newCongressStartMeridian']);
            $endDate = convertToSqlDateTime($_POST['newCongressEndDate'], $_POST['newCongressEndTime'] . $_POST['newCongressEndMeridian']);
            $hotelStartDate = convertToSqlDateTime($_POST['newCongressHotelStartDate'], "15:00:00");
            $hotelEndDate = convertToSqlDateTime($_POST['newCongressHotelEndDate'], "11:00:00");
            $venueName = urlencode($_POST['newCongressVenueName']);
            $venueName = strlen($venueName) > 32 ? substr($venueName, 0, 32) : $venueName;
            $venueHall = urlencode($_POST['newCongressVenueHall']);
            $venueHall = strlen($venueHall) > 16 ? substr($venueHall, 0, 16) : $venueHall;
            $venueBooth = urlencode($_POST['newCongressVenueBooth']);
            $venueBooth = strlen($venueBooth) > 16 ? substr($venueBooth, 0, 16) : $venueBooth;
            $venueAddress1 = urlencode($_POST['newCongressVenueAddress1']);
            $venueAddress1 = strlen($venueAddress1) > 32 ? substr($venueAddress1, 0, 32) : $venueAddress1;
            $venueAddress2 = urlencode($_POST['newCongressVenueAddress2']);
            $venueAddress2 = strlen($venueAddress2) > 16 ? substr($venueAddress2, 0, 16) : $venueAddress2;
            $venueCity = urlencode($_POST['newCongressVenueCity']);
            $venueCity = strlen($venueCity) > 16 ? substr($venueCity, 0, 16) : $venueCity;
            $venueState = urlencode($_POST['newCongressVenueState']);
            $venueState = strlen($venueState) > 16 ? substr($venueState, 0, 16) : $venueState;
            $venueCountry = urlencode($_POST['newCongressVenueCountry']);
            $venueCountry = strlen($venueCountry) > 16 ? substr($venueCountry, 0, 16) : $venueCountry;
            $venueZip = urlencode($_POST['newCongressVenueZip']);
            $venueZip = strlen($venueZip) > 16 ? substr($venueZip, 0, 16) : $venueZip;
            $hotels = '';
            $bios = '';
            $author = $user['id'];
            $imageUploadReturn = uploadCongressImage();
            $imageURL = $imageUploadReturn['code'] < 0 ? rawurlencode($imageUploadReturn['saveName']) : '';

            $query = "INSERT INTO congresses (id, ";
            $query .= "name, ";
            $query .= "shortName, ";
            $query .= "congressURL, ";
            $query .= "registrationURL, ";
            $query .= "startDate, ";
            $query .= "endDate, ";
            $query .= "hotelStartDate, ";
            $query .= "hotelEndDate, ";
            $query .= "showHours, ";
            $query .= "venueName, ";
            $query .= "venueHall, ";
            $query .= "venueBooth, ";
            $query .= "venueAddress1, ";
            $query .= "venueAddress2, ";
            $query .= "venueCity, ";
            $query .= "venueState, ";
            $query .= "venueCountry, ";
            $query .= "venueZip, ";
            $query .= "hotels, ";
            $query .= "bios, ";
            $query .= "author, ";
            $query .= "imageURL) VALUES (NULL, '";
            $query .= $name ."', '";
            $query .= $shortName ."', '";
            $query .= $congressURL ."', '";
            $query .= $registrationURL ."', '";
            $query .= $startDate ."', '";
            $query .= $endDate ."', '";
            $query .= $hotelStartDate ."', '";
            $query .= $hotelEndDate ."', '";
            $query .= "', '";
            $query .= $venueName ."', '";
            $query .= $venueHall ."', '";
            $query .= $venueBooth ."', '";
            $query .= $venueAddress1 ."', '";
            $query .= $venueAddress2 ."', '";
            $query .= $venueCity ."', '";
            $query .= $venueState ."', '";
            $query .= $venueCountry ."', '";
            $query .= $venueZip ."', '";
            $query .= $hotels ."', '";
            $query .= $bios ."', '";
            $query .= $author ."', '";
            $query .= $imageURL ."')";

            $result = $connection->query($query);

            if ($result)
            {
                $congress = getCongressByName($name, $connection);
            }
            else
            {
                if (!$errors){ $errors = []; }
                $error = array(
                    "code" => 4,
                    "data" => $congress
                );
                array_push($errors, $error);
            }
        }
    }
    else
    {
        if (!$errors){ $errors = []; }
        foreach ($duplicate['errors'] as $error)
        {
            array_push($errors, $error);
        }
    }
    return getCongressReturn($errors, $congress);
}

function modifyCongressRecord($connection)
{
    $congress = 0;
    $errors = 0;
    $congressID = $_POST['id'];
    $name = urlencode($_POST['newCongressName']);
    if (strlen($name) > 32)
    {
        substr($name, 0, 32);
    }
    
    $shortName = urlencode($_POST['newCongressShortName']);
    if (strlen($shortName) > 24)
    {
        substr($shortName, 0, 24);
    }
    
    $footprintData = array(
        "name" => urldecode($name),
        "shortName" => urldecode($shortName)
        );
    
    $duplicate = checkForDuplicateCongress($footprintData, $connection, $congressID);
    if (!$duplicate)
    {
        $congressURL = urlencode($_POST['newCongressURL']);
        $registrationURL = urlencode($_POST['newRegistrationURL']);
        if (strlen($congressURL) > 512)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 4,
                "data" => $congressURL
            );
            array_push($errors, $error);
        }
        else if (strlen($registrationURL) > 512)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 5,
                "data" => $registrationURL
            );
            array_push($errors, $error);
        }
        else
        {
            $startDate = convertToSqlDateTime($_POST['newCongressStartDate'], $_POST['newCongressStartTime'] . $_POST['newCongressStartMeridian']);
            $endDate = convertToSqlDateTime($_POST['newCongressEndDate'], $_POST['newCongressEndTime'] . $_POST['newCongressEndMeridian']);
            $hotelStartDate = convertToSqlDateTime($_POST['newCongressHotelStartDate'], "15:00:00");
            $hotelEndDate = convertToSqlDateTime($_POST['newCongressHotelEndDate'], "11:00:00");
            $venueName = urlencode($_POST['newCongressVenueName']);
            $venueName = strlen($venueName) > 32 ? substr($venueName, 0, 32) : $venueName;
            $venueHall = urlencode($_POST['newCongressVenueHall']);
            $venueHall = strlen($venueHall) > 16 ? substr($venueHall, 0, 16) : $venueHall;
            $venueBooth = urlencode($_POST['newCongressVenueBooth']);
            $venueBooth = strlen($venueBooth) > 16 ? substr($venueBooth, 0, 16) : $venueBooth;
            $venueAddress1 = urlencode($_POST['newCongressVenueAddress1']);
            $venueAddress1 = strlen($venueAddress1) > 32 ? substr($venueAddress1, 0, 32) : $venueAddress1;
            $venueAddress2 = urlencode($_POST['newCongressVenueAddress2']);
            $venueAddress2 = strlen($venueAddress2) > 16 ? substr($venueAddress2, 0, 16) : $venueAddress2;
            $venueCity = urlencode($_POST['newCongressVenueCity']);
            $venueCity = strlen($venueCity) > 16 ? substr($venueCity, 0, 16) : $venueCity;
            $venueState = urlencode($_POST['newCongressVenueState']);
            $venueState = strlen($venueState) > 16 ? substr($venueState, 0, 16) : $venueState;
            $venueCountry = urlencode($_POST['newCongressVenueCountry']);
            $venueCountry = strlen($venueCountry) > 16 ? substr($venueCountry, 0, 16) : $venueCountry;
            $venueZip = urlencode($_POST['newCongressVenueZip']);
            $venueZip = strlen($venueZip) > 16 ? substr($venueZip, 0, 16) : $venueZip;

            $imageURL = getCongressById($congressID, $connection)['imageURL'];
            $imageUploadReturn = uploadCongressImage();
            $imageURL = $imageUploadReturn['code'] < 0 ? rawurlencode($imageUploadReturn['saveName']) : getCongressById($congressID, $connection)['imageURL'];

            $congress = getCongressById($congressID, $connection);
            if ($congress)
            {
                $query = "UPDATE congresses SET ";
                $query .= "name = '$name'";
                $query .= ", shortName = '$shortName'";
                $query .= ", congressURL = '$congressURL'";
                $query .= ", registrationURL = '$registrationURL'";
                $query .= ", startDate = '$startDate'";
                $query .= ", endDate = '$endDate'";
                $query .= ", hotelStartDate = '$hotelStartDate'";
                $query .= ", hotelEndDate = '$hotelEndDate'";
                $query .= ", venueName = '$venueName'";
                $query .= ", venueHall = '$venueHall'";
                $query .= ", venueBooth = '$venueBooth'";
                $query .= ", venueAddress1 = '$venueAddress1'";
                $query .= ", venueAddress2 = '$venueAddress2'";
                $query .= ", venueCity = '$venueCity'";
                $query .= ", venueState = '$venueState'";
                $query .= ", venueCountry = '$venueCountry'";
                $query .= ", venueZip = '$venueZip'";
                $query .= ", imageURL = '$imageURL'";
                $query .= " WHERE id = '$congressID'";

                $result = $connection->query($query);

                if ($result)
                {
                    $congress = getCongressById($congressID, $connection);
                }
                else
                {
                    if (!$errors){ $errors = []; }
                    $error = array(
                        "code" => 7,
                        "data" => $result
                    );
                    array_push($errors, $error);
                }
            }
            else
            {
                if (!$errors){ $errors = []; }
                $error = array(
                    "code" => 6,
                    "data" => $congress
                );
                array_push($errors, $error);
            }
        }
    }
    else
    {
        if (!$errors){ $errors = []; }
        foreach ($duplicate['errors'] as $error)
        {
            array_push($errors, $error);
        }
    }
    return getCongressReturn($errors, $congress);
}

function uploadCongressImage()
{
    $code = 0;
    $saveLocation = 0;
    $saveName = 0;
    if (isset($_FILES))
    {
        if (isset($_FILES['imageFile']))
        {
            if (isset($_FILES['imageFile']['name']))
            {
                if ($_FILES['imageFile']['name'] != '')
                {
                    if ($_FILES["imageFile"]["size"] > FILE_IMPORT_SIZE_LIMIT)
                    {
                        $code = 5;
                    }
                    else
                    {
                        $raw_file = basename($_FILES["imageFile"]["name"]);
                        $extension = strtolower(pathinfo($raw_file,PATHINFO_EXTENSION));
                        if($extension != "png" && $extension != "jpg" && $extension != "jpeg")
                        {
                            $code = 6;
                        }
                        else
                        {
                            $target_dir = CONGRESS_IMAGES_PATH;
                            $fileDateString = date(getSaveForFileDateFormat(), time());
                            $saveName = $_POST['newCongressName'] . "_" . $fileDateString . "." . $extension;
                            $saveLocation = $target_dir . $saveName;
                            if (move_uploaded_file($_FILES["imageFile"]["tmp_name"], $saveLocation))
                            {
                                $code = -1;
                                createThumbnail($saveLocation, CONGRESS_IMAGES_THUMBS_PATH . $saveName, 512);
                            }
                            else
                            {
                                $code = 7;
                            }
                        }
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
        "saveName" => $saveName,
        "saveLocation" => $saveLocation
            );
}

function deleteCongressFromCongresses($congress, $connection)
{
    $id = $congress['id'];
    $query = "DELETE FROM congresses WHERE id = '$id'";
    $result = $connection->query($query);
    return $result;
}

/*
 * currently, only shortName or full name is used to determine if a duplicate
 * first arg = array of all fields used as footprint for duplicate analysis
 * 3rd arg is used for exclusion purposes (where necessary)
 */
function checkForDuplicateCongress($footprintData, $connection, $currentCongressID = 0)
{
    $result = 0;
    $congresses = getAllCongresses($connection);
    foreach ($congresses as $congress)
    {
        if ($congress['name'] == $footprintData['name'] ||
            $congress['shortName'] == $footprintData['shortName'])
        {
            if ($currentCongressID <= 0 || $currentCongressID != $congress['id'])
            {
                if (!$result)
                {
                    $result = array();
                    $result['errors'] = array();
                }
                if ($congress['name'] == $footprintData['name'])
                {
                    $error = array(
                        "code" => 20,
                        "data" => $congress['name']
                    );
                    array_push($result['errors'], $error);
                }
                if ($congress['shortName'] == $footprintData['shortName'])
                {
                    $error = array(
                        "code" => 21,
                        "data" => $congress['shortName']
                    );
                    array_push($result['errors'], $error);
                }
            }
        }
    }
    return $result;
}

function addHotelToCongressTable($hotelID, $congress, $connection)
{
    $congressID = $congress['id'];
    $hotels = getHotelsWithCongress($congress, $connection);
    $hotelIDs = array();
    if ($hotels)
    {
        foreach ($hotels as $hotel)
        {
            array_push($hotelIDs, $hotel['id']);
        }
    }
    array_push($hotelIDs, $hotelID);
    $query = "UPDATE congresses SET hotels = '" . implode(',',$hotelIDs) . "' WHERE id = '$congressID'";
    $result = $connection->query($query);
    return $result;
}

function removeHotelFromCongressTable($hotelID, $congress, $connection)
{
    $congressID = $congress['id'];
    $hotels = getHotelsWithCongress($congress, $connection);
    $newHotels = array();
    foreach ($hotels as $hotel)
    {
        if ($hotel['id'] != $hotelID)
        {
            array_push($newHotels, $hotel['id']);
        }
    }
    $hotelString = count($newHotels) > 0 ? implode(",",$newHotels) : "";
    $query = "UPDATE congresses SET hotels = '$hotelString' WHERE id = '$congressID'";
    $result = $connection->query($query);
    return $result;
}

function getCongressByName($name, $connection)
{
    $query = "SELECT * FROM congresses WHERE name = '$name'";
    $result = $connection->query($query);
    $congress = 0;
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $congress = packageCongressRowIntoArray($row, $connection);
        }
    }
    return $congress;
}

function getCongressById($id, $connection)
{
    $congress = 0;
    $query = "SELECT * FROM congresses WHERE id = '$id'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $congress = packageCongressRowIntoArray($row, $connection);
        }
    }
    return $congress;
}

function getAllCongresses($connection)
{
    $query = "SELECT * FROM congresses";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        $congresses = array();
        while ($row = mysqli_fetch_array($result))
        {
            $congress = packageCongressRowIntoArray($row, $connection);
            if ($congress)
            {
                array_push($congresses, $congress);
            }
        }

        /*
         * for later refernence
        $congressNames = array_column($congresses, 'shortName');
        array_multisort($congressNames, SORT_ASC, $congresses);
         */

        return $congresses;
    }
    else
    {
        return 0;
    }
}

function getAllUserAttendanceForCongress($congressID, $connection)
{
    $usersAttending = 0;
    
    $users = getAllUsers($connection);
    foreach ($users as $u)
    {
        if (getCongressAttendance($u, $congressID, $connection) > 0)
        {
            if (!$usersAttending){ $usersAttending = []; }
            array_push($usersAttending, $u);
        }
    }
    
    return $usersAttending;
}

function getAllCongressesWithInsights($connection)
{
    $congresses1 = getAllCongresses($connection);
    $congresses2 = 0;
    foreach ($congresses1 as $congress)
    {
        if (congressHasInsights($congress, $connection))
        {
            if (!$congresses2){ $congresses2 = []; }
            array_push($congresses2, $congress);
        }
    }
    return $congresses2;
}

function getBios($congress, $connection)
{
    $bios = 0;
    $biosArray = explode(",", $congress['bios']);
    if ($biosArray)
    {
        foreach($biosArray as $b)
        {
            if ($b != "")
            {
                $records = $congress['id'] . "," . $b;
                if (!isTrashed(BIO_RECORD, $records, $connection))
                {
                    $first = urldecode(explode("_", $b)[0]);
                    $last = urldecode(explode("_", $b)[1]);
                    $bio = array(
                        'first' => $first,
                        'last' => $last,
                        'url' => CONGRESS_BIOS_PATH . $congress['id'] . "/" . $first . "_" . $last . ".pdf"
                    );
                    if (!$bios){ $bios =[]; }
                    array_push($bios, $bio);
                }
            }
        }
    }
    return $bios;
}

function congressIsInTheFuture($congress)
{
    $valid = 0;
    $now = date('Y-m-d H:i:s');
    if (strtotime($now) < strtotime($congress['hotelEndDate']))
    {
        $valid = 1;
    }
    return $valid;
}

function congressIsInThePast($congress)
{
    $valid = 0;
    $now = date('Y-m-d H:i:s');
    if (strtotime($now) > strtotime($congress['hotelEndDate']))
    {
        $valid = 1;
    }
    return $valid;
}

function packageCongressRowIntoArray($row, $connection)
{
    $congress = 0;
    
    // check to see if the congress has been trashed
    if (!isTrashed(CONGRESS_RECORD, $row['id'], $connection))
    {
        $congress = array(
            'id' => $row['id'],
            'name' => urldecode($row['name']),
            'shortName' => urldecode($row['shortName']),
            'congressURL' => urldecode($row['congressURL']),
            'registrationURL' => urldecode($row['registrationURL']),
            'startDate' => $row['startDate'],
            'endDate' => $row['endDate'],
            'hotelStartDate' => $row['hotelStartDate'],
            'hotelEndDate' => $row['hotelEndDate'],
            'venueName' => urldecode($row['venueName']),
            'venueHall' => urldecode($row['venueHall']),
            'venueBooth' => urldecode($row['venueBooth']),
            'venueAddress1' => urldecode($row['venueAddress1']),
            'venueAddress2' => urldecode($row['venueAddress2']),
            'venueCity' => urldecode($row['venueCity']),
            'venueState' => urldecode($row['venueState']),
            'venueCountry' => urldecode($row['venueCountry']),
            'venueZip' => urldecode($row['venueZip']),
            'hotels' => $row['hotels'],
            'bios' => $row['bios'],
            'author' => $row['author'],
            'imageURL' => $row['imageURL']
        );
    }
    return $congress;
}

function packageCongressesIntoXML($congresses)
{
    
    $string .= "<congresses>";
    foreach ($congresses as $congress)
    {
        $string .= packageCongressIntoXMLString($congress);
    }
    $string .= "</congresses>";
    $xml = new SimpleXMLElement($string);
    return $xml->asXML();
}

function packageCongressIntoXMLString($congress)
{
    $string ="";
    if ($congress)
    {
        $string = "<congress>";
        $string .= "<id>" . $congress['id'] . "</id>";
        $string .= "<name>" . htmlspecialchars($congress['name']) . "</name>";
        $string .= "<shortName>" . htmlspecialchars($congress['shortName']) . "</shortName>";
        $string .= "<congressURL>" . htmlspecialchars($congress['congressURL']) . "</congressURL>";
        $string .= "<registrationURL>" . htmlspecialchars($congress['registrationURL']) . "</registrationURL>";
        $string .= "<hotelStartDate>" . $congress['hotelStartDate'] . "</hotelStartDate>";
        $string .= "<hotelEndDate>" . $congress['hotelEndDate'] . "</hotelEndDate>";
        $string .= "<startDate>" . $congress['startDate'] . "</startDate>";
        $string .= "<endDate>" . $congress['endDate'] . "</endDate>";
        $string .= "<showHours>" . htmlspecialchars($congress['showHours']) . "</showHours>";
        $string .= "<venueName>" . htmlspecialchars($congress['venueName']) . "</venueName>";
        $string .= "<venueHall>" . htmlspecialchars($congress['venueHall']) . "</venueHall>";
        $string .= "<venueBooth>" . htmlspecialchars($congress['venueBooth']) . "</venueBooth>";
        $string .= "<venueAddress1>" . htmlspecialchars($congress['venueAddress1']) . "</venueAddress1>";
        $string .= "<venueAddress2>" . htmlspecialchars($congress['venueAddress2']) . "</venueAddress2>";
        $string .= "<venueCity>" . htmlspecialchars($congress['venueCity']) . "</venueCity>";
        $string .= "<venueState>" . htmlspecialchars($congress['venueState']) . "</venueState>";
        $string .= "<venueCountry>" . htmlspecialchars($congress['venueCountry']) . "</venueCountry>";
        $string .= "<venueZip>" . htmlspecialchars($congress['venueZip']) . "</venueZip>";
        $string .= "<hotels>" . htmlspecialchars($congress['hotels']) . "</hotels>";
        $string .= "<bios>" . htmlspecialchars($congress['bios']) . "</bios>";
        $string .= "<author>" . htmlspecialchars($congress['author']) . "</author>";
        $string .= "</congress>";
    }
    return $string;
}

function congressDatesForHtmlShortFormat($congress)
{
    $startDate = date("n/d/y" ,strtotime($congress["startDate"]));
    $endDate = date("n/d/y" ,strtotime($congress["endDate"]));
    return (string)$startDate . " - " . (string)$endDate;
}

/*
 * Produces html for a short format block version display of a congress
 */
function getShortFormatCongressBlock($congress)
{
    $html = "
    <div class='shortCongressBlockDIV'";
    
    if ($congress["imageURL"] != "")
    {
        $html .= " style='background-image: linear-gradient(to right, rgba(70, 99, 32, 0.7), rgba(60, 101, 124, 0.7)), url(\"" . CONGRESS_IMAGES_PATH . $congress['imageURL'] . "\");opacity:1;'";
    }
    
    $html .= "><a href='" . HOME . "?action=" . POST_VIEW_CONGRESS . "&congress=" . $congress['id'] . "'>
        <div class='shortCongressShortName'>" . $congress['shortName'] . "</div>
        <div class='shortCongressFullName'>" . $congress['name'] . "</div>
        <div class='shortCongressShortDates'>" . congressDatesForHtmlShortFormat($congress) . "</div>
    </a></div>";
    return $html;
}

/*
 * Produces html for a short format block version display of a congress, without a link to view that congress in detail
 */
function getShortFormatCongressBlockWithoutLink($congress)
{
    $html = "
    <div class='shortCongressBlockDIV'";
    
    if ($congress["imageURL"] != "")
    {
        $html .= " style='background-image: linear-gradient(to right, rgba(70, 99, 32, 0.7), rgba(60, 101, 124, 0.7)), url(\"" . CONGRESS_IMAGES_PATH . $congress['imageURL'] . "\");opacity:1;'";
    }
    $html .= ">
        <div class='shortCongressShortName'>" . $congress['shortName'] . "</div>
        <div class='shortCongressFullName'>" . $congress['name'] . "</div>
        <div class='shortCongressShortDates'>" . congressDatesForHtmlShortFormat($congress) . "</div>
    </div>";
    return $html;
}

/*
 * Produces html for a long format block version display of a congress
 */
function getLongFormatCongressBlock($congress)
{
    $html = "
    <div class='longCongressBlockDIV'";
    
    if ($congress["imageURL"] != "")
    {
        $html .= " style='background-image: linear-gradient(to right, rgba(70, 99, 32, 0.7), rgba(60, 101, 124, 0.7)), url(\"" . CONGRESS_IMAGES_PATH . $congress['imageURL'] . "\");opacity:1;'";
    }
    
    $html .= ">
        <div class='longCongressShortName'>" . $congress["shortName"] . "</div>
        <div class='longCongressName'>" . $congress["name"] . "</div>
        <div class='longCongressShortDates'>" . congressDatesForHtmlShortFormat($congress) . "</div>
        <div class='caret congressCaret fa'>&#xf105;</div>
        <div class='longCongressVenueDetail' style='display:none;'>
            <div class='longCongressVenue'>Conference Venue :</div>
            <div>" . $congress["venueName"] . "</div>
            <div>" . $congress["venueAddress1"] . " " . $congress["venueAddress2"] . "</div>
            <div>" . $congress["venueCity"] . ", " . $congress['venueState'] . " " . $congress['venueZip'] . "</div>
            <div>" . $congress['venueCountry'] . "</div>
        </div>
    </div>
    <script>
        $('.congressCaret').click(function()
        {
            if($('.longCongressVenueDetail').is(':visible'))
            {
                $('.longCongressVenueDetail').hide();
                $('.congressCaret').html('&#xf105;');
            }
            else
            {
                $('.longCongressVenueDetail').show();
                $('.congressCaret').html('&#xf107;');
            }
        });
    </script>";
    return $html;
}


/*
 * Produces html for a long format block version display of a congress
 */
function getLongFormatCongressBlockWithEditLink($congress)
{
    $html = "
    <div class='longCongressBlockDIV'";
    
    if ($congress["imageURL"] != "")
    {
        $html .= " style='background-image: linear-gradient(to right, rgba(70, 99, 32, 0.7), rgba(60, 101, 124, 0.7)), url(\"" . CONGRESS_IMAGES_PATH . $congress['imageURL'] . "\");opacity:1;'";
    }
    
    $html .= ">
        <div class='edit congressEdit fa'><a href='" .HOME . "?action=" . POST_MODIFY_CONGRESS_DETAIL . "&congress=" . $congress['id'] . "'>&#xf044;</a></div>
        <div class='longCongressShortName'>" . $congress["shortName"] . "</div>
        <div class='longCongressName'>" . $congress["name"] . "</div>
        <div class='longCongressShortDates'>" . congressDatesForHtmlShortFormat($congress) . "</div>
        <div class='caret congressCaret fa'>&#xf105;</div>
        <div class='longCongressVenueDetail' style='display:none;'>
            <div class='longCongressVenue'>Conference Venue :</div>
            <div>" . $congress["venueName"] . "</div>
            <div>" . $congress["venueAddress1"] . " " . $congress["venueAddress2"] . "</div>
            <div>" . $congress["venueCity"] . ", " . $congress['venueState'] . " " . $congress['venueZip'] . "</div>
            <div>" . $congress['venueCountry'] . "</div>
        </div>
    </div>
    <script>
        $('.congressCaret').click(function()
        {
            if($('.longCongressVenueDetail').is(':visible'))
            {
                $('.longCongressVenueDetail').hide();
                $('.congressCaret').html('&#xf105;');
            }
            else
            {
                $('.longCongressVenueDetail').show();
                $('.congressCaret').html('&#xf107;');
            }
        });
    </script>";
    return $html;
}

function getCongressReturn($errors, $congress)
{
    return array(
        "errors" => $errors,
        "congress" => $congress
    );
}