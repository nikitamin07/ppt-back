<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}




get_tpl_part('admin/header');
?>

<h3 class="mb-3"><?=(isset($add) ? w('Добавить страницу') : w('Страницы'))?> - <span class="page-title"><?=$page -> title?></span></h3>
<form method="post" class="row">
	<div class="col-md-8">
		<div class="card mb-3">
			<div class="card-body">
				<div class="form-group">
					<label><?=w('Название')?></label>
					<input type="text" class="form-control" id="page-title" name="title" value="<?=$page -> title?>" />
				</div>
				<div class="form-group">
					<label><?=w('Алиас')?></label>
					<input type="text" class="form-control" name="alias" value="<?=$page -> alias?>" />
				</div>
				<div class="form-group">
					<label><?=w('Контент')?></label>
					<textarea class="cke form-control" name="content"><?=$page -> content?></textarea>
				</div>
				<div class="form-group">
					<label><?=w('Краткое описание')?></label>
					<input type="hidden" name="meta_key[]" value="short_desc">
					<textarea class="cke form-control" name="meta_value[]"><?=$short_desc?></textarea>
				</div>
			</div>
		</div>
		<div class="card mb-3">
			<div class="card-body">
				<h5><?=w('Дополнительные поля')?></h5>
				<div class="meta-list mb-2">
<?php foreach ($metas as $key => $value): ?>
					<div class="form-group meta-field">
						<div class="row">
							<div class="col-md-4">
								<input type="text" name="meta_key[]" class="form-control mb-1" placeholder="<?=w('Ключ')?>" value="<?=$key?>">
							</div>
							<div class="col-10 col-md-7">
								<textarea name="meta_value[]" class="form-control mb-1" placeholder="<?=w('Значение')?>"><?=$value?></textarea>
							</div>
							<div class="col-2 col-md-1 text-center">
								<i class="text-danger fas fa-times remove-meta"></i>
							</div>
						</div>
					</div>
<?php endforeach; ?>
				</div>
				<button type="button" class="add-meta btn btn-primary btn-block"><?=w('Добавить дополнительное поле')?></button>
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
					<button name="save" class="btn btn-success btn-block btn-sm" type="submit"><?=w('Сохранить')?></button>
				</div>
				<div>
					<a href="<?=get_path().'?delete='.$page -> id?>" class="btn btn-danger btn-block btn-sm" type="submit"><?=w('Удалить')?></a>
				</div>
			</div>
<?php endif; ?>
		
		</div>
	</div>
<?php if (!isset($add)): ?>
	<div class="card mb-3">
		<div class="card-body">
			<h5><?=w('Дата создания')?></h5>
			<div><?=date('H:i:s d.m.Y', strtotime($page -> created))?></div>
		</div>
	</div>
<?php endif; ?>
	<div class="card mb-3">
		<div class="card-body">
			<h5><?=w('Шаблон')?></h5>
			<select class="form-control" name="template">
				<option value=""><?=w('Стандартный')?></option>
<?php foreach ($templates as $t): ?>
				<option value="<?=$t?>"<?=(($page -> template == $t) ? ' selected' : '')?>><?=$t?></option>
<?php endforeach; ?>
			</select>
		</div>
	</div>
	<h5><?=w('Выберите родительские разделы')?></h5>
	<ul class="list-group mb-3">
<?php foreach ($partitions_list as $item): ?>
	  <li class="list-group-item">
	    <input type="checkbox" name="parents[]" value="<?=$item -> id?>" <?php if (in_array($item -> id, $parents)) {echo 'checked';} ?> />
		<?=str_repeat('-', $item -> lvl)?> <?=$item -> name?> (<u title="<?=w('Алиас')?>"><?=$item -> alias?></u>)
	  </li>
<?php endforeach; ?>
	</ul>
    <?php trigger('show_page_right_column', $page -> id); ?>
	</div>
</form>
<script>
	s_locale['Ключ'] = '<?=w('Ключ')?>';
	s_locale['Значение'] = '<?=w('Значение')?>';
</script>
<?php

get_tpl_part('admin/footer');
