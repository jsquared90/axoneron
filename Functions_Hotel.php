<?php

/*************************
 *  BASE FUNCTIONS
 * ***********************
 */


/*
 * Base function for an admin adding a hotel to the general database
 */
function addHotelToDatabase($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    
    $result = addHotelToHotels($connection);
    if ($result['errors'] == 0)
    {
        $recordData = array();
        $recordData['type'] = HOTEL_ADDED;
        $recordData['data'] = $result['hotel']['id'];
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
        foreach ($result['errors'] as $error)
        {
            switch ($error['code'])
            {
                case 2:
                    $code = 1;
                    $errors = packageGeneralError($errors, 2);
                break;
                case 20:
                    $code = 1;
                    $errors = packageGeneralError($errors, 20);
                break;
            }
        }
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

/*
 * Base function for an admin adding a hotel to a congress object
 */
function addHotelToCongress($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $hotel = 0;
    
    $congress = getCongressById($_POST['congressID'], $connection);
    if ($congress)
    {
        if (isset($_POST['hotelList']))
        {
            if ($_POST['hotelList'] != "")
            {
                $hotel = getHotelById($_POST['hotelList'], $connection);
                if ($hotel)
                {
                    $result = addHotelToCongressTable($hotel['id'], $congress, $connection);
                    if ($result)
                    {
                        $recordData = array();
                        $recordData['type'] = HOTEL_ADDED_TO_CONGRESS;
                        $recordData['data'] = $congress['id'] . "," . $hotel['id'];
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
                                // error retrieving record by footprint
                                $code = 1;
                                $errors = packageGeneralError($errors, 7);
                            }
                        }
                        else
                        {
                            // error adding record to user
                            $code = 1;
                            $errors = packageGeneralError($errors, 6);
                        }
                    }
                    else
                    {
                        // could not be added to congress table
                        $code = 1;
                        $errors = packageGeneralError($errors, 5);
                    }
                }
            }
            else
            {
                $result = addHotelToHotels($connection);
                if (($result['errors'] == 0))
                {
                    $addHotel = addHotelToCongressTable($result['hotel']['id'], $congress, $connection);
                    if ($addHotel)
                    {
                        $recordData = array();
                        $recordData['type'] = HOTEL_ADDED_TO_CONGRESS;
                        $recordData['data'] = $congress['id'] . "," . $hotel['id'];
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
                                // error retrieving record by footprint
                                $code = 1;
                                $errors = packageGeneralError($errors, 7);
                            }
                        }
                        else
                        {
                            // error adding record to user
                            $code = 1;
                            $errors = packageGeneralError($errors, 6);
                        }
                    }
                    else
                    {
                        // could not be added to congress table
                        $code = 1;
                        $errors = packageGeneralError($errors, 5);
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
                                $errors = packageGeneralError($errors, 4);
                            break;
                            case 20:
                                $code = 1;
                                $errors = packageGeneralError($errors, 20);
                            break;
                        }
                    }
                }
            }
        }
        else
        {
            $result = addHotelToHotels($connection);
            if (($result['errors'] == 0))
            {
                $addHotel = addHotelToCongressTable($result['hotel']['id'], $congress, $connection);
                if ($addHotel)
                {
                    $recordData = array();
                    $recordData['type'] = HOTEL_ADDED_TO_CONGRESS;
                    $recordData['data'] = $congress['id'] . "," . $hotel['id'];
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
                            // error retrieving record by footprint
                            $code = 1;
                            $errors = packageGeneralError($errors, 7);
                        }
                    }
                    else
                    {
                        // error adding record to user
                        $code = 1;
                        $errors = packageGeneralError($errors, 6);
                    }
                }
                else
                {
                    // could not be added to congress table
                    $code = 1;
                    $errors = packageGeneralError($errors, 5);
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
                            $errors = packageGeneralError($errors, 4);
                        break;
                        case 20:
                            $code = 1;
                            $errors = packageGeneralError($errors, 20);
                        break;
                    }
                }
            }
        }
    }
    else
    {
        // no congress could be found
        $code = 1;
        $errors = packageGeneralError($errors, 3);
    }
    $return = array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
    return $return;
}

