<?php

    define('TIMEZONE', 'America/New_York');
    date_default_timezone_set(TIMEZONE);

    // class types for user entries/records
    define('ACK_CONGRESS_ATTENDANCE', "acknowledged congress attendance");
    define('ADDED_AGENDA_ITEMS', "added agenda items");
    define('ADDED_CONGRESS', "added congress");
    define('ADDED_GENERAL_INSIGHT', "added general insight");
    define('ADDED_INSIGHT', "added insight");
    define('ADDED_INSIGHT_POST', "added insight post");
    define('ADDED_MESSAGE_GROUP', "added message group");
    define('ADDED_USER', "added user");
    define('AGENDA_UPLOADED_TO_CONGRESS', 'uploaded agenda to congress');
    define('BIO_ADDED_TO_CONGRESS', "added bio to congress");
    define('CANCELLED_HOSP_BOOKING', "cancelled hospitality booking");
    define('CANCELLED_HOTEL_RESERVATION', "cancelled hotel reservation");
    define('CONFIRMATION_VIEWED', "confirmation viewed");
    define('DELETE_CONGRESS', "delete congress");
    define('DOWNLOADED_INSIGHTS', 'downloaded insights');
    define('HOSP_ROOM_ADDED_TO_CONGRESS', "added hosp room to congress");
    define('HOSP_ROOM_BOOKED', "added booking to hosp room");
    define('HOSP_REQUEST_COMPLETION', "hosp request completion");
    define('HOSP_REQUEST_CONFIRMATION', "hosp request confirmation");
    define('HOTEL_ADDED', "added hotel");
    define('HOTEL_ADDED_TO_CONGRESS', "added hotel to congress");
    define('HOTEL_CONFIRMATION', "hotel confirmation");
    define('HOTEL_REMOVAL_FROM_CONGRESS', "removed hotel from congress");
    define('HOTEL_REQUEST', "hotel request");
    define('HOTEL_REQUEST_COMPLETION', "hotel request completion");
    define('MODIFIED_AGENDA_ITEM', "modified agenda item");
    define('MODIFIED_BIO', "modified bio");
    define('MODIFIED_CONGRESS_RECORD', "modified congress record");
    define('MODIFIED_GENERAL_INSIGHT', "modified general insight");
    define('MODIFIED_HOSP_ROOM', "modified hosp room");
    define('MODIFIED_HOSP_ROOM_BOOKING', "modified hosp room booking");
    define('MODIFIED_HOTEL_DETAIL', "modified hotel detail");
    define('MODIFIED_HOTEL_REQUEST', "modified hotel request");
    define('MODIFIED_INSIGHT_POST', "modified insight post");
    define('MODIFIED_MESSAGE_GROUP', "modified message group");
    define('MODIFIED_USER', "modified user");
    define('MODIFIED_USER_ACCOUNT', "modified user account");
    define('PACKAGED_INSIGHTS', "packaged insights");
    define('READ_CONVERSATION', "read conversation");
    define('RECEIVED_MESSAGE', "received message");
    define('REMOVED_AGENDA_ITEM', "removed agenda item");
    define('REMOVED_BIO', "removed bio");
    define('REMOVED_CONGRESS', "removed congress");
    define('REMOVED_HOSP_ROOM', "removed hospitality room");
    define('REMOVED_INSIGHT_POST', "removed insight post");
    define('REMOVED_MESSAGE', "removed message");
    define('REMOVED_USER', "removed user");
    define('REMOVED_USERS', "removed users");
    define('REPLACED_AGENDA', "replaced agenda");
    define('RESET_PASSWORD', "reset password");
    define('SENT_MESSAGE', "sent message");
    define('SITE_REGISTRATION', "site registration");
    
    // class types for form and primary action GET and POST objects
    define('ENGAGE_CONVERSATION' , "engageConversation");
    define('POST_ADD_AGENDA', "addAgenda");
    define('POST_ADD_CONGRESS', "addCongress");
    define('POST_ADD_GENERAL_INSIGHT', "addGeneralInsight");
    define('POST_ADD_HOSP_ROOM_TO_CONGRESS', "addHospRoomToCongress");
    define('POST_ADD_HOTEL', "addHotel");
    define('POST_ADD_HOTEL_TO_CONGRESS', "addHotelToCongress");
    define('POST_ADD_INSIGHT', "addInsight");
    define('POST_ADD_INSIGHTS', "addInsights");
    define('POST_ADD_MESSAGE_GROUP', "addMessageGroup");
    define('POST_ADD_SPEAKER_BIO', "addSpeakerBio");
    define('POST_ADD_USER', "addUser");
    define('POST_BOOK_HOSP_ROOM', "bookHospRoom");
    define('POST_CONFIRM_HOSP_REQUEST', "confirmHospitalityRequest");
    define('POST_CONFIRM_HOTEL', "confirmHotel");
    define('POST_DELETE_CONGRESS', "deleteCongress");
    define('POST_DOWNLOAD_AGENDA', "downloadAgenda");
    define('POST_DOWNLOAD_INSIGHTS', "downloadInsights");
    define('POST_DOWNLOAD_MANUAL', "downloadManual");
    define('POST_MODIFY_ACCOUNT', "modifyAccount");
    define('POST_MODIFY_AGENDA_ITEM', "modifyAgendaItem");
    define('POST_MODIFY_BIO', "modifyBio");
    define('POST_MODIFY_CONGRESS', "modifyCongress");
    define('POST_MODIFY_CONGRESS_DETAIL', "modifyCongressDetail");
    define('POST_MODIFY_CONGRESSES', "modifyCongresses");
    define('POST_MODIFY_HOSP_BOOKING', "modifyHospitalityBooking");
    define('POST_MODIFY_HOSP_ROOM', "modifyHospitalityRoom");
    define('POST_MODIFY_HOTEL', "modifyHotel");
    define('POST_MODIFY_HOTELS', "modifyHotels");
    define('POST_MODIFY_HOTEL_RESERVATION', "modifyHotelReservation");
    define('POST_MODIFY_INSIGHT', "modifyInsight");
    define('POST_MODIFY_MESSAGE_GROUP', "modifyMessageGroup");
    define('POST_MODIFY_PASSWORD', "modifyPassword");
    define('POST_MODIFY_USER', "modifyUser");
    define('POST_MODIFY_USERS', "modifyUsers");
    define('POST_REGISTER_USER', "registerUser");
    define('POST_REMOVE_AGENDA_ITEM_FROM_CONGRESS', "removeAgendaItemFromCongress");
    define('POST_REMOVE_BIO', "removeBio");
    define('POST_REMOVE_CONGRESS', "removeCongress");
    define('POST_REMOVE_HOSP_BOOKING', "removeHospitalityBooking");
    define('POST_REMOVE_HOSP_ROOM_FROM_CONGRESS', "removeHospRoomFromCongress");
    define('POST_REMOVE_HOTEL_FROM_CONGRESS', "removeHotelRoomFromCongress");
    define('POST_REMOVE_HOTEL_RESERVATION', "removeHotelReservation");
    define('POST_REMOVE_INSIGHT', "removeInsight");
    define('POST_REMOVE_USER', "removeUser");
    define('POST_REMOVE_USERS', "removeUsers");
    define('POST_REPLACE_AGENDA', "replaceAgenda");
    define('POST_RESET_FORGOTTEN_PASSWORD', "resetForgottenPassword");
    define('POST_REQUEST_HOTEL', "requestHotel");
    define('POST_SEND_MESSAGE', "sendMessage");
    define('POST_SIGN_OUT', "signOut");
    define('POST_UPLOAD_AGENDA', "uploadAgenda");
    define('POST_UPLOAD_BIO', "uploadBio");
    define('POST_VALIDATE_EMAIL', "validateEmail");
    define('POST_VALIDATE_PASSWORD', "validatePW");
    define('POST_VIEW_CONGRESS', "viewCongress");
    define('POST_VIEW_GROUP', "viewGroup");
    define('POST_VIEW_HOSP_BOOKINGS', "hospitalityBooking");
    define('POST_VIEW_INSIGHTS', "viewInsights");
    define('POST_VIEW_REQUEST', "viewRequest");
    define('POST_VIEW_REQUESTS', "viewRequests");
    define('PRE_DOWNLOAD_AGENDA', "preDownloadAgenda");
    
    // class types for trash bins items
    define('BIO_RECORD', "bioRecord");
    define('CONGRESS_RECORD', "congressRecord");
    define('HOSP_BOOKING_RECORD', "hospitalityBookingRecord");
    define('HOSP_ROOM_RECORD', "hospitalityRoomRecord");
    define('HOTEL_PROPERTY_RECORD', "hotelPropertyRecord");
    define('HOTEL_REQUEST_RECORD', "hotelRequestRecord");
    define('USER_RECORD', "userRecord");
    
    // sub-directory paths
    if ($webBuild)
    {
        define ('DOWNLOAD_PATH', "downloads/");
        define ('UPLOAD_PATH', "uploads/");
    }
    else
    {
        define ('DOWNLOAD_PATH', "../downloads/");
        define ('UPLOAD_PATH', "../uploads/");
    }
    
    
    define('RAW_AGENDA_PATH', UPLOAD_PATH . "raw_agendas/");
    define('CONGRESS_IMAGES_PATH', UPLOAD_PATH . "congress_images/");
    define('CONGRESS_IMAGES_THUMBS_PATH', UPLOAD_PATH . "congress_images_thumbs/");
    define('CONGRESS_BIOS_PATH', UPLOAD_PATH . "congress_bios/");
    define('USER_IMAGES_PATH', UPLOAD_PATH . "user_images/");
    define('USER_IMAGES_THUMBS_PATH', UPLOAD_PATH . "user_images_thumbs/");
    define('INSIGHTS_PATH', UPLOAD_PATH . "insights/");
    
    // file related stuff
    define ('FILE_IMPORT_SIZE_LIMIT', 10 * 1024 * 1024); // 10MB
    
    // class types for agenda items
    define('TYPE', "type");
    define('CATEGORY', "category");
    define('TITLE', "title");
    define('SUB_TITLE', "subTitle");
    define('START_DATE', "startDate");
    define('START_TIME', "startTime");
    define('END_DATE', "endDate");
    define('END_TIME', "endTime");
    define('LOCATION', "location");
    define('PRIORITY', "priority");
    define('CHAIR', "chair");
    define('PRESENTERS', "presenters");
    define('ASSIGNMENT', "assignment");
    define('SESSION_NAME', "sessionName");
    define('FOOTNOTES', "footnotes");
    
    // class types for agenda item types
    define('_BREAK', "break");
    define('EXHIBIT', "exhibit");
    define('EXPO_HOURS', "expoHours");
    define('INTERNAL', "internal");
    define('POSTER', "poster");
    define('PRESENTATION', "presentation");
    define('RECEPTION', "reception");
    
    // for schedule related formulas
    define('HOSP_SEGMENT_LENGTH', 15*60);     // eg. 15 minutes
    define('ONE_HOUR', 1000*60*60);
    define('ONE_DAY', ONE_HOUR*24);
    
    // Canyon Creek url
    define('CANYON_CREEK_URL', "https://secure.cctrvl.com/Axoneron/");
    
    // default text objects
    define('DEFAULT_NOTEPAD_TEXT', 'Click anywhere to edit');
    
    // manual name/url
    define('USER_MANUAL_URL', 'Axoneron_Congress_App_User_Guide.pdf');
    
    // messaging
    define('MESSAGE_BIRD_KEY', 'pPtlf0OZRemRYl598nsCjVG5R');
    define('FIREBASE_URL', 'https://fcm.googleapis.com/fcm/send');
    define('FIREBASE_SERVER_KEY', 'AAAA2MnBP2A:APA91bFBORGNT3kAj4eRfIMdB4_uS_ZXr_ZCBvMgqwlxF8VLIbLn0jcJdn3_-0T1g1V0ahCfez4mpOki5skPIfrcVahEbnVbEUv8tf92KUNZUspQzqi25EMUSnUZ27BCIHwbLKTu5EAN');
    define('MESSAGING_ENABLED', 1);
    
    // app download urls
    define('IOS_DOWNLOAD_URL', 'https://apps.apple.com/us/app/my-congress-app/id1538153646');
    define('ANDROID_DOWNLOAD_URL', 'https://play.google.com/store/apps/details?id=com.pixelmosaic.mycongressapp');
    define('OSX_DOWNLOAD_URL', '');
    define('WINDOWS_DOWNLOAD_URL', '');
    

