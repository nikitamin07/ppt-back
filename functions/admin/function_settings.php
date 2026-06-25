<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}


require_once WORK_SPACE.'includes'.DIRECTORY_SEPARATOR.'admin_functions.php';
require_once WORK_SPACE.'includes'.DIRECTORY_SEPARATOR.'cke_file_worker.php';

require_once 'languages'.DIRECTORY_SEPARATOR.lng().'.php';

//require_once WORK_SPACE.'..'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'api_worker.php';


$admin_structure = [
	[
		'url' => 'admin',
		'text' => w('Главная', true),
		'preg' => 'admin',
		'functions' => WORK_SPACE.'main.php'
	],
	[
		'url' => 'admin/partitions',
		'text' => w('Разделы', true),
		'preg' => 'admin\/partitions.*',
		'functions' => WORK_SPACE.'partitions.php'
	],
	[
		'url' => 'admin/pages',
		'text' => w('Страницы', true),
		'preg' => 'admin\/pages.*',
		'functions' => WORK_SPACE.'pages.php'
	],
	[
		'url' => 'admin/blocks',
		'text' => w('Блоки', true),
		'preg' => 'admin\/blocks.*',
		'functions' => WORK_SPACE.'blocks.php'
	],
    [
		'url' => 'admin/plugins',
		'text' => w('Плагины', true),
		'preg' => 'admin\/plugins',
		'functions' => WORK_SPACE.'plugins.php'
	]
];


trigger('admin_menu_created');

$admin_structure[] = [
	'url' => 'admin/mailing_templates',
	'text' => w('Шаблоны рассылки', true),
	'preg' => 'admin\/mailing_templates.*',
	'functions' => WORK_SPACE.'mailing_templates.php'
];

$admin_structure[] = [
	'url' => 'admin/mailing',
	'text' => w('Рассылка', true),
	'preg' => 'admin\/mailing',
	'functions' => WORK_SPACE.'mailing.php'
];

$admin_structure[] = [
		'url' => 'admin/logout',
		'text' => w('Выйти', true),
		'preg' => 'admin\/logout',
		'functions' => WORK_SPACE.'logout.php'
];

function get_functions_file($path){
	global $admin_structure;
	foreach ($admin_structure as $item){
        if (preg_match('/^'.$item['preg'].'$/', $path)){
            return $item['functions'];
        }
	}
	return null;
}

function get_menu(){
	global $admin_structure;
	return $admin_structure;
}





