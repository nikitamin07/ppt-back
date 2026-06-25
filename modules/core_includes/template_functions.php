<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

function get_tpl_part($template, $vars = array()){
	extract($vars);
	require(TEMPLATE_PATH.$template.'.php');
}



