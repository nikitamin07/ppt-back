<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

?><!DOCTYPE html>
<html>
	<head>
		<base href="<?=BASE?>">
		<?php output_styles('header');  ?>
		<?php output_scripts('header'); ?>
		<script>
			var s_locale = {};
		</script>
	</head>
	<body>
		<div class="auth-cont">
			<form method="post" class="auth-form card">
				<div class="card-body">
					<div class="form-group">
						<label><?=w('Логин')?></label>
						<input type="text" class="form-control" name="login" />
					</div>
					<div class="form-group">
						<label><?=w('Пароль')?></label>
						<input type="password" class="form-control" name="pass" />
					</div>
<?php if (isset($error)):?>
					<div class="form-group text-danger">
						<?=$error;?>
					</div>
<?php endif; ?>
					<button type="submit" name="admin_auth" class="btn btn-primary btn-block">Войти</div>
				</div>
			</form>
		</div>
	<?php output_styles('footer'); ?>
	<?php output_scripts('footer'); ?>
	</body>
</html>
	