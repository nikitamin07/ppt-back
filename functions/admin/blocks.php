<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

$page = new Page();

if (isset($_GET['delete'])){
	$db -> query("DELETE FROM blocks WHERE id='".intval($_GET['delete'])."'");
	header('location: '.host(true).urlarray(0).'/'.urlarray(1));
	die();
}

if (urlarray(2) !== null){
	if (urlarray(2) == 'add'){
		if (isset($_POST['add'])){
			$db -> query("INSERT INTO blocks VALUES(null, '".r($_POST['key'])."', '".r($_POST['data'])."')");
			$id = $db -> insert_id;
			
			header('location: '.host(true).urlarray(0).'/'.urlarray(1).'/'.$id);
			die();
		}
		
		
		$b = new StdClass();
		$b -> id = 0;
		$b -> block_key = '';
		$b -> data = '';

		$page -> setVar('block', $b);

		$page -> setVar('add', true);

		
		$page -> show('admin/block_edit');
		die();
	}
	
	$id = intval(urlarray(2));
	if (isset($_POST['save'])){
		$db -> query("UPDATE blocks SET 
			block_key='".r($_POST['key'])."', 
			data='".r($_POST['data'])."' WHERE id = '$id'");
	}
	
	$res = $db -> query("SELECT * FROM blocks WHERE id='$id'");
	
	if ($res -> num_rows == 0){
		call_404();
	}
	$block = $res -> fetch_object();
	
	$page -> setVar('block', $block);
	$page -> show('admin/block_edit');
	die();
}


$cpage = 0;
$onpage = 20;

if (isset($_GET['page'])){
	$tmp = intval($_GET['page'])-1;
	$cpage = ($tmp > 0) ? $tmp : 0;
}

$total = $db -> query("SELECT COUNT(id) FROM blocks") -> fetch_row()[0];

$res = $db -> query("SELECT * FROM blocks LIMIT ".$cpage*$onpage.", $onpage");

$blocks = array();

while ($b = $res -> fetch_object()){
	$blocks[] = $b;
}

$page -> setVar('total', $total); 
$page -> setVar('current', $cpage + 1);
$page -> setVar('onpage', $onpage);

$page -> setVar('blocks', $blocks);

$page -> show('admin/blocks');