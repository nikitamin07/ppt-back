<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

$_PATH = '';
$_URLARRAY = createGET($_SERVER['REQUEST_URI']);

$host  = 'http'.((IS_HTTPS) ? 's': '').'://'.$_SERVER['HTTP_HOST'].BASE;

function host($return = false){
	global $host;
	if ($return){
		return $host;
	}
	echo $host;
}

function get_path(){
	global $_PATH;
	return $_PATH;
}

function urlarray($index = null){
	global $_URLARRAY;
	
	if ($index === null){
		return $_URLARRAY;
	}
	
	if (!isset($_URLARRAY[$index])){
		return null;
	}
	
	return $_URLARRAY[$index];
}


function createGET($request){
	global $_PATH;
    $request = preg_replace("/^".addcslashes(BASE, '/')."/", '', $request);
    
	
    $request = explode('?', $request);
    $request = $request[0];
	
    $_PATH = $request;
    $request = explode('/', $request);
	if ($request[count($request) - 1] == ''){
		unset($request[count($request) - 1]);
	}
	
    foreach ($request as &$r){
        $r = urldecode($r);
    }
    
    
    return $request;
}




