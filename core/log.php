<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/30
 * Time: 14:25
 */

namespace core;


class log
{
    static $instance;

    const MSG_TYPR_ERROR   = 3;
    const MSG_TYPR_ALERT   = 2;
    const MSG_TYPR_MESSAGE = 1;

    private $type_map = [
        self::MSG_TYPR_ERROR => " [ERROR]: ",
        self::MSG_TYPR_ALERT => " [ALERT]: ",
        self::MSG_TYPR_MESSAGE => " [MESSAGE]: ",
    ];

    private function __construct(){}

    static public function shareInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public function log($msg = "", $msg_type = self::MSG_TYPR_ERROR , $file = APP_PATH."/runtime/log/")
    {
        !is_dir($file) && mkdir($file);
        $file .= date("Ymd");
        !is_file($file) && touch($file);
        file_put_contents($file , $this->getMeaage($msg , $msg_type) ,FILE_APPEND);
    }

    private function getMeaage($msg , $msg_type)
    {
        if (is_object($msg) || is_array($msg)) {
            $msg = json_encode($msg , true);
        }
        return date("Y-m-d H:i:s").$this->type_map[$msg_type].$msg.PHP_EOL;
    }
}