function modifyHotelDetail($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $result = modifyHotel($connection);
    if ($result['errors'] == 0)
    {
        $recordData = array();
        $recordData['type'] = MODIFIED_HOTEL_DETAIL;
        $recordData['data'] = $result['hotel']['id'];
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
                // error retrieving record by footprint
                $code = 1;
                $errors = packageGeneralError($errors, 5);
            }
        }
        else
        {
            // error adding record to user
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
                case 20:
                    $code = 1;
                    $errors = packageGeneralError($errors, 20);
                break;
            }
        }
    }
    $return = array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
    return $return;
}

/*
 * Base function for an admin removing a hotel from a congress object
 */
function removeHotelFromCongress($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $congress = getCongressById($_POST['congressID'], $connection);
    $hotelID = $_POST['hotelID'];
    $result = removeHotelFromCongressTable($hotelID, $congress, $connection);
    if ($result)
    {
        $recordData = array();
        $recordData['type'] = HOTEL_REMOVAL_FROM_CONGRESS;
        $recordData['data'] = $congress['id'] . "," . $hotelID;
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
                // error retrieving record by footprint
                $code = 1;
                $errors = packageGeneralError($errors, 5);
            }
        }
        else
        {
            // error adding record to user
            $code = 1;
            $errors = packageGeneralError($errors, 4);
        }
    }
    else
    {
        // remove hotel from congress error
        $code = 1;
        $errors = packageGeneralError($errors, 3);
    }
    $return = array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
    return $return;
}


/*************************
 *  SUB FUNCTIONS
 * ***********************
 */

/*
 * For adding a new hotel to the table "hotels". Returns hotel object.
 */
