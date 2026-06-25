<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}


require_once WORK_SPACE.'function_settings.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'modules/classes/mail/Exception.php';
require 'modules/classes/mail/PHPMailer.php';
require 'modules/classes/mail/SMTP.php';



if (isset($_URLARRAY[0])){
	$tmp = get_functions_file(get_path());
	
    
	if ($tmp !== null){
		if (file_exists($tmp)){
			require_once $tmp;
		}
	} else {
		$node = search_endpoint();

		if ($node -> type != 'page'){
            $partition = $node;
            if ($node -> controller != ''){
				require_once WORK_SPACE.$node -> controller . '.php';
			}
            
            if ($node -> page != 0){
                
                $node = $db -> query("SELECT * FROM pages WHERE id='{$partition -> page}'");
                if ($node -> num_rows > 0){
                    $node = $node -> fetch_object();
                }
                
                $node -> type = 'page';
                
                $children_partitions = [];
                $res = $db -> query("SELECT * FROM partitions WHERE id IN (SELECT partition_id FROM partitions_relations WHERE parent_id='{$partition -> id}')");
                
                while ($p = $res -> fetch_object()){
                    $children_partitions[] = $p;
                }
                
                $children_pages = [];
                $res = $db -> query("SELECT * FROM pages WHERE id IN (SELECT page_id FROM page_partitions WHERE partition_id='{$partition -> id}')");
                
                while ($p = $res -> fetch_object()){
                    $children_pages[] = $p;
                }
                
                require_once WORK_SPACE.'default_page.php';
            } else {
                call_404();
            }
            
		} else {
			require_once WORK_SPACE.'default_page.php';
		}
	}
} else {
	$page = new Page();
	
	$res = $db -> query("SELECT * FROM pages WHERE template='main'");
	
	if ($res -> num_rows > 0){
		$node = $res -> fetch_object();
		
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
	}


	$page -> show('main');
}



