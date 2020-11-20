<?php



function sendRecordToTrash($type, $records, $connection)
{
    $success = 0;
    $recordData = is_array($records) ? implode(",", $records) : $records;
    $query = "INSERT INTO trashBin (id, ";
    $query .= "type, ";
    $query .= "records) VALUES (NULL, '";
    $query .= $type ."', '";
    $query .= $recordData ."')";

    $result = $connection->query($query);

    if ($result)
    {
        $success = 1;
    }
    return $success;
}

function isTrashed($type, $records, $connection)
{
    $isTrashed = 0;
    $recordData = is_array($records) ? implode(",", $records) : $records;
    $query = "SELECT * FROM trashBin WHERE type = '$type' AND records = '$recordData'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        $isTrashed = 1;
    }
    return $isTrashed;
}

function removeFromTrash($type, $records, $connection)
{
    $code = 0;
    $recordData = is_array($records) ? implode(",", $records) : $records;
    $query = "SELECT * FROM trashBin WHERE type = '$type' AND records = '$recordData'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        $query = "DELETE FROM trashBin WHERE type = '$type' AND records = '$recordData'";
        $result = $connection->query($query);
        if ($result)
        {
            $code = 1;
        }
    }
    return $code;
}

function packageTrashRowIntoArray($row)
{
    $record = array(
        'id' => $row['id'],
        'type' => $row['type']
            );
    
    switch($row['type'])
    {
        case CONGRESS_RECORD:
            $record['congressID'] = $row['records'];
            break;
        case HOTEL_PROPERTY_RECORD:
            $record['hotelID'] = $row['records'];
            break;
        case USER_RECORD:
            $record['userID'] = $row['records'];
            break;
        case HOTEL_REQUEST_RECORD:
            $record['userID'] = explode(",", $row['records'])[0];
            $record['recordID'] = explode(",", $row['records'])[1];
            break;
        case HOSP_ROOM_RECORD:
            $record['congressID'] = explode(",", $row['records'])[0];
            $record['roomID'] = explode(",", $row['records'])[1];
            break;
        case HOSP_BOOKING_RECORD:
            $record['congressID'] = explode(",", $row['records'])[0];
            $record['bookingID'] = explode(",", $row['records'])[1];
            break;
    }
    
    return $record;
}



