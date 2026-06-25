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
		<header>
		</header>
		<div class="admin-panel">
			<div class="admin-menu">
<?php output_menu(get_menu()) ?>
			</div>
			<div class="admin-work-space">
				<div class="container-fluid">
	