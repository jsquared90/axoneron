<?php

/*************************
 *  BASE FUNCTIONS
 * *********************** 
 */

/*
 * Base function for an admin adding a hospitality room to a congress
 */
function addHospitalityRoomToCongress($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    
    $congressID = $_POST['congressID'];
    $room = addHospitalityRoomToTable($connection);
    
    if ($room)
    {
        $recordData = array();
        $recordData['type'] = HOSP_ROOM_ADDED_TO_CONGRESS;
        $recordData['data'] = $congressID . "," . $room['id'];
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
    "record" => $record,
    "room" => $room
        );
}

function removeHospitalityRoomFromCongress($user, $connection)
{
    $code = 0;
    $errors = 0;
    $affectedBookings = getAllHospitalityBookingsForRoom($_POST['congressID'], $_POST['hospRoomID'], $connection);
    $records = $_POST['congressID'] . "," . $_POST['hospRoomID'];
    $result =  sendRecordToTrash(HOSP_ROOM_RECORD, $records, $connection);
    if ($result)
    {
        /*
        foreach ($affectedBookings as $booking)
        {
            $records2 = $booking['room']['congressID'] . "," . $booking['id'];
            $result =  sendRecordToTrash(HOSP_BOOKING_RECORD, $records2, $connection);
        }
         * 
         */
        $recordData = array();
        $recordData['type'] = REMOVED_HOSP_ROOM;
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
        "record" => $record,
        "affectedBookings" => $affectedBookings
    );
}

function modifyHospitalityRoom($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $affectedBookings = 0;
    $congressID = $_POST['congressID'];
    $hospRoomID = $_POST['hospRoomID'];
    $result = modifyHospitalityRoomRecord($connection);
    if ($result['code'] < 0)
    {   
        $affectedBookings = $result['affectedBookings'];
        $recordData = array();
        $recordData['type'] = MODIFIED_HOSP_ROOM;
        $recordData['data'] = $congressID . "," . $hospRoomID;
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
        foreach ($result['errors'] as $error)
        {
            switch ($error['code'])
            {
                case 2:
                    $code = 1;
                    $errors = packageGeneralError($errors, 2);
                break;
                case 3:
                    $code = 1;
                    $errors = packageGeneralError($errors, 3);
                break;
            }
        }
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record,
        "affectedBookings" => $affectedBookings
    );
}

/*
 * Base function for a user bookng a hospitality room
 */
