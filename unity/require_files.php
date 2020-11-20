<?php

// These are files that need to be required for all Unity scripts to work.

$webBuild = 0;

require_once '../Constants.php';
require_once '../Database.php';
require_once '../Functions_Connection.php';
require_once '../Functions_Login.php';
require_once '../Functions_User.php';
require_once '../Functions_Congress.php';
require_once '../Functions_Hotel.php';
require_once '../Functions_Hospitality.php';
require_once '../Functions_Agenda.php';
require_once '../Functions_Trash.php';
require_once '../Functions_Error.php';
require_once '../Functions_Other.php';

require_once '../PhpSpreadsheet-master/vendor/autoload.php';

require_once 'Functions_Unity.php';