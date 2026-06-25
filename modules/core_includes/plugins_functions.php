<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}



function set_plugin_data($key, $plugin, $data){
    global $_PLUGIN;
    db() -> query("REPLACE INTO plugins_storage VALUES ('".r($key)."', '".r($plugin)."', '".r($data)."')");
}

function get_plugin_data($key, $plugin){
    $res = db() -> query("SELECT data FROM plugins_storage WHERE id='".r($key)."' AND plugin='".r($plugin)."'");
    if ($res -> num_rows == 0){
        return null;
    }
    
    return $res -> fetch_row()[0];
    
}

function unset_plugin_data($key, $plugin){
    db() -> query("DELETE FROM plugins_storage WHERE id='".r($key)."' AND plugin='".r($plugin)."'");
}

function clear_plugin_data($plugin){
    db() -> query("DELETE FROM plugins_storage WHERE plugin='".r($plugin)."'");
}


// Дополнение

function get_plugin_all_data($plugin){
    $res = db() -> query("SELECT data FROM plugins_storage WHERE plugin='".r($plugin)."'");
    if ($res -> num_rows == 0){
        return null;
    }
	
	$tmp = array();
    
	while($data = $res -> fetch_row()[0]){
		$tmp[] = $data;
	}
	
    return $tmp;
    
}


function plugin_exists($plugin){
    $res = db() -> query("SELECT id FROM plugins WHERE name='".r($plugin)."'");
    
    if ($res -> num_rows > 0){
        return true;
    }
    return false;
}



