<?php

function initialize($connection)
{
    $query = "SHOW TABLES";
    $result = $connection->query($query);
    $usersTable = $congressTable = $pendingTable = $hotelsTable = 0;
    $congressAgendaTables = $congressHospRoomTables = $congressHospSchedTables = 0;
    $userTables = 0;
    $trashTable = $messageGroupsTable = 0;
    $congressImageColumn = $userImageColumn = 0;
    $congressBiosColumn = 0;
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $usersTable = $row[0] == "users" ? 1 : $usersTable;
            $congressTable = $row[0] == "congresses" ? 1 : $congressTable;
            $pendingTable = $row[0] == "pending_requests" ? 1 : $pendingTable;
            $hotelsTable = $row[0] == "hotels" ? 1 : $hotelsTable;
            $trashTable = $row[0] == "trashBin" ? 1 : $trashTable;
            $messageGroupsTable = $row[0] == "messageGroups" ? 1 : $messageGroupsTable;
            
            $query2 = "DESCRIBE " . $row[0];
            $result2 = $connection->query($query2);
            
            //var_dump($result2);
            //echo "<br/><br/>";
            
            while ($row2 = mysqli_fetch_array($result2))
            {
                foreach ($row2 as $columnName)
                {
                    if ($congressTable)
                    {
                        $congressImageColumn = $columnName == "imageURL" ? 1 : $congressImageColumn;
                        $congressBiosColumn = $columnName == "bios" ? 1 : $congressBiosColumn;
                    }
                    if ($usersTable)
                    {
                        $userImageColumn = $columnName == "imageURL" ? 1 : $userImageColumn;
                    }
                }
            }
            
            if (strpos(strtolower($row[0]), "congressagenda_") !== false)
            {
                if (!$congressAgendaTables){ $congressAgendaTables = []; }
                array_push($congressAgendaTables, $row[0]);
            }
            if (strpos(strtolower($row[0]), "congresshospitalityrooms_") !== false)
            {
                if (!$congressHospRoomTables){ $congressHospRoomTables = []; }
                array_push($congressHospRoomTables, $row[0]);
            }
            if (strpos(strtolower($row[0]), "congresshospitalityschedule_") !== false)
            {
                if (!$congressHospSchedTables){ $congressHospSchedTables = []; }
                array_push($congressHospSchedTables, $row[0]);
            }
            if (strpos(strtolower($row[0]), "user_") !== false)
            {
                if (!$userTables){ $userTables = []; }
                array_push($userTables, $row[0]);
            }
        }
    }
    if (!$usersTable)
    {
        initUsersTable($connection);
    }
    else
    {
        if (!$userImageColumn)
        {
            $query = "ALTER TABLE users ADD imageURL VARCHAR(256) NOT NULL AFTER role";
            $result = $connection->query($query);
        }
        $users = getAllUsers($connection);
        if ($users)
        {
            foreach ($users as $user)
            {
                // determine if their subsidary tables exist, and if not, add them
                $exists = 0;
                if ($userTables)
                {
                    foreach ($userTables as $table)
                    {
                        $position = strlen("user_");
                        $tableID = substr($table, $position);
                        if ($user['id'] == $tableID)
                        {
                            $exists = 1;
                        }
                    }
                }
                if (!$exists)
                {
                    generateUserTable(NULL, $user, $connection);
                }
            }
        }
    }
    if (!$congressTable)
    {
        initCongressTable($connection);
    }
    else
    {
        if (!$congressImageColumn)
        {
            $query = "ALTER TABLE congresses ADD imageURL VARCHAR(256) NOT NULL AFTER author";
            $result = $connection->query($query);
        }
        if (!$congressBiosColumn)
        {
            $query = "ALTER TABLE congresses ADD bios VARCHAR(2048) NOT NULL AFTER hotels";
            $result = $connection->query($query);
        }
        $congresses = getAllCongresses($connection);
        if ($congresses)
        {
            foreach ($congresses as $congress)
            {
                // determine if their subsidary tables exist, and if not, add them
                $exists = 0;
                if ($congressAgendaTables)
                {
                    foreach ($congressAgendaTables as $table)
                    {
                        $position = strlen("congressagenda_");
                        $tableID = substr($table, $position);
                        if ($congress['id'] == $tableID)
                        {
                            $exists = 1;
                        }
                    }
                }
                if (!$exists)
                {
                    generateCongressAgendaTable($congress, $connection);
                }
                $exists = 0;
                if ($congressHospRoomTables)
                {
                    foreach ($congressHospRoomTables as $table)
                    {
                        $position = strlen("congresshospitalityrooms_");
                        $tableID = substr($table, $position);
                        if ($congress['id'] == $tableID)
                        {
                            $exists = 1;
                        }
                    }
                }
                if (!$exists)
                {
                    generateCongressHospitalityRoomsTable($congress, $connection);
                }
                $exists = 0;
                if ($congressHospSchedTables)
                {
                    foreach ($congressHospSchedTables as $table)
                    {
                        $position = strlen("congresshospitalityschedule_");
                        $tableID = substr($table, $position);
                        if ($congress['id'] == $tableID)
                        {
                            $exists = 1;
                        }
                    }
                }
                if (!$exists)
                {
                    generateCongressHospitalityScheduleTable($congress, $connection);
                }
                else
                {
                    $query = "SELECT openEnd FROM congressHospitalitySchedule_" . $congress['id'];
                    $result = $connection->query($query);
                    if (!$result)
                    {
                        $query = "ALTER TABLE congressHospitalitySchedule_" . $congress['id'] . " ADD openEnd varchar(1024) NOT NULL AFTER endDate";
                        $result = $connection->query($query);
                    }
                }
            }
        }
    }
    if (!$messageGroupsTable)
    {
        initMessageGroupsTable($connection);
    }
    else
    {
        $legacySize = 0;
        $query = "SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DATABASE . "' AND TABLE_NAME = 'messageGroups' AND COLUMN_NAME = 'title'";
        $result = $connection->query($query);
        while ($row = mysqli_fetch_array($result))
        {
            foreach ($row as $s)
            {
                if ($s != "64")
                {
                    $legacySize = 1;
                }
            }
        }
        if ($legacySize)
        {
            $query = "ALTER TABLE messageGroups CHANGE title title varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
            $result = $connection->query($query);
            $query = "ALTER TABLE messageGroups CHANGE users users varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
            $result = $connection->query($query);
        }
    }
    if (!$pendingTable)
    {
        initPendingTable($connection);
    }
    if (!$hotelsTable)
    {
        initHotelsTable($connection);
    }
    if (!$trashTable)
    {
        initTrashBinTable($connection);
    }
    if (!file_exists(UPLOAD_PATH)) 
    {
        mkdir(UPLOAD_PATH, 0777, true);
    }
    if (!file_exists(DOWNLOAD_PATH))
    {
        mkdir(DOWNLOAD_PATH, 0777, true);
    }
    if (!file_exists(RAW_AGENDA_PATH))
    {
        mkdir(RAW_AGENDA_PATH, 0777, true);
    }
    if (!file_exists(CONGRESS_IMAGES_PATH))
    {
        mkdir(CONGRESS_IMAGES_PATH, 0777, true);
    }
    if (!file_exists(USER_IMAGES_PATH))
    {
        mkdir(USER_IMAGES_PATH, 0777, true);
    }
    if (!file_exists(INSIGHTS_PATH))
    {
        mkdir(INSIGHTS_PATH, 0777, true);
    }
    if (!file_exists(CONGRESS_BIOS_PATH))
    {
        mkdir(CONGRESS_BIOS_PATH, 0777, true);
    }
    if (!file_exists(USER_IMAGES_THUMBS_PATH))
    {
        mkdir(USER_IMAGES_THUMBS_PATH, 0777, true);
        foreach (scandir(USER_IMAGES_PATH) as $imageFile)
        {
            $extension = strtolower(pathinfo($imageFile,PATHINFO_EXTENSION));
            if ($extension === 'png' || $extension === 'jpg' || $extension === 'jpeg')
            {
                $source = USER_IMAGES_PATH . $imageFile;
                $target = USER_IMAGES_THUMBS_PATH . $imageFile;
                createThumbnail($source, $target, 256);
            }
        }
    }
    if (!file_exists(CONGRESS_IMAGES_THUMBS_PATH))
    {
        mkdir(CONGRESS_IMAGES_THUMBS_PATH, 0777, true);
        foreach (scandir(CONGRESS_IMAGES_PATH) as $imageFile)
        {
            $extension = strtolower(pathinfo($imageFile,PATHINFO_EXTENSION));
            if ($extension === 'png' || $extension === 'jpg' || $extension === 'jpeg')
            {
                $source = CONGRESS_IMAGES_PATH . $imageFile;
                $target = CONGRESS_IMAGES_THUMBS_PATH . $imageFile;
                createThumbnail($source, $target, 512);
            }
        }
    }
    foreach (scandir(INSIGHTS_PATH) as $f1)
    {
        $folder1 = INSIGHTS_PATH . $f1 . "/";
        if (is_dir($folder1) && strpos($f1, ".") === false)
        {
            foreach (scandir($folder1) as $f2)
            {
                $folder2 = $folder1 . $f2 . "/";
                if (is_dir($folder2) && strpos($f2, ".") === false)
                {
                    //debug($folder2);
                    foreach (scandir($folder2) as $f3)
                    {
                        //debug($f3);
                        if ($f3 == "Image.png")
                        {
                            if (!file_exists($folder2 . "Thumb.png"))
                            {
                                $source = $folder2 . "Image.png";
                                $target = $folder2 . "Thumb.png";
                                createThumbnail($source, $target, 256);
                            }
                        }
                    }
                }
            }
        }
    }
}

