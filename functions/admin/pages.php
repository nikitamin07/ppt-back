<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

$page = new Page();


if (isset($_GET['delete'])){
	$db -> query("DELETE FROM pages WHERE id='".intval($_GET['delete'])."'");
    trigger('page_delete', $id);
	header('location: '.host(true).urlarray(0).'/'.urlarray(1));
	die();
}

if (urlarray(2) !== null){
	$metas = [
		'meta_keywoords' => '',
		'meta_description' => '',
		'og:title' => '',
		'og:site_name' => '',
		'og:description' => '',
		'og:image' => ''
	];
	
	$templates = ['main'];
	$tmp = scandir('template/page_templates');
	
	
	
	foreach ($tmp as $t){
		if ($t == 'default_page.php' || $t == '.' || $t == '..' || is_dir('template/page_templates/'.$t)){
			continue;
		}
		
		$t = preg_replace('/\.php$/', '', $t);
		
		$templates[] = $t;
	}
	
	$page -> setVar('templates', $templates);
	
	if (urlarray(2) == 'add'){
		if (isset($_POST['add'])){
			if ($_POST['template'] == 'main'){
				$db -> query("UPDATE pages SET template = '' WHERE template = 'main'");
			}
			$db -> query("INSERT INTO pages VALUES(null, '".r($_POST['alias'])."', '".r($_POST['title'])."', '".r($_POST['content'])."', NOW(), '".r($_POST['template'])."')");
			$id = $db -> insert_id;
			if (isset($_POST['parents'])){
				foreach ($_POST['parents'] as $val){
					$val = intval($val);
					if ($db -> query("SELECT COUNT(id) FROM partitions WHERE id='$val'") -> fetch_row()[0] > 0){
						$db -> query("REPLACE INTO page_partitions VALUES ('$id', '$val')");
					}
				}
			}
			
			if (isset($_POST['meta_key']) && isset($_POST['meta_value'])){
				foreach ($_POST['meta_key'] as $key => $val){
					$db -> query("REPLACE INTO page_meta VALUES ('$id', '".r($val)."', '".r($_POST['meta_value'][$key])."')");
				}
			}
			
			trigger('page_save', $id);
			
			header('location: '.host(true).urlarray(0).'/'.urlarray(1).'/'.$id);
			die();
		}
		
		
		$p = new StdClass();
		$p -> id = 0;
		$p -> alias = '';
		$p -> title = '';
		$p -> content = '';
		$p -> template = '';
		$page -> setVar('page', $p);
		$page -> setVar('parents', []);
		$page -> setVar('add', true);
		$page -> setVar('short_desc', '');
		$page -> setVar('metas', $metas);
		
		$res = $db -> query("SELECT * FROM partitions WHERE id NOT IN (SELECT partition_id FROM partitions_relations)");

		$page -> setVar('partitions_list', get_child_partitions($res));
		
		$page -> show('admin/page_edit');
		die();
	}
	
	
	$id = intval(urlarray(2));
	if (isset($_POST['save'])){
		if ($_POST['template'] == 'main'){
			$db -> query("UPDATE pages SET template = '' WHERE template = 'main'");
		}
		
		$db -> query("UPDATE pages SET 
			title='".r($_POST['title'])."', 
			alias='".r($_POST['alias'])."',
			content='".r($_POST['content'])."', 
			template='".r($_POST['template'])."' WHERE id = '$id'");
		
		
		$db -> query("DELETE FROM page_meta WHERE page_id = '$id'");
		
		if (isset($_POST['meta_key']) && isset($_POST['meta_value'])){
			foreach ($_POST['meta_key'] as $key => $val){
				$db -> query("REPLACE INTO page_meta VALUES ('$id', '".r($val)."', '".r($_POST['meta_value'][$key])."')");
			}
		}
		
		
		$db -> query("DELETE FROM page_partitions WHERE page_id = '$id'");
		if (isset($_POST['parents'])){
			foreach ($_POST['parents'] as $val){
				$val = intval($val);
				if ($db -> query("SELECT COUNT(id) FROM partitions WHERE id='$val'") -> fetch_row()[0] > 0){
					$db -> query("REPLACE INTO page_partitions VALUES ('$id', '$val')");
					
				}
			}
		}
		trigger('page_save', $id);
		
	}
	$res = $db -> query("SELECT * FROM pages WHERE id='$id'");
	
	if ($res -> num_rows == 0){
		call_404();
	}
	$p = $res -> fetch_object();
	$page -> setVar('page', $p);
	
	
	
	$res = $db -> query("SELECT meta_id, data FROM page_meta WHERE page_id='$id'");
	while ($meta = $res -> fetch_object()){
		$metas[$meta -> meta_id] = $meta -> data;
	}
	$page -> setVar('short_desc', $metas['short_desc']);
	//print_r($metas); die();
	
	unset($metas['short_desc']);
	
	$page -> setVar('metas', $metas);

	
	
	$parents = array();
	
	$res = $db -> query("SELECT partition_id FROM page_partitions WHERE page_id = '$id'");
	
	while ($relation = $res -> fetch_object()){
		$parents[] = $relation -> partition_id;
	}
	$page -> setVar('parents', $parents);
	
	$res = $db -> query("SELECT * FROM partitions WHERE id NOT IN (SELECT partition_id FROM partitions_relations)");
	$page -> setVar('partitions_list', get_child_partitions($res));
	$page -> show('admin/page_edit');
	die();
}





$cpage = 0;
$onpage = 20;

if (isset($_GET['page'])){
	$tmp = intval($_GET['page'])-1;
	$cpage = ($tmp > 0) ? $tmp : 0;
}

$partitions = [];
$res = $db -> query("SELECT id, name FROM partitions");
while ($p = $res -> fetch_object()){
    $partitions[] = $p;
}

$filter_values = new stdClass();
if (isset($_GET['search'])) {
    $search_get = "&partition={$_GET['partition']}"
        . "&page_name={$_GET['page_title']}"
        . "&page_alias={$_GET['page_alias']}"
        . "&page_sort={$_GET['page_sort']}"
        . "&direction={$_GET['direction']}"
        . "&search=1";
    $filter_values->partition = ($_GET['partition'] == 0) ? null : $_GET['partition'];
    $filter_values->page_title = (trim($_GET['page_title']) == '') ? null : htmlentities(trim($_GET['page_title']));
    $filter_values->page_alias = (trim($_GET['page_alias']) == '') ? null : htmlentities(trim($_GET['page_alias']));
    $filter_values->page_sort = $_GET['page_sort'];
    $filter_values->direction = $_GET['direction'];
    $sort = " ORDER BY pages.{$_GET['page_sort']} {$_GET['direction']} ";
}
$conditions = [];
if (isset($filter_values->partition)) {
    $conditions[] = "page_partitions.partition_id = '{$filter_values->partition}'";
}

if (isset($filter_values->page_title)) {
    $conditions[] = "pages.title COLLATE UTF8_GENERAL_CI LIKE '%". r($filter_values->page_title) ."%'";
}

if (isset($filter_values->page_alias)) {
    $conditions[] = "pages.alias COLLATE UTF8_GENERAL_CI LIKE '%". r($filter_values->page_alias) ."%'";
}
$query = (count($conditions) > 0) ?  ' WHERE ' . implode(' AND ', $conditions) . ' ' : '';
$join = isset($filter_values->partition) ? ' INNER JOIN page_partitions on pages.id = page_partitions.page_id ' : '';
$sort = $sort ?? " ORDER BY pages.created desc ";
$sql = $join . $query . $sort;

//var_dump($filter_values);
//var_dump($sql);
//die();

$total = $db -> query("SELECT COUNT(id) FROM pages" . $sql) -> fetch_row()[0];

$res = $db -> query("SELECT * FROM pages $sql LIMIT ".$cpage*$onpage.", $onpage");

$pages = array();

while ($p = $res -> fetch_object()){
	$pages[] = $p;
}

// echo '<pre>';
// print_r(get_child_partitions($res)); die();

$page -> setVar('pages', $pages);
$page -> setVar('partitions', $partitions);
$page -> setVar('total', $total); 
$page -> setVar('current', $cpage + 1);
$page -> setVar('onpage', $onpage);

$page -> show('admin/pages');