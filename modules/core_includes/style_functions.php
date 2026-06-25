<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

$_styles['header'] = array();
$_styles['footer'] = array();
$_styles['last_position_header'] = 0;
$_styles['last_position_footer'] = 0;
$_styles['id_position_relation'] = array();

function add_style($id, $src, $position = null, $attrs = array() ,$head = false){
	global $_styles;
	$page_part = 'footer';
	if ($head){
		$page_part = 'header';
	}
	
	if ($position === null){
		$position = $_styles['last_position_'.$page_part];
        $isnull = true;
	} else {
		$position = intval($position);
	}
	
	
	while (isset($_styles[$page_part][$position])) {
		$position++;
	}
	$tmp = array();
	foreach($attrs as $key => $value){
		$tmp[] = $key.'="'.$value.'"';
	}
	
	$_styles[$page_part][$position] = '<link id="style-'.$id.'" '.implode(' ', $tmp).' href="'.$src.'" rel="stylesheet">';
	$_styles['id_position_relation'][$id] = ['part' => $page_part, 'pos' => $position];
	$position++;
    if (isset($isnull)){
        $_styles['last_position_'.$page_part] = $position;
	}
}

function remove_style($id){
	global $_styles;
	$tmp = $_styles['id_position_relation'][$id];
	unset($_styles[$tmp['part']][$tmp['pos']]);
}

function output_styles($part,$return = false){
	global $_styles;
	ksort($_styles[$part]);
	$tmp = '';
	foreach ($_styles[$part] as $style){
		$tmp .= $style."\n";
	}
	if ($return){
		return $tmp;
	}
	echo $tmp;
}


