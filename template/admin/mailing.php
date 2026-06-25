<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}




get_tpl_part('admin/header');
?>

<h3 class="mb-3"><?=w('Рассылка');?></h3>
<div class="row">
	<div class="col-md-8">
		<div class="card mb-3">
			<div class="card-header">
                <h5><?=w('Результаты рассылки');?></h5>
				<div class="row">
					<div class="col-md-5">
						<h6><?=w('Адрес');?></h6>
					</div>
					<div class="col-md-7">
						<h6><?=w('Статус');?></h6>
					</div>
				</div>
            </div>
			<div class="card-body">
			<?php $it=1;
			foreach($mail_adresses as $adress) { ?>
				<div data-mail-iterator="<?= $it ?>" data-adress-id="<?= $adress -> id ?>" class="row form-group mailing-results">
					<div class="col-md-5 mailing-adress">
						<?= $adress -> adress ?>
					</div>
					<div data-state="<?= ($adress -> state == 0) ? '0' : '1'?>" data-error="<?= ($adress -> error != NULL ) ? '1':'0'?>" class="col-md-7 mailing-status">
						<?= ($adress -> state == 0) ? 'Не отправлено' : 'Отправлено'?><?= ($adress -> error != NULL ) ? '. ' . $adress -> error : ''?>
					</div>
				</div>
			<?php $it++; } ?>
			</div>
		</div>
	</div>
	<div class="col-md-4">
	<form class="mailing-sending-form">
	<div class="card mb-3">
		<div class="card-body">
			<h5><?=w('Заголовок письма')?></h5>
			<textarea class="form-control mb-3" name="mail_title"><?= $mail_title ?></textarea>
			<h5><?=w('Шаблон')?></h5>
			<select class="form-control mb-3" name="template">
				<?php foreach ($templates as $t): ?>
					<option value="<?=$t?>"><?=$t?></option>
				<?php endforeach; ?>
			</select>
			<div class="buttons-container">
				<button class="btn btn-success btn-block btn-sm mail-send-btn" type="submit"><?=w('Начать')?></button>
			</div>
			<div class="stats-container">
				<div class="mailing-blocked">
					
				</div>
				<div class="mailing-total">
					Всего: <?= $mailing_info['total'] ?>
				</div>
				<div>
					Не отправлено: <span class="mailing-not-sent"><?=$mailing_info['not_sent']?></span>
				</div>
				<div>
					Ошибки: <span class="mailing-errors"><?=$mailing_info['errors']?></span>
				</div>
			</div>
		</div>
	</div>
	</form>
	</div>
</div>
<?php

get_tpl_part('admin/footer');
