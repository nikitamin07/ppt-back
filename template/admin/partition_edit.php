<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}




get_tpl_part('admin/header');
?>

<h3 class="mb-3"><?=(isset($add) ? w('Добавить раздел') : w('Разделы'))?> - <span class="partition-name"><?=$partition -> name?></span></h3>
<form method="post" class="row">
	<div class="col-md-8">
		<div class="card mb-3">
			<div class="card-body">
				<div class="form-group">
					<label><?=w('Название')?></label>
					<input type="text" class="form-control" id="partition-name" name="pname" value="<?=$partition -> name?>" />
				</div>
				<div class="form-group">
					<label><?=w('Алиас')?></label>
					<input type="text" class="form-control" name="alias" value="<?=$partition -> alias?>" />
				</div>
				<div class="form-group">
					<label><?=w('Страница')?></label>
                    <select class="form-control" name="page">
                        <option value="0">Не выбрано</option>
<?php foreach ($pages as $page): ?>
                        <option value="<?=$page -> id?>" <?php if ($partition -> page == $page -> id) echo 'selected'; ?> ><?=$page -> title?> (<?=$page -> alias?>)</option>
<?php endforeach; ?>
                    </select>
<?php /*
					<input type="text" class="form-control" name="controller" value="<?=$partition -> controller?>" />
*/ ?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card mb-3">
			<div class="card-body">
	<?php if (isset($add)): ?>
				<div>
					<button name="add" class="btn btn-success btn-block" type="submit"><?=w('Добавить')?></button>
				</div>
<?php else: ?>
				<div class="buttons-container">
					<div>
						<button name="save" class="btn btn-success btn-block" type="submit"><?=w('Сохранить')?></button>
					</div>
					<div>
						<a href="<?=get_path().'?delete='.$partition -> id?>" class="btn btn-danger btn-block" type="submit"><?=w('Удалить')?></a>
					</div>
				</div>
<?php endif; ?>
			</div>
		</div>
	
	
	<h5><?=w('Выберите родительские разделы')?></h5>
	<ul class="list-group">
<?php foreach ($partitions_list as $item): ?>
	  <li class="list-group-item">
	    <input type="checkbox" name="parents[]" value="<?=$item -> id?>" <?php if (in_array($item -> id, $parents)) {echo 'checked';} ?> />
		<?=str_repeat('-', $item -> lvl)?> <?=$item -> name?> (<u title="<?=w('Алиас')?>"><?=$item -> alias?></u>)
	  </li>
<?php endforeach; ?>
	</ul>
	</div>
</form>
<?php

get_tpl_part('admin/footer');
