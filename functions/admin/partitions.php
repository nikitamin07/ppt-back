<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}


if (isset($_GET['delete'])){
	$db -> query("DELETE FROM partitions WHERE id='".intval($_GET['delete'])."'");
	header('location: '.host(true).urlarray(0).'/'.urlarray(1));
	die();
}


$page = new Page();

if (urlarray(2) !== null){
    
    $pages = [];
    
    $res = $db -> query("SELECT id, title, alias FROM pages");
    
    while ($p = $res -> fetch_object()){
        $pages[] = $p;
    }
    
    
    $page -> setVar('pages', $pages);
    
	if (urlarray(2) == 'add'){
		if (isset($_POST['add'])){
			$db -> query("INSERT INTO partitions VALUES(null, '".r($_POST['pname'])."', '".r($_POST['alias'])."', '', '".intval($_POST['page'])."')");
			$id = $db -> insert_id;
			if (isset($_POST['parents'])){
				foreach ($_POST['parents'] as $val){
					$val = intval($val);
					if ($db -> query("SELECT COUNT(id) FROM partitions WHERE id='$val'") -> fetch_row()[0] > 0){
						$db -> query("REPLACE INTO partitions_relations VALUES ('$id', '$val')");
					}
				}
			}
			header('location: '.host(true).urlarray(0).'/'.urlarray(1).'/'.$id);
		}
		
		
		$part = new StdClass();
		$part -> id = 0;
		$part -> alias = '';
		$part -> name = '';
		$part -> controller = '';
		$part -> page = '';
		$page -> setVar('partition', $part);		
		$page -> setVar('parents', []);
		$page -> setVar('add', true);
		
		$res = $db -> query("SELECT * FROM partitions WHERE id NOT IN (SELECT partition_id FROM partitions_relations)");

		$page -> setVar('partitions_list', get_child_partitions($res, $part -> id));
		
		$page -> show('admin/partition_edit');
		die();
	}
	
	
	$id = intval(urlarray(2));
	if (isset($_POST['save'])){
		if (!($_POST['alias'] == 'admin' && !isset($_POST['parents']))){
			$db -> query("UPDATE partitions SET 
				name='".r($_POST['pname'])."', 
				alias='".r($_POST['alias'])."',
                page='".r($_POST['page'])."' 
                WHERE id = '$id'");
			echo $db -> error;
			$db -> query("DELETE FROM partitions_relations WHERE partition_id = '$id'");
			if (isset($_POST['parents'])){
				foreach ($_POST['parents'] as $val){
					$val = intval($val);
					if ($db -> query("SELECT COUNT(id) FROM partitions WHERE id='$val'") -> fetch_row()[0] > 0){
						$db -> query("REPLACE INTO partitions_relations VALUES ('$id', '$val')");
					}
				}
			}
			
		}
	}
	$res = $db -> query("SELECT * FROM partitions WHERE id='$id'");
	
	if ($res -> num_rows == 0){
		call_404();
	}
	$part = $res -> fetch_object();
	$page -> setVar('partition', $part);
	
	$parents = array();
	
	$res = $db -> query("SELECT parent_id FROM partitions_relations WHERE partition_id = '$id'");
	
	while ($relation = $res -> fetch_object()){
		$parents[] = $relation -> parent_id;
	}
	
	$res = $db -> query("SELECT * FROM partitions WHERE id != '{$part -> id}' AND id NOT IN (SELECT partition_id FROM partitions_relations)");
	$page -> setVar('parents', $parents);
	$page -> setVar('partitions_list', get_child_partitions($res, $part -> id));
	$page -> show('admin/partition_edit');
	die();
}







$cpage = 0;
$onpage = 20;

if (isset($_GET['page'])){
	$tmp = intval($_GET['page'])-1;
	$cpage = ($tmp > 0) ? $tmp : 0;
}

$total = $db -> query("SELECT COUNT(id) FROM partitions") -> fetch_row()[0];

$res = $db -> query("SELECT * FROM partitions LIMIT ".$cpage*$onpage.", $onpage");

$partitions = array();

while ($p = $res -> fetch_object()){
	$partitions[] = $p;
}

// echo '<pre>';
// print_r(get_child_partitions($res)); die();

$page -> setVar('partitions', $partitions);
$page -> setVar('total', $total); 
$page -> setVar('current', $cpage + 1);
$page -> setVar('onpage', $onpage);

$page -> show('admin/partitions');