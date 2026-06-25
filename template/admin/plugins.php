<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}




get_tpl_part('admin/header');
?>

<h3 class="mb-3"><?=w('Плагины')?></h3>
<div class="table-overflow">
	<table class="table table-striped">
	  <thead class="thead-dark">
		<tr>
		  <th class="w1" scope="col"><?=w('Название')?></th>
		  <th class="w1" scope="col"><?=w('Версия')?></th>
		  <th scope="col"><?=w('Описание')?></th>
		  <th class="w1" scope="col"></th>
		</tr>
	  </thead>
	  <tbody class="align-center">
<?php 
	foreach ($plugins as $item){
		echo '<tr>
		  <th>'.$item -> name.'</th>
		  <td class="text-center">'.$item -> ver.'</td>
		  <td>'.nl2br($item -> description).'</td>
		  <td class="nowrap">
            <form method="post">
			'.($item -> installed ? '<button name="uninstall" value="'.$item -> name.'" class="btn btn-danger">Удалить</button>' : '<button name="install" value="'.$item -> name.'" class="btn btn-primary">Установить</button>').'
            </form>
		  </td>
		</tr>';
	} 
?>
	  </tbody>
	</table>
</div>

<?php

get_tpl_part('admin/footer');
