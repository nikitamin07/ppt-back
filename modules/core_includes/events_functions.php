<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}


$_events = array();



function on($event, $function, $order){
    global $_events;
    while (isset($_events[$event][$order])) {
        $order++;
    }
    $_events[$event][$order] = $function;
}

function trigger(){
    global $_events;
	
	$args = func_get_args();
	$event = $args[0];
    unset($args[0]);
	
	$args = array_values($args);
    if (isset($_events[$event]) && is_array($_events[$event])){
        foreach ($_events[$event] as $function){
            $res = call_user_func_array($function, $args);
        }
        return $res;
    }
}




