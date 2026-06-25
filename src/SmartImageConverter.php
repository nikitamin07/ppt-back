<?php

class SmartImageConverter {
    const DIR = __DIR__.'/..';
    
    private static $filename = null;
    private static $db = null;
    private static $enabledSizes = null;

    private static function initDB() {
        if (self::$db == null) {
            self::$db = new mysqli("localhost", DB_USER, DB_PASS, DB_NAME);
            if (self::$db->connect_error) {
                die("Connect Error (" . self::$db->connect_errno . ") " . self::$db->connect_error);
            }
            if (!self::$db->set_charset("utf8")) {
                die(printf("Ошибка при загрузке набора символов utf8: %s\n", self::$db->error));
            }
        }
    }
    
    private static function getResizeCode() {
        if (isset($_GET['w'])){
            return 'w='.intval($_GET['w']);
        }
        if (isset($_GET['h'])){
            return 'h='.intval($_GET['h']);
        }
        if (isset($_GET['s'])){
            $tmp = explode('x', $_GET['s']);
            return 's='.intval($tmp[0]).'x'.intval($tmp[1]);
        }
    }
    
    public static function executeConvertation($src, $size){
        $info = pathinfo($src);
        switch ($info['extension']){
            case 'png': $pngimg = imagecreatefrompng($src);
                
                $w = imagesx($pngimg);
                $h = imagesy($pngimg);
                
                $img = imagecreatetruecolor ($w, $h);
                imageAlphaBlending($img, false);
                imageSaveAlpha($img, true);
                
                $trans = imagecolorallocatealpha($img, 0, 0, 0, 127);
                imagefilledrectangle($img, 0, 0, $w - 1, $h - 1, $trans);

                // copy png to canvas

                imagecopy($img, $pngimg, 0, 0, 0, 0, $w, $h);
                
                imagedestroy($pngimg);
                break;
            case "jpeg":
            case "jpg": $img = imagecreatefromjpeg($src);
                break;
        }

        $sizeData = explode('=', $size);

        $sizeType = $sizeData[0];
        $sizeValue = $sizeData[1];
        switch ($sizeType) {
            case 'w':
                $img = imagescale($img, $sizeValue);
                break;
            case 'h':
                $data = getimagesize($src);
                $ratio = $data[0]/$data[1];
                $sizeValue = $sizeValue*$ratio;
                $img = imagescale($img, $sizeValue);
                break;
            case 's':
                $data = getimagesize($src);
                $initialWidth = $data[0];
                $initialHeight = $data[1];

                $ratio = $initialWidth/$initialHeight;

                $sizeValue = explode('x', $sizeValue);
                $scaleWidth = $sizeValue[0];
                $scaleHeight = $sizeValue[1];

                $newScaleHeight = $scaleWidth/$ratio;

                if ($newScaleHeight > $scaleHeight){
                    $sizeValue = $scaleHeight*$ratio;
                } else {
                    $sizeValue = $scaleWidth;
                }

                $img = imagescale($img, $sizeValue);
                break;
        }
        
        $newFile = $info['dirname'] . '/' . $info['filename'] .'_' . $size . '.' . 'webp';
        
        imagewebp($img, $newFile, 100);
        imagedestroy($img);
        return $newFile;
    }


    public static function runJobs() {
        self::initDB();
        $res = self::$db -> query("SELECT * FROM converter_jobs LIMIT 50");
        while ($job = $res -> fetch_object()){
            self::executeConvertation($job -> file_path, $job -> size);
            self::$db -> query("DELETE FROM converter_jobs WHERE id = '{$job -> id}'");
        }
    }
    
    public static function addJob($image_path, $size) {
        self::$db -> query("REPLACE INTO converter_jobs VALUES (null, '".md5($image_path)."', '".self::$db ->real_escape_string($image_path)."', '$size')");
    }
    
    private static function checkSizeEnable($size){
        if (self::$enabledSizes == null){
            $data = file_get_contents(self::DIR.'/tmp/enabled_sizes.ini');
            self::$enabledSizes = [];
            $data = explode("\n", $data);
            foreach ($data as $item){
                self::$enabledSizes[] = trim($item);
            }
        }
        if (in_array($size, self::$enabledSizes)){
            return true;
        }
        self::saveResizeList(self::$enabledSizes);
        return true;
    }
    
    public static function saveResizeList($list) {
        $fileContent = implode("\n", $list);
        file_put_contents(self::DIR.'/tmp/enabled_sizes.ini', $fileContent);
    }


    public static function onUnconvertedImageRequest() {
        self::$filename = self::DIR.'/'.$_GET['filename'];

        $resize = self::getResizeCode();
        
        if (self::checkSizeEnable($resize)){
            self::$filename = self::executeConvertation(self::$filename, $resize);
        }
        //self::$db -> query("REPLACE INTO converter_jobs VALUES (null, '".md5(self::$filename)."', '".self::$db ->real_escape_string(self::$filename)."', '$resize')");

        self::renderImage();
    }

    private static function renderImage() {
        $file_extension = strtolower(substr(strrchr(basename(self::$filename), "."), 1));
        
        switch ($file_extension) {
            case "png": $ctype = "image/png";
                break;
            case "jpeg":
            case "jpg": $ctype = "image/jpeg";
                break;
            case "webp": $ctype = "image/webp";
                break;
            default:
        }
        
        header('Content-type: ' . $ctype);

        echo file_get_contents(self::$filename);
    }

}
