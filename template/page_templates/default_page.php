<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

get_tpl_part('default/header', ['metas' => $metas, 'title' => $page -> title]);
$breadcrumbs = getBreadEl(urlarray());

?>

<div class="container <?=$page -> alias?>-page">
	<?php get_tpl_part('default/page-top', ['page' => $page, 'breadcrumbs' => $breadcrumbs]); ?>
	<?=$page->content?>
</div>

<?php

get_tpl_part('default/footer');

