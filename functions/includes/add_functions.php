<?php 

	function static_image($image_path){
		return TEMPLATE_URL.'inc/images/'.$image_path;
	}
	
	function shortText($tx){
		$tx = strip_tags($tx);
		$tx = substr($tx, 0, 120);
		$tx = rtrim($tx, "!,.-");
		$tx = substr($tx, 0, strrpos($tx, ' '));
		return $tx."… ";
	}
	
	function getChildsFromPart($alias){
		global $db;
		
		$childrens = array();
		
		$res = $db -> query("SELECT `page_id` FROM `page_partitions` WHERE `partition_id` IN (SELECT `id` FROM partitions WHERE alias='".$alias."')");
		
		while($r = $res -> fetch_object()){	
		
			$res2 = $db -> query("SELECT * FROM `pages` WHERE id = '". $r->page_id ."'");
			
			if ($res2 -> num_rows > 0){
				$tmp = $res2 -> fetch_object();
				
				$metas = [];
				$res3 = $db -> query("SELECT * FROM page_meta WHERE page_id='{$tmp -> id}'");

				while ($m = $res3 -> fetch_object()){
					$tmp->{$m -> meta_id} = $m -> data;
				}
				$childrens[] = $tmp;		
			}	
			
		}
		
		return $childrens;
	}
	
	function getMetaPages($arr){
		global $db;
		
		foreach($arr as $k => $v){
			$metas = [];
			$res = $db -> query("SELECT * FROM page_meta WHERE page_id='{$v -> id}'");			
			while ($m = $res -> fetch_object()){
				$arr[$k]->{$m -> meta_id} = $m -> data;
			}
		}
		
		return $arr;
	}
	
	function getBreadEl($pages){
		global $db;		
		$return = [];
		
		foreach($pages as $v){
			$tmp = [];
			$res = $db -> query("SELECT alias, page, id ,name FROM partitions WHERE alias='{$v}'");						
			if($res -> num_rows > 0){
				$tmp = $res -> fetch_object();				
				if($tmp->page > 0){
					$res = $db -> query("SELECT title, alias, id FROM pages WHERE id = {$tmp->page}");					
					$tmp = $res -> fetch_object();
				}
			}else{
				$res = $db -> query("SELECT title, alias, id FROM pages WHERE alias = '{$v}'");
				if($res->num_rows > 0){		
					$tmp = $res -> fetch_object();
				}
			}
			if(!empty($tmp)){
				$return[] = $tmp;
			}			
		}		
		
		return $return;
	}
	
	
	
	