function initUsersTable($connection)
{
    $query = "CREATE TABLE users (";
    $query .= "id int(5) NOT NULL AUTO_INCREMENT,";
    $query .= "first varchar(20) NOT NULL,";
    $query .= "last varchar(30) NOT NULL,";
    $query .= "email varchar(40) NOT NULL,";
    $query .= "password varchar(64) NOT NULL,";
    $query .= "phone bigint(16) NOT NULL,";
    $query .= "title varchar(32) NOT NULL,";
    $query .= "level int(1) NOT NULL,";
    $query .= "role varchar(20) NOT NULL,";
    $query .= "imageURL varchar(256) NOT NULL,";
    $query .= "PRIMARY KEY (id))";
    $connection->query($query);
}

function initCongressTable($connection)
{
    $query = "CREATE TABLE congresses (";
    $query .= "id int(5) NOT NULL AUTO_INCREMENT,";
    $query .= "name varchar(48) NOT NULL,";
    $query .= "shortName varchar(24) NOT NULL,";
    $query .= "congressURL varchar(512) NOT NULL,";
    $query .= "registrationURL varchar(512) NOT NULL,";
    $query .= "hotelStartDate datetime NOT NULL,";
    $query .= "hotelEndDate datetime NOT NULL,";
    $query .= "startDate datetime NOT NULL,";
    $query .= "endDate datetime NOT NULL,";
    $query .= "showHours varchar(128) NOT NULL,";
    $query .= "venueName varchar(32) NOT NULL,";
    $query .= "venueHall varchar(16) NOT NULL,";
    $query .= "venueBooth varchar(16) NOT NULL,";
    $query .= "venueAddress1 varchar(32) NOT NULL,";
    $query .= "venueAddress2 varchar(16) NOT NULL,";
    $query .= "venueCity varchar(16) NOT NULL,";
    $query .= "venueState varchar(16) NOT NULL,";
    $query .= "venueCountry varchar(16) NOT NULL,";
    $query .= "venueZip varchar(16) NOT NULL,";
    $query .= "hotels varchar(32) NOT NULL,";
    $query .= "bios varchar(2048) NOT NULL,";
    $query .= "author int(5) NOT NULL,";
    $query .= "imageURL varchar(256) NOT NULL,";
    $query .= "PRIMARY KEY (id))";
    $connection->query($query);
}

