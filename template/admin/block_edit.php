<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}




get_tpl_part('admin/header');
?>

<h3 class="mb-3"><?=(isset($add) ? w('Добавить блок') : w('Блоки'))?> - <span class="block-key"><?=$block -> block_key?></span></h3>
<form method="post" class="row">
	<div class="col-md-8">
		<div class="card mb-3">
			<div class="card-body">
				<div class="form-group">
					<label><?=w('Название')?></label>
					<input type="text" class="form-control" id="block-key" name="key" value="<?=$block -> block_key?>" />
				</div>
				<div class="form-group">
					<label><?=w('Контент')?></label>
					<textarea class="cke form-control" name="data"><?=$block -> data?></textarea>
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
					<button name="save" class="btn btn-success btn-block btn-sm" type="submit"><?=w('Сохранить')?></button>
				</div>
				<div>
					<a href="<?=get_path().'?delete='.$block -> id?>" class="btn btn-danger btn-block btn-sm" type="submit"><?=w('Удалить')?></a>
				</div>
			</div>
<?php endif; ?>
		
		</div>
	</div>
	</div>
</form>

<?php

get_tpl_part('admin/footer');