function addHotelToHotels($connection)
{
    $hotel = 0;
    $errors = 0;
    
    $name = urlencode($_POST['newHotelName']);
    if (strlen($name) > 32)
    {
        substr($name, 0, 29) . "...";
    }
    $address1 = urlencode($_POST['newHotelAddress1']);
    if (strlen($address1) > 32)
    {
        substr($address1, 0, 29) . "...";
    }
    $zip = urlencode($_POST['newHotelZip']);
    if (strlen($zip) > 16)
    {
        substr($zip, 0, 13) . "...";
    }
    $footprintData = array(
        "name" => urldecode($name),
        "address1" => urldecode($address1),
        "zip" => urldecode($zip)
    );

    $duplicate = checkForDuplicateHotel($footprintData, $connection);
    if (!$duplicate)
    {
        $url = urlencode($_POST['newHotelUrl']);
        if (strlen($url) > 512)
        {
            if (!$errors){ $errors = []; }
            $error = array(
                "code" => 4,
                "data" => $url
            );
            array_push($errors, $error);
        }
        
        $address2 = urlencode($_POST['newHotelAddress2']);
        if (strlen($address2) > 16)
        {
            substr($address2, 0, 13) . "...";
        }

        $city = urlencode($_POST['newHotelCity']);
        if (strlen($city) > 32)
        {
            substr($city, 0, 29) . "...";
        }

        $state = urlencode($_POST['newHotelState']);
        if (strlen($state) > 16)
        {
            substr($state, 0, 13) . "...";
        }
        $phone = urlencode($_POST['newHotelPhone']);
        if (strlen($phone) > 32)
        {
            substr($phone, 0, 29) . "...";
        }

        {
            $query = "INSERT INTO hotels (id, ";
            $query .= "name, ";
            $query .= "url, ";
            $query .= "address1, ";
            $query .= "address2, ";
            $query .= "city, ";
            $query .= "state, ";
            $query .= "zip, ";
            $query .= "phone) VALUES (NULL, '";
            $query .= $name ."', '";
            $query .= $url ."', '";
            $query .= $address1 ."', '";
            $query .= $address2 ."', '";
            $query .= $city ."', '";
            $query .= $state ."', '";
            $query .= $zip ."', '";
            $query .= $phone ."')";

            // echo $query;
            $result = $connection->query($query);

            if ($result)
            {
                $data = array(
                'name' => $name,
                'address1' => $address1,
                'address2' => $address2,
                'city' => $city,
                'state' => $state,
                'zip' => $zip
                );
                $hotel = getHotelByNameAndLocation($data, $connection);
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
    return getHotelReturn($errors, $hotel);
}

function modifyHotel($connection)
{
    $hotel = 0;
    $errors = 0;
    $hotelID = $_POST['id'];
    if ($hotelID)
    {
        $name = urlencode($_POST['newHotelName']);
        if (strlen($name) > 32)
        {
            substr($name, 0, 29) . "...";
        }
        
        $address1 = urlencode($_POST['newHotelAddress1']);
        if (strlen($address1) > 32)
        {
            substr($address1, 0, 29) . "...";
        }
        
        $zip = urlencode($_POST['newHotelZip']);
        if (strlen($zip) > 16)
        {
            substr($zip, 0, 13) . "...";
        }
        
        $footprintData = array(
            "name" => urldecode($name),
            "address1" => urldecode($address1),
            "zip" => urldecode($zip)
        );

        $duplicate = checkForDuplicateHotel($footprintData, $connection, $hotelID);
        if (!$duplicate)
        {
            $url = urlencode($_POST['newHotelUrl']);
            if (strlen($url) > 512)
            {
                if (!$errors){ $errors = []; }
                $error = array(
                    "code" => 3,
                    "data" => $url
                );
                array_push($errors, $error);
            }
            
            $address2 = urlencode($_POST['newHotelAddress2']);
            if (strlen($address2) > 16)
            {
                substr($address2, 0, 13) . "...";
            }

            $city = urlencode($_POST['newHotelCity']);
            if (strlen($city) > 32)
            {
                substr($city, 0, 29) . "...";
            }

            $state = urlencode($_POST['newHotelState']);
            if (strlen($state) > 16)
            {
                substr($state, 0, 13) . "...";
            }

            $phone = urlencode($_POST['newHotelPhone']);
            if (strlen($phone) > 32)
            {
                substr($phone, 0, 29) . "...";
            }

            {
                $query = "UPDATE hotels SET ";
                $query .= "name = '$name', ";
                $query .= "url = '$url', ";
                $query .= "address1 = '$address1', ";
                $query .= "address2 = '$address2', ";
                $query .= "city = '$city', ";
                $query .= "state = '$state', ";
                $query .= "zip = '$zip', ";
                $query .= "phone = '$phone' ";
                $query .= " WHERE id = '$hotelID'";
                $result = $connection->query($query);
                if ($result)
                {
                    $data = array(
                    'name' => $name,
                    'address1' => $address1,
                    'address2' => $address2,
                    'city' => $city,
                    'state' => $state,
                    'zip' => $zip
                    );
                    $hotel = getHotelByNameAndLocation($data, $connection);
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
    }
    else
    {
        if (!$errors){ $errors = []; }
        $error = array(
            "code" => 2,
            "data" => $hotelID
        );
        array_push($errors, $error);
    }
    return getHotelReturn($errors, $hotel);
}

function getHotelByNameAndLocation($data, $connection)
{
    $name = $data['name'];
    $address1 = $data['address1'];
    $address2 = $data['address2'];
    $city = $data['city'];
    $zip = $data['zip'];
    $query = "SELECT * FROM hotels WHERE name = '$name'";
    $query .= "AND address1 ='$address1'";
    $query .= "AND address2 ='$address2'";
    $query .= "AND city ='$city'";
    $query .= "AND zip ='$zip'";
    $result = $connection->query($query);
    $hotel = 0;
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $hotel = packageHotelRowIntoArray($row);
        }
    }
    return $hotel;
}

function getHotelById($id, $connection)
{
    $query = "SELECT * FROM hotels WHERE id = '$id'";
    $result = $connection->query($query);
    $hotel = 0;
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $hotel = packageHotelRowIntoArray($row);
        }
    }
    return $hotel;
}

function getAllHotels($connection)
{
    $query = "SELECT * FROM hotels";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        $hotels = array();
        while ($row = mysqli_fetch_array($result))
        {
            $hotel = packageHotelRowIntoArray($row);
            array_push($hotels, $hotel);
        }
        usort($hotels, function($a, $b)
        {
            return $a['name'] <=> $b['name'];
        });
        return $hotels;
    }
    else
    {
        return 0;
    }
}

function getHotelsWithCongress($congress, $connection)
{
    $hotels = 0;
    if ($congress['hotels'] != "")
    {
        $hotelIDs = explode(",",$congress['hotels']);
        $hotels = array();
        foreach ($hotelIDs as $hotelID)
        {
            $hotel = getHotelById($hotelID, $connection);
            array_push($hotels, $hotel);
        }
        usort($hotels, function($a, $b)
        {
            return $a['name'] <=> $b['name'];
        });
    }
    return $hotels;
}

function packageHotelRowIntoArray($row)
{
    $hotel = array(
        'id' => $row['id'],
        'name' => urldecode($row['name']),
        'url' => urldecode($row['url']),
        'address1' => urldecode($row['address1']),
        'address2' => urldecode($row['address2']),
        'city' => urldecode($row['city']),
        'state' => urldecode($row['state']),
        'zip' => urldecode($row['zip']),
        'phone' => urldecode($row['phone'])
    );
    return $hotel;
}

/*
 * returns an array object for a hotel request pulled from a comma-delineated cell from the database
 */
function parseHotelRequestData($cellData)
{
    $data = explode(",",$cellData);
    $reservation = array(
        "congressID" => $data[0],
        "roomType" => $data[1],
        "occupancy" => $data[2]
    );
    return $reservation;
}

/*
 * returns an array object for a hotel reservation pulled from a comma-delineated cell from the database
 */
function parseHotelReservationData($cellData)
{
    $data = explode(",",$cellData);
    $reservation = array(
        "congressID" => $data[0],
        "checkInDate" => parseDateFromDateTime($data[1]),
        "checkOutDate" => parseDateFromDateTime($data[2]),
        "roomType" => $data[3],
        "occupancy" => $data[4],
        "hotelID" => $data[5],
        "confirmationNumber" => urldecode($data[6])
    );
    return $reservation;
}

function packageHotelRequest()
{
    $requestString = $_POST["congressID"] . ",";
    $requestString .= $_POST["roomType"] . ",";
    $requestString .= $_POST["occupancy"];
    return $requestString;
}

function packageHotelConfirmation()
{
    $confirmString = $_POST["congressID"] . ",";
    $confirmString .= date('Y-m-d', strtotime($_POST["checkInDate"])) . ",";
    $confirmString .= date('Y-m-d', strtotime($_POST["checkOutDate"])) . ",";
    $confirmString .= $_POST["roomType"] . ",";
    $confirmString .= $_POST["occupancy"] . ",";
    $confirmString .= $_POST["hotelList"] . ",";
    $confirmString .= urlencode($_POST["confirmationNumber"]);
    return $confirmString;
}

function convertHotelTermForDisplay($type, $term)
{
    switch ($type)
    {
        case "roomType":
            switch ($term)
            {
                case 'King':
                    return "King Bed";
                case 'Double':
                    return "Double Bed";
            }
            break;
    }
}

function checkForDuplicateHotel($footprintData, $connection, $currentHotelID = 0)
{
    $result = 0;
    $hotels = getAllHotels($connection);
    foreach ($hotels as $hotel)
    {
        if ($hotel['name'] == $footprintData['name'] &&
            $hotel['address1'] == $footprintData['address1'] &&
            $hotel['zip'] == $footprintData['zip'])
        {
            if ($currentHotelID <= 0 || $currentHotelID != $hotel['id'])
            {
                if (!$result)
                {
                    $result = array();
                    $result['errors'] = array();
                }
                if ($hotel['name'] == $footprintData['name'] &&
                    $hotel['address1'] == $footprintData['address1'] &&
                    $hotel['zip'] == $footprintData['zip'])
                {
                    $error = array(
                        "code" => 20,
                        "data" => $hotel['name'], $hotel['address1'], $hotel['zip']
                    );
                    array_push($result['errors'], $error);
                }
            }
        }
    }
    return $result;
}

function getHotelReturn($errors, $hotel)
{
    return array(
        "errors" => $errors,
        "hotel" => $hotel
    );
}