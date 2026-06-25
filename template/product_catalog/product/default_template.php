<?php

if (!defined('INDEXED')) {
	header("HTTP/1.0 404 Not Found");
	die();
}

$page = new StdClass();

$page->title = $product->name;
$descr = strip_tags($product->descr);
get_tpl_part('default/header', ['title' => $page->title, 'descr' => $descr]);

?>

<div class="page-product container <?= $page->alias ?? 'product-no-alias' ?>-page">
	<?php get_tpl_part('default/page-top', ['page' => $page, 'breadcrumbs' => $breadcrumbs]); ?>


	<div class="row page-product-top">
		<div class="product-col-left col-12 col-lg-8">
				<div class="product-item_product-img">
					<?php if ($product->image) : ?>
						<a href="<?= $product->image ?>" target="_blank">
							<img src="<?= $product->image ?>?h=750" alt="<?= $product->name ?>" onerror="this.onerror=null; this.src='template/inc/images/no_image.jpg'">
						</a>
					<?php else : ?>
						<a href="template/inc/images/no_image.jpg?h=750" target="_blank">
							<img src="template/inc/images/no_image.jpg?h=750" alt="no_product_image">
						</a>
					<?php endif; ?>
				</div>
		</div>
		<div class="product-col-right col-12 col-lg-4">

			<div class="product-buy-block params-selector product-page">
				<div class="prices product-item-info-container product-item-price-container" data-entity="price-block">
					<?php if ($product->is_volume_price == 1) { 
						if ($product->price_before10 != 0) {?>
						<div class="price-current product-item-price-current">
								<?= format_price($product->price_before10) ?> <?= (($product->price_ed == 'куб') ? 'р/м<sup>3</sup>' : $product->price_ed) ?>
						</div>
						<div class="volume_price_desc">При заказе до 10 м<sup>3</sup></div>
						<?php } 
						if ($product->price_10to20 != 0) {?>
						<div class="price-current product-item-price-current">
								<?= format_price($product->price_10to20) ?> <?= (($product->price_ed == 'куб') ? 'р/м<sup>3</sup>' : $product->price_ed) ?>
						</div>
						<div class="volume_price_desc">При заказе от 10 до 20 м<sup>3</sup></div>
						<?php } 
						if ($product->price_after20 != 0) {?>
						<div class="price-current product-item-price-current">
								<?= format_price($product->price_after20) ?> <?= (($product->price_ed == 'куб') ? 'р/м<sup>3</sup>' : $product->price_ed) ?>
						</div>
						<div class="volume_price_desc">При заказе от 20 м<sup>3</sup></div>
					<?php } } else { ?>
						<?php if ($product->discount_price == 0) : ?>
							<div class="price-current product-item-price-current">
								<?= format_price($product->price) ?> <?= (($product->price_ed == 'куб') ? 'р/м<sup>3</sup>' : $product->price_ed) ?>
							</div>
						<?php else : ?>
							<div class="price-current product-item-price-current">
								<?= format_price($product->discount_price) ?> <?= (($product->price_ed == 'куб') ? 'р/м<sup>3</sup>' : $product->price_ed) ?>
							</div>
							<span class="price-old product-item-price-old">
								<?= format_price($product->price) ?> <?= (($product->price_ed == 'куб') ? 'р/м<sup>3</sup>' : $product->price_ed) ?>
							</span>
							<div class="price-economy">
								Экономия: <?= format_price($product->price - $product->discount_price) ?> <?= (($product->price_ed == 'куб') ? 'р/м<sup>3</sup>' : $product->price_ed) ?>
							</div>
						<?php endif; ?>
					<?php }?>
				</div>

				<div class="stocks qty-view-m">
					В наличии на складе
					<div class="stock stock-high">
						<i class="fas fa-check"></i>
					</div>
				</div>
				<div class="stocks qty-view-m">
					Действует система скидок в зависимости от объема!
					<div class="stock stock-high">
						<i class="fas fa-check"></i>
					</div>
				</div>
				<div data-entity="buttons-block">
					<div class="buy-buttons">
						<button data-toggle="modal" data-target="#callback-modal" class="btn-buy-1click btn btn-primary btn-border js_ajax_modal" data-product-dprice="<?= $product->discount_price ?>" data-product-price="<?= $product->price ?>" data-product-name="<?= $product->name ?>">
							Заказать консультацию
						</button>
						<a target="_blank" href="https://t.me/+375296918417" class="product-tg-link-btn btn btn-primary btn-border">
							<i class="product-tg-button">
								<img alt="Написать в Телеграм" src="template/inc/images/telegram.svg" width="28" height="28" />
							</i>
							Начать чат в Telegram
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="product-title-labels"><span class="product-title-label active">Описание</span><span class="product-title-label">Характеристики</span></div>
	<div class="row product-attributes-row">
		<div class="col-lg-12 product-info-block">
			<div class="product-attributes-box">
				<div class="product-attributes-descr">
					<?= nl2br($product->descr) ?>
				</div>
			</div>
		</div>
		<div class="col-lg-12 product-info-block hidden-info-block">
			<div class="product-attributes-box">
				<div class="product-features-list-detailed" data-entity="tab-container" data-value="properties">
					<?php foreach ($product->param_options as $param) : ?>
						<div class="feature">
							<div class="feature-name"><span><?= $param['param']->name ?></span></div>
							<div class="feature-value">
								<?php
								$param_values = [];
								foreach ($param['values'] as $value) {
									$param_values[] = $value->name;
								}
								echo implode(", ", $param_values);
								?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>

</div>


<?php if(count($product->related_products) > 0 && $product->related_products[0] != 0) { ?>
	<div class="container">
		<div class="box-title">
			С этим товаром также покупают:
		</div>
		<div class="catalog-products-grid">
            <?php foreach($product->related_products as $related_product){
				if(intval($related_product) != 0){
				$item = product_catalog_get_product($related_product);
                get_tpl_part('product_catalog/product_catalog_item', ['product' => $item]);
				}
            } ?>
		</div>
	</div>
<?php } 

get_tpl_part('default/footer');
