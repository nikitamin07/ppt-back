<?php

if (!defined('INDEXED')) {
    header("HTTP/1.0 404 Not Found");
    die();
}

?>
<div class="catalog-products-item">
    <div class="catalog-products-item-in">
        <div class="product-in-image">
            <a href="<?= generate_product_link($product) ?>" class="product-in-image-wrapper">
                <div class="product-in-image-box">
                    <img class="lazy" alt="<?= $product->name ?>" width="295" height="211" data-src="<?= $product->image ?? '' ?>?s=295x211" onerror="this.src='template/inc/images/no_image.jpg'" />
                </div>
            </a>
            <div class="catalog-product-item-buttons">
            </div>
        </div>
        <div class="product-in-name">
            <a href="<?= generate_product_link($product) ?>">
                <?= $product->name ?>
            </a>
        </div>
        <div class="prices product-item-info-container product-item-price-container" data-entity="price-block">
            <?php if ($product->is_volume_price == 1) {
                if ($product->price_before10 != 0) { ?>
                    <div class="price-current product-item-price-current">
                        <?= format_price($product->price_before10) ?> <?= (($product->price_ed == 'куб') ? 'р/м<sup>3</sup>' : $product->price_ed) ?>
                    </div>
                    <div class="volume_price_desc">При заказе до 10 м<sup>3</sup></div>
                <?php }
                if ($product->price_10to20 != 0) { ?>
                    <div class="price-current product-item-price-current">
                        <?= format_price($product->price_10to20) ?> <?= (($product->price_ed == 'куб') ? 'р/м<sup>3</sup>' : $product->price_ed) ?>
                    </div>
                    <div class="volume_price_desc">При заказе от 10 до 20 м<sup>3</sup></div>
                <?php }
                if ($product->price_after20 != 0) { ?>
                    <div class="price-current product-item-price-current">
                        <?= format_price($product->price_after20) ?> <?= (($product->price_ed == 'куб') ? 'р/м<sup>3</sup>' : $product->price_ed) ?>
                    </div>
                    <div class="volume_price_desc">При заказе от 20 м<sup>3</sup></div>
                <?php }
            } else { ?>
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
            <?php } ?>
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
            </div>
        </div>
    </div>
</div>