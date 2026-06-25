<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}


get_tpl_part('default/header');

?>

<div class="container dogruz_info">
	<h2>Страница не найдена</h2>
	Страница "<?=get_path()?>" не найдена. 
</div>

<?php

get_tpl_part('default/footer');

