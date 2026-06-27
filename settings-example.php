<?php
if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

define('DB_HOST', getenv('DB_HOST') ?: 'host.docker.internal');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'ppt_db');
define('BASE', getenv('DB_BASE') ?: '/');

date_default_timezone_set('Europe/Minsk');

define('IS_HTTPS', getenv('DB_IS_HTTPS') ? getenv('DB_IS_HTTPS') === 'true' : false);
define('TEMPLATE_PATH', 'template'.DIRECTORY_SEPARATOR);
define('TEMPLATE_URL', 'template/');
define('page_404', TEMPLATE_PATH.'404.php');
define('EXECUTABLE_PATH', 'functions'.DIRECTORY_SEPARATOR);
define('PLUGINS_PATH', 'plugins/');

define('TELEGRAM_CHATS', getenv('TELEGRAM_CHATS') ?: '-000000000');
define('TELEGRAM_API', getenv('TELEGRAM_API') ?: 'bot_token_here');
define('RECAPTCHA_SECRET', getenv('RECAPTCHA_SECRET') ?: 'secret_key_here');
define('RECAPTCHA_SITEKEY', getenv('RECAPTCHA_SITEKEY') ?: 'sitekey_here');

$language = 'ru';