<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

$page = $breadcrumbs[count($breadcrumbs) - 1];

get_tpl_part('default/header', ['title' => $page -> name]);
?>

<div class="container <?=$page -> alias?>-page">
	<?php get_tpl_part('default/page-top', ['page' => $page, 'breadcrumbs' => $breadcrumbs]); ?>
	
	<div class="row">
		<div class="col-lg-12">
			<div class="catalog-products-grid">
                <?php 
				if (count($products) > 0){
                    foreach ($products as $product){
                        get_tpl_part('product_catalog/product_catalog_item', ['product' => $product]);
                    } 
				} else { ?>
                    <div class="col-lg-12">
                        <h2>Подходящих товаров не найдено!</h2>
                    </div>
                <?php } ?>
			</div>
		</div>
	</div>
</div>

<?php

get_tpl_part('default/footer');

