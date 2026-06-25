<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}

class Page{
	private $vars = array();
	
	public function setVar($name, $value){
		$this -> vars[$name] = $value;
	}
	
	public function delVar($name){
		unset($this -> vars[$name]);
	}
	
	public function getVar($name){
		return $this -> vars[$name];
	}
	
    
    public function show($template, $from_root = false){
		 require_once TEMPLATE_SETTING_PATH.'template_settings.php';
        if ($from_root){
            if (file_exists($template.'.php')){
                extract($this -> vars);
                require_once($template.'.php');
            } else {
                call_404();
            }
        } else {
            if (file_exists(TEMPLATE_PATH.$template.'.php')){
                extract($this -> vars);
                require_once(TEMPLATE_PATH.$template.'.php');
            } else {
                call_404();
            }
        }
	}
    
	
}


