<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/9
 * Time: 19:23
 */

namespace core;


class command
{

    public function __construct()
    {
    }

    static public function handle($argv)
    {
        if (empty($argv)) {
            exit("命令行格式不正确");
        }
        switch (array_pop($argv)) {
            case "http":
                self::httpHandle($argv);
                break;

            case "tcp":
            case "websocket":
            default:
                exit("目前只支持http");
        }
    }

    /**
     * 处理http命令
     * @param $argv 暂时保留 后续动态配置项
     */
    static private function httpHandle($argv)
    {

        config::shareInstance()->loadConfig();

        $configServer = Di::shareInstance()->get("config.server");

        http::beforeRequest();
        $http = new \Swoole\Http\Server($configServer['host'], $configServer['port']);

        $http->set([
            'document_root' => PUBLIC_PATH, // v4.4.0以下版本, 此处必须为绝对路径
            'enable_static_handler' => true,
            'pid_file' => PID_FILE
        ]);
        $http->on('request', 'core\\http::onRequest');
        $http->start();
        http::afterRequest();
    }
}