function initPendingTable($connection)
{
    $query = "CREATE TABLE pending_requests (";
    $query .= "id int(7) NOT NULL AUTO_INCREMENT,";
    $query .= "userID int(5) NOT NULL,";
    $query .= "recordID int(5) NOT NULL,";
    $query .= "PRIMARY KEY (id))";
    $connection->query($query);
}

function initHotelsTable($connection)
{
    $query = "CREATE TABLE hotels (";
    $query .= "id int(4) NOT NULL AUTO_INCREMENT,";
    $query .= "name varchar(32) NOT NULL,";
    $query .= "url varchar(512) NOT NULL,";
    $query .= "address1 varchar(32) NOT NULL,";
    $query .= "address2 varchar(16) NOT NULL,";
    $query .= "city varchar(32) NOT NULL,";
    $query .= "state varchar(16) NOT NULL,";
    $query .= "zip varchar(16) NOT NULL,";
    $query .= "phone varchar(32) NOT NULL,";
    $query .= "PRIMARY KEY (id))";
    $connection->query($query);
}

function initTrashBinTable($connection)
{
    $query = "CREATE TABLE trashBin (";
    $query .= "id int(6) NOT NULL AUTO_INCREMENT,";
    $query .= "type varchar(32) NOT NULL,";
    $query .= "records varchar(32) NOT NULL,";
    $query .= "PRIMARY KEY (id))";
    $connection->query($query);
}

function initMessageGroupsTable($connection)
{
    $query = "CREATE TABLE messageGroups (";
    $query .= "id int(6) NOT NULL AUTO_INCREMENT,";
    $query .= "author int(5) NOT NULL,";
    $query .= "title varchar(64) NOT NULL,";
    $query .= "users varchar(256) NOT NULL,";
    $query .= "PRIMARY KEY (id))";
    $connection->query($query);
}

