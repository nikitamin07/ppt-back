<?php

if (!defined('INDEXED')) {
	header("HTTP/1.0 404 Not Found");
	die();
}

$page = new StdClass();

$page->alias = 'catalog';
$page->name = 'Каталог';

get_tpl_part('default/header', ['title' => $page->name, 'need_canonical' => true]);

//$breadcrumbs = getBreadEl(urlarray());

?>

<div class="container <?= $page->alias ?>-page">
	<?php get_tpl_part('default/page-top', ['page' => $page, 'breadcrumbs' => $breadcrumbs]); ?>

	<div class="catalog-wrapper row">


		<?php foreach ($categories as $cat) : ?>
			<div class="catalog-item col-lg-6 col-12">
				<div class="catalog-item-box">
					<div class="catalog-item-img">
						<a href="catalog/<?= $cat->alias ?>">
							<img alt="<?= $cat->name ?>" src="<?= partition_image_get_image($cat->id) ?>?s=350x350" onerror="this.src='template/inc/images/no_image.jpg'" />

						</a>
					</div>
					<div class="catalog-item-content">
						<div class="catalog-item-title">
							<a href="catalog/<?= $cat->alias ?>"><?= $cat->name ?></a>
						</div>
						<?php if (isset($cat->childrens)) : ?>
							<div class="catalog-item-subcategories">
								<?php foreach ($cat->childrens as $child) : ?>
									<a href="catalog/<?= $cat->alias . '/' . $child->alias ?>"><?= $child->name ?></a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

</div>

<?php

get_tpl_part('default/footer'); ?>