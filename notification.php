<?php

define('INDEXED', true);

require_once 'settings.php';

require_once 'modules/TelegramBot.php';

if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
    if ($_POST["g_recaptcha_response"]) {
		$myCurl = curl_init();
		curl_setopt_array($myCurl, array(
			CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query(array('secret' => RECAPTCHA_SECRET, 'response' => $_POST["g_recaptcha_response"]))
		));
		$response = curl_exec($myCurl);
		curl_close($myCurl);
		$response = json_decode($response);
    }

    if (isset($_POST["policy"]) && isset($_POST["name"]) && isset($_POST["phone"])){
        if ($response) {
			if (isset($_POST["product"]) && $_POST["product"]!='none') {
				$message = "НОВАЯ ЗАЯВКА НА КОНСУЛЬТАЦИЮ ПО ТОВАРУ!\nИМЯ: " . $_POST["name"] . "\nТЕЛЕФОН: " . $_POST["phone"] . "\nТОВАР: " . $_POST["product"] . "\n";
			} else {
				$message = "НОВАЯ ЗАЯВКА НА ОБРАТНЫЙ ЗВОНОК!\nИМЯ: " . $_POST["name"] . "\nТЕЛЕФОН: " . $_POST["phone"] . "\n";
			}
			$resp='success';
			print_r($resp);
        }
        else {
            $resp = 'Вы не прошли проверку на робота';
            print_r($resp);
			die();
        }
    }
}

TelegramBot::init();

$res=TelegramBot::sendMessages($message);