function bookHospitalityRoom($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    
    $congressID = $_POST['congressID'];
    $roomID = $_POST['roomID'];
    $room = getHospitalityRoomByID($roomID, $congressID, $connection);
    
    if ($room)
    {
        $booking = addBookingToHospRoom($room, $congressID, $user, $connection);
        
        if ($booking)
        {
            $recordData = array();
            $recordData['type'] = HOSP_ROOM_BOOKED;
            $recordData['data'] = $congressID . "," . $booking['id'];
            $recordData['openEnd'] = isset($_POST['openEnd']) ? urlencode($_POST['openEnd']) : "";
            $result = addRecordToUser($user, $recordData, $user, $connection);
            if ($result)
            {
                $record = getRecordByFootprint($recordData, $user, $connection);
                if ($record)
                {
                    if (isset($_POST['openEnd']))
                    {
                        if ($_POST['openEnd'] != "")
                        {
                            $result2 = addToPendingRequests($record, $user, $connection);
                            if ($result2)
                            {
                                $code = -1;
                            }
                            else
                            {
                                $code = 1;
                                $errors = packageGeneralError($errors, 6);
                            }
                        }
                        else
                        {
                            $code = -1;
                        }
                    }
                    else
                    {
                        $code = -1;
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

function modifyHospitalityRoomBooking($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    // check to see if there is an existing pending request and delete it
    
    $congressID = $_POST['congressID'];
    $roomID = $_POST['roomID'];
    $bookingID = $_POST['bookingID'];
    $booking = getHospitalityBookingByID($bookingID, $congressID, $connection);
    $userRecord = getUserRecordForHospBooking($booking, $connection);
    $pR = getPendingRequestByUserAndRecordID($userRecord['id'], $user, $connection);
    if ($pR)
    {
        removeFromPendingRequests($pR, $connection);
    }
    
    $bookingName = urlencode($_POST['bookingName']);
    $startDate = convertToSqlDateTime($_POST['dateInput'], $_POST['startTime']);
    $endDate = convertToSqlDateTime($_POST['dateInput'], $_POST['endTime']);
    $openEnd = urlencode($_POST['openEnd']);
    
    $query = "UPDATE congressHospitalitySchedule_" . $congressID . " SET ";
    $query .= "startDate = '$startDate'";
    $query .= ", endDate = '$endDate'";
    $query .= ", openEnd = '$openEnd'";
    $query .= ", bookingName = '$bookingName'";
    $query .= " WHERE id = '$bookingID'";
    $result = $connection->query($query);

    if ($result)
    {
        $booking = getHospitalityBookingByID($bookingID, $congressID, $connection);
        if ($booking)
        {
            $recordData = array();
            $recordData['type'] = MODIFIED_HOSP_ROOM_BOOKING;
            $recordData['data'] = $congressID . "," . $booking['id'];
            $recordData['openEnd'] = isset($_POST['openEnd']) ? urlencode($_POST['openEnd']) : "";
            $result = addRecordToUser($user, $recordData, $user, $connection);
            if ($result)
            {
                $record = getRecordByFootprint($recordData, $user, $connection);
                if ($record)
                {
                    if (isset($_POST['openEnd']))
                    {
                        if ($_POST['openEnd'] != "")
                        {
                            $result2 = addToPendingRequests($record, $user, $connection);
                            if ($result2)
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
                            $code = -1;
                        }
                    }
                    else
                    {
                        $code = -1;
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
    
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
            );
}

function removeHospitalityBooking($congressID, $bookingID, $user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    
    $records = $congressID . "," . $bookingID;
    $result =  sendRecordToTrash(HOSP_BOOKING_RECORD, $records, $connection);
    if ($result)
    {
        $recordData = array();
        $recordData['type'] = CANCELLED_HOSP_BOOKING;
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



function generateCongressHospitalityRoomsTable($congress, $connection)
{
    $query = "CREATE TABLE congressHospitalityRooms_" . $congress['congress']['id'] . " (";
    $query .= "id int(5) NOT NULL AUTO_INCREMENT,";
    $query .= "name varchar(32) NOT NULL,";
    $query .= "location varchar(32) NOT NULL,";
    $query .= "size varchar(32) NOT NULL,";
    $query .= "availability varchar(512) NOT NULL,";
    $query .= "PRIMARY KEY (id))";
    $result = $connection->query($query);
    
    return $result;
}

function deleteCongressHospitalityRoomsTable($congress, $connection)
{
    $id = $congress['id'];
    $query = "DROP TABLE congressHospitalityRooms_" . $id;
    $result = $connection->query($query);
    return $result;
}

function generateCongressHospitalityScheduleTable($congress, $connection)
{
    $query = "CREATE TABLE congressHospitalitySchedule_" . $congress['congress']['id'] . " (";
    $query .= "id int(5) NOT NULL AUTO_INCREMENT,";
    $query .= "roomID int(2) NOT NULL,";
    $query .= "startDate datetime NOT NULL,";
    $query .= "endDate datetime NOT NULL,";
    $query .= "openEnd varchar(1024) NOT NULL,";
    $query .= "bookingName varchar(128) NOT NULL,";
    $query .= "author int(5) NOT NULL,";
    $query .= "PRIMARY KEY (id))";
    $result = $connection->query($query);
    
    return $result;
}

function deleteCongressHospitalityScheduleTable($congress, $connection)
{
    $id = $congress['id'];
    $query = "DROP TABLE congressHospitalitySchedule_" . $id;
    $result = $connection->query($query);
    return $result;
}

function addHospitalityRoomToTable($connection)
{
    $room = 0;
    $congressID = $_POST['congressID'];
    $name = urlencode($_POST['newHospRoomName']);
    $location = urlencode($_POST['newHospRoomLocation']);
    $size = urlencode($_POST['newHospRoomSize']);
    $numOfTimeSlots = $_POST['numTimeSlots'];
    $availability = "";
    for ($i = 1 ; $i < $numOfTimeSlots + 1 ; $i++)
    {
        $startDate = parseDateFromDateTime($_POST['startDate' . $i]) . " " . parseTimeFromDateTime($_POST['startTime' . $i] . $_POST['startMeridian' . $i]);
        $endDate = parseDateFromDateTime($_POST['startDate' . $i]) . " " . parseTimeFromDateTime($_POST['endTime' . $i] . $_POST['endMeridian' . $i]);
        $availability .= $startDate . "-" . $endDate;
        if ($i+1 < $numOfTimeSlots+1)
        {
            $availability .= ",";
        }
    }
    $query = "INSERT INTO congressHospitalityRooms_" . $congressID . " (id, ";
    $query .= "name, ";
    $query .= "location, ";
    $query .= "size, ";
    $query .= "availability) VALUES (NULL, '";
    $query .= $name ."', '";
    $query .= $location ."', '";
    $query .= $size ."', '";
    $query .= $availability ."')";
    $result = $connection->query($query);
    
    if ($result)
    {
        $room = getHospitalityRoomByName($congressID, $name, $connection);
    }
    
    return $room;
}

function modifyHospitalityRoomRecord($connection)
{
    $code = 0;
    $errors = 0;
    $affectedBookings = 0;
    $hospRoomID = $_POST['hospRoomID'];
    $congressID = $_POST['congressID'];
    $room = getHospitalityRoomByID($hospRoomID, $congressID, $connection);
    if ($room)
    {
        $name = urlencode($_POST['newHospRoomName']);
        $location = urlencode($_POST['newHospRoomLocation']);
        $size = urlencode($_POST['newHospRoomSize']);
        $numTimeSlots = $_POST['numTimeSlots'];
        $availability = "";
        for ($i = 1 ; $i < $numTimeSlots + 1 ; $i++)
        {
            $startDate = parseDateFromDateTime($_POST['startDate' . $i]) . " " . parseTimeFromDateTime($_POST['startTime' . $i] . $_POST['startMeridian' . $i]);
            $endDate = parseDateFromDateTime($_POST['startDate' . $i]) . " " . parseTimeFromDateTime($_POST['endTime' . $i] . $_POST['endMeridian' . $i]);
            $availability .= $startDate . "-" . $endDate;
            if ($i+1 < $numTimeSlots+1)
            {
                $availability .= ",";
            }
        }
        $query = "UPDATE congressHospitalityRooms_" . $congressID . " SET ";
        $query .= "name = '$name', ";
        $query .= "location = '$location', ";
        $query .= "size = '$size', ";
        $query .= "availability = '$availability'";
        $query .= " WHERE id = '$hospRoomID'";
        $result = $connection->query($query);
        if ($result)
        {
            // need to iterate through all bookings to see if any time changes affect existing bookings
            $hospRoom = getHospitalityRoomByID($hospRoomID, $congressID, $connection);
            $bookings = getAllHospitalityBookingsForRoom($congressID, $hospRoomID, $connection);
            foreach ($bookings as $booking)
            {
                $clear = 0;
                $bookingID = $booking['id'];
                $bookingStart = strtotime($booking['date'] . " " . $booking['startTime']);
                $bookingEnd = strtotime($booking['date'] . " " . $booking['endTime']);
                $newStart = 0;
                $newEnd = 0;
                foreach ($hospRoom['timeSlots'] as $timeSlot)
                {
                    $slotStart = strtotime($timeSlot['start']);
                    $slotEnd = strtotime($timeSlot['end']);
                    if ($slotStart <= $bookingStart && $slotEnd >= $bookingEnd)
                    {
                        $clear = 1;
                    }
                    else if ($slotStart <= $bookingStart && $slotEnd > $bookingStart)
                    {
                        $newStart = $bookingStart;
                        $newEnd = $slotEnd;
                    }
                    else if ($slotEnd >= $bookingEnd && $slotStart < $bookingEnd)
                    {
                        $newStart = $slotStart;
                        $newEnd = $bookingEnd;
                    }
                    else if ($bookingStart <= $slotStart && $bookingEnd >= $slotEnd)
                    {
                        $newStart = $slotStart;
                        $newEnd = $slotEnd;
                    }
                }
                if (!$clear)
                {
                    if ($newStart && $newEnd)
                    {
                        $startTime = date("g:ia", $newStart);
                        $endTime = date("g:ia", $newEnd);
                        $query = "UPDATE congressHospitalitySchedule_" . $congressID . " SET ";
                        $query .= "startDate = '" . date(getSqlDateFormat(), $newStart) . "', ";
                        $query .= "endDate = '" . date(getSqlDateFormat(), $newEnd) . "'";
                        $query .= " WHERE id = '$bookingID'";
                        $result2 = $connection->query($query);
                    }
                    else
                    {
                        $startTime = $endTime = 0;
                        $records = $booking['room']['congressID'] . "," . $booking['id'];
                        $result =  sendRecordToTrash(HOSP_BOOKING_RECORD, $records, $connection);
                    }
                    $newBooking = array(
                        "id" => $bookingID,
                        "room" => $hospRoom,
                        "date" => $booking['date'],
                        "startTime" => $startTime,
                        "endTime" => $endTime,
                        "originalStartTime" => $booking['startTime'],
                        "originalEndTime" => $booking['endTime'],
                        "openEnd" => $booking['openEnd'],
                        "bookingName" => $booking['bookingName'],
                        "author" => $booking['author']
                    );
                    if (!$affectedBookings) { $affectedBookings = []; }
                    array_push($affectedBookings, $newBooking);
                }
            }
            $code = -1;
        }
        else
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 4,
                "data" => $result
            );
            array_push($errors, $error);
        }
    }
    else
    {
        if (!$errors){ $errors = []; }
        $error = array(
            "code" => 3,
            "data" => $room
        );
        array_push($errors, $error);
    }
    
    return array(
        "code" => $code,
        "errors" => $errors,
        "affectedBookings" => $affectedBookings
    );
}

function addBookingToHospRoom($room, $congressID, $user, $connection)
{
    $booking = 0;
    $bookingName = urlencode($_POST['bookingName']);
    $startDate = convertToSqlDateTime($_POST['dateInput'], $_POST['startTime']);
    $endDate = convertToSqlDateTime($_POST['dateInput'], $_POST['endTime']);
    $openEnd = urlencode($_POST['openEnd']);
    $query = "INSERT INTO congressHospitalitySchedule_" . $congressID . " (id, ";
    $query .= "roomID, ";
    $query .= "startDate, ";
    $query .= "endDate, ";
    $query .= "openEnd, ";
    $query .= "bookingName, ";
    $query .= "author) VALUES (NULL, '";
    $query .= $room['id'] ."', '";
    $query .= $startDate ."', '";
    $query .= $endDate ."', '";
    $query .= $openEnd ."', '";
    $query .= $bookingName ."', '";
    $query .= $user['id'] ."')";

    $result = $connection->query($query);
    
    if ($result)
    {
        $booking = getHospitalityBookingByName($congressID, $bookingName, $connection);
    }
    
    return $booking;
}

function getHospitalityRoomByID($id, $congressID, $connection, $ignoreTrash = 0)
{
    $query = "SELECT * FROM congressHospitalityRooms_" . $congressID . " WHERE id = '$id'";
    $result = $connection->query($query);
    $room = 0;
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $room = packageHospitalityRoomTableDataIntoArray($row, $congressID, $connection, $ignoreTrash);
        }
    }
    return $room;
}

function getHospitalityRoomByName($congressID, $name, $connection)
{
    $query = "SELECT * FROM congressHospitalityRooms_" . $congressID . " WHERE name = '$name'";
    $result = $connection->query($query);
    $room = 0;
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $room = packageHospitalityRoomTableDataIntoArray($row, $congressID, $connection);
        }
    }
    return $room;
}

function getAllHospitalityRoomsForCongress($congress, $connection)
{
    $rooms = 0;
    $query = "SELECT * FROM congressHospitalityRooms_" . $congress['id'];
    $result = $connection->query($query);
    if ($result)
    {
        if (mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_array($result))
            {
                $room = packageHospitalityRoomTableDataIntoArray($row, $congress['id'], $connection);
                if ($room)
                {
                    if (!$rooms) { $rooms = []; }
                    array_push($rooms, $room);
                }
            }  
        }
    }
    return $rooms;  
}

function getAllHospBookingsForUser($user, $connection)
{
    $bookings = 0;
    $congresses = getAllCongresses($connection);
    if ($congresses)
    {
        foreach ($congresses as $congress)
        {
            if (!congressIsInThePast($congress))
            {
                $congressID = $congress["id"];
                $bookings2 = getUserHospBookingsForCongress($user, $congressID, $connection);
                if ($bookings2)
                {
                    foreach ($bookings2 as $booking)
                    {
                        if (!$bookings){ $bookings = []; }
                        array_push($bookings, $booking);
                    }
                }
            }
        }
    }
    return $bookings;
}

function getUserHospBookingsForCongress($user, $congressID, $connection)
{
    $bookings = 0;
    $userID = $user['id'];
    $query = "SELECT * FROM congressHospitalitySchedule_" . $congressID . " WHERE author = '$userID'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        $bookings = array();
        while ($row = mysqli_fetch_array($result))
        {
            $booking = packageHospBookingDataIntoArray($row, $congressID, $connection);
            if ($booking)
            {
                $record = getUserRecordForHospBooking($booking, $connection);
                $booking['timeStamp'] = $record['timeStamp'];
                if (!$bookings){ $bookings = []; }
                array_push($bookings, $booking);
            }
        }
    }
    return $bookings;
}



function packageHospitalityRoomTableDataIntoArray($row, $congressID, $connection, $ignoreTrash = 0)
{
    $room = 0;
    $records = $congressID . "," . $row['id'];
    if (!isTrashed(HOSP_ROOM_RECORD, $records, $connection) || $ignoreTrash)
    {
        $availabilityString = $row['availability'];
        $timeSlotStrings = explode(",",$availabilityString);
        $timeSlots = array();
        foreach ($timeSlotStrings as $timeSlotString)
        {
            $timeSlot = parseTimeSlotDataFromString($timeSlotString);
            array_push($timeSlots, $timeSlot);
        }
        usort($timeSlots, function($a, $b)
        {
            return new DateTime($a['start']) <=> new DateTime($b['start']);
        });
        $room = array(
            'congressID' => $congressID,
            'id' => $row['id'],
            'name' => urldecode($row['name']),
            'location' => urldecode($row['location']),
            'size' => urldecode($row['size']),
            'timeSlots' => $timeSlots
        );
    }
    
    return $room;
}

function getHospitalityBookingByID($id, $congressID, $connection, $ignoreTrash = 0)
{
    $query = "SELECT * FROM congressHospitalitySchedule_" . $congressID . " WHERE id = '$id'";
    $result = $connection->query($query);
    $booking = 0;
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $booking = packageHospBookingDataIntoArray($row, $congressID, $connection, $ignoreTrash);
        }
    }
    return $booking;
}

function getHospitalityBookingByName($congressID, $bookingName, $connection)
{
    $booking = 0;
    $query = "SELECT * FROM congressHospitalitySchedule_" . $congressID . " WHERE bookingName = '$bookingName'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $booking = packageHospBookingDataIntoArray($row, $congressID, $connection);
        }
    }
    return $booking;
}

