<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

function db(){
	global $db;
	return $db;
}

function r($data){
	return db() -> real_escape_string($data);
}

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($db->connect_error) {
    die("Connect Error (" . $db->connect_errno . ") " . $db->connect_error);
}

if (!$db->set_charset("utf8")) {
    die (printf("Ошибка при загрузке набора символов utf8: %s\n", $db->error));
}

$db -> query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");


