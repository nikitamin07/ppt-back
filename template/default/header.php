<?php
if (!defined('INDEXED')) {
	header("HTTP/1.0 404 Not Found");
	die();
}

if (isset($_COOKIE['at'], $_COOKIE['time']) && ($_COOKIE['time'] - time() > 0)) {
	$panel_flag = true;
} else {
	$panel_flag = false;
}

$head_menu = site_menus_output_menu_get_object_by_id('head_menu');
$head_menu->items = json_decode($head_menu->menu_data, false);


$catalog_menu = site_menus_output_menu_get_object_by_id('catalog');
$catalog_menu->items = json_decode($catalog_menu->menu_data, false);

if (isset($metas) && $metas["og:title"] != '') {
	$page_title = $metas["og:title"];
} else if (isset($title)) {
	$page_title = $title . ' - ППТ.бел';
} else {
	$page_title = 'ППТ.бел';
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<base href="<?= BASE ?>">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0">
	<link rel="shortcut icon" href="/template/inc/images/logo_black.png"/>

	<title><?= $page_title ?></title>
	<meta name="description" content="
	<?php if (isset($metas) && $metas["og:description"] != '') {
		echo $metas["og:description"];
	} else if (isset($descr)) {
		echo $descr;
	} else {
		echo $page_title . ' //шаблонная фраза';
	}
	?>" />
	<?php if (isset($need_canonical) && $need_canonical) : ?>
		<link rel="canonical" href="<?php host();
									echo get_path(); ?>" />
	<?php endif; ?>
	<!--css-->
	<?php output_styles('header');  ?>
	<!--js-->
	<?php output_scripts('header'); ?>
</head>

<body>


	<header class="header">
		<?php if ($panel_flag) { ?>
			<div class="adminer-panel" style="background:white; display:block; width:100%;" role="alert">
				<a class="btn btn-outline-dark" href="./admin">Админ панель</a>
				<button class="btn btn-outline-dark" name="edit_page">Редактиравать страницу</button>
			</div>
		<?php } ?>
		<div class="head-wr container">
			<div class="head-wr-box">
				<div class="head-col header-col-logo">
					<div class="header-logo">
						<a href="./">
							<img width="70" height="60" alt="logo" src="template/inc/images/logo.png" onerror="this.src='template/inc/images/no_image.jpg'" />
						</a>
					</div>
				</div>
				<?php if (!isset($no_search)) { ?>
				<div class="head-col header-col-search">
					<div class="header-search-box">
						<form action="search">
							<input type="text" name="query" value="<?= (isset($_GET['query']) ? htmlentities($_GET['query']) : '') ?>" autocomplete="off" placeholder="Поиск товаров…" class="form-control">
							<button class="btn" type="submit"><i class="fas fa-search"></i></button>
						</form>
					</div>
				</div>
				<?php } ?>
				<div class="head-col header-col-contacts">
					<div class="head-contacts-box">
						<div class="head-ct-item phone-item">
							<i class="fas fa-phone-alt"></i>
							<a href="tel:80296918417">8 (029) 691-84-17</a>
						</div>
					</div>
					<div class="head-contacts-box">
						<div class="head-ct-item phone-item">
							<i class="fas fa-phone-alt"></i>
							<a href="tel:80259264845">8 (025) 926-48-45</a>
						</div>
					</div>
					<div class="head-ct-item callback-item">
						<span data-toggle="modal" data-target="#callback-modal" class="btn-callback">
							Заказать звонок
						</span>
					</div>
				</div>
				<div class="head-col header-col-social">
					<a class="social-img tg" target="_blank" href="https://t.me/+375296918417">
						<img alt="Написать в Телеграм" src="template/inc/images/telegram.svg" width="32" height="32">
					</a>
					<a class="social-img" target="_blank" href="viber://chat?number=%2B375296918417">
						<img alt="Написать в Вайбер" src="template/inc/images/viber.svg" width="32" height="32">
					</a>
				</div>
			</div>
		</div>
		<div class="head-menu">
			<div class="head-menu-box container">
				<div class="menu-wr">
					<div class="menu-item havesub catalog-menu-item">
						<div class="item-link">
							<a href="catalog">
								<i class="fas fa-bars"></i>
								Каталог
							</a>
						</div>
						<div class="submenu catalog_sub">
							<?php foreach ($catalog_menu->items as $item) : ?>
								<div class="sub-item">
									<div class="sub-item-link">
										<a href="<?= $item->url; ?>"><?= $item->name ?></a>
									</div>
									<?php if (isset($item->childs)) : ?>
										<div class="sub_item_childs">
											<?php foreach ($item->childs as $c) : ?>
												<div class="sub_child_item">
													<a href="<?= $c->url; ?>">
														<div class="sub_child_item_box">
															<div class="sub_child_item_img">
																<img alt="<?= $c->name ?>" width="200" height="200" class="lazy" data-src="<?= $c->image; ?>?s=200x200" onerror="this.src='template/inc/images/no_image.jpg'" />
															</div>
															<div class="sub-item-link">
																<?= $c->name ?>
															</div>
														</div>
													</a>
												</div>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
					<?php foreach ($head_menu->items as $item) : ?>
						<?php if (isset($item->childs)) : ?>
							<div class="menu-item havesub">
								<div class="item-link">
									<a href="<?= $item->url ?>">
										<?= $item->name ?>
										<i class="fas fa-chevron-down"></i>
									</a>
								</div>
								<div class="submenu">
									<?php foreach ($item->childs as $c) : ?>
										<div class="sub-item">
											<div class="sub-item-link">
												<a href="<?= $c->url; ?>"><?= $c->name ?></a>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						<?php else : ?>
							<div class="menu-item">
								<div class="item-link">
									<a href="<?= $item->url ?>"><?= $item->name ?></a>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
					<div class="menu-mobile">
						<div class="menu-mobile-switcher">
							Меню сайта
							<i class="fas fa-chevron-down"></i>
						</div>
						<div class="menu-mobile-box">
							<?php foreach ($head_menu->items as $item) : ?>
								<?php if (isset($item->childs)) : ?>
									<div class="menu-item havesub">
										<div class="item-link">
											<a href="<?= $item->url ?>">
												<?= $item->name ?>
												<i class="fas fa-chevron-down"></i>
											</a>
										</div>
										<div class="submenu">
											<?php foreach ($item->childs as $c) : ?>
												<div class="sub-item">
													<div class="sub-item-link">
														<a href="<?= $c->url; ?>"><?= $c->name ?></a>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								<?php else : ?>
									<div class="menu-item">
										<div class="item-link">
											<a href="<?= $item->url ?>"><?= $item->name ?></a>
										</div>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

	</header>


	<div class="main-page-wrapper">