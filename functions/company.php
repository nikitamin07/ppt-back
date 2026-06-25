<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}


/* $page = new Page();

$page -> setVar('page', $node); */


/* $res = $db -> query("SELECT `page_id` FROM `page_partitions` WHERE `partition_id` IN (SELECT `id` FROM partitions WHERE alias='". $node->alias."')");	

	
$childrens = array();

while($r = $res -> fetch_object()){		
	
	$res2 = $db -> query("SELECT * FROM `pages` WHERE id = '". $r->page_id ."'");
	if ($res2 -> num_rows > 0){
		$childrens[] = $res2 -> fetch_object();
	}
	
} 
 */

/* 
$page -> setVar('childrens', $childrens); */


//$page -> show('page_templates/company_template');






