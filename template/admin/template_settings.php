<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}


add_style(
	'bootstrap',
	TEMPLATE_URL.'inc/css/bootstrap/bootstrap.min.css',
	0,
	[],
	true
);


add_style(
	'main',
	TEMPLATE_URL.'admin/inc/css/main.css', 
	0,
	[],
	true
);



add_script(
	'jquery',
	'https://code.jquery.com/jquery-3.4.0.min.js', 
	null,
	[
		'integrity' => 'sha256-BJeo0qm959uMBGb65z40ejJYGSgR7REI4+CW1fNKwOg=',
		'crossorigin' => 'anonymous'
	]
);

add_script(
	'popper',
	'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', 
	null,
	[
		'integrity' => 'sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1',
		'crossorigin' => 'anonymous'
	]
);

add_script(
	'bootstrap',
	TEMPLATE_URL.'inc/js/bootstrap/bootstrap.bundle.min.js', 
	null,
	[]
);

add_script(
	'fontawesome',
	'https://kit.fontawesome.com/b408cbe684.js'
);

add_script(
	'ckeditor',
	TEMPLATE_URL.'admin/inc/js/ckeditor/ckeditor.js'
);


add_script(
	'main',
	TEMPLATE_URL.'admin/inc/js/main.js'
);



function output_menu($array, $lvl = 0){
	echo '<ul>';
	$path = get_path();
	foreach($array as $item){
		$tlvl = $lvl;
		if (isset($item['js_button'])){
			echo '<li><div><a class="menu-link '.$item['js_button'].'" href="#">'.$item['text'].'</a></div>';
		} else {
			echo '<li><div><a class="menu-link'.(preg_match('/^'.$item['preg'].'$/', $path) ? ' active': '').'" href="'.$item['url'].'">'.$item['text'].'</a></div>';
		}
		if (isset($item['childrens'])){
			$tlvl++;
			output_menu($item['childrens'], $tlvl);
		}
		echo '</li>';
	}
	echo '</ul>';
}

function pagination($total, $onpage, $current_page, $link, $search_get = ''){
	$last_page = ceil($total/$onpage);
	if ($last_page == 1 || $total == 0){
		return false;
	}
	$start = ($current_page - 2 > 1) ? $current_page - 2 : 2;
	$end = ($current_page + 2 < $last_page - 1) ? $current_page + 2 : $last_page - 1;
	//echo $start; return;
	
	?>
	<ul class="pagination">
<?php if ($last_page > 2) if ($current_page == 1):  ?>
		<li class="page-item disabled">
		  <span class="page-link" aria-label="Previous">
			<span aria-hidden="true">&laquo;</span>
		  </span>
		</li>
<?php else :?>
		<li class="page-item ">
		  <a class="page-link" href="<?=$link.'page='.($current_page-1).$search_get?>" aria-label="Previous">
			<span aria-hidden="true">&laquo;</span>
		  </a>
		</li>
<?php endif; ?>


<?php if ($current_page == 1): ?>
	<li class="page-item active" aria-current="page">
	  <span class="page-link">
		1
	  </span>
	</li>
<?php else: ?>
	<li class="page-item"><a class="page-link" href="<?=$link.'page=1'.$search_get?>">1</a></li>
<?php endif; ?>
		

<?php if ($start > 2): ?>
		<li class="page-item">
		  <span class="page-link">
				...
		  </span>
		</li>
<?php endif; ?>
		
<?php for ($i = $start; $i <= $end; $i++): ?>
		<?php if ($current_page == $i): ?>
			<li class="page-item active" aria-current="page">
			  <span class="page-link">
				<?=$i?>
			  </span>
			</li>
		<?php else: ?>
			<li class="page-item"><a class="page-link" href="<?=$link.'page='.$i.$search_get?>"><?=$i?></a></li>
		<?php endif; ?>
<?php endfor; ?>
<?php if ($end < $last_page - 1): ?>
		<li class="page-item">
		  <span class="page-link">
				...
		  </span>
		</li>
<?php endif; ?>


<?php if ($current_page == $last_page): ?>
	<li class="page-item active" aria-current="page">
	  <span class="page-link">
		<?=$last_page?>
	  </span>
	</li>
<?php else: ?>
	<li class="page-item"><a class="page-link" href="<?=$link.'page='.$last_page.$search_get?>"><?=$last_page?></a></li>
<?php endif; ?>


		
<?php if ($last_page > 2) if ($current_page == $last_page):  ?>
		<li class="page-item  disabled">
		  <span class="page-link" aria-label="Next">
			<span aria-hidden="true">&raquo;</span>
		  </span>
		</li>
<?php else :?>
		<li class="page-item ">
		  <a class="page-link" href="<?=$link.'page='.($current_page+1).$search_get?>" aria-label="Next">
			<span aria-hidden="true">&raquo;</span>
		  </a>
		</li>
<?php endif; ?>
	</ul>
	<?php
}



