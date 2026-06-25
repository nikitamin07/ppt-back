<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}


require_once WORK_SPACE.'includes'.DIRECTORY_SEPARATOR.'add_functions.php';

require_once 'languages'.DIRECTORY_SEPARATOR.lng().'.php';

$static_pages = [
	// [
	// 	'preg' => 'delivery',
	// 	'functions' => 'functions/delivery.php'
	// ],
];

trigger('static_pages_initialized');

function search_endpoint(){
    checkURL();
	$_URLARRAY = urlarray();
	$res = db() -> query("SELECT * FROM partitions WHERE alias='".r($_URLARRAY[count($_URLARRAY) - 1])."'");
    
	if ($res -> num_rows > 0){
		$node = $res -> fetch_object();
		$node -> type = 'partition';
		for ($i = count($_URLARRAY) - 2; $i >= 0; $i--){
			$res2 = db() -> query("SELECT * FROM partitions_relations WHERE partition_id='{$node -> id}' AND parent_id=(SELECT id FROM partitions WHERE alias='".r($_URLARRAY[$i])."')");
			if ($res2 -> num_rows == 0){
				call_404();
			}
		}
	} else {
		$res = db() -> query("SELECT * FROM pages WHERE alias='".r($_URLARRAY[count($_URLARRAY) - 1])."'");
		if ($res -> num_rows > 0){
			$node = $res -> fetch_object();
			if ($node -> template == 'main'){
				call_404();
			}
			$node -> type = 'page';
			if (isset($_URLARRAY[count($_URLARRAY) - 2])){
				$res2 = db() -> query("SELECT * FROM page_partitions WHERE page_id='{$node -> id}' AND partition_id=(SELECT id FROM partitions WHERE alias='".r($_URLARRAY[count($_URLARRAY) - 2])."')");
				if ($res2 -> num_rows > 0){
					$partid = $res2 -> fetch_object() -> partition_id;
					for ($i = count($_URLARRAY) - 3; $i >= 0; $i--){
						$res3 = db() -> query("SELECT * FROM partitions_relations WHERE partition_id = '$partid' AND parent_id= (SELECT id FROM partitions WHERE alias='".r($_URLARRAY[$i])."')");
						
						if ($res3 -> num_rows == 0){
							call_404();
						}
						$partid = $res3 -> fetch_object() -> partition_id;
					}
				} else {
					call_404();
				}
			}
		} else {
			call_404();
		}
	}
	return $node;
}

function checkURL() {
    $_URLARRAY = urlarray();
    $partitios_sql_res = db() -> query("SELECT id FROM partitions WHERE alias='".r($_URLARRAY[count($_URLARRAY) - 1])."'");
    if ($partitios_sql_res -> num_rows > 0){
        $partition = $partitios_sql_res -> fetch_object();
        checkParent($partition -> id, $_URLARRAY);
    } else {
        $pages_sql_res = db() -> query("SELECT id FROM `pages` WHERE alias='".r($_URLARRAY[count($_URLARRAY) - 1])."'");
        if ($pages_sql_res -> num_rows > 0) {
            $page = $pages_sql_res -> fetch_object();
            $page_partitions_sql_res = db() -> query("SELECT partition_id FROM `page_partitions` WHERE page_id = '" . $page -> id . "'");
            if ($page_partitions_sql_res -> num_rows == 0 && count($_URLARRAY) != 1) {
                call_404();
            } elseif ($page_partitions_sql_res -> num_rows > 0) {
                $page_partitions = $page_partitions_sql_res -> fetch_object();
                array_pop($_URLARRAY);
                checkParent($page_partitions -> partition_id, $_URLARRAY);
            }
        } else {
            call_404();
        }
    }
}

function checkParent($partition_id, $url) {
    if ($partition_id && empty($url)) {
        call_404();
    } elseif ($partition_id && !empty($url)) {
        $current_partition =  array_pop($url);
        $partitions_sql_res = db() -> query("SELECT id FROM partitions WHERE alias='". r($current_partition) ."'");
        if ($partitions_sql_res -> num_rows == 0) {
            call_404();
        }
        $partitions_relations_sql_res = db() -> query(
            "SELECT * FROM partitions_relations "
                . "WHERE partition_id='{$partition_id}'");
        if ($partitions_relations_sql_res -> num_rows == 0) {
            checkParent(null, $url);
        }
        $partitions_relations = $partitions_relations_sql_res -> fetch_object();
        checkParent($partitions_relations -> parent_id ?? null , $url);
    }

}


function get_functions_file($path){
	global $static_pages;
	foreach ($static_pages as $item){
		if (preg_match('/^'.$item['preg'].'$/', $path)){
			return $item['functions'];
		}
	}
	return null;
}

function get_menu(){
	global $admin_structure;
	return $admin_structure;
}

function get_childrenPages($ARR){
}

function absint( $maybeint ) {
	return abs( intval( $maybeint ) );
}



