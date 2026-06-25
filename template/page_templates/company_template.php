<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}



get_tpl_part('default/header', ['metas' => $metas, 'title' => $page -> title]);

$curl = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '') . '://';
$curl = $curl . $_SERVER['SERVER_NAME'] . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : (':' . $_SERVER['SERVER_PORT'])) . $_SERVER['REQUEST_URI'] . '/';

$childs = $children_pages;

$breadcrumbs = getBreadEl(urlarray());	
?>


<div class="company-page page-parent">
	<div class="container">
		<?php get_tpl_part('default/page-top', ['page' => $page, 'breadcrumbs' => $breadcrumbs]); ?>
		<div class="row">
			<div class="sideleft-bar col-sm-4 col-md-3">
				<ul class="side-menu-left">
	<?php foreach($childs as $c):?>
					<li><a href="<?=$curl . $c->alias?>"><?=isset($c->title) ? $c->title : $c->name;?></a></li>
	<?php endforeach;?>
				</ul>
			</div>
			<div class="col-md-9 col-12">
				<div class="page-blocks-content">
					<?=$page->content;?>
				</div>
			</div>
		</div>
	</div>
</div>




<?php

get_tpl_part('default/footer');