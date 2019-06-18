<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/8
 * Time: 18:54
 */

namespace core;


class Di
{

    //DI配置名称
    const DI_LOG = "LOG";//日志容器
    const DI_MYSQL = "MYSQL";//db容器
    const DI_REDIS = "REDIS";//redis容器
    const DI_CONFIG = "CONFIG";//配置容器


    static $instance;
    private $container = array();

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

    public function set($key, $value)
    {
        $this->container[$key] = $value;
    }

    function delete($key)
    {
        unset($this->container[$key]);
    }

    function clear()
    {
        $this->container = array();
    }

    function get($key)
    {
        if (isset($this->container[$key])) {
            $obj = $this->container[$key];
            return $obj;
        }
        return false;
    }
}