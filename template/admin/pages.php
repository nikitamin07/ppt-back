<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}




get_tpl_part('admin/header');
?>

<h3 class="mb-3"><?=w('Страницы')?> <a class="btn btn-success btn-sm" href="<?=get_path()?>/add"><?=w('Добавить')?></a></h3>
<form name="filters" class="filters-form" method="GET" action="<?=get_path()?>">
	<div class="filters-box">
		<div class="filter-box-group">
			<label>Раздел</label>
			<select name="partition" class="form-control">
				<option <?=(isset($_GET['page_title']) && $_GET['partition'] == 0) ? 'selected' : ''?> value="0">Все</option>
		<?php foreach($partitions as $p):?>
				<option <?=(isset($_GET['page_title']) && $_GET['partition'] == $p->id) ? 'selected' : ''?> value="<?=$p->id?>"><?=$p->name?></option>
		<?php endforeach;?>
			</select>
		</div>
		<div class="filter-box-group">
			<label>Название</label>
			<input value="<?=isset($_GET['page_title']) ? htmlentities($_GET['page_title']) : ''?>" placeholder="Название страницы" name="page_title" class="form-control" type="text" />
		</div>	
		<div class="filter-box-group">
			<label>Алиас</label>
			<input value="<?=isset($_GET['page_alias']) ? htmlentities($_GET['page_alias']) : ''?>" name="page_alias" placeholder="Ссылка страницы" class="form-control" type="text" />
		</div>
		<div class="filter-box-group">
			<label>Сортировка</label>
			<select name="page_sort" class="form-control">
				<option <?=(isset($_GET['page_sort']) && $_GET['page_sort'] == 'created') ? 'selected' : ''?> value="created">по дате</option>		
				<option <?=(isset($_GET['page_sort']) && $_GET['page_sort'] == 'title')   ? 'selected' : ''?> value="title">по названию</option>		
				<option <?=(isset($_GET['page_sort']) && $_GET['page_sort'] == 'alias')   ? 'selected' : ''?> value="alias">по ссылке</option>		
			</select>
		</div>
        <div class="filter-box-group">
            <label>Напраление</label>
            <select name="direction" class="form-control">
                <option <?=(isset($_GET['page_sort']) && $_GET['direction'] == 'desc ')   ? 'selected' : ''?> value="desc">по убыванию</option>
                <option <?=(isset($_GET['page_sort']) && $_GET['direction'] == 'asc')   ? 'selected' : ''?> value="asc">по взрастанию</option>
            </select>
        </div>
        <input style="display: none" name = "search" value="1">
		<div class="filter-box-group">			
			<button type="submit" class="btn btn-primary">Найти</button>
		</div>
	</div>
</form>

<div class="table-overflow">
	<table class="table table-striped">
	  <thead class="thead-dark">
		<tr>
		  <th scope="col"><?=w('Название')?></th>
		  <th scope="col"><?=w('Алиас')?></th>
		  <th scope="col"><?=w('Дата создания')?></th>
		  <th class="w1" scope="col"></th>
		</tr>
	  </thead>
	  <tbody class="align-center">
<?php 
	foreach ($pages as $item){
		echo '<tr>
		  <th>'.$item -> title.'</th>
		  <td>'.$item -> alias.'</td>
		  <td>'.date('H:i:s d.m.Y', strtotime($item -> created)).'</td>
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
<?php
	if(isset($_GET['page'])){
		unset($_GET['page']);
	}
	pagination($total, $onpage, $current, preg_replace('#(\&|\?)page=\d+#', '',$_SERVER['REQUEST_URI']) . (count($_GET) > 0 ? '&' : '?'))?>
<?php

get_tpl_part('admin/footer');
