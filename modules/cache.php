<?php


if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}


class cache{
    private $db;
    private $cache_time = 0;
    
    public function __construct($db, $cache_time = 30){
        $this -> db = $db;
        $this -> cache_time = $cache_time;
    }
    
    public function set($key, $data){
        $key = md5($key);
        
        $this -> db -> query("REPLACE INTO cache VALUES ('$key', '".r($data)."', NOW())");
        
    }
    
    
    public function get($key){
        $key = md5($key);
        
        $cahe_time = $this -> cache_time;
        
        if (in_array($key, ['e88c7a28d61617f25bd81dcb2b70bac0', '57594bd742ba918ad4d39b5e4fd22404'])){
            $cahe_time = 360;
        }
        
        $this -> db -> query("DELETE FROM cache WHERE id='$key' AND created < DATE_SUB(NOW(),INTERVAL {$cahe_time} MINUTE)");
        $res = $this -> db -> query("SELECT data FROM cache WHERE id='$key'");
        echo $this -> db -> error;
        if ($res -> num_rows == 0){
            return null;
        }
        
        $data = $res -> fetch_object() -> data;
        
        if (in_array($key, ['e88c7a28d61617f25bd81dcb2b70bac0'])){
            $tmp = xml2array($data);
            if (!isset($tmp['aj']['0']['row'])){
                return null;
            }
        }
        
        return $data;
        
    }
    
}


