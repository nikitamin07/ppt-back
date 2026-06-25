<?php
class TelegramBot
{

    private static $list_chat = [];
    private static $api = null;

    public static function init(){
        self::$list_chat = explode(",", TELEGRAM_CHATS);
        self::$api = TELEGRAM_API;
    }

    public static function sendMessages($text){
        foreach (self::$list_chat as $chat){
            self::send($chat, $text);
        }
    }

    private static function send($chat_id, $text)
    {

        $array = array(
            'chat_id' => $chat_id,
            'text' => $text
        );

        $ch = curl_init("https://api.telegram.org/" . self::$api . "/sendMessage");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $array);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);
    }
}