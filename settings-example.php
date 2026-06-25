<?php
if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

define('DB_HOST', 'db_host');
define('DB_USER', 'db_user');
define('DB_PASS', '12341234');
define('DB_NAME', 'db_name');
define('BASE', '/');

date_default_timezone_set('Europe/Minsk');

define('IS_HTTPS', true);
define('TEMPLATE_PATH', 'template'.DIRECTORY_SEPARATOR);
define('TEMPLATE_URL', 'template/');
define('page_404', TEMPLATE_PATH.'404.php');
define('EXECUTABLE_PATH', 'functions'.DIRECTORY_SEPARATOR);
define('PLUGINS_PATH', 'plugins/');

define('TELEGRAM_CHATS', '-000000000');
define('TELEGRAM_API', 'bot0000000000:exampleefjvefnejvwedwewEDCcedsd123');
define('RECAPTCHA_SECRET', 'secret_key');
define('RECAPTCHA_SITEKEY', 'sitekey');

$language = 'ru';