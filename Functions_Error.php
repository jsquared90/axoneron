<?php

function queryError($return, $type, $webBuild = 1)
{
    //$webBuild = $webBuild == null ? 1 : $webBuild;
    
    $code = is_int($return) ? $return : (is_array($return) ? $return["code"] : -1);
    $html = "";
    $message = "";
    
    $lR = $webBuild ? "<br />" : "\r\n";
    
    switch ($type)
    {   
        case PACKAGED_INSIGHTS:
            if ($code == 4)
            {
                $message = "The applied filters did not yield any activated insights." . $lR;
                $html = getGeneralErrorCode($message);
            }
            else if ($code >= 0)
            {
                $message = "There was an error creating the download link." . $lR;
                $message .= "Please contact tech support to resolve this issue" . $lR;
                $message .= "Error Code : " . $code . $lR;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_ADD_AGENDA:
            if ($return['errors'] >= 0)
            {
                $fatalFlag = -1;
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were numerous errors trying to add the agenda to the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the agenda to the congress." . $lR;
                }
                $error = $return["errors"];
                foreach ($error as $e)
                {
                    if ($e['failureType'] == 1)
                    {
                        $fatalFlag = 1;
                    }
                    else if ($fatalFlag != 1 && $e['failureType'] == 0)
                    {
                        $fatalFlag = 0;
                    }
                    switch ($e['code'])
                    {
                        case 1:
                            $messageBody .= "The file size was too large." . $lR;
                            $messageBody .= "Please select a file of a smaller size to upload." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 2:
                            $messageBody .= "The file is an improper format." . $lR;
                            $messageBody .= "Please select either an '.xls' or '.xlsx'." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "Something unexpected prohibited the file from being saved to the server." . $lR;
                            $messageBody .= "The server attempted to save to the following location:" . $lR;
                            $messageBody .= $e["details"] . $lR;
                            $messageBody .= "Please contact support and supply this information." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 11:
                            $messageBody .= "There was an insufficient number of columns in the spreadsheet." . $lR;
                            $messageBody .= "The spreadsheet requires a total of 15 columns." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 12:
                            $messageBody .= "The spreadsheet was missing the required header. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 13:
                            $messageBody .= "A header in the spreadsheet used an invalid name. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 14:
                            $messageBody .= "A type in the spreadsheet, row #(" . $e["row"] . ") used an invalid name. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 15:
                            $messageBody .= "A category in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 16:
                            $messageBody .= "A title in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 17:
                            $messageBody .= "A sub title in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 18:
                            $messageBody .= "A start date in the spreadsheet, row #(" . $e["row"] . ") is in an incorrect format. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 19:
                            $messageBody .= "A start time in the spreadsheet, row #(" . $e["row"] . ") is in an incorrect format. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 20:
                            $messageBody .= "An end date in the spreadsheet, row #(" . $e["row"] . ") is in an incorrect format. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 21:
                            $messageBody .= "An end time in the spreadsheet, row #(" . $e["row"] . ") is in an incorrect format. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 22:
                            $messageBody .= "The location in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 23:
                            $messageBody .= "A priority in the spreadsheet, row #(" . $e["row"] . ") is in an incorrect format. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 25:
                            $messageBody .= "The chairs in the spreadsheet, row #(" . $e["row"] . ") are too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 27:
                            $messageBody .= "The presenters in the spreadsheet, row #(" . $e["row"] . ") are too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 28:
                            $messageBody .= "An assignment in the spreadsheet, row #(" . $e["row"] . ") is invalid. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was partially uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 29:
                            $messageBody .= "An assignment in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 31:
                            $messageBody .= "The session name in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 32:
                            $messageBody .= "The footnote in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 45:
                            $messageBody .= "There was an error trying to fetch the agenda." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 50:
                            $messageBody .= "The agenda is not an array or object." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 51:
                            $messageBody .= "The data was not successfully added to the database." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 60:
                            $messageBody .= "There was an error adding the record to the user." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error uploading an agenda file." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                // More than one fatal error
                if ($fatalFlag == 1 || $return['record'] == 0)
		{
                    if (count($return["errors"]) > 1)
                    {
                        $messageHeader .= "These errors prevented the agenda file from being added." . $lR;
                    }
                    else
                    {
                        $messageHeader .= "This error prevented the agenda file from being added." . $lR;
                    }
		}
                // There were errors, but none of them were fatal.
		else if ($fatalFlag == 0)
		{
                    if ($return["errors"] > 1)
                    {
                        $messageHeader .= "The agenda file was able to be uploaded, but there were errors that prevented data from being added." . $lR;
                    }
                    else
                    {
                        $messageHeader .= "The agenda file was able to be uploaded, but an error prevented data from being added." . $lR;
                    }
		}
                // No fatal errors, but the info submitted has been altered.
		else if ($fatalFlag == -1)
		{
			$messageHeader .= "The agenda was able to be fully added. However, some alteration has occured." . $lR;
		}
                
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_ADD_CONGRESS:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "The congress URL entered is over the character limit." . $lR;
                            $messageBody .= "Please enter in a shorter length URL." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "The registration URL entered is over the character limit." . $lR;
                            $messageBody .= "Please enter in a shorter length URL." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to retrieve the congress by name." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to generate the congress agenda table." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to generate the congress hospitality rooms table." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 7:
                            $messageBody .= "There was an error trying to generate the congress hospitality schedule table." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 8:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 9:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 20:
                            $messageBody .= "There is a congress in the database with that name already." . $lR;
                            $messageBody .= "Please enter in a different congress name." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 21:
                            $messageBody .= "There is a congress short name in the database with that name already." . $lR;
                            $messageBody .= "Please enter in a different congress short name." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the congress to the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 10:
                            $messageBody .= "Invalid congress date range values entered." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 11:
                            $messageBody .= "Invalid hotel date range values entered." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the congress to the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_ADD_GENERAL_INSIGHT:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the general insight." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the general insight." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "The congress specified was unable to be found." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "The agenda item could not be found." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There are no insights." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the general insight." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the general insight." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the general insight." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 300:
                            $messageBody .= "Insight method not defined" . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the general insight." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_ADD_HOSP_ROOM_TO_CONGRESS:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the hospitality room to the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the hospitality room to the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "There was an error trying to add the hospitality room to the table." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the hospitality room to the congress." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the hospitality room to the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the hospitality room to the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error adding the hospitality room to the congress." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_ADD_HOTEL:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the hotel." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the hotel." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "The URL entered is over the character limit." . $lR;
                            $messageBody .= "Please enter in a shorter length URL." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 20:
                            $messageBody .= "There is a hotel in the database with that name, address, and zip already." . $lR;
                            $messageBody .= "Please enter in a different hotel." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the hotel to the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the hotel." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the hotel." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error adding the hotel to the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_ADD_HOTEL_TO_CONGRESS:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the hotel to the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the hotel to the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 3:
                            $messageBody .= "No congress could be found." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "The URL entered is over the character limit." . $lR;
                            $messageBody .= "Please enter in a shorter length URL." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "The hotel could not be added to the congress table." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 7:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 20:
                            $messageBody .= "There is a hotel in the database with that name, address, and zip already." . $lR;
                            $messageBody .= "Please enter in a different hotel." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the hotel to the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the hotel to the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the hotel to the congress." . $lR;
                }
                switch ($code)
                {
                    case 2:
                        $messageBody .= "The specified hotel was unable to be found." . $lR;
                        $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                        $messageBody .= "Error Code : " . $error . $lR . $lR;
                    break;
                    default:
                        $messageBody .= "There was an unknown error adding the hotel to the congress." . $lR;
                        $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                        $messageBody .= "Error Code : 0" . $lR . $lR;
                    break;
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_ADD_INSIGHT:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the insight post." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the insight post." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "The congress specified was unable to be found." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "The agenda item could not be found." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There are no insights." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;

                        break;
                        case 5:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the insight post." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the insight post." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the insight post." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 300:
                            $messageBody .= "Insight method not defined" . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the insight post." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_ADD_INSIGHTS:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the insights directory." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the insights directory." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "The congress specified was unable to be found." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "The agenda item could not be found." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There are no insights." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There is no insight directory path." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 7:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the insights directory." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the insights directory." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the insights directory." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 300:
                            $messageBody .= "Insight method not defined" . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the insights directory." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_ADD_MESSAGE_GROUP:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the message group." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the message group." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 6:
                            $messageBody .= "There was an error making the query." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 7:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 8:
                            $messageBody .= "There was an error trying to get the message group by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 9:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 20:
                            $messageBody .= "There is a reserved message group with that title already." . $lR;
                            $messageBody .= "Please enter in a different message group name." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 21:
                            $messageBody .= "There is a message group with that title already." . $lR;
                            $messageBody .= "Please enter in a different message group name." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the message group." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the message group." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the message group." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "Please enter a group title." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "Please select users to be part of the group." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "The data supplied for group users is invalid." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the message group." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_ADD_SPEAKER_BIO:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the speaker bio." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the speaker bio." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 3:
                            $messageBody .= "The file size was too large." . $lR;
                            $messageBody .= "Please select a file of a smaller size to upload." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "The congress specified was unable to be found." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "The uploaded file was unable to be saved to the temp location." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error making the query." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 7:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 8:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the speaker bio to the congress." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the speaker bio." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the speaker bio." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "Please select a PDF to upload." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        default:
                            $messageBody .= "There was an unknown error adding the speaker bio to the congress." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_ADD_USER:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the user to the database." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the user to the database." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "There is already a user in the database with that email." . $lR;
                            $messageBody .= "Please enter in a different email address." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "There was an error trying to generate the user table." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to add the user to the database." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to add the user to the database." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error adding the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_BOOK_HOSP_ROOM:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to book the hospitality room." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to book the hospitality room." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 3:
                            $messageBody .= "There was an error trying to retrieve the hospitality room." . $lR;
                            $messageBody .= "Please make sure all specified fields are entered." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to add the booking to the hospitality room." . $lR;
                            $messageBody .= "Please make sure all specified fields are entered." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 7:
                            $messageBody .= "There was an error trying to add the record to the pending requests." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error trying to book the hospitality room." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to book the hospitality room." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to book the hospitality room." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "Invalid hospitality room time values entered." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error trying to book the hospitality room." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_CONFIRM_HOSP_REQUEST:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to confirm the hospitality room booking." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to confirm the hospitality room booking." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to remove the record from the pending requests." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error trying to confirm the hospitality room booking." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to confirm the hospitality room booking." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to confirm the hospitality room booking." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error trying to confirm the hospitality room booking." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_CONFIRM_HOTEL:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to confirm the hotel request." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to confirm the hotel request." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 3:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to remove the record from the pending requests." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 7:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error trying to confirm the hotel request." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to confirm the hotel request." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to confirm the hotel request." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "Invalid hotel date range values entered." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error trying to confirm the hotel request." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_DELETE_CONGRESS:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to delete the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to delete the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "There was an error deleting the congress agenda table." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "There was an error deleting the congress hospitality rooms table." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error deleting the congress hospitality schedule table." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error removing the congress from the congresses." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 7:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error removing the congress from the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to delete the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to delete the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error deleting the congress from the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_DOWNLOAD_AGENDA:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to download the agenda for the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to download the agenda for the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "Please specify a congress." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "The congress specified was unable to be found." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There is currently no agenda with the selected congress." . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error trying to download the agenda for the congress." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to download the agenda for the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to download the agenda for the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error trying to download the agenda for the congress." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_MODIFY_ACCOUNT:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the account." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the account." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 3:
                            $messageBody .= "There was an error trying to update the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the account." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the account." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the account." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "Please enter in a valid phone number." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the account." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_MODIFY_AGENDA_ITEM:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the agenda item." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the agenda item." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 3:
                            $messageBody .= "The assignments entered are over the character limit." . $lR;
                            $messageBody .= "Please enter in less people." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "The chairs entered are over the character limit." . $lR;
                            $messageBody .= "Please enter in less people." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "The presenters entered are over the character limit." . $lR;
                            $messageBody .= "Please enter in less people." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error making the query." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 7:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 8:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the agenda item." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the agenda item." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the agenda item." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "Invalid agenda date range values entered." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the agenda item." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_MODIFY_BIO:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the speaker bio." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the speaker bio." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "The file size was too large." . $lR;
                            $messageBody .= "Please select a file of a smaller size to upload." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "The congress specified was unable to be found." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "The uploaded file was unable to be saved to the temp location." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error making the query." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 7:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the speaker bio to the congress." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the speaker bio." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the speaker bio." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error modifying the speaker bio to the congress." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_MODIFY_CONGRESS:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 4:
                            $messageBody .= "The congress URL entered is over the character limit." . $lR;
                            $messageBody .= "Please enter in a shorter length congress URL." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "The registration URL entered is over the character limit." . $lR;
                            $messageBody .= "Please enter in a shorter length registration URL." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to retrieve the congress by ID." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 7:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 8:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 20:
                            $messageBody .= "There is a congress in the database with that name already." . $lR;
                            $messageBody .= "Please enter in a different congress name." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 21:
                            $messageBody .= "There is a congress in the database with that short name already." . $lR;
                            $messageBody .= "Please enter in a different congress short name." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the congress to the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "Invalid congress date range values entered." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "Invalid hotel date range values entered." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the congress in the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_MODIFY_CONGRESS_DETAIL:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 4:
                            $messageBody .= "The congress URL entered is over the character limit." . $lR;
                            $messageBody .= "Please enter in a shorter length congress URL." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "The registration URL entered is over the character limit." . $lR;
                            $messageBody .= "Please enter in a shorter length registration URL." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to retrieve the congress by ID." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 7:
                            $messageBody .= "There was an error trying to make the query." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 8:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 9:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 20:
                            $messageBody .= "There is a congress in the database with that name already." . $lR;
                            $messageBody .= "Please enter in a different congress name." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 21:
                            $messageBody .= "There is a congress in the database with that short name already." . $lR;
                            $messageBody .= "Please enter in a different congress short name." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the congress to the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "Invalid congress date range values entered." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "Invalid hotel date range values entered." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the congress in the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_MODIFY_HOSP_BOOKING:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the hospitality room booking." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the hospitality room booking." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 4:
                            $messageBody .= "There was an error modifying the hospitality room booking." . $lR;
                            $messageBody .= "The update did not successfully complete." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to retrieve the hospitality room booking." . $lR;
                            $messageBody .= "Please make sure all specified fields are entered." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 7:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 8:
                            $messageBody .= "There was an error trying to add the record to the pending requests." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the hospitality room." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the hospitality room booking." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the hospitality room booking." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "A bookingID was not specified." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "Invalid hospitality room time values entered." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the hospitality room booking." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_MODIFY_HOSP_ROOM:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the hospitality room." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the hospitality room." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "There was an error trying to retrieve the hospitality room." . $lR;
                            $messageBody .= "Please make sure all specified fields are entered." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "There was an error modifying the hospitality room." . $lR;
                            $messageBody .= "The update did not successfully complete." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the hospitality room." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the hospitality room." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the hospitality room." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error adding the hotel to the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_MODIFY_HOTEL:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the hotel." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the hotel." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "The hotelID was not specified." . $lR;
                            $messageBody .= "Please select a hotel." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "The URL entered is over the character limit." . $lR;
                            $messageBody .= "Please enter in a shorter length URL." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 20:
                            $messageBody .= "There is a hotel in the database with that name, address, and zip already." . $lR;
                            $messageBody .= "Please enter in a different hotel." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the hotel to the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the hotel." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the hotel." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error adding the hotel to the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_MODIFY_HOTEL_RESERVATION:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the hotel reservation." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the hotel reservation." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to add to pending requests." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the hotel reservation." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the hotel reservation." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the hotel reservation." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error modifying the hotel reservation." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_MODIFY_INSIGHT:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the insight post." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the insight post." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "The congress specified was unable to be found." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "The agenda item could not be found." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There are no insights." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the insight post." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the insight post." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the insight post." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error modifying the insight post." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_MODIFY_MESSAGE_GROUP:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the message group." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the message group." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 7:
                            $messageBody .= "Unauthorized access." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 8:
                            $messageBody .= "There is an error making the query." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 9:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 10:
                            $messageBody .= "There was an error trying to get the message group by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 11:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 20:
                            $messageBody .= "There is a reserved message group with that title already." . $lR;
                            $messageBody .= "Please enter in a different message group name." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 21:
                            $messageBody .= "There is a message group with that title already." . $lR;
                            $messageBody .= "Please enter in a different message group name." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the message group." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the message group." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the message group." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error modifying the message group." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_MODIFY_USER:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the user." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the user." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "There was an error trying to update the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to modify the user." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to modify the user." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error modifying the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_REGISTER_USER:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to register the user." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to register the user." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 6:
                            $messageBody .= "There was an error trying to update the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 7:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 8:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error registering the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to register the user." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to register the user." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "Please enter an email address." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "There was an error trying to retrieve the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "Unauthorized access." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "The email address is already associated with a registered user." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error registering the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_REMOVE_AGENDA_ITEM_FROM_CONGRESS:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the agenda item from the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the agenda item from the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "There was an error trying make the query." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error removing the agenda item from the congress." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the agenda item from the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the agenda item from the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error removing the agenda item from the congress." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_REMOVE_BIO:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the speaker bio from the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the speaker bio from the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 3:
                            $messageBody .= "There was an error trying to send the record to the trash." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error removing the speaker bio from the congress." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the speaker bio from the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the speaker bio from the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "Invalid speaker bio name error." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error removing the speaker bio from the congress." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_REMOVE_CONGRESS:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "There was an error trying to send the record to the trash." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error removing the congress from the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error removing the congress from the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_REMOVE_HOSP_BOOKING:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the hospitality room booking." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the hospitality room booking." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 3:
                            $messageBody .= "There was an error trying to send the record to the trash." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error removing the hospitality room booking." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the hospitality room booking." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the hospitality room booking." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "A booking ID was not specified." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the hotel to the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_REMOVE_HOSP_ROOM_FROM_CONGRESS:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the hospitality room from the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the hospitality room from the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 3:
                            $messageBody .= "There was an error trying to send the record to the trash." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error modifying the hospitality room." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the hospitality room from the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the hospitality room from the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "Please specify a hospitality room." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error adding the hotel to the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_REMOVE_HOTEL_FROM_CONGRESS:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the hotel from the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the hotel from the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 3:
                            $messageBody .= "The hotel could not be removed from the congress table." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error removing the hotel from the congress." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the hotel from the congress." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the hotel from the congress." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "The hotelID was not specified." . $lR;
                            $messageBody .= "Please select a hotel." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error removing the hotel from the congress." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_REMOVE_HOTEL_RESERVATION:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the hotel request." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the hotel request." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 4:
                            $messageBody .= "There was an error trying to retrieve the most recent hotel requests for the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to send the record to the trash." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 7:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 8:
                            $messageBody .= "There was an error trying to retrieve the pending requests by record." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 9:
                            $messageBody .= "There was an error trying to remove from pending requests." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error removing the hotel request." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the hotel request." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the hotel request." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "There was no author ID specified." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "Unauthorized Access." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error removing the hotel request." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_REMOVE_INSIGHT:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the insight post." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the insight post." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "The congress specified was unable to be found." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "The agenda item could not be found." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "The insight path could not be found." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error removing the insight post." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the insight post." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the insight post." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error removing the insight post." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_REMOVE_USER:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the user." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the user." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "There was an error trying to send the record to the trash." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error removing the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the user." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the user." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error removing the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_REMOVE_USERS:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the users." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the users." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 5:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error removing the users." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to remove the users." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to remove the users." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error removing the users." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_REPLACE_AGENDA:
            if ($return['errors'] >= 0)
            {
                $fatalFlag = -1;
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were numerous errors trying to replace the agenda." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to replace the agenda." . $lR;
                }
                $error = $return["errors"];
                foreach ($error as $e)
                {
                    if ($e['failureType'] == 1)
                    {
                        $fatalFlag = 1;
                    }
                    else if ($fatalFlag != 1 && $e['failureType'] == 0)
                    {
                        $fatalFlag = 0;
                    }
                    switch ($e['code'])
                    {
                        case 1:
                            $messageBody .= "The file size was too large." . $lR;
                            $messageBody .= "Please select a file of a smaller size to upload." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 2:
                            $messageBody .= "The file is an improper format." . $lR;
                            $messageBody .= "Please select either an '.xls' or '.xlsx'." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "Something unexpected prohibited the file from being saved to the server." . $lR;
                            $messageBody .= "The server attempted to save to the following location:" . $lR;
                            $messageBody .= $e["details"] . $lR;
                            $messageBody .= "Please contact support and supply this information." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 11:
                            $messageBody .= "There was an insufficient number of columns in the spreadsheet." . $lR;
                            $messageBody .= "The spreadsheet requires a total of 15 columns." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 12:
                            $messageBody .= "The spreadsheet was missing the required header. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 13:
                            $messageBody .= "A header in the spreadsheet used an invalid name. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 14:
                            $messageBody .= "A type in the spreadsheet, row #(" . $e["row"] . ") used an invalid name. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 15:
                            $messageBody .= "A category in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 16:
                            $messageBody .= "A title in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 17:
                            $messageBody .= "A sub title in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 18:
                            $messageBody .= "A start date in the spreadsheet, row #(" . $e["row"] . ") is in an incorrect format. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 19:
                            $messageBody .= "A start time in the spreadsheet, row #(" . $e["row"] . ") is in an incorrect format. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 20:
                            $messageBody .= "An end date in the spreadsheet, row #(" . $e["row"] . ") is in an incorrect format. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 21:
                            $messageBody .= "An end time in the spreadsheet, row #(" . $e["row"] . ") is in an incorrect format. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 22:
                            $messageBody .= "The location in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 23:
                            $messageBody .= "A priority in the spreadsheet, row #(" . $e["row"] . ") is in an incorrect format. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 25:
                            $messageBody .= "The chairs in the spreadsheet, row #(" . $e["row"] . ") are too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 27:
                            $messageBody .= "The presenters in the spreadsheet, row #(" . $e["row"] . ") are too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 28:
                            $messageBody .= "An assignment in the spreadsheet, row #(" . $e["row"] . ") is invalid. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was partially uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 29:
                            $messageBody .= "An assignment in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 31:
                            $messageBody .= "The session name in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 32:
                            $messageBody .= "The footnote in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 45:
                            $messageBody .= "There was an error trying to fetch the agenda." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 50:
                            $messageBody .= "The agenda is not an array or object." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 51:
                            $messageBody .= "The data was not successfully added to the database." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 60:
                            $messageBody .= "There was an error adding the record to the user." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error uploading an agenda file." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                // More than one fatal error
                if ($fatalFlag == 1 || $return['record'] == 0)
		{
                    if (count($return["errors"]) > 1)
                    {
                        $messageHeader .= "These errors prevented the agenda file from uploading." . $lR;
                    }
                    else
                    {
                        $messageHeader .= "This error prevented the agenda file from uploading." . $lR;
                    }
		}
                // There were errors, but none of them were fatal.
		else if ($fatalFlag == 0)
		{
                    if ($return["errors"] > 1)
                    {
                        $messageHeader .= "The agenda file was able to be uploaded, but there were errors that prevented data from being added." . $lR;
                    }
                    else
                    {
                        $messageHeader .= "The agenda file was able to be uploaded, but an error prevented data from being added." . $lR;
                    }
		}
                // No fatal errors, but the info submitted has been altered.
		else if ($fatalFlag == -1)
		{
			$messageHeader .= "The agenda was able to be fully uploaded. However, some alteration has occured." . $lR;
		}
                
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_REQUEST_HOTEL:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to submit the hotel request." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to submit the hotel request." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to add to pending requests." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to remove the record from the trash." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error submitting the hotel request." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to submit the hotel request." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to submit the hotel request." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error submitting the hotel request." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_RESET_FORGOTTEN_PASSWORD:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to reset the user's password." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to reset the user's password." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 3:
                            $messageBody .= "There was an error trying make the query." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error resetting the user's password." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to reset the user's password." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to reset the user's password." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "There was an error trying to retrieve the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error resetting the user's password." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_SEND_MESSAGE:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to send the message." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to send the message." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "Unauthorized access." . $lR;
                        break;
                        case 3:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error trying to send a message." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to send the message." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to send the message." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error trying to send a message." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_UPLOAD_AGENDA:
            if ($return['errors'] >= 0)
            {
                $fatalFlag = -1;
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were numerous errors in the upload process." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error in the upload process." . $lR;
                }
                $error = $return["errors"];
                foreach ($error as $e)
                {
                    if ($e['failureType'] == 1)
                    {
                        $fatalFlag = 1;
                    }
                    else if ($fatalFlag != 1 && $e['failureType'] == 0)
                    {
                        $fatalFlag = 0;
                    }
                    switch ($e['code'])
                    {
                        case 1:
                            $messageBody .= "The file size was too large." . $lR;
                            $messageBody .= "Please select a file of a smaller size to upload." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 2:
                            $messageBody .= "The file is an improper format." . $lR;
                            $messageBody .= "Please select either an '.xls' or '.xlsx'." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "Something unexpected prohibited the file from being saved to the server." . $lR;
                            $messageBody .= "The server attempted to save to the following location:" . $lR;
                            $messageBody .= $e["details"] . $lR;
                            $messageBody .= "Please contact support and supply this information." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 11:
                            $messageBody .= "There was an insufficient number of columns in the spreadsheet." . $lR;
                            $messageBody .= "The spreadsheet requires a total of 15 columns." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 12:
                            $messageBody .= "The spreadsheet was missing the required header. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 13:
                            $messageBody .= "A header in the spreadsheet used an invalid name. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 14:
                            $messageBody .= "A type in the spreadsheet, row #(" . $e["row"] . ") used an invalid name. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 15:
                            $messageBody .= "A category in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 16:
                            $messageBody .= "A title in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 17:
                            $messageBody .= "A sub title in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 18:
                            $messageBody .= "A start date in the spreadsheet, row #(" . $e["row"] . ") is in an incorrect format. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 19:
                            $messageBody .= "A start time in the spreadsheet, row #(" . $e["row"] . ") is in an incorrect format. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 20:
                            $messageBody .= "An end date in the spreadsheet, row #(" . $e["row"] . ") is in an incorrect format. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 21:
                            $messageBody .= "An end time in the spreadsheet, row #(" . $e["row"] . ") is in an incorrect format. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was unable to be uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 22:
                            $messageBody .= "The location in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 23:
                            $messageBody .= "A priority in the spreadsheet, row #(" . $e["row"] . ") is in an incorrect format. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 25:
                            $messageBody .= "The chairs in the spreadsheet, row #(" . $e["row"] . ") are too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 27:
                            $messageBody .= "The presenters in the spreadsheet, row #(" . $e["row"] . ") are too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 28:
                            $messageBody .= "An assignment in the spreadsheet, row #(" . $e["row"] . ") is invalid. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Due to this, this row was partially uploaded." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 29:
                            $messageBody .= "An assignment in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 31:
                            $messageBody .= "The session name in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 32:
                            $messageBody .= "The footnote in the spreadsheet, row #(" . $e["row"] . ") is too many characters. (" . $e["details"] . ")" . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 45:
                            $messageBody .= "There was an error trying to fetch the agenda." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 50:
                            $messageBody .= "The agenda is not an array or object." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 51:
                            $messageBody .= "The data was not successfully added to the database." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        case 60:
                            $messageBody .= "There was an error adding the record to the user." . $lR;
                            $messageBody .= "Error Code : " . $e['code'] . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error uploading an agenda file." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                // More than one fatal error
                if ($fatalFlag == 1 || $return['record'] == 0)
		{
                    if (count($return["errors"]) > 1)
                    {
                        $messageHeader .= "These errors prevented the agenda file from uploading." . $lR;
                    }
                    else
                    {
                        $messageHeader .= "This error prevented the agenda file from uploading." . $lR;
                    }
		}
                // There were errors, but none of them were fatal.
		else if ($fatalFlag == 0)
		{
                    if ($return["errors"] > 1)
                    {
                        $messageHeader .= "The agenda file was able to be uploaded, but there were errors that prevented data from being added." . $lR;
                    }
                    else
                    {
                        $messageHeader .= "The agenda file was able to be uploaded, but an error prevented data from being added." . $lR;
                    }
		}
                // No fatal errors, but the info submitted has been altered.
		else if ($fatalFlag == -1)
		{
			$messageHeader .= "The agenda was able to be fully uploaded. However, some alteration has occured." . $lR;
		}
                
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_VALIDATE_PASSWORD:
            if ($code == 0)
            {
                $message = "The password you tried failed";
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_VIEW_CONGRESS:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to set the attendance." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to set the attendance." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "There was an error trying to add the record to the user." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "There was an error trying to get the user record by footprint." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error trying to set the attendance." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to set the attendance." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to set the attendance." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error trying to set the attendance." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        case POST_VIEW_INSIGHTS:
            if ($code == 1)
            {
                $messageHeader = "";
                $messageBody = "";
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to view the insights." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to view the insights." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        case 2:
                            $messageBody .= "Please specify a congress." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 3:
                            $messageBody .= "The congress specified was unable to be found." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 4:
                            $messageBody .= "There was an error getting the agenda from the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 5:
                            $messageBody .= "There was an error getting the users from the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        case 6:
                            $messageBody .= "There was an error getting the agenda and the users from the database." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : " . $error . $lR . $lR;
                        break;
                        default:
                            $messageBody .= "There was an unknown error viewing the insights." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            else if ($code == 0 || $code > 1)
            {
                $messageHeader = "";
                $messageBody = "";
                
                if (count($return["errors"]) > 1)
                {
                    $messageHeader = "There were multiple errors trying to view the insights." . $lR;
                }
                else
                {
                    $messageHeader = "There was an error trying to view the insights." . $lR;
                }
                foreach ($return['errors'] as $error)
                {
                    switch ($error)
                    {
                        default:
                            $messageBody .= "There was an unknown error viewing the insights." . $lR;
                            $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                            $messageBody .= "Error Code : 0" . $lR . $lR;
                        break;
                    }
                }
                $message = $messageHeader . $lR . $messageBody;
                $html = getGeneralErrorCode($message);
            }
            break;
        default:
            if ($code >= 0)
            {
                $messageBody .= "There was an error completing request." . $lR;
                $messageBody .= "Please contact tech support to resolve this issue." . $lR;
                $messageBody .= "Error Code : 0" . $lR . $lR;
                $html = getGeneralErrorCode($message);
            }
            break;

    }
    return array("code" => $code, "html" => $html, "message" => $message);
}

function getGeneralErrorCode($message)
{
    $return = '<div id="errorDIV">';
    $return .= getBGDiv();
    $return .= '<div id="errorDIV2">';
    $return .= getGenericHeaderDIV();
    $return .= '<div id="errorDIV3">' . $message . '</div>';
    $return .= '</div>';
    $return .= '</div>';
    return $return;
}

function getNoResultsCode()
{
    $return = '<div id="errorDIV">';
    $return .= getBGDiv();
    $return .= '<div id="errorDIV2">';
    $return .= getGenericHeaderDIV();
    $return .= '<div id="errorDIV3">';
    $return .= 'No Results.<br/>';
    $return .= '</div>';
    $return .= '</div>';
    $return .= '</div>';
    return $return;
}

function getUnathorizedCode()
{
    $return = '<div id="successDIV">';
    $return .= getBGDiv();
    $return .= '<div id="successDIV2">';
    $return .= '<div id="successDIV3" class="contentDIV">';
    $return .= '<p>Unathorized Access!</p>';
    $return .= '<div class="button"><a href="' . HOME . '">OK</a></div>';
    $return .= '</div>';
    $return .= '</div>';
    $return .= '</div>';
    return $return;
}

function getSuccessCode()
{
    $return = '<div id="successDIV">';
    $return .= getBGDiv();
    $return .= '<div id="successDIV2">';
    $return .= '<div id="successDIV3" class="contentDIV">';
    $return .= '<p>Success!</p>';
    $return .= '<a href="' . HOME . '"><div class="button">OK</div></a>';
    $return .= '</div>';
    $return .= '</div>';
    $return .= '</div>';
    return $return;
}

function getSuccessCodeWithRedirect($action)
{
    $return = '<div id="successDIV">';
    $return .= getBGDiv();
    $return .= '<div id="successDIV2">';
    $return .= getGenericHeaderDIV();
    $return .= '<div id="successDIV3" class="contentDIV">';
    $return .= '<p>Success!</p>';
    $return .= '<a href="' . HOME . '?' . $action . '"><div class="button">OK</div></a>';
    $return .= '</div>';
    $return .= '</div>';
    $return .= '</div>';
    return $return;
}

function packageGeneralError($errors, $errorCode)
{
    if (!is_array($errors))
    {
        $errors = array();
    }
    array_push($errors, $errorCode);
    return $errors;
}