function getAllHospitalityBookingsForRoom($congressID, $roomID, $connection, $ignoreTrash = false)
{
    $bookings = 0;
    $query = "SELECT * FROM congressHospitalitySchedule_" . $congressID;
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            if ($row["roomID"] == $roomID)
            {
                $booking = packageHospBookingDataIntoArray($row, $congressID, $connection, $ignoreTrash);
                if ($booking)
                {
                    if (!$bookings){ $bookings = []; }
                    array_push($bookings, $booking);
                }
            }
        }
    }
    return $bookings;
}

function packageHospBookingDataIntoArray($row, $congressID, $connection, $ignoreTrash = 0)
{
    $booking = 0;
    $records = $congressID . "," . $row['id'];
    $records2 = $congressID . "," . $row['roomID'];
    if ((!isTrashed(HOSP_BOOKING_RECORD, $records, $connection) &&
            !isTrashed(HOSP_ROOM_RECORD, $records2, $connection)) ||
            $ignoreTrash)
    {
        $room = getHospitalityRoomByID($row['roomID'], $congressID, $connection);
        $date = parseDateFromDateTime($row['startDate']);
        $startTime = parseTimeFromDateTime($row['startDate']);
        $endTime = parseTimeFromDateTime($row['endDate']);
        $author = getUserById($row['author'], $connection);
        $booking = array(
            'id' => $row['id'],
            'room' => $room,
            'date' => $date,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'openEnd' => urldecode($row['openEnd']),
            'bookingName' => urldecode($row['bookingName']),
            'author' => $author
        );
        $booking = injectHospRoomBookingConfirmation($booking, $connection);
    }
    
    return $booking;
}

