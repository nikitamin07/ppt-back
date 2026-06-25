<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

get_tpl_part('default/header', ['descr' => 'ППТ.бел - Главная страница. Купить качественный пенопласт в минске по низким ценам.
 Доставка пенопласта по всей Беларуси. Самовывоз пенопласта в Минске. Пенопласт от производителя в Беларуси. Купить пенопласт оптом в Беларуси.']);
?>
<div class="main-catalogs-box">
	<div class="container">
		<h1 class="main-page-h1">Главная</h1>
		<div class="row new-grid align-items-stretch">
		
		<?=get_block('Главная плитка категорий');?>
		
		</div>
	</div>
</div>

<?php

get_tpl_part('default/footer');
