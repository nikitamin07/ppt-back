<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

require_once WORK_SPACE.'function_settings.php';


$page = new Page();

if (isset($_POST['admin_auth'])){
	$res = $db -> query("SELECT * FROM users WHERE login='".r($_POST['login'])."' AND pass='".md5(salt($_POST['pass']))."' AND type='0'");
	if ($res -> num_rows > 0){
		$user = $res -> fetch_object();
		$id = $user -> id;
		$token = md5(salt($_POST['login']).salt(time()));
		$expire = time() + 3600*24*10;
		$db -> query("UPDATE users SET token='".md5(salt($token))."', token_expire='".date('Y-m-d H:i:s', $expire)."' WHERE id='$id'");
		setcookie('at', $token, $expire, '/');
        setcookie('time', $expire);
		$resp['s'] = true;
	} else {
		$page -> setVar('error', w('Авторизация не удалась'));
	}
}

if (!isset($user)){
	$page -> show('admin/auth');
	die();
}


if ($user -> type != 0){
	header("location: ".host());
	die();
}


if (isset($_URLARRAY[1])){
	$tmp = get_functions_file(get_path());
    
	if ($tmp !== null){
        
		require_once $tmp;
	} else {
		call_404();
	}
} else {
	$page -> show('admin/main');
}



