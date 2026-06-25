<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}


add_style(
	'bootstrap',
	TEMPLATE_URL.'inc/css/bootstrap/bootstrap.min.css',
	0,
	[],
	true
);

$including_styles = [
    'fonts' => 'fonts.css',
    'awesome' => 'awesome.min.css'
];


$including_styles['style'] 		= 'style.css?ver='.time();
$including_styles['add-styles'] = 'add.css?ver='.time();


foreach ($including_styles as $id => $file){
    add_style(
        $id,
        TEMPLATE_URL.'inc/css/'.$file, 
        0,
        [],
        true
    );
}

add_script(
	'jquery',
	'https://code.jquery.com/jquery-3.6.0.min.js'
);

add_script(
	'bootstrap',
	TEMPLATE_URL.'inc/js/bootstrap/bootstrap.bundle.min.js', 
	0,
	[]
);


$including_scripts = [   
	'notify' => 'bootstrap-notify.min.js',
    'main' => 'main.js?ver='.time(),
    'add' => 'add.js?ver='.time()
];


foreach ($including_scripts as $id => $file){
    add_script(
        $id,
        TEMPLATE_URL.'inc/js/'.$file, 
        0,
        [],
        false
    );
}






//require_once TEMPLATE_URL.DIRECTORY_SEPARATOR.'render_functions.php';


