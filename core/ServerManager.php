<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 19-6-13
 * Time: 下午7:49
 */

namespace core;


class ServerManager
{
    static $instance;
    private $swooleServer;

    static public function shareInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 设置swooleServer
     */
    public function setSwooleServer($server)
    {
        $this->swooleServer = $server;
    }

    /**
     * 获取swooleServer
     * @param $server
     */
    public function getSwooleServer()
    {
        return $this->swooleServer;
    }
}