function parseTimeSlotDataFromString($timeSlotString)
{
    $start = explode("-",$timeSlotString)[0];
    $end = explode("-",$timeSlotString)[1];
    $timeSlot = array(
        "start" => $start,
        "end" => $end
    );
    return $timeSlot;
}

function getHospitalityRoomTotalDateSpan($room)
{
    $minDate = 0;
    $maxDate = 0;
    foreach ($room['timeSlots'] as $timeSlot)
    {
        $start = strtotime($timeSlot["start"]);
        $minDate = $minDate == 0 ? $start : ($start < $minDate ? $start : $minDate);
        $end = strtotime($timeSlot["end"]);
        $maxDate = $end > $maxDate ? $end : $maxDate;
    }
    $s = date("n/d/y", $minDate);
    if ($s != date("n/d/y", $maxDate))
    {
        $s .= " - " . date("n/d/y", $maxDate);
    }
    return $s;
}

function getHospRoomSchedule($room, $connection)
{
    $schedule = 0;
    
    // get current bookings
    $currentBookings = getAllHospitalityBookingsForRoom($room['congressID'], $room['id'], $connection);
        
    usort($room['timeSlots'], function($a, $b)
    {
        return new DateTime($a['start']) <=> new DateTime($b['start']);
    });

    // initialize a previous day variable and the array variable for containing segments
    $previousDay = 0;
    $day = 0;

    // now let's iterate through each time slot
    foreach ($room['timeSlots'] as $timeSlot)
    {

        $start = strtotime($timeSlot["start"]);

        // group segments by day
        $currentDay = date("n/d/y", $start);
        if ($currentDay !== $previousDay)
        {
            $day = array(
                "date" => $currentDay,
                "segments" => array()
            );
        }

        // dice in 15 minute segments. if a start or end does not follow on an even 15 minute mark, adjust relevant segment accordingly
        while ($start < strtotime($timeSlot["end"]))
        {
            $end = $start + HOSP_SEGMENT_LENGTH - ($start % HOSP_SEGMENT_LENGTH);
            $end = $end < strtotime($timeSlot["end"]) ? $end : strtotime($timeSlot["end"]);

            // check against current segments to determine if available. If not available, assign author from current booking
            $available = true;
            $author = 0;
            
            if ($currentBookings)
            {
                foreach ($currentBookings as $booking)
                {
                    if ($currentDay == $booking['date'])
                    {
                        $bookingStart = strtotime($currentDay . " " . $booking['startTime']);
                        $bookingEnd = strtotime($currentDay . " " . $booking['endTime']);
                        
                        if (
                                ($start >= $bookingStart && $start < $bookingEnd)
                                ||
                                ($end > $bookingStart && $end <= $bookingEnd)
                                ||
                                ($start <= $bookingStart && $end >= $bookingEnd)
                            )
                        {
                            $available = false;
                            $author = $booking['author'];
                        }
                    }
                }
            }
            

            $segment = array(
                "date" => $currentDay,
                "startTime" => rtrim(date("g:ia", $start), "m"),
                "endTime" => rtrim(date("g:ia", $end), "m"),
                "duration" => ($end - $start) / 60,
                "available" => $available,
                "author" => $author
            );
            array_push($day["segments"], $segment);
            $start = $end;
        }

        $next = next($room['timeSlots']);
        if ($next)
        {
            if ($currentDay != date("n/d/y", strtotime($next['start'])))
            {
                if (!$schedule){ $schedule = []; }
                array_push($schedule, $day);
            }
            else
            {
                array_push($day["segments"], 0);
            }
        }
        else
        {
            if (!$schedule){ $schedule = []; }
            array_push($schedule, $day);
        }

        $previousDay = $currentDay;
    }
    
    
    return $schedule;
}

