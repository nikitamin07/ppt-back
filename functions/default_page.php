<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

$page = new Page();
$pageId = intval($node -> id);
$res = $db -> query("SELECT * FROM pages WHERE id=$pageId");

while ($t = $res -> fetch_object()){
	$node -> title = $t -> title;
}


$metas = [];
$res = $db -> query("SELECT * FROM page_meta WHERE page_id='{$pageId}'");

while ($m = $res -> fetch_object()){
	$metas[$m -> meta_id] = $m -> data;
}


foreach ($metas as $key => $meta){
	$node -> content = str_replace('['.$key.']', $meta, $node -> content);
}

$page -> setVar('page', $node);
$page -> setVar('metas', $metas);


if (isset($partition)){
    $page -> setVar('partition', $partition);
    $page -> setVar('children_partitions', $children_partitions);
    $page -> setVar('children_pages', $children_pages);
}


if ($node -> template == ''){
	$page -> show('page_templates/default_page');
} else {
	$page -> show('page_templates/'.$node -> template);
}




