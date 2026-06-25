<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

if (isset($_COOKIE['at'])){
    $res = $db -> query("SELECT * FROM users WHERE token='".md5(salt($_COOKIE['at']))."' AND token_expire >= NOW()");
	echo $db -> error;
    if ($res -> num_rows != 0){
        $user = $res -> fetch_object();
    }
}


function user(){
	global $user;
	if (isset($user)){
		return $user;
	}
	return null;
}
