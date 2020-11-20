<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Get Reservations - Version 1.0 ************************
 *
 * This script gets all reservations.
 *
 * 1. The script first checks to make sure the user parameter isset, which is a number, and then assigns it to userID.
 *
 * 2. We then pass the userID variable into the getUserById function and assign that to a new variable, user.
 *
 * 3. We then check to make sure the booking parameter isset, which is a number, and then assign it to bookingID.
 *
 * 4. We then check to make sure the congress parameter isset, which is a number, and then assign it to congressID.
 *
 * 5. If congressID isn't set, we then enter into part one of a 3 way if/if else/else statement.
 *
 * 6. We call the function getAllMostRecentHotelReservationsForUser by passing through the variable user and then assigning it to a new variable, hotelRezRecords.
 *
 * 7. Then we call the function getAllHospBookingsForUser by passing through the variable user and then assigning it to a new variable, hospBookings.
 *
 * 8. If we find a hotelRezRecords or a hospBookings, we then declare a new variable, reservations, and assign it to an array.
 *
 * 9. We then pass hotelReservationRecords into reservations and assign it to hotelRezRecords and/or hospitalityBookings into reservations and assign it to hospBookings.
 *
 * 10. We then echo out reservations. This is the first if of the 3 part loop.
 *
 * 11. If a bookingID isset, we then take the congressID and pass it into the function, getCongressById and assign it to a new variable, congress.
 *
 * 12. We then call the function, getHospitalityBookingByID, and pass through the bookingID and congressID and assign it to a new variable, booking.
 *
 * 13. We then echo out the booking. This is the second if in the 3 part loop.
 *
 * 14. In the last part of the loop we call the function getMostRecentReservationRequestForUser. We pass through the user and congressID. We assign this to a new variable, mostRecentHotelRecord.
 *
 * 15. If successful we echo out mostRecentHotelRecord.
 *
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
    $congressID = isset($_POST['congress']) ? $_POST['congress'] : null;
    $bookingID = isset($_POST['booking']) ? $_POST['booking'] : null;
    if (!isset($congressID))
    {
        $hotelRezRecords = getAllMostRecentHotelReservationsForUser($user, $connection);
        $hospBookings = getAllHospBookingsForUser($user, $connection);
        if ($hotelRezRecords || $hospBookings)
        {
            $reservations = array(
                "hotelConfirmations" => $hotelRezRecords,
                "hospitalityBookings" => $hospBookings
            );
            echo packageDataForUnity($format, $reservations, "reservations");
        }
        else
        {
            echo packageTypeErrorForUnity($format, "There are currently no reservations for the user.", 3);
        }
    }
    elseif (isset($bookingID))
    {
        $booking = getHospitalityBookingByID($bookingID, $congressID, $connection);
        if ($booking)
        {
            if ($booking['openEnd'] != "" && isset($booking['adminComment']))
            {
                $confirmationRecord = getRecordByID($booking['confirmationRecordID'], $user, $connection);
                setReservationToViewed($confirmationRecord, $user, $connection);
            }
            echo packageDataForUnity($format, $booking, "hospitalityBooking");
        }
        else
        {
            echo packageTypeErrorForUnity($format, "There are currently no bookings for the user.", 2);
        }
    }
    else
    {
        $mostRecentHotelRecord = getMostRecentReservationRequestForUser($user, $congressID, $connection);
        if ($mostRecentHotelRecord)
        {
            setReservationToViewed($mostRecentHotelRecord, $user, $connection);
            echo packageDataForUnity($format, $mostRecentHotelRecord, "hotelConfirmation");
        }
        else
        {
            echo packageTypeErrorForUnity($format, "There are currently no hotel reservation requests for this congress.", 1);
        }
    }
}