function checkScheduleForDate($schedule, $date)
{
    $valid = 0;
    foreach ($schedule as $day)
    {
        if ($day['date'] == $date)
        {
            $valid = 1;
        }
    }
    return $valid;
}

/*
 * returns an array object for a hospitality booking pulled from a comma-delineated cell from the database
 */
function parseHospRoomBookingData($cellData, $connection)
{
    $data = explode(",",$cellData);
    $congressID = $data[0];
    $recordID = $data[1];
    $booking = getHospitalityBookingByID($recordID, $congressID, $connection);
    return $booking;
}

/*
 * Produces html for a short format block version display of a hospitality room object
 */
function getShortFormatHospitalityRoom($room, $congressID)
{
    $html = "
    <div class='shortReservationDIV'><a href='" . HOME . "?page=hospitalityBooking&congress=" . $congressID . "&room=" . $room['id'] . "'>
        <div class='shortHospRoomName'>" . $room["name"] . "</div>
        <div class='shortHospRoomLocation'>" . $room["location"] . "</div>
        <div class='shortHospRoomSize'>" . $room["size"] . "</div>
        <div class='shortHospRoomSpan'>" . getHospitalityRoomTotalDateSpan($room) . "</div>
    </a></div>
    ";
    return $html;
}


/*
 * Produces html for a long format block version display of a hospitality room object
 */
