<?php
es_include("user.php");
define("CRM_DATA_DIR", PROJECT_DIR."var/data/");
define("CRM_DATA_PATH", PROJECT_PATH."var/data/");
define("CRM_UNSUBSCRIBE_SALT", "unsub");
define("AUTOSAVE_INTERVAL", 10000);

require_once(dirname(__FILE__)."/include/utilities.php");
$GLOBALS['entityConfig'] = array();
require_once(dirname(__FILE__)."/config/child.php");
require_once(dirname(__FILE__)."/config/parent.php");
require_once(dirname(__FILE__)."/config/season.php");
require_once(dirname(__FILE__)."/config/school.php");
require_once(dirname(__FILE__)."/config/staff.php");
require_once(dirname(__FILE__)."/config/legal.php");
require_once(dirname(__FILE__)."/config/category.php");
require_once(dirname(__FILE__)."/config/task.php");
require_once(dirname(__FILE__)."/config/mailing.php");
require_once(dirname(__FILE__)."/config/sender.php");
require_once(dirname(__FILE__)."/config/storage.php");
require_once(dirname(__FILE__)."/config/dashboard.php");
require_once(dirname(__FILE__)."/config/event.php");
require_once(dirname(__FILE__)."/config/report.php");
require_once(dirname(__FILE__)."/config/directory.php");
require_once(dirname(__FILE__)."/config/bookkeeping.php");
require_once(dirname(__FILE__)."/config/bookkeepingout.php");
require_once(dirname(__FILE__)."/config/call.php");

?>
