<?php

define('INDEXED', true);

require_once 'settings.php';

require_once 'modules/core.php';

require_once 'modules/url_handler.php';

require_once 'modules/auth_checker.php';


ini_set('display_errors', 1); 
error_reporting(E_ALL);

$work_space = '';

if (isset($_URLARRAY[0]) && $_URLARRAY[0] == 'admin'){
	$work_space = 'admin'.DIRECTORY_SEPARATOR;
    define('ADMIN_ZONE', true);
} else {
    define('ADMIN_ZONE', false);
}


$plugin_res = $db -> query("SELECT * FROM plugins WHERE state='1'");

while ($plugin = $plugin_res -> fetch_object()){
    $_PLUGIN_PATH = PLUGINS_PATH.$plugin -> name.'/';
    require_once $_PLUGIN_PATH.'index.php';
}

unset($_PLUGIN_PATH);

define('WORK_SPACE', EXECUTABLE_PATH.$work_space);
define('TEMPLATE_SETTING_PATH', TEMPLATE_PATH.$work_space);

//global $deliveryHandler;

//$deliveryHandler -> updateCitiesList();
//$deliveryHandler -> updateTerminalsList();

require_once WORK_SPACE.'main.php';

