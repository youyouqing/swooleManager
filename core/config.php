<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/10
 * Time: 14:30
 */

namespace core;


class config
{
    static $instance;

    private $configs = [];

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    static public function shareInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function loadConfig()
    {
        echo "loadconfig";
    }

    public function getConfig()
    {

    }

    public function setConfig()
    {

    }
}