<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}


if (isset($_POST['install'])){
    $res = $db -> query("SELECT id FROM plugins WHERE name='".r($_POST['install'])."'");
    
    if ($res -> num_rows == 0){
        if (file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.$_POST['install'].DIRECTORY_SEPARATOR.'install.php')){
            require_once PLUGINS_PATH.DIRECTORY_SEPARATOR.$_POST['install'].DIRECTORY_SEPARATOR.'install.php';
        }
        $db -> query("REPLACE INTO plugins VALUES (null, '".r($_POST['install'])."', 1)");
    }
}


if (isset($_POST['uninstall'])){
    $res = $db -> query("SELECT id FROM plugins WHERE name='".r($_POST['uninstall'])."'");
    
    if ($res -> num_rows > 0){
        $res = $db -> query("DELETE FROM plugins WHERE name='".r($_POST['uninstall'])."'");
        if (file_exists(PLUGINS_PATH.DIRECTORY_SEPARATOR.$_POST['uninstall'].DIRECTORY_SEPARATOR.'uninstall.php')){
            require_once PLUGINS_PATH.DIRECTORY_SEPARATOR.$_POST['uninstall'].DIRECTORY_SEPARATOR.'uninstall.php';
        }
        clear_plugin_data($_POST['uninstall']);
        header('location:'.BASE.'admin/plugins');
        die();
    }
}


$page = new Page();

$plugins = [];


$tmp = scandir(PLUGINS_PATH);

array_splice($tmp, 0, 2);

foreach ($tmp as $plugin_folder){
    $tmp1 = json_decode(file_get_contents(PLUGINS_PATH.DIRECTORY_SEPARATOR.$plugin_folder.DIRECTORY_SEPARATOR.'about.json'));
    
    $tmp1 -> installed = false;
    
    $res = $db -> query("SELECT id FROM plugins WHERE name='".r($tmp1 -> name)."'");
    
    if ($res -> num_rows > 0){
        $tmp1 -> installed = true;
    }
    
    $plugins[] = $tmp1;
}

$page -> setVar('plugins', $plugins);

$page -> show('admin/plugins');


