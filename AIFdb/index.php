<?php

define('INSTALLDIR', dirname(__FILE__));
set_include_path(INSTALLDIR.'/extlib/PEAR' . PATH_SEPARATOR . get_include_path());

require_once(INSTALLDIR . "/lib/common.php");

$action = $_REQUEST['action'];
if (!$action) {
    common_redirect(common_local_url('search'));
}

$actionfile = INSTALLDIR."/actions/$action.php";

if (file_exists($actionfile)) {
    require_once($actionfile);
    $action_class = ucfirst($action) . "Action";
    $action_obj = new $action_class();
    call_user_func(array($action_obj, 'handle'), $_REQUEST);
} else {
    common_user_error('Unknown action');
}
