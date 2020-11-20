<?php

// Universal function for all Unity Scripts

function getUnityOutputFormat()
{
    $format = isset($_GET['format']) ? $_GET['format'] : "json";
    $format = $format != "json" && $format != "xml" ? "json" : $format;

    header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
    header ("Cache-Control: no-cache, must-revalidate");
    header ("Pragma: no-cache");

    if ($format == "json")
    {
        header('Content-Type: application/json');
    }
    else
    {
        header("Content-Type: text/xml");
    }

    header ("Content-Description: PHP Generated Data" );

    return $format;
}

// Functions for returning XML data to Unity.

function packageUserIntoXML($user)
{
  $string = "<user>";
    $string .= "<id>" . $user['id'] . "</id>";
    $string .= "<first>" . htmlspecialchars($user['first']) . "</first>";
    $string .= "<last>" . htmlspecialchars($user['last']) . "</last>";
    $string .= "<password>" . $user['password'] . "</password>";
    $string .= "<phone>" . $user['phone'] . "</phone>";
    $string .= "<title>" . htmlspecialchars($user['title']) . "</title>";
    $string .= "<level>" . $user['level'] . "</level>";
    $string .= "<role>" . $user['role'] . "</role>";
    $string .= "</user>";
    $xml = new SimpleXMLElement($string);
    return $xml->asXML();
}

// Error functions for Unity scripts.

function getGeneralErrorForUnity($format)
{
    return packageTypeErrorForUnity($format, "general");
}

function packageTypeErrorForUnity($format, $message, $code)
//function packageTypeErrorForUnity($format, $message)
{
    if ($format == "xml")
    {
        $string = "<error>";
        $string .= "<type>" . $message . "</type>";
        $string .= "</error>";
        $xml = new SimpleXMLElement($string);
        return $xml->asXML();
    }
    else
    {
        $error = array(
            "code" => $code,
            "message" => $message
        );
        /*
        $error = array(
            "error" => 0,
            "message" => $message
        );
         * 
         */
        return json_encode($error);
    }
}

function packageDataForUnity($format, $data, $label)
{
    if ($format == "xml")
    {
        
    }
    else
    {
        $return = array(
            "code" => -1,
            $label => $data
        );
        return json_encode($return);
    }
}

function validateUserForUnity($connection, $format)
{
    $user = 0;
    if (isset($_POST['email']) &&
        isset($_POST['password']))
    {
        $email = $_POST['email'];
        $password = md5($_POST['password']);
        $user = getUserByEmail($email, $connection);
        if ($user)
        {
            if ($user['password'] != $password)
            {
                $user = 0;
                echo packageTypeErrorForUnity($format, "Unauthorized access.", 103);
            }
        }
        else
        {
            echo packageTypeErrorForUnity($format, "Unauthorized access.", 102);
        }
    }
    else
    {
        echo packageTypeErrorForUnity($format, "Unauthorized access.", 101);
    }
    return $user;
}

function checkUserLevel($user, $format)
{
    if ($user['level'] > 1)
    {
        return $user['level'];
    }
    else
    {
        echo packageTypeErrorForUnity($format, "Unauthorized access.", 104);
    }
}

function validateCongress($connection, $format)
{
    if (isset($_POST['congress']))
    {
        $congressID = $_POST['congress'];
        if (is_numeric($congressID))
        {
            $congress = getCongressById($congressID, $connection);
            if ($congress)
            {
                return $congress;
            }
            else
            {
                echo packageTypeErrorForUnity($format, "A congress cannot be located.", 113);
            }
        }
        else
        {
            echo packageTypeErrorForUnity($format, "Invalid congress ID error.", 112);
        }
    }
    else if (isset($_POST['congressID']))
    {
        $congressID = $_POST['congressID'];
        if (is_numeric($congressID))
        {
            $congress = getCongressById($congressID, $connection);
            if ($congress)
            {
                return $congress;
            }
            else
            {
                echo packageTypeErrorForUnity($format, "A congress cannot be located.", 113);
            }
        }
        else
        {
            echo packageTypeErrorForUnity($format, "Invalid congress ID error.", 112);
        }
    }
    else
    {
        echo packageTypeErrorForUnity($format, "Invalid congress ID error.", 111);
    }
}

function checkItemID($connection, $format)
{
    $itemID = isset($_POST['itemID']) ? $_POST['itemID'] : null;
    if ($itemID)
    {
        return $itemID;
    }
    else
    {
        echo packageTypeErrorForUnity($format, "Invalid agenda item ID error.", 121);
    }
}

function checkRoomID($connection, $format)
{
    $roomID = isset($_POST['roomID']) ? $_POST['roomID'] : null;
    if ($roomID)
    {
        return $roomID;
    }
    else
    {
        echo packageTypeErrorForUnity($format, "Invalid room ID error.", 131);
    }
}

function sendIncompleteFormDataError ($format)
{
    $message = "Incomplete Form Data.";
    echo packageTypeErrorForUnity($format, $message, 150);
}

function sendInvalidFormDataError ($format)
{
    $message = "Invalid Form Data.";
    echo packageTypeErrorForUnity($format, $message, 151);
}

function isCongressDataSet ($requireID)
{
    if (isset($_POST['newCongressName']) &&
        isset($_POST['newCongressShortName']) &&
        isset($_POST['newCongressURL']) &&
        isset($_POST['newRegistrationURL']) &&
        isset($_POST['newCongressStartDate']) &&
        isset($_POST['newCongressStartTime']) &&
        isset($_POST['newCongressStartMeridian']) &&
        isset($_POST['newCongressEndDate']) &&
        isset($_POST['newCongressEndTime']) &&
        isset($_POST['newCongressEndMeridian']) &&
        isset($_POST['newCongressVenueName']) &&
        isset($_POST['newCongressVenueHall']) &&
        isset($_POST['newCongressVenueBooth']) &&
        isset($_POST['newCongressVenueAddress1']) &&
        isset($_POST['newCongressVenueAddress2']) &&
        isset($_POST['newCongressVenueCity']) &&
        isset($_POST['newCongressVenueState']) &&
        isset($_POST['newCongressVenueCountry']) &&
        isset($_POST['newCongressVenueZip']) &&
        isset($_POST['newCongressHotelStartDate']) &&
        isset($_POST['newCongressHotelEndDate']))
    {
        if ($requireID)
        {
            if (isset($_POST['id']))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return true;
        }
    }
    else
    {
        return false;
    }
}

function isHotelDataSet ($requireID)
{
    if (isset($_POST['newHotelName']) &&
        isset($_POST['newHotelUrl']) &&
        isset($_POST['newHotelAddress1']) &&
        isset($_POST['newHotelAddress2']) &&
        isset($_POST['newHotelCity']) &&
        isset($_POST['newHotelState']) &&
        isset($_POST['newHotelZip']) &&
        isset($_POST['newHotelPhone']))
    {
        if ($requireID)
        {
            if (isset($_POST['id']))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return true;
        }
    }
    else
    {
        return false;
    }
}