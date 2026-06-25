<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

function get_child_partitions($res, $id = null, &$i = 0, $lvl = 0){
	$partitions = array();
	while ($p = $res -> fetch_object()){
		$p -> lvl = $lvl;

		$partitions[] = $p;
		$i++;
		
		
		$res2 = db() -> query("SELECT * FROM partitions WHERE ".(($id !== null) ? "id != '$id' AND " : '')." id IN (SELECT partition_id FROM partitions_relations WHERE parent_id='{$p -> id}')");

		
		if ($res2 -> num_rows > 0){
			$partitions = array_merge($partitions, get_child_partitions($res2, $id, $i, $lvl+1));
		}
		
		
		
	}
	return $partitions;
}





