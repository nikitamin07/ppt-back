<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

$page = new Page();


$metas = [];
$res = $db -> query("SELECT * FROM page_meta WHERE page_id='{$node -> id}'");

while ($m = $res -> fetch_object()){
	$metas[$m -> meta_id] = $m -> data;
}


foreach ($metas as $key => $meta){
	$node -> content = str_replace('['.$key.']', $meta, $node -> content);
}

$page -> setVar('page', $node);
$page -> setVar('metas', $metas);

if ($node -> template == ''){
	$page -> show('page_templates/default_page');
} else {
	$page -> show('page_templates/'.$node -> template);
}




