<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}




get_tpl_part('admin/header');
?>

<h3 class="mb-3"><?=w('Разделы')?> <a class="btn btn-success btn-sm" href="<?=get_path()?>/add"><?=w('Добавить')?></a></h3>
<div class="table-overflow">
	<table class="table table-striped">
	  <thead class="thead-dark">
		<tr>
		  <th scope="col"><?=w('Название')?></th>
		  <th scope="col"><?=w('Алиас')?></th>
		  <th scope="col"><?=w('Контроллер')?></th>
		  <th class="w1" scope="col"></th>
		</tr>
	  </thead>
	  <tbody class="align-center">
<?php 
	foreach ($partitions as $item){
		echo '<tr>
		  <th>'.$item -> name.'</th>
		  <td>'.$item -> alias.'</td>
		  <td>'.$item -> controller.'</td>
		  <td class="nowrap">
			<a class="btn btn-primary btn-sm" href="'.get_path().'/'.$item -> id.'">
				<i class="fas fa-pencil-alt"></i>
			</a>
			<a class="btn btn-danger btn-sm" href="'.get_path().'?delete='.$item -> id.'">
				<i class="fas fa-times"></i>
			</a>
		  </td>
		</tr>';
	} 
?>
	  </tbody>
	</table>
</div>
<?php pagination($total, $onpage, $current, get_path().'?')?>
<?php

get_tpl_part('admin/footer');
