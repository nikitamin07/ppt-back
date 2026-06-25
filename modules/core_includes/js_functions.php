<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

$_scripts['header'] = array();
$_scripts['footer'] = array();
$_scripts['last_position_header'] = 0;
$_scripts['last_position_footer'] = 0;
$_scripts['id_position_relation'] = array();

function add_script($id, $src, $position = null, $attrs = array() ,$head = false){
	global $_scripts;
	$page_part = 'footer';
	if ($head){
		$page_part = 'header';
	}
	
	if ($position === null){
		$position = $_scripts['last_position_'.$page_part];
        $isnull = true;
	} else {
		$position = intval($position);
	}
	
	
	while (isset($_scripts[$page_part][$position])) {
		$position++;
	}
	$tmp = array();
	foreach($attrs as $key => $value){
		$tmp[] = $key.'="'.$value.'"';
	}
	
	
	$_scripts[$page_part][$position] = '<script id="script-'.$id.'" '.implode(' ', $tmp).' src="'.$src.'"></script>';
	$_scripts['id_position_relation'][$id] = ['part' => $page_part, 'pos' => $position];
	$position++;
    if (isset($isnull)){
        $_scripts['last_position_'.$page_part] = $position;
    }
	
}

function remove_script($id){
	global $_scripts;
	$tmp = $_scripts['id_position_relation'][$id];
	unset($_scripts[$tmp['part']][$tmp['pos']]);
}

function output_scripts($part,$return = false){
	global $_scripts;
	ksort($_scripts[$part]);
    
	$tmp = '';
	foreach ($_scripts[$part] as $script){
		$tmp .= $script."\n";
	}
	if ($return){
		return $tmp;
	}
	echo $tmp;
}