function getLongFormatHospitalityRoom($room, $congress)
{
    
    $html = "
    <div class='longCongressBlockDIV'";
    
    if ($congress["imageURL"] != "")
    {
        $html .= " style='background-image: linear-gradient(to right, rgba(70, 99, 32, 0.7), rgba(60, 101, 124, 0.7)), url(\"" . CONGRESS_IMAGES_PATH . $congress['imageURL'] . "\");opacity:1;'";
    }
    
    $html .= ">
        <div class='longCongressShortName'>" . $room["name"] . "</div>
        <div>" . $congress['shortName'] . "</div>
        <div>" . congressDatesForHtmlShortFormat($congress) . "</div>";
    
    if ($room["location"] != "" || $room["size"] != "")
    {
        $html .= "
        <div class='caret hospRoomCaret fa'>&#xf105;</div>
        <div class='longHospRoomDetail' style='display:none;'>";
        if ($room["location"] != "")
        {
            $html .= "
            <div class='titleStyle2'>" . $room["location"] . "</div>";
        }
        if ($room["size"] != "")
        {
            $html .= "
            <div>" . $room["size"] . "</div>";
        }
        $html .= "
        </div>
        <script>
        $('.hospRoomCaret').click(function()
        {
            if($('.longHospRoomDetail').is(':visible'))
            {
                $('.longHospRoomDetail').hide();
                $('.hospRoomCaret').html('&#xf105;');
            }
            else
            {
                $('.longHospRoomDetail').show();
                $('.hospRoomCaret').html('&#xf107;');
            }
        });
        </script>";
    }
    
    $html .= "
    </div>";
    
    return $html;
}




/*
 * Produces html for a short format block version display of a hospitality booking object
 */
function getShortFormatHospitalityBooking($booking, $connection)
{
    $congress = getCongressById($booking['room']['congressID'], $connection);
    
    $html = "
    <div class='shortReservationDIV'><a href='" . HOME . "?page=reservations&congress=" . $booking['room']['congressID'] . "&booking=" . $booking['id'] . "'>
        <div class='shortCongressShortName'>" . $congress['shortName'] . "</div>
        <div class='shortHospRoomName'>" . $booking["room"]["name"] . "</div>
        <div class='shortHospBookingName'>" . $booking["bookingName"] . "</div>
        <div class='shortHospRoomSpan'>" . format1ForSingleDateTimeDisplay($booking['date'], $booking['startTime'], $booking['endTime']) . "</div>";
    
    if ($booking['openEnd'] != "" && !isset($booking['adminComment']))
    {
        $html .= "
        <div class='copyStyle2'>Request Pending</div>";
    }
    
    $html .= "
    </a></div>";
    return $html;
}


/*
 * Produces html for a long format block version display of a hospitality booking object
 */
function getLongFormat1HospitalityBooking($booking, $connection)
{
    $congress = getCongressById($booking['room']['congressID'], $connection);
    
    $html = "
    <div class='longReservationDIV'>    
        <form class='removalForm hospBookRemovalForm' name='hospBookingRemove' method='post' onsubmit='confirmHospBookingDelete();' action='" . HOME . "'>
            <div class='trash rezTrash fa'><label for='rezRemove'>&#xf1f8;</label></div>
            <input name='congressID' hidden='true' type='number' value='" . $congress['id'] . "'/>
            <input name='bookingID' hidden='true' type='number' value='" . $booking['id'] . "'/>
            <input id='rezRemove' type='submit' name='" . POST_REMOVE_HOSP_BOOKING . "' value=''/>
        </form>
        <a href='" . HOME . "?page=" . POST_VIEW_HOSP_BOOKINGS . "&congress=" . $congress['id'] . "&bookingID=" . $booking['id'] . "'>
            <form class='editForm hospBookEditForm'>
                <div class='edit rezEdit fa'><label for='rezEdit'>&#xf044;</label></div>
            </form>
        </a>
        <div class='longHospBookBlockDIV'>
            <div class='longHospBookName'>" . $booking["bookingName"] . "</div>
            <div>" . $booking["room"]["name"] . "</div>";
    if ($booking["location"] != "")
    {
        echo "
            <div>" . $booking["location"] . "</div>";
    }
    if ($booking["size"] != "")
    {
        echo "
            <div>" . $booking["size"] . "</div>";
    }
    $html .= "
            <div>" . format1ForSingleDateTimeDisplay($booking['date'], $booking['startTime'], $booking['endTime']) . "</div>";
    if ($booking['openEnd'] != "")
    {
        $html .= "
            <div class='titleStyle2 extraTopMargin'>Special Requests:</div>
            <div class='commentsDisplayDIV'>" . $booking["openEnd"] . "</div>";
    }
    $html .= "
        </div>
    </div>
    ";
    return $html;
}

